<?php
// test_notification_script.php
// This is a minimal helper script to test the notification generation for Phase 2 without a UI.
// Access it via: http://localhost/blood_donor_system/test_notification_script.php
require_once 'auth/guard.php'; // Ensures script isn't easily tampered with, though it's just for testing
require_login();
require_once 'config/database.php';
require_once 'includes/notification_helper.php';

// Only Admin or Requester/Hospital roles should typically test firing a notification, 
// but we'll accept any logged-in user for simple testing.

$message = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $blood_group = $_POST['blood_group'] ?? '';
    $location = $_POST['location'] ?? '';
    $requester_name = $_SESSION['user_name'] ?? 'System Tester';

    if ($blood_group && $location) {
        $count = notify_matching_donors($pdo, $blood_group, $location, $requester_name);
        $message = "Successfully processed match logic. Notified $count matching donors in $location for $blood_group.";
    } else {
        $message = "Please fill in all fields.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Test Notification - BloodNet</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container py-5">
    <div class="row">
        <div class="col-md-6 mx-auto">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-danger text-white">
                    <h5 class="mb-0">Test Notification Helper Generator</h5>
                </div>
                <div class="card-body">
                    <p class="text-muted small">This is a hidden admin/dev tool to test `notify_matching_donors()` until the actual 'Post Request' form is built by your teammate.</p>
                    
                    <?php if ($message): ?>
                        <div class="alert alert-info"><?php echo htmlspecialchars($message); ?></div>
                    <?php endif; ?>

                    <form method="POST">
                        <div class="mb-3">
                            <label class="form-label">Blood Group to Request</label>
                            <select name="blood_group" class="form-select" required>
                                <option value="">Select Blood Group</option>
                                <option value="A+">A+</option>
                                <option value="A-">A-</option>
                                <option value="B+">B+</option>
                                <option value="B-">B-</option>
                                <option value="O+">O+</option>
                                <option value="O-">O-</option>
                                <option value="AB+">AB+</option>
                                <option value="AB-">AB-</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Location</label>
                            <input type="text" name="location" class="form-control" placeholder="e.g. Dhaka, Bangladesh" required>
                        </div>
                        <button type="submit" class="btn btn-danger w-100">Simulate Posting Request & Trigger Notifications</button>
                    </form>
                    <div class="mt-3 text-center">
                        <a href="dashboard.php" class="text-decoration-none">Return to Dashboard</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>
