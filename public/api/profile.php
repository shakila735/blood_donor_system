<?php
session_start();
require_once __DIR__ . '/../../app/config/db.php';
require_once __DIR__ . '/../../app/helpers/json_response.php';
require_once __DIR__ . '/../../app/helpers/auth.php';

if (!is_logged_in()) {
    json_response(false, "Unauthorized.", [], 401);
}

$user_id = $_SESSION['user_id'];

$stmt = $conn->prepare("SELECT id, name, phone, email, role, blood_group, location, availability_status, is_verified, is_active, created_at FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows !== 1) {
    json_response(false, "User not found.", [], 404);
}

$user = $result->fetch_assoc();
json_response(true, "Profile fetched successfully.", $user, 200);
?>