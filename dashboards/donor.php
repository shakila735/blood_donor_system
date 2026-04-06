<?php
require_once '../auth/guard.php';
require_role('donor');
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
        
        <div class="card shadow-sm border-0 border-top border-danger border-3">
            <div class="card-body">
                <h5 class="card-title">Welcome, <?php echo htmlspecialchars($_SESSION['user_name']); ?>!</h5>
                <p class="card-text text-muted">Thank you for being a lifesaver. From here you can manage your availability and view requests.</p>
                <hr>
                <div class="row mt-4">
                    <div class="col-md-4 mb-3">
                        <div class="card bg-light border-0">
                            <div class="card-body text-center py-4">
                                <i class="fa-solid fa-clock fa-3x text-danger mb-3"></i>
                                <h5>My Availability</h5>
                                <p class="text-muted small">Update your status to start or stop receiving requests.</p>
                                <button class="btn btn-outline-danger btn-sm" disabled>Coming Soon</button>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-4 mb-3">
                        <div class="card bg-light border-0">
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
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
