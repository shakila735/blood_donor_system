<?php
$error = $_GET['error'] ?? '';
$success = $_GET['success'] ?? '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Blood Donor System</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">

    <script>
        function toggleBloodGroup() {
            const role = document.getElementById('role').value;
            const bloodGroupWrapper = document.getElementById('bloodGroupWrapper');

            if (role === 'donor') {
                bloodGroupWrapper.style.display = 'block';
            } else {
                bloodGroupWrapper.style.display = 'none';
            }
        }

        document.addEventListener('DOMContentLoaded', function () {
            toggleBloodGroup();

            const roleSelect = document.getElementById('role');
            roleSelect.addEventListener('change', toggleBloodGroup);
        });
    </script>
</head>
<body>

<div class="auth-wrapper">
    <div class="card auth-card">
        <div class="auth-header">
            <div class="brand-logo">🩸</div>
            <h2>Create Account</h2>
            <p>Join the Blood Donor Finder System</p>
        </div>

        <div class="auth-body">
            <?php if (!empty($error)): ?>
                <div class="alert alert-danger">
                    <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>

            <?php if (!empty($success)): ?>
                <div class="alert alert-success">
                    <?php echo htmlspecialchars($success); ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="register_action.php">
                <div class="mb-3">
                    <label class="form-label">Full Name</label>
                    <input type="text" name="name" class="form-control" placeholder="Enter your full name" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Phone Number</label>
                    <input type="text" name="phone" class="form-control" placeholder="e.g. 01712345678" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Email (Optional)</label>
                    <input type="email" name="email" class="form-control" placeholder="Enter your email">
                </div>

                <div class="mb-3">
                    <label class="form-label">Password</label>
                    <input type="password" name="password" class="form-control" placeholder="Enter password" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Role</label>
                    <select name="role" id="role" class="form-select" required>
                        <option value="">Select role</option>
                        <option value="donor">Donor</option>
                        <option value="requester">Requester</option>
                        <option value="hospital">Hospital</option>
                    </select>
                </div>

                <div class="mb-3" id="bloodGroupWrapper" style="display:none;">
                    <label class="form-label">Blood Group</label>
                    <select name="blood_group" class="form-select">
                        <option value="">Select blood group</option>
                        <option value="A+">A+</option>
                        <option value="A-">A-</option>
                        <option value="B+">B+</option>
                        <option value="B-">B-</option>
                        <option value="AB+">AB+</option>
                        <option value="AB-">AB-</option>
                        <option value="O+">O+</option>
                        <option value="O-">O-</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label">Location</label>
                    <input type="text" name="location" class="form-control" placeholder="Enter your location" required>
                </div>

                <button type="submit" class="btn btn-auth w-100">Register</button>
            </form>

            <div class="auth-footer mt-3 text-center">
                Already have an account? <a href="login.php">Login here</a>
            </div>
        </div>
    </div>
</div>

</body>
</html>