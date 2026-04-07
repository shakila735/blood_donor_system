<?php
// admin_action.php
require_once 'auth/guard.php';
require_once 'config/database.php';
require_once 'includes/validation.php';

require_login();
require_role('admin');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $action = sanitize_input($_POST['action'] ?? '');
    $target_id = (int)($_POST['target_id'] ?? 0);
    $redirect_to = sanitize_input($_POST['redirect_to'] ?? 'admin_users.php');

    if ($target_id <= 0 || empty($action)) {
        $_SESSION['error_message'] = "Invalid action parameters.";
        header("Location: $redirect_to");
        exit();
    }

    try {
        switch ($action) {
            case 'verify_user':
                $stmt = $pdo->prepare("UPDATE users SET is_verified = 1 WHERE id = ?");
                $stmt->execute([$target_id]);
                $_SESSION['success_message'] = "User marked as verified.";
                break;
            
            case 'unverify_user':
                $stmt = $pdo->prepare("UPDATE users SET is_verified = 0 WHERE id = ?");
                $stmt->execute([$target_id]);
                $_SESSION['success_message'] = "User marked as unverified.";
                break;
            
            case 'block_user':
                // Check if user is trying to block themselves
                if ($target_id == $_SESSION['user_id']) {
                    $_SESSION['error_message'] = "You cannot block yourself.";
                } else {
                    $stmt = $pdo->prepare("UPDATE users SET is_active = 0 WHERE id = ?");
                    $stmt->execute([$target_id]);
                    $_SESSION['success_message'] = "User blocked successfully.";
                }
                break;

            case 'activate_user':
                $stmt = $pdo->prepare("UPDATE users SET is_active = 1 WHERE id = ?");
                $stmt->execute([$target_id]);
                $_SESSION['success_message'] = "User activated successfully.";
                break;

            default:
                $_SESSION['error_message'] = "Unknown action.";
        }
    } catch (PDOException $e) {
        // Log error appropriately in a real app
        $_SESSION['error_message'] = "Database error occurred.";
    }

    header("Location: $redirect_to");
    exit();
} else {
    // If accessed via GET directly
    header("Location: dashboard.php");
    exit();
}
