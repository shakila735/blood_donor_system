<?php
require_once 'config/database.php';
require_once 'includes/validation.php';
require_once 'auth/guard.php';

require_login();

// Allow only Requester and Hospital
$role = $_SESSION['role_name'] ?? '';
if (!in_array($role, ['requester', 'hospital'])) {
    header("Location: dashboard.php");
    exit();
}

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: donor_search.php");
    exit();
}

$donor_id = $_GET['id'];

// Fetch active donor details
$sql = "SELECT u.name, u.phone, u.email, u.blood_group, u.location, u.availability_status 
        FROM users u 
        JOIN roles r ON u.role_id = r.id 
        WHERE u.id = ? AND r.name = 'donor' AND u.is_active = 1";
$stmt = $pdo->prepare($sql);
$stmt->execute([$donor_id]);
$donor = $stmt->fetch();

if (!$donor) {
    // Donor not found or inactive
    echo "Donor not found.";
    exit();
}

include 'includes/header.php';
?>

<div class="row mb-4">
    <div class="col-12 d-flex justify-content-between align-items-center">
        <h2 class="text-danger">Donor Profile</h2>
        <a href="javascript:history.back()" class="btn btn-outline-secondary">Go Back</a>
    </div>
</div>

<div class="row justify-content-center">
    <div class="col-md-8 col-lg-6">
        <div class="card shadow border-0 border-top border-danger border-4">
            <div class="card-body p-4 text-center">
                
                <div class="mb-4">
                    <div class="d-inline-flex justify-content-center align-items-center bg-danger text-white rounded-circle mb-3" style="width: 80px; height: 80px; font-size: 2rem;">
                        <?php echo htmlspecialchars($donor['blood_group']); ?>
                    </div>
                    <h3 class="fw-bold mb-1"><?php echo htmlspecialchars($donor['name']); ?></h3>
                    <p class="text-muted"><i class="fa-solid fa-location-dot"></i> <?php echo htmlspecialchars($donor['location']); ?></p>
                </div>
                
                <div class="d-flex justify-content-center gap-2 mb-4">
                    <?php if ($donor['availability_status'] == 'Available'): ?>
                        <span class="badge bg-success-subtle text-success fs-6"><i class="fa-solid fa-check-circle"></i> Available to Donate</span>
                    <?php else: ?>
                        <span class="badge bg-secondary-subtle text-secondary fs-6"><i class="fa-solid fa-clock"></i> Currently Unavailable</span>
                    <?php endif; ?>
                    <span class="badge bg-primary-subtle text-primary fs-6"><i class="fa-solid fa-certificate"></i> Verified Donor</span>
                </div>

                <div class="card bg-light border-0 text-start">
                    <div class="card-body">
                        <h5 class="card-title text-danger mb-3">Contact Information</h5>
                        <ul class="list-unstyled mb-0">
                            <li class="mb-2">
                                <i class="fa-solid fa-phone text-muted me-2"></i> 
                                <a href="tel:<?php echo htmlspecialchars($donor['phone']); ?>" class="text-decoration-none text-dark fw-bold">
                                    <?php echo htmlspecialchars($donor['phone']); ?>
                                </a>
                            </li>
                            <?php if (!empty($donor['email'])): ?>
                                <li class="mb-2">
                                    <i class="fa-solid fa-envelope text-muted me-2"></i> 
                                    <a href="mailto:<?php echo htmlspecialchars($donor['email']); ?>" class="text-decoration-none text-dark">
                                        <?php echo htmlspecialchars($donor['email']); ?>
                                    </a>
                                </li>
                            <?php endif; ?>
                        </ul>
                    </div>
                </div>
                
                <?php if ($donor['availability_status'] == 'Available'): ?>
                    <div class="mt-4">
                        <p class="text-muted small">Please contact this donor directly to coordinate. Treat their time with respect.</p>
                    </div>
                <?php endif; ?>

            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
