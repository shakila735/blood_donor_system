<?php
require_once '../auth/guard.php';
require_role('admin');
include '../includes/header.php';
?>

<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="text-danger">Admin Dashboard</h2>
            <div>
                <span class="badge bg-danger rounded-pill px-3 py-2 me-2">Role: Admin</span>
                <a href="../logout.php" class="btn btn-outline-danger btn-sm">Logout</a>
            </div>
        </div>
        
        <div class="card shadow-sm border-0 border-top border-danger border-3">
            <div class="card-body">
                <h5 class="card-title">Welcome back, <?php echo htmlspecialchars($_SESSION['user_name']); ?>!</h5>
                <p class="card-text text-muted">You are viewing the administration dashboard.</p>
                <hr>
                <div class="row mt-4">
                    <div class="col-md-4 mb-3">
                        <div class="card bg-light border-0">
                            <div class="card-body text-center py-4">
                                <i class="fa-solid fa-users fa-3x text-danger mb-3"></i>
                                <h5>Manage Users</h5>
                                <p class="text-muted small">View and manage all registered users.</p>
                                <button class="btn btn-outline-danger btn-sm" disabled>Coming Soon</button>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 mb-3">
                        <div class="card bg-light border-0">
                            <div class="card-body text-center py-4">
                                <i class="fa-solid fa-hospital fa-3x text-danger mb-3"></i>
                                <h5>Hospitals</h5>
                                <p class="text-muted small">Verify hospital accounts.</p>
                                <button class="btn btn-outline-danger btn-sm" disabled>Coming Soon</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
