<?php
// index.php
session_start();

if (isset($_SESSION['user_id'])) {
    header("Location: dashboard.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome to BloodNet</title>
    <!-- Bootstrap 5 CSS via CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body class="bg-light d-flex flex-column min-vh-100 justify-content-center align-items-center">

<div class="text-center px-4">
    <h1 class="display-4 fw-bold text-danger mb-3">BloodNet</h1>
    <p class="lead text-muted mb-5">A Role-Based Blood Donor Finder System</p>
    
    <div class="d-grid gap-3 d-sm-flex justify-content-sm-center">
        <a href="login.php" class="btn btn-danger btn-lg px-5">Login</a>
        <a href="register.php" class="btn btn-outline-danger btn-lg px-5">Register</a>
    </div>
</div>

</body>
</html>
