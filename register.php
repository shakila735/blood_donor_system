<?php
require_once 'config/database.php';
require_once 'includes/validation.php';
session_start();

$errors = [];
$success = "";

if (isset($_SESSION['user_id'])) {
    header("Location: dashboard.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = sanitize_input($_POST['name'] ?? '');
    $phone = sanitize_input($_POST['phone'] ?? '');
    $email = sanitize_input($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $role_id = $_POST['role_id'] ?? '';
    $blood_group = $_POST['blood_group'] ?? null;
    $location = sanitize_input($_POST['location'] ?? '');
    
    // Validations
    if (empty($name)) $errors[] = "Name is required.";
    if (empty($phone)) {
        $errors[] = "Phone number is required.";
    } else {
        $formatted_phone = validate_bd_phone($phone);
        if (!$formatted_phone) {
            $errors[] = "Invalid Bangladeshi phone number format. Use 01XXXXXXXXX.";
        } else {
            $phone = $formatted_phone; // use normalized version
            if (phone_exists($pdo, $phone)) {
                $errors[] = "This phone number is already registered.";
            }
        }
    }
    
    if (!empty($email)) {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = "Invalid email format.";
        } elseif (email_exists($pdo, $email)) {
            $errors[] = "This email is already registered.";
        }
    } else {
        $email = null;
    }
    
    if (empty($password)) {
        $errors[] = "Password is required.";
    } elseif (strlen($password) < 6) {
        $errors[] = "Password must be at least 6 characters long.";
    }
    
    if (empty($role_id)) $errors[] = "Role selection is required.";
    if (empty($location)) $errors[] = "Location is required.";
    
    // Check if role requires blood group
    // Hospital might not need a blood group, but donor and requester typically do.
    // For simplicity, we make it optional here unless explicitly requested otherwise, 
    // but the users asks "blood group selection not required for hospital".
    $stmt = $pdo->prepare("SELECT name FROM roles WHERE id = ?");
    $stmt->execute([$role_id]);
    $role_name = $stmt->fetchColumn();
    
    if ($role_name !== 'hospital' && empty($blood_group)) {
        $errors[] = "Blood group is required for your role.";
    } elseif ($role_name === 'hospital') {
        $blood_group = null; // Ensure null for hospital
    }

    if (empty($errors)) {
        $password_hash = password_hash($password, PASSWORD_DEFAULT);
        
        $sql = "INSERT INTO users (role_id, name, phone, email, password_hash, blood_group, location) 
                VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        
        try {
            $stmt->execute([$role_id, $name, $phone, $email, $password_hash, $blood_group, $location]);
            $success = "Registration successful! You can now login.";
            // Optionally redirect
            // header("refresh:2;url=login.php");
        } catch (PDOException $e) {
            $errors[] = "Registration failed. Please try again.";
        }
    }
}

// Fetch roles for dropdown
$roles_stmt = $pdo->query("SELECT id, name FROM roles WHERE name != 'admin'");
$roles = $roles_stmt->fetchAll();

include 'includes/header.php';
?>

<div class="row justify-content-center">
    <div class="col-md-6 col-lg-5">
        <div class="card shadow-sm border-0 mt-4">
            <div class="card-body p-4">
                <h3 class="text-center mb-4 text-danger">Register</h3>
                
                <?php if (!empty($errors)): ?>
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            <?php foreach ($errors as $error) echo "<li>$error</li>"; ?>
                        </ul>
                    </div>
                <?php endif; ?>

                <?php if ($success): ?>
                    <div class="alert alert-success">
                        <?php echo $success; ?> <a href="login.php" class="alert-link">Login here</a>.
                    </div>
                <?php else: ?>
                    <form method="POST" action="" class="needs-validation" novalidate onsubmit="return validateForm()">
                        <div class="mb-3">
                            <label class="form-label">Full Name *</label>
                            <input type="text" name="name" id="reg_name" class="form-control" required value="<?php echo htmlspecialchars($name ?? ''); ?>">
                            <div class="invalid-feedback">Please enter your name.</div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Phone Number * (01XXXXXXXXX)</label>
                            <input type="text" name="phone" id="reg_phone" class="form-control" required value="<?php echo htmlspecialchars($_POST['phone'] ?? ''); ?>">
                            <div class="invalid-feedback">Please enter a valid 11-digit BD phone number.</div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Email (Optional)</label>
                            <input type="email" name="email" id="reg_email" class="form-control" value="<?php echo htmlspecialchars($email ?? ''); ?>">
                            <div class="invalid-feedback">Please enter a valid email address.</div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Password *</label>
                            <input type="password" name="password" id="reg_pass" class="form-control" required minlength="6">
                            <div class="invalid-feedback">Password must be at least 6 characters.</div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Register As *</label>
                            <select name="role_id" id="role_id" class="form-select" required onchange="toggleBloodGroup()">
                                <option value="">Select Role...</option>
                                <?php foreach ($roles as $r): ?>
                                    <option value="<?php echo $r['id']; ?>" <?php echo (isset($role_id) && $role_id == $r['id']) ? 'selected' : ''; ?>>
                                        <?php echo ucfirst($r['name']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <div class="invalid-feedback">Please select a role.</div>
                        </div>

                        <div class="mb-3" id="blood_group_div">
                            <label class="form-label">Blood Group *</label>
                            <select name="blood_group" id="blood_group" class="form-select">
                                <option value="">Select Blood Group...</option>
                                <?php 
                                $bgs = ['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'];
                                foreach ($bgs as $bg): ?>
                                    <option value="<?php echo $bg; ?>" <?php echo (isset($blood_group) && $blood_group == $bg) ? 'selected' : ''; ?>>
                                        <?php echo $bg; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <div class="invalid-feedback">Please select a blood group.</div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Location *</label>
                            <input type="text" name="location" id="reg_location" class="form-control" required value="<?php echo htmlspecialchars($location ?? ''); ?>">
                            <div class="invalid-feedback">Please enter your location.</div>
                        </div>

                        <button type="submit" class="btn btn-danger w-100 mb-3">Register</button>
                        
                        <div class="text-center">
                            Already have an account? <a href="login.php" class="text-decoration-none">Login here</a>
                        </div>
                    </form>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<script>
// Pass roles mapping to JS for dynamic UI logic
const roles = <?php echo json_encode(array_column($roles, 'name', 'id')); ?>;

function toggleBloodGroup() {
    const roleSelect = document.getElementById('role_id');
    const bgDiv = document.getElementById('blood_group_div');
    const bgSelect = document.getElementById('blood_group');
    
    if (roleSelect.value && roles[roleSelect.value] === 'hospital') {
        bgDiv.style.display = 'none';
        bgSelect.removeAttribute('required');
    } else {
        bgDiv.style.display = 'block';
        bgSelect.setAttribute('required', 'required');
    }
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', toggleBloodGroup);

// Basic client side validation
function validateForm() {
    let isValid = true;
    const phone = document.getElementById('reg_phone').value;
    const phonePattern = /^(?:\+88|88)?(01[3-9]\d{8})$/;
    
    if (!phonePattern.test(phone)) {
        document.getElementById('reg_phone').classList.add('is-invalid');
        isValid = false;
    } else {
        document.getElementById('reg_phone').classList.remove('is-invalid');
        document.getElementById('reg_phone').classList.add('is-valid');
    }
    
    const pass = document.getElementById('reg_pass').value;
    if(pass.length < 6) {
        document.getElementById('reg_pass').classList.add('is-invalid');
        isValid = false;
    } else {
        document.getElementById('reg_pass').classList.remove('is-invalid');
        document.getElementById('reg_pass').classList.add('is-valid');
    }
    
    return isValid;
}

// Bootstrap form validation
(function () {
  'use strict'
  var forms = document.querySelectorAll('.needs-validation')
  Array.prototype.slice.call(forms)
    .forEach(function (form) {
      form.addEventListener('submit', function (event) {
        if (!form.checkValidity() || !validateForm()) {
          event.preventDefault()
          event.stopPropagation()
        }
        form.classList.add('was-validated')
      }, false)
    })
})()
</script>

<?php include 'includes/footer.php'; ?>
