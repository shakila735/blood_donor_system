<?php
// auth/guard.php
session_start();

/**
 * Check if user is logged in
 */
function is_logged_in() {
    return isset($_SESSION['user_id']);
}

/**
 * Require user to be logged in, otherwise redirect to login page
 */
function require_login() {
    if (!is_logged_in()) {
        header("Location: ../login.php");
        exit();
    }
}

/**
 * Require user to have a specific role, otherwise redirect to dashboard routing
 */
function require_role($required_role) {
    require_login();
    if (!isset($_SESSION['role_name']) || $_SESSION['role_name'] !== $required_role) {
        header("Location: ../dashboard.php");
        exit();
    }
}
?>
