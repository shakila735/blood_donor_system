<?php
session_start();
require_once __DIR__ . '/../../app/config/db.php';
require_once __DIR__ . '/../../app/helpers/validation.php';
require_once __DIR__ . '/../../app/helpers/json_response.php';
require_once __DIR__ . '/../../app/helpers/auth.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    json_response(false, "Method not allowed.", [], 405);
}

$input = json_decode(file_get_contents("php://input"), true);

if (!$input) {
    json_response(false, "Invalid JSON input.", [], 400);
}

$phone = clean_input($input['phone'] ?? '');
$password = $input['password'] ?? '';

$form_data = [
    'phone' => $phone,
    'password' => $password
];

$errors = validate_login($form_data);

if (!empty($errors)) {
    json_response(false, "Validation failed.", ['errors' => $errors], 422);
}

$stmt = $conn->prepare("SELECT id, name, phone, password, role, is_active FROM users WHERE phone = ? LIMIT 1");
$stmt->bind_param("s", $phone);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows !== 1) {
    json_response(false, "Account Not Found.", [], 404);
}

$user = $result->fetch_assoc();

if ((int)$user['is_active'] !== 1) {
    json_response(false, "Your account is deactivated.", [], 403);
}

if (!password_verify($password, $user['password'])) {
    json_response(false, "Invalid Password.", [], 401);
}

login_user($user);

json_response(true, "Login successful.", [
    'user_id' => $user['id'],
    'name' => $user['name'],
    'phone' => $user['phone'],
    'role' => $user['role']
], 200);
?>