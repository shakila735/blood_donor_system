<?php
require_once '../auth/guard.php';
require_once '../config/database.php';
require_role('admin');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BloodNet Admin Dashboard</title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Google Fonts - Inter -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    
    <div class="dashboard-wrapper">
        
        <!-- TOP NAVBAR -->
        <nav class="navbar navbar-expand-lg navbar-light dashboard-navbar">
            <div class="container-fluid">
                <button class="btn btn-link d-lg-none" id="sidebarToggle">
                    <i class="fas fa-bars fs-4"></i>
                </button>
                
                <a class="navbar-brand" href="admin.php">
                    <i class="fas fa-tint text-danger"></i>
                    <span class="fw-bold">BloodNet Admin</span>
                </a>
                
                <div class="navbar-nav ms-auto">
                                        
                    <!-- Profile Dropdown -->
                    <div class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" id="profileDropdown" role="button" data-bs-toggle="dropdown">
                            <div class="avatar-circle bg-danger text-white rounded-circle d-flex align-items-center justify-content-center me-2" style="width: 32px; height: 32px;">
                                <i class="fas fa-user-shield"></i>
                            </div>
                            <span class="d-none d-md-inline">Administrator</span>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><h6 class="dropdown-header">Admin Account</h6></li>
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
                    <a href="admin.php" class="sidebar-menu-link active">
                        <i class="fas fa-home"></i>
                        <span class="sidebar-menu-text">Dashboard</span>
                    </a>
                </li>
                <li class="sidebar-menu-item">
                    <a href="../admin_users.php" class="sidebar-menu-link">
                        <i class="fas fa-users"></i>
                        <span class="sidebar-menu-text">Manage Users</span>
                    </a>
                </li>
                <li class="sidebar-menu-item">
                    <a href="../admin_requests.php" class="sidebar-menu-link">
                        <i class="fas fa-notes-medical"></i>
                        <span class="sidebar-menu-text">Monitor Requests</span>
                    </a>
                </li>
                            </ul>
        </aside>
        
        <!-- MAIN CONTENT -->
        <main class="dashboard-main" id="dashboardMain">
            
            <!-- WELCOME CARD -->
            <div class="welcome-card fade-in-up">
                <div class="row align-items-center">
                    <div class="col-lg-8">
                        <h1>Admin Dashboard</h1>
                        <p>System overview and management controls for BloodNet platform.</p>
                    </div>
                    <div class="col-lg-4 text-lg-end">
                        <div class="d-flex justify-content-lg-end gap-3">
                            <a href="../admin_users.php" class="btn btn-light btn-lg rounded-pill">
                                <i class="fas fa-users me-2"></i>Manage Users
                            </a>
                            <a href="../admin_requests.php" class="btn btn-outline-light btn-lg rounded-pill">
                                <i class="fas fa-list me-2"></i>View Requests
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- ADMIN ANNOUNCEMENT CARD -->
            <div class="announcement-card fade-in-up">
                <h4><i class="fas fa-shield-alt me-2"></i>System Status</h4>
                <p>All systems operational. <?php echo date('F j, Y'); ?> - BloodNet platform running smoothly with <?php 
                    try {
                        $stmt = $pdo->query("SELECT COUNT(*) FROM users");
                        $total_users = $stmt->fetchColumn();
                        echo $total_users;
                    } catch(PDOException $e) {
                        echo '0';
                    }
                ?> registered users.</p>
            </div>
            
            <!-- STATS CARDS ROW -->
            <div class="row g-4 mb-4">
                <?php
                // Fetch Statistics
                $stats = [
                    'total_users' => 0,
                    'total_donors' => 0,
                    'verified_donors' => 0,
                    'total_requests' => 0,
                    'pending_requests' => 0
                ];

                try {
                    // Total Users
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
                
                <div class="col-lg-3 col-md-6">
                    <div class="stat-card fade-in-up">
                        <div class="stat-icon primary">
                            <i class="fas fa-users"></i>
                        </div>
                        <div class="stat-number"><?php echo $stats['total_users']; ?></div>
                        <div class="stat-label">Total Users</div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="stat-card fade-in-up">
                        <div class="stat-icon success">
                            <i class="fas fa-hand-holding-medical"></i>
                        </div>
                        <div class="stat-number"><?php echo $stats['total_donors']; ?></div>
                        <div class="stat-label">Total Donors</div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="stat-card fade-in-up">
                        <div class="stat-icon warning">
                            <i class="fas fa-check-circle"></i>
                        </div>
                        <div class="stat-number"><?php echo $stats['verified_donors']; ?></div>
                        <div class="stat-label">Verified Donors</div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="stat-card fade-in-up">
                        <div class="stat-icon info">
                            <i class="fas fa-clock"></i>
                        </div>
                        <div class="stat-number"><?php echo $stats['pending_requests']; ?></div>
                        <div class="stat-label">Pending Requests</div>
                    </div>
                </div>
            </div>
            
            <!-- QUICK ACTIONS SECTION -->
            <div class="row g-4 mb-4">
                <div class="col-lg-3 col-md-6">
                    <a href="../admin_users.php" class="action-card fade-in-up">
                        <i class="fas fa-users-gear"></i>
                        <h5>Manage Users</h5>
                    </a>
                </div>
                <div class="col-lg-3 col-md-6">
                    <a href="../admin_requests.php" class="action-card fade-in-up">
                        <i class="fas fa-notes-medical"></i>
                        <h5>Monitor Requests</h5>
                    </a>
                </div>
                            </div>
            
            <div class="row g-4">
                <!-- ADMIN PROFILE CARD -->
                <div class="col-lg-12">
                    <div class="profile-card dashboard-card fade-in-up">
                        <div class="profile-avatar">
                            <i class="fas fa-user-shield"></i>
                        </div>
                        <h4 class="profile-name"><?php echo htmlspecialchars($_SESSION['user_name']); ?></h4>
                        <p class="profile-role">System Administrator</p>
                        
                        <div class="profile-info">
                            <div class="profile-info-item">
                                <span class="profile-info-label">Total Users</span>
                                <span class="profile-info-value"><?php echo $stats['total_users']; ?></span>
                            </div>
                            <div class="profile-info-item">
                                <span class="profile-info-label">Active Requests</span>
                                <span class="profile-info-value">
                                    <span class="badge bg-warning"><?php echo $stats['pending_requests']; ?></span>
                                </span>
                            </div>
                        </div>
                        
                        <a href="../profile.php" class="btn btn-danger btn-sm rounded-pill w-100">
                            <i class="fas fa-edit me-2"></i>Edit Profile
                        </a>
                    </div>
                </div>
            </div>
                        
                        <a href="#" class="btn btn-danger btn-sm rounded-pill w-100">
                            <i class="fas fa-cog me-2"></i>Admin Settings
                        </a>
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
