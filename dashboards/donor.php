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
                    <a href="donor.php" class="sidebar-menu-link active">
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
                    <a href="../blood_requests_feed.php" class="sidebar-menu-link">
                        <i class="fas fa-tint"></i>
                        <span class="sidebar-menu-text">Blood Requests</span>
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
            <div class="welcome-card fade-in-up">
                <div class="row align-items-center">
                    <div class="col-lg-8">
                        <h1>Welcome back, <?php echo htmlspecialchars($_SESSION['user_name']); ?>!</h1>
                        <p>Thank you for being a lifesaver. Your contribution saves lives every day.</p>
                    </div>
                    <div class="col-lg-4 text-lg-end">
                        <div class="d-flex justify-content-lg-end gap-3">
                            <a href="../blood_requests_feed.php" class="btn btn-light btn-lg rounded-pill">
                                <i class="fas fa-tint me-2"></i>View Requests
                            </a>
                            <a href="../profile.php" class="btn btn-outline-light btn-lg rounded-pill">
                                <i class="fas fa-user me-2"></i>My Profile
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
                    <a href="../blood_requests_feed.php" class="action-card fade-in-up">
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
                                        $stmt = $pdo->query("SELECT br.*, u.name as requester_name, u.location 
                                                          FROM blood_requests br 
                                                          JOIN users u ON br.requester_id = u.id 
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
            
        </main>
        
    </div>
    
    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Custom JS -->
    <script src="../assets/js/script.js"></script>
    
</body>
</html>
