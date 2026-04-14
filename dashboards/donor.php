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
$stmt = $pdo->prepare("SELECT availability_status, blood_group, location FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user_data = $stmt->fetch();
$current_status = $user_data['availability_status'] ?? 'Available';
$user_blood_group = $user_data['blood_group'] ?? 'Not Set';
$user_location = $user_data['location'] ?? 'Not Set';

// Fetch all pending blood requests for the blood requests section
$stmt = $pdo->prepare("SELECT br.*, u.name as requester_name 
                     FROM blood_requests br 
                     JOIN users u ON br.user_id = u.id 
                     WHERE br.status = 'Pending' 
                     ORDER BY (br.blood_group = ?) DESC, br.created_at DESC");
$stmt->execute([$user_blood_group]);
$all_requests = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BloodNet Donor Dashboard</title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Google Fonts - Inter -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="../assets/css/style.css?v=1.7">
</head>
<body>
    
    <div class="dashboard-wrapper">
        
        <!-- TOP NAVBAR -->
        <nav class="navbar navbar-expand-lg navbar-light dashboard-navbar">
            <div class="container-fluid">
                <button class="btn btn-link d-lg-none" id="sidebarToggle">
                    <i class="fas fa-bars fs-4"></i>
                </button>
                
                <a class="navbar-brand" href="donor.php">
                    <i class="fas fa-tint text-danger"></i>
                    <span class="fw-bold">BloodNet Donor</span>
                </a>
                
                <div class="navbar-nav ms-auto">
                    <!-- Notifications -->
                    <div class="nav-item dropdown">
                        <a class="nav-link position-relative" href="#" id="notificationDropdown" role="button" data-bs-toggle="dropdown">
                            <i class="fas fa-bell"></i>
                            <?php
                            if(isset($pdo)) {
                                require_once '../includes/notification_helper.php';
                                $dash_unread = get_unread_notification_count($pdo, $_SESSION['user_id']);
                                if($dash_unread > 0) {
                                    echo '<span class="notification-badge">' . $dash_unread . '</span>';
                                }
                            }
                            ?>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><h6 class="dropdown-header">Donor Notifications</h6></li>
                            <li><a class="dropdown-item" href="../notifications.php"><i class="fas fa-tint text-danger me-2"></i>Blood requests</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="../notifications.php">View all notifications</a></li>
                        </ul>
                    </div>
                    
                    <!-- Profile Dropdown -->
                    <div class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" id="profileDropdown" role="button" data-bs-toggle="dropdown">
                            <div class="avatar-circle bg-danger text-white rounded-circle d-flex align-items-center justify-content-center me-2" style="width: 32px; height: 32px;">
                                <?php echo strtoupper(substr($_SESSION['user_name'], 0, 1)); ?>
                            </div>
                            <span class="d-none d-md-inline"><?php echo htmlspecialchars($_SESSION['user_name']); ?></span>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><h6 class="dropdown-header">Donor Account</h6></li>
                            <li><a class="dropdown-item" href="../profile.php"><i class="fas fa-user me-2"></i>My Profile</a></li>
                            <li><a class="dropdown-item" href="../donor_profile.php"><i class="fas fa-edit me-2"></i>Edit Profile</a></li>
                            <li><a class="dropdown-item" href="../settings.php"><i class="fas fa-cog me-2"></i>Settings</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="../logout.php"><i class="fas fa-sign-out-alt me-2"></i>Logout</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </nav>
        
        <!-- LEFT SIDEBAR -->
        <aside class="dashboard-sidebar" id="dashboardSidebar">
            <ul class="sidebar-menu">
                <li class="sidebar-menu-item">
                    <a href="javascript:void(0)" class="sidebar-menu-link active" data-section="dashboard">
                        <i class="fas fa-home"></i>
                        <span class="sidebar-menu-text">Dashboard</span>
                    </a>
                </li>
                <li class="sidebar-menu-item">
                    <a href="javascript:void(0)" class="sidebar-menu-link" data-section="profile">
                        <i class="fas fa-user"></i>
                        <span class="sidebar-menu-text">My Profile</span>
                    </a>
                </li>
                <li class="sidebar-menu-item">
                    <a href="javascript:void(0)" class="sidebar-menu-link" data-section="blood-requests">
                        <i class="fas fa-tint"></i>
                        <span class="sidebar-menu-text">Blood Requests</span>
                    </a>
                </li>
                <li class="sidebar-menu-item">
                    <a href="javascript:void(0)" class="sidebar-menu-link" data-section="notifications">
                        <i class="fas fa-bell"></i>
                        <span class="sidebar-menu-text">Notifications</span>
                    </a>
                </li>
            </ul>
        </aside>
        
        <!-- MAIN CONTENT -->
        <main class="dashboard-main" id="dashboardMain">
            
            <div id="section-dashboard" class="content-section active">
                <!-- WELCOME CARD -->
                <div class="welcome-card premium-card fade-in-up">
                    <div class="row align-items-center g-3 g-md-4">
                        <div class="col-md-7">
                            <span class="badge bg-white text-danger px-3 py-2 rounded-pill mb-2 fw-bold">DASHBOARD OVERVIEW</span>
                            <h1 class="mb-1">Welcome back, <?php echo htmlspecialchars($_SESSION['user_name']); ?>!</h1>
                            <p class="opacity-90 mb-0">Thank you for being a lifesaver. Your contribution saves lives every day.</p>
                        </div>
                        <div class="col-md-5 text-md-end mt-3 mt-md-0">
                            <div class="welcome-card-actions">
                                <a href="javascript:void(0)" onclick="document.querySelector('[data-section=\'blood-requests\']').click()" class="btn btn-light shadow-sm">
                                    <i class="fas fa-tint fa-fw me-2"></i><span>View Requests</span>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                
                <?php if ($success): ?>
                    <div class="alert alert-success alert-dismissible fade show fade-in-up" role="alert">
                        <i class="fas fa-check-circle me-2"></i><?php echo $success; ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>
                <?php if ($error): ?>
                    <div class="alert alert-danger alert-dismissible fade show fade-in-up" role="alert">
                        <i class="fas fa-exclamation-triangle me-2"></i><?php echo $error; ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>
                
                <!-- DONOR ANNOUNCEMENT CARD -->
                <div class="announcement-card fade-in-up">
                    <h4><i class="fas fa-heart me-2"></i>Donor Recognition</h4>
                    <p>Your blood type <?php echo htmlspecialchars($user_blood_group); ?> is in high demand. Update your availability to help save lives.</p>
                </div>
                
                <!-- STATS CARDS ROW -->
                <div class="row g-4 mb-4">
                    <div class="col-lg-3 col-md-6">
                        <div class="stat-card fade-in-up">
                            <div class="stat-icon info">
                                <i class="fas fa-tint"></i>
                            </div>
                            <div class="stat-number"><?php echo htmlspecialchars($user_blood_group); ?></div>
                            <div class="stat-label">Blood Type</div>
                        </div>
                    </div>
                </div>
                
                <!-- QUICK ACTIONS SECTION -->
                <div class="row g-4 mb-4">
                    <div class="col-lg-3 col-md-6">
                        <div class="action-card fade-in-up">
                            <i class="fas fa-clock"></i>
                            <h5>Update Availability</h5>
                            <form method="POST" action="" class="mt-3">
                                <select name="availability_status" class="form-select" onchange="this.form.submit()">
                                    <option value="Available" <?php echo ($current_status == 'Available') ? 'selected' : ''; ?>>Available</option>
                                    <option value="Unavailable" <?php echo ($current_status == 'Unavailable') ? 'selected' : ''; ?>>Unavailable</option>
                                </select>
                            </form>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6">
                        <a href="javascript:void(0)" onclick="document.querySelector('[data-section=\'blood-requests\']').click()" class="action-card fade-in-up">
                            <i class="fas fa-list-check"></i>
                            <h5>Browse Requests</h5>
                        </a>
                    </div>
                    <div class="col-lg-3 col-md-6">
                        <a href="../notifications.php" class="action-card fade-in-up">
                            <i class="fas fa-bell"></i>
                            <h5>Notifications</h5>
                        </a>
                    </div>
                </div>
                
                <div class="row g-4">
                    <!-- RECENT ACTIVITY TABLE -->
                    <div class="col-lg-8">
                        <div class="activity-table dashboard-card fade-in-up">
                            <div class="card-header">
                                <h4>Recent Blood Requests</h4>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Request ID</th>
                                            <th>Blood Group</th>
                                            <th>Location</th>
                                            <th>Urgency</th>
                                            <th>Date</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        try {
                                            $stmt = $pdo->query("SELECT br.*, u.name as requester_name 
                                                              FROM blood_requests br 
                                                              JOIN users u ON br.user_id = u.id 
                                                              WHERE br.status = 'Pending'
                                                              ORDER BY br.created_at DESC 
                                                              LIMIT 5");
                                            $recent_requests = $stmt->fetchAll(PDO::FETCH_ASSOC);
                                            
                                            foreach ($recent_requests as $request) {
                                                echo '<tr>';
                                                echo '<td>#' . htmlspecialchars($request['id']) . '</td>';
                                                echo '<td><span class="badge bg-danger">' . htmlspecialchars($request['blood_group']) . '</span></td>';
                                                echo '<td>' . htmlspecialchars($request['location']) . '</td>';
                                                echo '<td><span class="badge bg-warning">Urgent</span></td>';
                                                echo '<td>' . date('M d, Y', strtotime($request['created_at'])) . '</td>';
                                                echo '</tr>';
                                            }
                                        } catch(PDOException $e) {
                                            echo '<tr><td colspan="5" class="text-center">Unable to load recent requests</td></tr>';
                                        }
                                        ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    
                    <!-- DONOR PROFILE CARD -->
                    <div class="col-lg-4">
                        <div class="profile-card dashboard-card fade-in-up">
                            <div class="profile-avatar">
                                <?php echo strtoupper(substr($_SESSION['user_name'], 0, 1)); ?>
                            </div>
                            <h4 class="profile-name"><?php echo htmlspecialchars($_SESSION['user_name']); ?></h4>
                            <p class="profile-role">Blood Donor</p>
                            
                            <div class="profile-info">
                                <div class="profile-info-item">
                                    <span class="profile-info-label">Blood Group</span>
                                    <span class="profile-info-value"><?php echo htmlspecialchars($user_blood_group); ?></span>
                                </div>
                                <div class="profile-info-item">
                                    <span class="profile-info-label">Availability</span>
                                    <span class="profile-info-value">
                                        <span class="badge bg-<?php echo $current_status == 'Available' ? 'success' : 'secondary'; ?>">
                                            <?php echo $current_status; ?>
                                        </span>
                                    </span>
                                </div>
                                <div class="profile-info-item">
                                    <span class="profile-info-label">Location</span>
                                    <span class="profile-info-value"><?php echo htmlspecialchars($user_location); ?></span>
                                </div>
                            </div>
                            
                            <a href="../profile.php" class="btn btn-danger btn-sm rounded-pill w-100">
                                <i class="fas fa-edit me-2"></i>Edit Profile
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <div id="section-blood-requests" class="content-section">
                <div class="dashboard-card border-0 shadow-sm">
                    <div class="card-header border-0 bg-white pt-4 px-4 d-flex justify-content-between align-items-center">
                        <div>
                            <h4 class="mb-0 fw-bold">All Blood Requests</h4>
                            <p class="text-muted small mb-0">Manage and respond to pending blood requests</p>
                        </div>
                        <span class="badge bg-danger-subtle text-danger px-3 py-2 rounded-pill fw-bold">
                            <i class="fas fa-clock me-1"></i> <?php echo count($all_requests); ?> Pending
                        </span>
                    </div>
                    <div class="card-body p-4">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="bg-light">
                                    <tr>
                                        <th class="border-0 text-uppercase small fw-bold py-3 ps-4">ID</th>
                                        <th class="border-0 text-uppercase small fw-bold py-3">Blood Group</th>
                                        <th class="border-0 text-uppercase small fw-bold py-3">Requester</th>
                                        <th class="border-0 text-uppercase small fw-bold py-3">Location</th>
                                        <th class="border-0 text-uppercase small fw-bold py-3">Needed Date</th>
                                        <th class="border-0 text-uppercase small fw-bold py-3 text-center pe-4">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (count($all_requests) > 0): ?>
                                        <?php foreach ($all_requests as $req): ?>
                                            <tr>
                                                <td class="fw-bold text-muted">#<?php echo $req['id']; ?></td>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <span class="badge bg-danger fs-6 rounded-3 px-3 py-2"><?php echo htmlspecialchars($req['blood_group']); ?></span>
                                                        <?php if ($req['blood_group'] === $user_blood_group): ?>
                                                            <span class="badge bg-success-subtle text-success ms-2 border border-success border-opacity-25 px-2">Perfect Match</span>
                                                        <?php endif; ?>
                                                    </div>
                                                </td>
                                                <td><?php echo htmlspecialchars($req['requester_name']); ?></td>
                                                <td><?php echo htmlspecialchars($req['location']); ?></td>
                                                <td><?php echo date('M d, Y', strtotime($req['needed_date'])); ?></td>
                                                <td class="text-center">
                                                    <div class="d-flex justify-content-center gap-2">
                                                        <button type="button" class="btn btn-sm btn-outline-primary rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#viewModal<?php echo $req['id']; ?>">
                                                            <i class="fas fa-eye me-1"></i> View
                                                        </button>
                                                        <button type="button" class="btn btn-sm btn-danger rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#acceptModal<?php echo $req['id']; ?>">
                                                            <i class="fas fa-check me-1"></i> Accept
                                                        </button>
                                                    </div>
                                                </td>
                                            </tr>

                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="6" class="text-center py-4 text-muted">No pending blood requests available.</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            
        </main>
        
    </div>
    
    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <style>
        .content-section {
            display: none;
        }
        .content-section.active {
            display: block;
        }
        

    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const sidebarLinks = document.querySelectorAll('.sidebar-menu-link[data-section]');
            const sections = document.querySelectorAll('.content-section');
            const welcomeCards = document.querySelectorAll('.welcome-card, .announcement-card, .stat-card, .action-card, .dashboard-card');

            sidebarLinks.forEach(link => {
                link.addEventListener('click', function(e) {
                    e.preventDefault();
                    const targetSectionId = 'section-' + this.getAttribute('data-section');
                    const targetSection = document.getElementById(targetSectionId);

                    if (targetSection) {
                        // Update active link
                        sidebarLinks.forEach(l => l.classList.remove('active'));
                        this.classList.add('active');

                        // Switch sections
                        sections.forEach(s => s.classList.remove('active'));
                        targetSection.classList.add('active');

                        // Close sidebar on mobile
                        const sidebar = document.getElementById('dashboardSidebar');
                        if (sidebar.classList.contains('show')) {
                            sidebar.classList.remove('show');
                        }
                    } else if (this.getAttribute('data-section') === 'profile') {
                        window.location.href = '../profile.php';
                    } else if (this.getAttribute('data-section') === 'notifications') {
                        window.location.href = '../notifications.php';
                    }
                });
            });

            // Handle URL parameters for direct section access
            const urlParams = new URLSearchParams(window.location.search);
            const sectionParam = urlParams.get('section');
            if (sectionParam) {
                const link = document.querySelector(`.sidebar-menu-link[data-section="${sectionParam}"]`);
                if (link) link.click();
            }
        });
    </script>
    
    <!-- Modals Section (Moved to end of file for stability) -->
    <?php foreach ($all_requests as $req): ?>
        <!-- View Modal -->
        <div class="modal fade" id="viewModal<?php echo $req['id']; ?>" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 shadow-lg">
                    <div class="modal-header bg-primary text-white border-0 py-3">
                        <h5 class="modal-title fw-bold"><i class="fas fa-info-circle me-2"></i>Request Details</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body p-4">
                        <div class="d-flex align-items-center mb-4">
                            <div class="bg-primary bg-opacity-10 text-primary rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 65px; height: 65px; font-size: 1.5rem; font-weight: 800; border: 2px solid rgba(13, 110, 253, 0.2);">
                                <?php echo htmlspecialchars($req['blood_group']); ?>
                            </div>
                            <div>
                                <h5 class="mb-0 fw-bold"><?php echo htmlspecialchars($req['requester_name']); ?></h5>
                                <p class="text-muted small mb-0"><i class="far fa-clock me-1"></i> Posted on <?php echo date('M d, Y', strtotime($req['created_at'])); ?></p>
                            </div>
                        </div>
                        
                        <div class="row g-4 mb-2">
                            <div class="col-6">
                                <div class="p-3 bg-light rounded-3 h-100">
                                    <label class="text-muted small text-uppercase fw-bold d-block mb-1">Location</label>
                                    <span class="fw-semibold text-dark"><i class="fas fa-map-marker-alt text-danger me-1"></i> <?php echo htmlspecialchars($req['location']); ?></span>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="p-3 bg-light rounded-3 h-100">
                                    <label class="text-muted small text-uppercase fw-bold d-block mb-1">Needed Date</label>
                                    <span class="fw-semibold text-dark"><i class="fas fa-calendar-alt text-primary me-1"></i> <?php echo date('M d, Y', strtotime($req['needed_date'])); ?></span>
                                </div>
                            </div>
                            <?php if (!empty($req['note'])): ?>
                            <div class="col-12">
                                <div class="p-3 border-start border-4 border-primary bg-light rounded-end-3">
                                    <label class="text-muted small text-uppercase fw-bold d-block mb-1">Requester's Note</label>
                                    <p class="mb-0 text-dark" style="font-style: italic;">"<?php echo htmlspecialchars($req['note']); ?>"</p>
                                </div>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="modal-footer border-0 pt-0 pb-4 px-4">
                        <button type="button" class="btn btn-light rounded-pill px-4 fw-semibold" data-bs-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-danger rounded-pill px-4 fw-semibold shadow-sm" data-bs-toggle="modal" data-bs-target="#acceptModal<?php echo $req['id']; ?>">
                            <i class="fas fa-heart me-1"></i> Accept Request
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Accept Modal -->
        <div class="modal fade" id="acceptModal<?php echo $req['id']; ?>" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 shadow-lg">
                    <form method="POST" action="../blood_request_accept.php">
                        <div class="modal-header bg-danger text-white border-0 py-3">
                            <h5 class="modal-title fw-bold">Confirm Donation</h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body p-4 text-center">
                            <div class="mb-4">
                                <i class="fas fa-hand-holding-heart text-danger display-4"></i>
                            </div>
                            <p class="fs-5 mb-4">Are you sure you want to accept this request for <strong><?php echo htmlspecialchars($req['blood_group']); ?></strong> blood?</p>
                            <div class="alert alert-warning border-0 shadow-sm text-start py-3">
                                <div class="d-flex">
                                    <i class="fas fa-shield-alt text-warning fs-4 me-3"></i>
                                    <div>
                                        <strong>Safe Donation:</strong> Your contact information will be securely shared with <strong><?php echo htmlspecialchars($req['requester_name']); ?></strong> to coordinate the donation.
                                    </div>
                                </div>
                            </div>
                            <input type="hidden" name="request_id" value="<?php echo $req['id']; ?>">
                            <input type="hidden" name="redirect" value="dashboards/donor.php?section=blood-requests">
                        </div>
                        <div class="modal-footer border-0 justify-content-center pt-0 pb-4">
                            <button type="button" class="btn btn-light rounded-pill px-4 fw-semibold" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-danger rounded-pill px-5 fw-bold shadow-sm">Confirm & Donate</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    <?php endforeach; ?>

    <!-- Custom JS -->
    <script src="../assets/js/script.js"></script>
    
</body>
</html>
