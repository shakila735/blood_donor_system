<?php
// BloodNet Modern Dashboard
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Include database connection
require_once 'config/database.php';

// Get user information
$user_id = $_SESSION['user_id'];
$user_query = "SELECT * FROM users WHERE id = ?";
$stmt = $conn->prepare($user_query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user_result = $stmt->get_result();
$user = $user_result->fetch_assoc();

// Get dashboard statistics
$total_requests_query = "SELECT COUNT(*) as total FROM blood_requests";
$total_requests_result = $conn->query($total_requests_query);
$total_requests = $total_requests_result->fetch_assoc()['total'];

$active_donors_query = "SELECT COUNT(*) as total FROM users WHERE role = 'donor' AND availability = 'available'";
$active_donors_result = $conn->query($active_donors_query);
$active_donors = $active_donors_result->fetch_assoc()['total'];

$available_now_query = "SELECT COUNT(*) as total FROM users WHERE role = 'donor' AND availability = 'available'";
$available_now_result = $conn->query($available_now_query);
$available_now = $available_now_result->fetch_assoc()['total'];

$hospitals_query = "SELECT COUNT(*) as total FROM users WHERE role = 'hospital'";
$hospitals_result = $conn->query($hospitals_query);
$hospitals = $hospitals_result->fetch_assoc()['total'];

// Get recent blood requests
$recent_requests_query = "SELECT br.*, u.name as requester_name, u.location 
                          FROM blood_requests br 
                          JOIN users u ON br.requester_id = u.id 
                          ORDER BY br.created_at DESC 
                          LIMIT 5";
$recent_requests_result = $conn->query($recent_requests_query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BloodNet Dashboard - <?php echo htmlspecialchars($user['name']); ?></title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Google Fonts - Inter -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    
    <div class="dashboard-wrapper">
        
        <!-- TOP NAVBAR -->
        <nav class="navbar navbar-expand-lg navbar-light dashboard-navbar">
            <div class="container-fluid">
                <button class="btn btn-link d-lg-none" id="sidebarToggle">
                    <i class="fas fa-bars fs-4"></i>
                </button>
                
                <a class="navbar-brand" href="dashboard_new.php">
                    <i class="fas fa-tint text-danger"></i>
                    <span class="fw-bold">BloodNet</span>
                </a>
                
                <div class="navbar-nav ms-auto">
                    <!-- Notifications -->
                    <div class="nav-item dropdown">
                        <a class="nav-link position-relative" href="#" id="notificationDropdown" role="button" data-bs-toggle="dropdown">
                            <i class="fas fa-bell"></i>
                            <span class="notification-badge">3</span>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><h6 class="dropdown-header">Notifications</h6></li>
                            <li><a class="dropdown-item" href="#"><i class="fas fa-tint text-danger me-2"></i>New blood request</a></li>
                            <li><a class="dropdown-item" href="#"><i class="fas fa-user text-success me-2"></i>New donor registered</a></li>
                            <li><a class="dropdown-item" href="#"><i class="fas fa-hospital text-info me-2"></i>Hospital joined</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="notifications.php">View all notifications</a></li>
                        </ul>
                    </div>
                    
                    <!-- Profile Dropdown -->
                    <div class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" id="profileDropdown" role="button" data-bs-toggle="dropdown">
                            <div class="avatar-circle bg-danger text-white rounded-circle d-flex align-items-center justify-content-center me-2" style="width: 32px; height: 32px;">
                                <?php echo strtoupper(substr($user['name'], 0, 1)); ?>
                            </div>
                            <span class="d-none d-md-inline"><?php echo htmlspecialchars($user['name']); ?></span>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><h6 class="dropdown-header">Account</h6></li>
                            <li><a class="dropdown-item" href="profile.php"><i class="fas fa-user me-2"></i>My Profile</a></li>
                            <li><a class="dropdown-item" href="settings.php"><i class="fas fa-cog me-2"></i>Settings</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="logout.php"><i class="fas fa-sign-out-alt me-2"></i>Logout</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </nav>
        
        <!-- LEFT SIDEBAR -->
        <aside class="dashboard-sidebar" id="dashboardSidebar">
            <ul class="sidebar-menu">
                <li class="sidebar-menu-item">
                    <a href="dashboard_new.php" class="sidebar-menu-link active">
                        <i class="fas fa-home"></i>
                        <span class="sidebar-menu-text">Dashboard</span>
                    </a>
                </li>
                <li class="sidebar-menu-item">
                    <a href="profile.php" class="sidebar-menu-link">
                        <i class="fas fa-user"></i>
                        <span class="sidebar-menu-text">My Profile</span>
                    </a>
                </li>
                <li class="sidebar-menu-item">
                    <a href="donor_search.php" class="sidebar-menu-link">
                        <i class="fas fa-search"></i>
                        <span class="sidebar-menu-text">Search Donors</span>
                    </a>
                </li>
                <li class="sidebar-menu-item">
                    <a href="blood_request_create.php" class="sidebar-menu-link">
                        <i class="fas fa-tint"></i>
                        <span class="sidebar-menu-text">Blood Requests</span>
                    </a>
                </li>
                                <li class="sidebar-menu-item">
                    <a href="settings.php" class="sidebar-menu-link">
                        <i class="fas fa-cog"></i>
                        <span class="sidebar-menu-text">Settings</span>
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
                        <h1>Welcome back, <?php echo htmlspecialchars($user['name']); ?>!</h1>
                        <p>Your blood donation journey continues. Together we're saving lives every day.</p>
                    </div>
                    <div class="col-lg-4 text-lg-end">
                        <div class="d-flex justify-content-lg-end gap-3">
                            <a href="blood_request_create.php" class="btn btn-light btn-lg rounded-pill">
                                <i class="fas fa-plus me-2"></i>Request Blood
                            </a>
                            <a href="donor_search.php" class="btn btn-outline-light btn-lg rounded-pill">
                                <i class="fas fa-search me-2"></i>Find Donors
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- ANNOUNCEMENT CARD -->
            <div class="announcement-card fade-in-up">
                <h4><i class="fas fa-exclamation-triangle me-2"></i>Emergency Blood Drive</h4>
                <p>Urgent need for O+ and A+ blood types at City General Hospital. Your donation can save lives today.</p>
            </div>
            
            <!-- STATS CARDS ROW -->
            <div class="row g-4 mb-4">
                <div class="col-lg-3 col-md-6">
                    <div class="stat-card fade-in-up">
                        <div class="stat-icon primary">
                            <i class="fas fa-tint"></i>
                        </div>
                        <div class="stat-number"><?php echo $total_requests; ?></div>
                        <div class="stat-label">Total Requests</div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="stat-card fade-in-up">
                        <div class="stat-icon success">
                            <i class="fas fa-users"></i>
                        </div>
                        <div class="stat-number"><?php echo $active_donors; ?></div>
                        <div class="stat-label">Active Donors</div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="stat-card fade-in-up">
                        <div class="stat-icon warning">
                            <i class="fas fa-clock"></i>
                        </div>
                        <div class="stat-number"><?php echo $available_now; ?></div>
                        <div class="stat-label">Available Now</div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="stat-card fade-in-up">
                        <div class="stat-icon info">
                            <i class="fas fa-hospital"></i>
                        </div>
                        <div class="stat-number"><?php echo $hospitals; ?></div>
                        <div class="stat-label">Hospitals Connected</div>
                    </div>
                </div>
            </div>
            
            <!-- QUICK ACTIONS SECTION -->
            <div class="row g-4 mb-4">
                <div class="col-lg-3 col-md-6">
                    <a href="blood_request_create.php" class="action-card fade-in-up">
                        <i class="fas fa-tint"></i>
                        <h5>Request Blood</h5>
                    </a>
                </div>
                <div class="col-lg-3 col-md-6">
                    <a href="donor_search.php" class="action-card fade-in-up">
                        <i class="fas fa-search"></i>
                        <h5>Search Donors</h5>
                    </a>
                </div>
                <div class="col-lg-3 col-md-6">
                    <a href="profile.php" class="action-card fade-in-up">
                        <i class="fas fa-edit"></i>
                        <h5>Update Availability</h5>
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
                                        <th>Status</th>
                                        <th>Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while ($request = $recent_requests_result->fetch_assoc()): ?>
                                    <tr>
                                        <td>#<?php echo $request['id']; ?></td>
                                        <td>
                                            <span class="badge bg-danger"><?php echo htmlspecialchars($request['blood_group']); ?></span>
                                        </td>
                                        <td><?php echo htmlspecialchars($request['location']); ?></td>
                                        <td>
                                            <span class="status-badge <?php echo $request['status']; ?>">
                                                <?php echo ucfirst($request['status']); ?>
                                            </span>
                                        </td>
                                        <td><?php echo date('M d, Y', strtotime($request['created_at'])); ?></td>
                                    </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                
                <!-- PROFILE CARD -->
                <div class="col-lg-4">
                    <div class="profile-card dashboard-card fade-in-up">
                        <div class="profile-avatar">
                            <?php echo strtoupper(substr($user['name'], 0, 1)); ?>
                        </div>
                        <h4 class="profile-name"><?php echo htmlspecialchars($user['name']); ?></h4>
                        <p class="profile-role"><?php echo ucfirst(htmlspecialchars($user['role'])); ?></p>
                        
                        <div class="profile-info">
                            <div class="profile-info-item">
                                <span class="profile-info-label">Blood Group</span>
                                <span class="profile-info-value"><?php echo htmlspecialchars($user['blood_group'] ?? 'Not Set'); ?></span>
                            </div>
                            <div class="profile-info-item">
                                <span class="profile-info-label">Availability</span>
                                <span class="profile-info-value">
                                    <span class="badge bg-<?php echo $user['availability'] == 'available' ? 'success' : 'secondary'; ?>">
                                        <?php echo ucfirst($user['availability'] ?? 'Not Set'); ?>
                                    </span>
                                </span>
                            </div>
                            <div class="profile-info-item">
                                <span class="profile-info-label">Location</span>
                                <span class="profile-info-value"><?php echo htmlspecialchars($user['location'] ?? 'Not Set'); ?></span>
                            </div>
                        </div>
                        
                        <a href="profile.php" class="btn btn-danger btn-sm rounded-pill w-100">
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
    <script src="assets/js/script.js"></script>
    
    <!-- Dashboard Specific JS -->
    <script>
        // Sidebar toggle for mobile
        document.getElementById('sidebarToggle').addEventListener('click', function() {
            document.getElementById('dashboardSidebar').classList.toggle('show');
        });
        
        // Add fade-in-up animation to elements on scroll
        const observerOptions = {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        };
        
        const observer = new IntersectionObserver(function(entries) {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.style.opacity = '1';
                    entry.target.style.transform = 'translateY(0)';
                    observer.unobserve(entry.target);
                }
            });
        }, observerOptions);
        
        // Observe all fade-in-up elements
        document.querySelectorAll('.fade-in-up').forEach(element => {
            element.style.opacity = '0';
            element.style.transform = 'translateY(30px)';
            element.style.transition = 'opacity 0.6s ease, transform 0.6s ease';
            observer.observe(element);
        });
        
        // Animate stats numbers
        function animateCounter(element, target, duration = 2000) {
            let start = 0;
            const increment = target / (duration / 16);
            
            function updateCounter() {
                start += increment;
                if (start < target) {
                    element.textContent = Math.floor(start);
                    requestAnimationFrame(updateCounter);
                } else {
                    element.textContent = target;
                }
            }
            
            updateCounter();
        }
        
        // Trigger counter animation when stats are visible
        const statNumbers = document.querySelectorAll('.stat-number');
        const statsObserver = new IntersectionObserver(function(entries) {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const target = parseInt(entry.target.textContent);
                    animateCounter(entry.target, target);
                    statsObserver.unobserve(entry.target);
                }
            });
        }, { threshold: 0.5 });
        
        statNumbers.forEach(number => {
            statsObserver.observe(number);
        });
    </script>
    
</body>
</html>
