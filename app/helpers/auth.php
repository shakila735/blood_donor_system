<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function set_no_cache_headers() {
    header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
    header("Pragma: no-cache");
    header("Expires: 0");
}

function login_user($user) {
    session_regenerate_id(true);
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['user_name'] = $user['name'];
    $_SESSION['user_role'] = $user['role'];
    $_SESSION['is_logged_in'] = true;
}

function logout_user() {
    $_SESSION = [];

    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(
            session_name(),
            '',
            time() - 42000,
            $params["path"],
            $params["domain"],
            $params["secure"],
            $params["httponly"]
        );
    }

    session_unset();
    session_destroy();
}

function is_logged_in() {
    return isset($_SESSION['is_logged_in']) && $_SESSION['is_logged_in'] === true;
}

function current_user_role() {
    return $_SESSION['user_role'] ?? null;
}

function require_login() {
    set_no_cache_headers();

    if (!is_logged_in()) {
        header("Location: /blood_donor_system/public/login.php");
        exit;
    }
}

function require_role($roles = []) {
    require_login();

    if (!in_array(current_user_role(), $roles)) {
        header("Location: /blood_donor_system/public/dashboard.php");
        exit;
    }
}

function redirect_by_role($role) {
    switch ($role) {
        case 'donor':
            header("Location: donor/dashboard.php");
            break;
        case 'requester':
            header("Location: requester/dashboard.php");
            break;
        case 'hospital':
            header("Location: hospital/dashboard.php");
            break;
        case 'admin':
            header("Location: admin/dashboard.php");
            break;
        default:
            header("Location: login.php");
            break;
    }
    exit;
}
?>