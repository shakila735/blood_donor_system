<?php
// includes/validation.php

/**
 * Sanitize input data
 */
function sanitize_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
    return $data;
}

/**
 * Validate Bangladeshi Phone Number
 * Must start with +8801, 8801, or 01, followed by 9 digits.
 */
function validate_bd_phone($phone) {
    // Regex for BD phone number
    $pattern = '/^(?:\+88|88)?(01[3-9]\d{8})$/';
    if (preg_match($pattern, $phone, $matches)) {
        // Return normalized 11-digit format (01XXXXXXXXX)
        return $matches[1];
    }
    return false;
}

/**
 * Check if a phone number exists in the users table
 */
function phone_exists($pdo, $phone, $exclude_user_id = null) {
    $query = "SELECT id FROM users WHERE phone = ?";
    $params = [$phone];
    
    if ($exclude_user_id !== null) {
        $query .= " AND id != ?";
        $params[] = $exclude_user_id;
    }
    
    $stmt = $pdo->prepare($query);
    $stmt->execute($params);
    return $stmt->fetchColumn() !== false;
}

/**
 * Check if an email exists in the users table
 */
function email_exists($pdo, $email, $exclude_user_id = null) {
    $query = "SELECT id FROM users WHERE email = ?";
    $params = [$email];
    
    if ($exclude_user_id !== null) {
        $query .= " AND id != ?";
        $params[] = $exclude_user_id;
    }
    
    $stmt = $pdo->prepare($query);
    $stmt->execute($params);
    return $stmt->fetchColumn() !== false;
}
?>
