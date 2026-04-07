<?php
require_once '../config/database.php';
require_once '../auth/guard.php';

require_role('donor');

$user_id = $_SESSION['user_id'];
$success = "";
$error = "";

// Handle Availability Update
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['availability_status'])) {
    $new_status = $_POST['availability_status'];
    if (in_array($new_status, ['Available', 'Unavailable'])) {
        $sql = "UPDATE users SET availability_status = ? WHERE id = ?";
        $stmt = $pdo->prepare($sql);
        try {
            $stmt->execute([$new_status, $user_id]);
            $success = "Availability status updated successfully.";
        } catch (PDOException $e) {
            $error = "Failed to update availability status.";
        }
    }
}

// Fetch current user details including availability
$stmt = $pdo->prepare("SELECT availability_status FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user_data = $stmt->fetch();
$current_status = $user_data['availability_status'] ?? 'Available';

include '../includes/header.php';
?>

<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="text-danger">Donor Dashboard</h2>
            <div>
                <span class="badge bg-danger rounded-pill px-3 py-2 me-2">Role: Donor</span>
                <a href="../logout.php" class="btn btn-outline-danger btn-sm">Logout</a>
            </div>
        </div>
        
        <?php if ($success): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
        <?php endif; ?>
        <?php if ($error): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>

        <div class="card shadow-sm border-0 border-top border-danger border-3">
            <div class="card-body">
                <h5 class="card-title">Welcome, <?php echo htmlspecialchars($_SESSION['user_name']); ?>!</h5>
                <p class="card-text text-muted">Thank you for being a lifesaver. From here you can manage your availability and view requests.</p>
                <hr>
                <div class="row mt-4">
                    <div class="col-md-4 mb-3">
                        <div class="card bg-light border-0 h-100">
                            <div class="card-body py-4">
                                <div class="text-center mb-3">
                                    <i class="fa-solid fa-clock fa-3x text-danger mb-3"></i>
                                    <h5>My Availability</h5>
                                    <p class="text-muted small">Update your status to start or stop appearing in searches.</p>
                                </div>
                                
                                <form method="POST" action="" class="bg-white p-3 rounded shadow-sm border">
                                    <div class="d-flex align-items-center justify-content-between">
                                        <div>
                                            <strong>Current Status:</strong><br>
                                            <?php if ($current_status == 'Available'): ?>
                                                <span class="text-success"><i class="fa-solid fa-check-circle"></i> Available</span>
                                            <?php else: ?>
                                                <span class="text-secondary"><i class="fa-solid fa-clock"></i> Unavailable</span>
                                            <?php endif; ?>
                                        </div>
                                        <div>
                                            <select name="availability_status" class="form-select form-select-sm d-inline-block w-auto" onchange="this.form.submit()">
                                                <option value="Available" <?php echo ($current_status == 'Available') ? 'selected' : ''; ?>>Available</option>
                                                <option value="Unavailable" <?php echo ($current_status == 'Unavailable') ? 'selected' : ''; ?>>Unavailable</option>
                                            </select>
                                        </div>
                                    </div>
                                    <noscript>
                                        <button type="submit" class="btn btn-sm btn-danger mt-3 w-100">Save</button>
                                    </noscript>
                                </form>
                                
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-4 mb-3">
                        <div class="card bg-light border-0 h-100">
                            <div class="card-body text-center py-4">
                                <div class="position-relative d-inline-block">
                                    <i class="fa-solid fa-bell fa-3x text-danger mb-3"></i>
                                    <?php
                                    // Make sure we have the pdo and helper available
                                    if(isset($pdo)) {
                                        require_once '../includes/notification_helper.php';
                                        $dash_unread = get_unread_notification_count($pdo, $_SESSION['user_id']);
                                        if($dash_unread > 0) {
                                            echo '<span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-warning text-dark">' . $dash_unread . '</span>';
                                        }
                                    }
                                    ?>
                                </div>
                                <h5>Notifications</h5>
                                <p class="text-muted small">View recent blood requests matching your profile.</p>
                                <a href="../notifications.php" class="btn btn-danger btn-sm">View Notifications</a>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4 mb-3">
                        <div class="card bg-light border-0 h-100">
                            <div class="card-body text-center py-4">
                                <i class="fa-solid fa-list-check fa-3x text-danger mb-3"></i>
                                <h5>Blood Request Feed</h5>
                                <p class="text-muted small">View all active blood requests and accept one to help.</p>
                                <a href="../blood_requests_feed.php" class="btn btn-danger btn-sm">Browse Requests</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
