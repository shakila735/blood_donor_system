<?php
require_once 'config/database.php';
require_once 'auth/guard.php';
require_once 'includes/notification_helper.php';

require_role('donor');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['request_id'])) {
    $request_id = $_POST['request_id'];
    $donor_id = $_SESSION['user_id'];
    $donor_name = $_SESSION['user_name'];

    // 1. Check if the request is still pending
    $stmt = $pdo->prepare("SELECT user_id, blood_group, status FROM blood_requests WHERE id = ?");
    $stmt->execute([$request_id]);
    $request = $stmt->fetch();

    if ($request) {
        if ($request['status'] === 'Pending') {
            // 2. Update the request
            $update_stmt = $pdo->prepare("UPDATE blood_requests SET status = 'Accepted', donor_id = ?, accepted_at = CURRENT_TIMESTAMP WHERE id = ?");
            try {
                $update_stmt->execute([$donor_id, $request_id]);
                
                // 3. Notify the requester
                notify_requester_on_acceptance($pdo, $request['user_id'], $donor_name, $request['blood_group']);
                
                header("Location: blood_requests_feed.php?success=accepted");
                exit();
            } catch (PDOException $e) {
                header("Location: blood_requests_feed.php?error=db");
                exit();
            }
        } else {
            header("Location: blood_requests_feed.php?error=already_taken");
            exit();
        }
    } else {
        header("Location: blood_requests_feed.php?error=not_found");
        exit();
    }
} else {
    header("Location: blood_requests_feed.php");
    exit();
}
