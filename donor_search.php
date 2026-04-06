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

$donors = [];
$searched = false;

if ($_SERVER["REQUEST_METHOD"] == "GET" && (isset($_GET['blood_group']) || isset($_GET['location']))) {
    $searched = true;
    $bg = $_GET['blood_group'] ?? '';
    $loc = sanitize_input($_GET['location'] ?? '');
    
    // Base query: only active donors
    $sql = "SELECT u.id, u.name, u.blood_group, u.location, u.availability_status 
            FROM users u 
            JOIN roles r ON u.role_id = r.id 
            WHERE r.name = 'donor' AND u.is_active = 1";
    
    $params = [];
    
    if (!empty($bg)) {
        $sql .= " AND u.blood_group = ?";
        $params[] = $bg;
    }
    
    if (!empty($loc)) {
        $sql .= " AND u.location LIKE ?";
        $params[] = "%$loc%";
    }
    
    // Order by availability (Available first)
    $sql .= " ORDER BY u.availability_status ASC, u.name ASC";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $donors = $stmt->fetchAll();
}

include 'includes/header.php';
$bgs = ['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'];
?>

<div class="row mb-4">
    <div class="col-12 d-flex justify-content-between align-items-center">
        <h2 class="text-danger">Search Donors</h2>
        <a href="dashboard.php" class="btn btn-outline-secondary">Back to Dashboard</a>
    </div>
</div>

<!-- Search Form -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card shadow-sm border-0">
            <div class="card-body">
                <form method="GET" action="" class="row g-3 align-items-end">
                    <div class="col-md-4">
                        <label class="form-label">Blood Group</label>
                        <select name="blood_group" class="form-select">
                            <option value="">Any</option>
                            <?php foreach ($bgs as $b): ?>
                                <option value="<?php echo $b; ?>" <?php echo (isset($_GET['blood_group']) && $_GET['blood_group'] == $b) ? 'selected' : ''; ?>>
                                    <?php echo $b; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Location (City, Area)</label>
                        <input type="text" name="location" class="form-control" placeholder="e.g. Dhaka" value="<?php echo htmlspecialchars($_GET['location'] ?? ''); ?>">
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-danger w-100">
                            <i class="fa-solid fa-magnifying-glass"></i> Search
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Search Results -->
<?php if ($searched): ?>
    <div class="row">
        <div class="col-12">
            <h5 class="mb-3">Results (<?php echo count($donors); ?> found)</h5>
            <?php if (count($donors) > 0): ?>
                <div class="row">
                    <?php foreach ($donors as $d): ?>
                        <div class="col-md-4 mb-3">
                            <div class="card shadow-sm border-0 h-100 <?php echo ($d['availability_status'] == 'Available') ? 'border-start border-success border-4' : 'border-start border-secondary border-4'; ?>">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between">
                                        <h5 class="card-title text-truncate" title="<?php echo htmlspecialchars($d['name']); ?>"><?php echo htmlspecialchars($d['name']); ?></h5>
                                        <span class="badge bg-danger rounded-circle p-2" style="width: 35px; height: 35px; display: flex; align-items: center; justify-content: center;">
                                            <?php echo htmlspecialchars($d['blood_group']); ?>
                                        </span>
                                    </div>
                                    <p class="text-muted small mb-2"><i class="fa-solid fa-location-dot"></i> <?php echo htmlspecialchars($d['location']); ?></p>
                                    
                                    <div class="mb-3">
                                        <?php if ($d['availability_status'] == 'Available'): ?>
                                            <span class="badge bg-success-subtle text-success"><i class="fa-solid fa-check-circle"></i> Available Now</span>
                                        <?php else: ?>
                                            <span class="badge bg-secondary-subtle text-secondary"><i class="fa-solid fa-clock"></i> Unavailable</span>
                                        <?php endif; ?>
                                        
                                        <!-- Mock Verified Badge -->
                                        <span class="badge bg-primary-subtle text-primary"><i class="fa-solid fa-certificate"></i> Verified User</span>
                                    </div>
                                    
                                    <a href="donor_profile.php?id=<?php echo $d['id']; ?>" class="btn btn-outline-danger btn-sm w-100">View Profile</a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="alert alert-info">
                    No active donors found matching your criteria. Try loosening your search.
                </div>
            <?php endif; ?>
        </div>
    </div>
<?php else: ?>
    <div class="row">
        <div class="col-12 text-center text-muted py-5">
            <i class="fa-solid fa-users fa-4x mb-3 text-light"></i>
            <h5>Find Lifesavers</h5>
            <p>Use the search form above to find blood donors near you.</p>
        </div>
    </div>
<?php endif; ?>

<?php include 'includes/footer.php'; ?>
