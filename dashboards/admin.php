<?php
require_once '../auth/guard.php';
require_once '../config/database.php';
require_role('admin');
include '../includes/header.php';

// Fetch Statistics
$stats = [
    'total_users' => 0,
    'total_donors' => 0,
    'verified_donors' => 0,
    'total_requests' => 0,
    'pending_requests' => 0
];

try {
    // Total Users (Excluding admin if preferred, but usually includes all)
    $stmt = $pdo->query("SELECT COUNT(*) FROM users");
    $stats['total_users'] = $stmt->fetchColumn();

    // Total Donors
    $stmt = $pdo->query("SELECT COUNT(*) FROM users u JOIN roles r ON u.role_id = r.id WHERE r.name = 'donor'");
    $stats['total_donors'] = $stmt->fetchColumn();

    // Verified Donors
    $stmt = $pdo->query("SELECT COUNT(*) FROM users u JOIN roles r ON u.role_id = r.id WHERE r.name = 'donor' AND u.is_verified = 1");
    $stats['verified_donors'] = $stmt->fetchColumn();

    // Total Requests
    $stmt = $pdo->query("SELECT COUNT(*) FROM blood_requests");
    $stats['total_requests'] = $stmt->fetchColumn();

    // Pending Requests
    $stmt = $pdo->query("SELECT COUNT(*) FROM blood_requests WHERE status = 'Pending'");
    $stats['pending_requests'] = $stmt->fetchColumn();

} catch (PDOException $e) {
    // Silently fail or log in real app
}
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
        
        <div class="row mb-4">
            <!-- Widgets -->
            <div class="col-md-3 mb-3">
                <div class="card bg-danger text-white h-100 shadow-sm border-0">
                    <div class="card-body text-center py-4">
                        <i class="fa-solid fa-users fa-2x mb-2"></i>
                        <h3 class="fw-bold"><?php echo $stats['total_users']; ?></h3>
                        <p class="mb-0">Total Users</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="card bg-danger text-white h-100 shadow-sm border-0 opacity-75">
                    <div class="card-body text-center py-4">
                        <i class="fa-solid fa-hand-holding-medical fa-2x mb-2"></i>
                        <h3 class="fw-bold"><?php echo $stats['total_donors']; ?></h3>
                        <p class="mb-0">Total Donors</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="card bg-success text-white h-100 shadow-sm border-0">
                    <div class="card-body text-center py-4">
                        <i class="fa-solid fa-check-circle fa-2x mb-2"></i>
                        <h3 class="fw-bold"><?php echo $stats['verified_donors']; ?></h3>
                        <p class="mb-0">Verified Donors</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="card bg-warning text-dark h-100 shadow-sm border-0">
                    <div class="card-body text-center py-4">
                        <i class="fa-solid fa-clock fa-2x mb-2"></i>
                        <h3 class="fw-bold"><?php echo $stats['pending_requests']; ?></h3>
                        <p class="mb-0">Pending Requests</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mt-4">
            <div class="col-md-6 mb-3">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-body text-center py-5">
                        <i class="fa-solid fa-users-gear fa-4x text-danger mb-3"></i>
                        <h4>Manage Users</h4>
                        <p class="text-muted">View, filter, block, or verify users and donors.</p>
                        <a href="../admin_users.php" class="btn btn-outline-danger">Go to User Management</a>
                    </div>
                </div>
            </div>
            <div class="col-md-6 mb-3">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-body text-center py-5">
                        <i class="fa-solid fa-notes-medical fa-4x text-danger mb-3"></i>
                        <h4>Monitor Requests</h4>
                        <p class="text-muted">Review all blood requests across the system.</p>
                        <a href="../admin_requests.php" class="btn btn-outline-danger">Go to Requests</a>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

<?php include '../includes/footer.php'; ?>
