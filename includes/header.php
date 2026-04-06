<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BloodNet - Blood Donor Finder System</title>
    <!-- Bootstrap 5 CSS via CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- FontAwesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <?php 
        // Determine base URL relative to current script depth
        $depth = substr_count(str_replace("\\", "/", $_SERVER['PHP_SELF']), "/") - 2; // adjust based on folder depth
        // A simpler approach for XAMPP:
        $base_url = '/blood_donor_system/';
        
        // Fetch unread notification count if user is logged in
        $unread_count = 0;
        if(isset($_SESSION['user_id']) && isset($pdo)) {
            require_once __DIR__ . '/notification_helper.php';
            $unread_count = get_unread_notification_count($pdo, $_SESSION['user_id']);
        }
    ?>
    <link rel="stylesheet" href="<?php echo $base_url; ?>assets/css/style.css">
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark bg-danger shadow-sm">
    <div class="container">
        <a class="navbar-brand fw-bold" href="<?php echo $base_url; ?>"><i class="fa-solid fa-droplet text-white"></i> BloodNet</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <?php if(isset($_SESSION['user_id'])): ?>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo $base_url; ?>dashboard.php">Dashboard</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link position-relative" href="<?php echo $base_url; ?>notifications.php">
                            <i class="fa-solid fa-bell"></i>
                            <?php if($unread_count > 0): ?>
                                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-warning text-dark" style="font-size: 0.65em;">
                                    <?php echo $unread_count; ?>
                                </span>
                            <?php endif; ?>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo $base_url; ?>profile.php">Profile</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link btn btn-light text-danger ms-2" href="<?php echo $base_url; ?>logout.php">Logout</a>
                    </li>
                <?php else: ?>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo $base_url; ?>login.php">Login</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link btn btn-light text-danger ms-2" href="<?php echo $base_url; ?>register.php">Register</a>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>
<main class="container py-4">
