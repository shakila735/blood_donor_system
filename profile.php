<?php
require_once 'config/database.php';
require_once 'includes/validation.php';
require_once 'auth/guard.php';

require_login();

$user_id = $_SESSION['user_id'];
$errors = [];
$success = "";

// Fetch logic
if ($_SERVER["REQUEST_METHOD"] == "GET") {
    $stmt = $pdo->prepare("SELECT name, phone, email, location FROM users WHERE id = ?");
    $stmt->execute([$user_id]);
    $user = $stmt->fetch();
    
    $name = $user['name'];
    $phone = $user['phone'];
    $email = $user['email'];
    $location = $user['location'];
}

// Update logic
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = sanitize_input($_POST['name'] ?? '');
    $phone = sanitize_input($_POST['phone'] ?? '');
    $email = sanitize_input($_POST['email'] ?? '');
    $location = sanitize_input($_POST['location'] ?? '');
    
    if (empty($name)) $errors[] = "Name is required.";
    if (empty($phone)) {
        $errors[] = "Phone number is required.";
    } else {
        $formatted_phone = validate_bd_phone($phone);
        if (!$formatted_phone) {
            $errors[] = "Invalid Bangladeshi phone number format. Use 01XXXXXXXXX.";
        } else {
            $phone = $formatted_phone; // use normalized version
            // Check duplicate phone (excluding current user)
            if (phone_exists($pdo, $phone, $user_id)) {
                $errors[] = "This phone number is already registered to another account.";
            }
        }
    }
    
    if (!empty($email)) {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = "Invalid email format.";
        } elseif (email_exists($pdo, $email, $user_id)) {
            $errors[] = "This email is already registered to another account.";
        }
    } else {
        $email = null;
    }
    
    if (empty($location)) $errors[] = "Location is required.";

    if (empty($errors)) {
        $sql = "UPDATE users SET name = ?, phone = ?, email = ?, location = ? WHERE id = ?";
        $stmt = $pdo->prepare($sql);
        try {
            $stmt->execute([$name, $phone, $email, $location, $user_id]);
            $success = "Profile updated successfully.";
            $_SESSION['user_name'] = $name; // Update session name
        } catch (PDOException $e) {
            $errors[] = "Profile update failed.";
        }
    }
}

include 'includes/header.php';
?>

<div class="row justify-content-center">
    <div class="col-md-8 col-lg-6">
        <div class="card shadow-sm border-0 border-top border-danger border-3 mt-4">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h3 class="text-danger mb-0">My Profile</h3>
                    <a href="dashboard.php" class="btn btn-outline-secondary btn-sm">Back to Dashboard</a>
                </div>
                
                <?php if (!empty($errors)): ?>
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            <?php foreach ($errors as $error) echo "<li>$error</li>"; ?>
                        </ul>
                    </div>
                <?php endif; ?>

                <?php if ($success): ?>
                    <div class="alert alert-success">
                        <?php echo $success; ?>
                    </div>
                <?php endif; ?>

                <form method="POST" action="">
                    <div class="mb-3">
                        <label class="form-label">Full Name *</label>
                        <input type="text" name="name" class="form-control" required value="<?php echo htmlspecialchars($name ?? ''); ?>">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Phone Number *</label>
                        <input type="text" name="phone" class="form-control" required value="<?php echo htmlspecialchars($phone ?? ''); ?>">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Email Address (Optional)</label>
                        <input type="email" name="email" class="form-control" value="<?php echo htmlspecialchars($email ?? ''); ?>">
                    </div>

                    <div class="mb-4">
                        <label class="form-label">Location *</label>
                        <input type="text" name="location" class="form-control" required value="<?php echo htmlspecialchars($location ?? ''); ?>">
                    </div>

                    <button type="submit" class="btn btn-danger w-100">Update Profile</button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
