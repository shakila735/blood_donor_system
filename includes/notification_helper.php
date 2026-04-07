<?php
// includes/notification_helper.php

/**
 * Mock email sending by logging it to the database.
 * This ensures the app doesn't break locally when SMTP isn't available.
 */
function mock_send_email($pdo, $user_id, $to_email, $subject, $message) {
    if (empty($to_email)) return false;

    // Simulate sending email. We could use mail() here, but for XAMPP
    // without SMTP, it will fail and hang. So we log to database.
    try {
        $stmt = $pdo->prepare("INSERT INTO email_logs (user_id, to_email, subject, message, status) VALUES (?, ?, ?, ?, 'sent')");
        return $stmt->execute([$user_id, $to_email, $subject, $message]);
    } catch (\PDOException $e) {
        // Log error silently so app doesn't break
        error_log("Mock Email Error: " . $e->getMessage());
        return false;
    }
}

/**
 * Find matching donors and send them in-app and email notifications.
 * This should be called by the teammate's "Post Request" logic.
 *
 * @param PDO $pdo Database connection
 * @param string $blood_group Required blood group
 * @param string $location Required location
 * @param string $requester_name The name of the hospital or person requesting
 * @return int Number of donors notified
 */
function notify_matching_donors($pdo, $blood_group, $location, $requester_name) {
    // 1. Find matching active donors
    // Using simple exact match for location right now. Can be expanded to LIKE %location%
    $query = "SELECT id, name, email FROM users 
              WHERE role_id = (SELECT id FROM roles WHERE name='donor') 
              AND blood_group = ? 
              AND location = ? 
              AND is_active = 1";
              
    $stmt = $pdo->prepare($query);
    $stmt->execute([$blood_group, $location]);
    $matching_donors = $stmt->fetchAll();

    $notified_count = 0;

    foreach ($matching_donors as $donor) {
        $user_id = $donor['id'];
        
        // In-App Notification Message
        $in_app_msg = "Urgent: Blood request for $blood_group at $location by $requester_name. Please check if you can donate.";
        
        // Insert In-app Notification
        $notif_stmt = $pdo->prepare("INSERT INTO notifications (user_id, message) VALUES (?, ?)");
        $notif_stmt->execute([$user_id, $in_app_msg]);

        // Email Notification
        if (!empty($donor['email'])) {
            $subject = "Urgent Blood Request: $blood_group in your area";
            $email_msg = "Hello {$donor['name']},\n\nAn urgent request for $blood_group blood has been posted in your location ($location) by $requester_name.\nIf you are available to donate, please log in to your BloodNet dashboard to respond.\n\nThank you,\nBloodNet Team";
            
            mock_send_email($pdo, $user_id, $donor['email'], $subject, $email_msg);
        }
        
        $notified_count++;
    }

    return $notified_count;
}

/**
 * Get unread notification count for a user
 */
function get_unread_notification_count($pdo, $user_id) {
    $stmt = $pdo->prepare("SELECT COUNT(id) FROM notifications WHERE user_id = ? AND is_read = 0");
    $stmt->execute([$user_id]);
    return $stmt->fetchColumn();
}

/**
 * Notify the requester that their request has been accepted by a donor.
 */
function notify_requester_on_acceptance($pdo, $requester_id, $donor_name, $blood_group) {
    $msg = "Great news! $donor_name has accepted your request for $blood_group blood. You can now see their contact details in your request history.";
    
    // In-app notification
    $stmt = $pdo->prepare("INSERT INTO notifications (user_id, message) VALUES (?, ?)");
    $stmt->execute([$requester_id, $msg]);
    
    // Fetch requester email to send "mock" email if needed
    $stmt = $pdo->prepare("SELECT email, name FROM users WHERE id = ?");
    $stmt->execute([$requester_id]);
    $requester = $stmt->fetch();
    
    if ($requester && !empty($requester['email'])) {
        $subject = "Blood Request Accepted!";
        $email_msg = "Hello {$requester['name']},\n\nYour request for $blood_group blood has been accepted by $donor_name.\nPlease log in to BloodNet and check your request history to get the donor's contact information.\n\nThank you,\nBloodNet Team";
        mock_send_email($pdo, $requester_id, $requester['email'], $subject, $email_msg);
    }
}
?>
