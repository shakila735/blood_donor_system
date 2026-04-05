<?php
// dashboard.php (Router)
require_once 'auth/guard.php';

require_login(); // Ensure user is logged in

$role = $_SESSION['role_name'] ?? '';

switch ($role) {
    case 'admin':
        header("Location: dashboards/admin.php");
        break;
    case 'donor':
        header("Location: dashboards/donor.php");
        break;
    case 'requester':
        header("Location: dashboards/requester.php");
        break;
    case 'hospital':
        header("Location: dashboards/hospital.php");
        break;
    default:
        // Fallback for unknown role
        echo "Invalid Role Configuration.";
        exit();
}
?>
