<?php
require_once 'config/database.php';
require_once 'includes/validation.php';
session_start();

$errors = [];

if (isset($_SESSION['user_id'])) {
    header("Location: dashboard.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $identifier = sanitize_input($_POST['identifier'] ?? '');
    $password = $_POST['password'] ?? '';
    
    if (empty($identifier)) $errors[] = "Phone or Email is required.";
    if (empty($password)) $errors[] = "Password is required.";

    if (empty($errors)) {
        // Can login with email or phone
        $sql = "SELECT u.*, r.name as role_name 
                FROM users u 
                JOIN roles r ON u.role_id = r.id 
                WHERE u.phone = ? OR u.email = ?";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$identifier, $identifier]);
        $user = $stmt->fetch();
        
        if ($user && password_verify($password, $user['password_hash'])) {
            if ($user['is_active'] != 1) {
                $errors[] = "Your account is deactivated. Please contact admin.";
            } else {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['role_name'] = $user['role_name'];
                $_SESSION['user_name'] = $user['name'];
                
                header("Location: dashboard.php");
                exit();
            }
        } else {
            $errors[] = "Invalid credentials.";
        }
    }
}

include 'includes/header.php';
?>

<div class="row justify-content-center">
    <div class="col-md-5 col-lg-4">
        <div class="card shadow-sm border-0 mt-5">
            <div class="card-body p-4">
                <div class="text-center mb-4">
                    <i class="fa-solid fa-user-circle fa-3x text-danger mb-3"></i>
                    <h3 class="text-danger">Login</h3>
                </div>
                
                <?php if (!empty($errors)): ?>
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            <?php foreach ($errors as $error) echo "<li>$error</li>"; ?>
                        </ul>
                    </div>
                <?php endif; ?>

                <form method="POST" action="">
                    <div class="mb-3">
                        <label class="form-label">Phone or Email</label>
                        <input type="text" name="identifier" class="form-control" required value="<?php echo htmlspecialchars($identifier ?? ''); ?>">
                    </div>

                    <div class="mb-4">
                        <label class="form-label">Password</label>
                        <input type="password" name="password" class="form-control" required>
                    </div>

                    <button type="submit" class="btn btn-danger w-100 py-2">Login</button>
                    
                    <div class="text-center mt-3">
                        Don't have an account? <a href="register.php" class="text-decoration-none">Register here</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
