<?php
require_once '../config/database.php';
require_once '../auth/guard.php';
require_role('requester');

$user_id = $_SESSION['user_id'];

// Get user statistics
try {
    // Total requests
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM blood_requests WHERE requester_id = ?");
    $stmt->execute([$user_id]);
    $total_requests = $stmt->fetchColumn();

    // Pending requests
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM blood_requests WHERE requester_id = ? AND status = 'Pending'");
    $stmt->execute([$user_id]);
    $pending_requests = $stmt->fetchColumn();

    // Approved requests
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM blood_requests WHERE requester_id = ? AND status = 'Approved'");
    $stmt->execute([$user_id]);
    $approved_requests = $stmt->fetchColumn();

    // Get user details
    $stmt = $pdo->prepare("SELECT blood_group, location FROM users WHERE id = ?");
    $stmt->execute([$user_id]);
    $user_data = $stmt->fetch();
    $user_blood_group = $user_data['blood_group'] ?? 'Not Set';
    $user_location = $user_data['location'] ?? 'Not Set';

} catch (PDOException $e) {
    $total_requests = 0;
    $pending_requests = 0;
    $approved_requests = 0;
    $user_blood_group = 'Not Set';
    $user_location = 'Not Set';
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BloodNet Requester Dashboard</title>
    
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
    <!-- TOP NAVBAR -->
    <nav class="navbar navbar-expand-lg navbar-light dashboard-navbar">
        <div class="container-fluid">
            <button class="btn btn-link d-lg-none" id="sidebarToggle">
                <i class="fas fa-bars fs-4"></i>
            </button>
            
            <a class="navbar-brand" href="requester.php">
                <i class="fas fa-tint text-danger"></i>
                <span class="fw-bold">BloodNet Requester</span>
            </a>
            
            <div class="navbar-nav ms-auto">
                <!-- Notifications -->
                <div class="nav-item dropdown">
                    <a class="nav-link position-relative" href="#" id="notificationDropdown" role="button" data-bs-toggle="dropdown">
                        <i class="fas fa-bell"></i>
                        <?php
                        require_once '../includes/notification_helper.php';
                        $unread_count = get_unread_notification_count($pdo, $_SESSION['user_id']);
                        if ($unread_count > 0) {
                            echo '<span class="notification-badge">' . $unread_count . '</span>';
                        }
                        ?>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><h6 class="dropdown-header">Requester Notifications</h6></li>
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
                        <li><h6 class="dropdown-header">Requester Account</h6></li>
                        <li><a class="dropdown-item" href="../profile.php"><i class="fas fa-user me-2"></i>My Profile</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="../logout.php"><i class="fas fa-sign-out-alt me-2"></i>Logout</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </nav>
    
    <div class="dashboard-wrapper">
        <!-- LEFT SIDEBAR -->
        <aside class="dashboard-sidebar" id="dashboardSidebar">
            <ul class="sidebar-menu">
                <li class="sidebar-menu-item">
                    <a href="requester.php" class="sidebar-menu-link active">
                        <i class="fas fa-home"></i>
                        <span class="sidebar-menu-text">Dashboard</span>
                    </a>
                </li>
                <li class="sidebar-menu-item">
                    <a href="../profile.php" class="sidebar-menu-link">
                        <i class="fas fa-user"></i>
                        <span class="sidebar-menu-text">My Profile</span>
                    </a>
                </li>
                <li class="sidebar-menu-item">
                    <a href="../donor_search.php" class="sidebar-menu-link">
                        <i class="fas fa-search"></i>
                        <span class="sidebar-menu-text">Search Donors</span>
                    </a>
                </li>
                <li class="sidebar-menu-item">
                    <a href="../blood_request_create.php" class="sidebar-menu-link">
                        <i class="fas fa-tint"></i>
                        <span class="sidebar-menu-text">Blood Requests</span>
                    </a>
                </li>
                <li class="sidebar-menu-item">
                    <a href="../blood_request_history.php" class="sidebar-menu-link">
                        <i class="fas fa-history"></i>
                        <span class="sidebar-menu-text">Request History</span>
                    </a>
                </li>
                <li class="sidebar-menu-item">
                    <a href="../notifications.php" class="sidebar-menu-link">
                        <i class="fas fa-bell"></i>
                        <span class="sidebar-menu-text">Notifications</span>
                    </a>
                </li>
                            </ul>
        </aside>
        
        <!-- MAIN CONTENT -->
        <main class="dashboard-main" id="dashboardMain">
            
            <!-- WELCOME CARD -->
            <div class="welcome-card premium-card fade-in-up">
                <div class="row align-items-center g-3 g-md-4">
                    <div class="col-md-7">
                        <span class="badge bg-white text-danger px-3 py-2 rounded-pill mb-2 fw-bold">DASHBOARD OVERVIEW</span>
                        <h1 class="mb-1">Welcome back, <?php echo htmlspecialchars($_SESSION['user_name']); ?>!</h1>
                        <p class="opacity-90 mb-0">Find blood donors and manage your requests efficiently through our platform.</p>
                    </div>
                    <div class="col-md-5 text-md-end mt-3 mt-md-0">
                        <div class="welcome-card-actions">
                            <a href="../blood_request_create.php" class="btn btn-light shadow-sm">
                                <i class="fas fa-plus fa-fw me-2"></i><span>Request Blood</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- REQUESTER ANNOUNCEMENT CARD -->
            <div class="announcement-card fade-in-up">
                <h4><i class="fas fa-tint me-2"></i>Blood Request Tips</h4>
                <p>Provide accurate information and contact details to help donors respond quickly to your requests.</p>
            </div>
            
                        
            <!-- QUICK ACTIONS SECTION -->
            <div class="row g-4 mb-4">
                <div class="col-lg-3 col-md-6">
                    <a href="../blood_request_create.php" class="action-card fade-in-up">
                        <i class="fas fa-plus-circle"></i>
                        <h5>Post Request</h5>
                    </a>
                </div>
                <div class="col-lg-3 col-md-6">
                    <a href="../donor_search.php" class="action-card fade-in-up">
                        <i class="fas fa-search"></i>
                        <h5>Find Donors</h5>
                    </a>
                </div>
                <div class="col-lg-3 col-md-6">
                    <a href="../blood_request_history.php" class="action-card fade-in-up">
                        <i class="fas fa-history"></i>
                        <h5>Request History</h5>
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
                <!-- REQUESTER PROFILE CARD -->
                <div class="col-lg-12">
                    <div class="profile-card dashboard-card fade-in-up">
                        <div class="profile-avatar">
                            <?php echo strtoupper(substr($_SESSION['user_name'], 0, 1)); ?>
                        </div>
                        <h4 class="profile-name"><?php echo htmlspecialchars($_SESSION['user_name']); ?></h4>
                        <p class="profile-role">Blood Requester</p>
                        
                        <div class="profile-info">
                            <div class="profile-info-item">
                                <span class="profile-info-label">Blood Group</span>
                                <span class="profile-info-value"><?php echo htmlspecialchars($user_blood_group); ?></span>
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
            
        </main>
        
    </div>
    
    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Custom JS -->
    <script src="../assets/js/script.js"></script>
    
</body>
</html>
