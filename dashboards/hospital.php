<?php
require_once '../auth/guard.php';
require_role('hospital');
include '../includes/header.php';
?>

<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="text-danger">Hospital Dashboard</h2>
            <div>
                <span class="badge bg-danger rounded-pill px-3 py-2 me-2">Role: Hospital</span>
                <a href="../logout.php" class="btn btn-outline-danger btn-sm">Logout</a>
            </div>
        </div>
        
        <div class="card shadow-sm border-0 border-top border-danger border-3">
            <div class="card-body">
                <h5 class="card-title">Welcome, <?php echo htmlspecialchars($_SESSION['user_name']); ?>!</h5>
                <p class="card-text text-muted">Manage your blood inventory and requests.</p>
                <hr>
                <div class="row mt-4">
                    <div class="col-md-4 mb-3">
                        <div class="card bg-light border-0 h-100">
                            <div class="card-body text-center py-4">
                                <i class="fa-solid fa-bed-pulse fa-3x text-danger mb-3"></i>
                                <h5>Post Blood Request</h5>
                                <p class="text-muted small">Post emergency blood requirements.</p>
                                <a href="../blood_request_create.php" class="btn btn-outline-danger btn-sm">Post Request</a>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 mb-3">
                        <div class="card bg-light border-0 h-100">
                            <div class="card-body text-center py-4">
                                <i class="fa-solid fa-magnifying-glass fa-3x text-danger mb-3"></i>
                                <h5>Find Donors</h5>
                                <p class="text-muted small">Search for available blood donors near you.</p>
                                <a href="../donor_search.php" class="btn btn-outline-danger btn-sm">Search Now</a>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 mb-3">
                        <div class="card bg-light border-0 h-100">
                            <div class="card-body text-center py-4">
                                <i class="fa-solid fa-clock-rotate-left fa-3x text-danger mb-3"></i>
                                <h5>My Requests</h5>
                                <p class="text-muted small">View and manage your requests.</p>
                                <a href="../blood_request_history.php" class="btn btn-outline-danger btn-sm">View History</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
