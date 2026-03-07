<?php
require_once __DIR__ . '/../../app/config/db.php';
require_once __DIR__ . '/../../app/helpers/validation.php';
require_once __DIR__ . '/../../app/helpers/json_response.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    json_response(false, "Method not allowed.", [], 405);
}

$input = json_decode(file_get_contents("php://input"), true);

if (!$input) {
    json_response(false, "Invalid JSON input.", [], 400);
}

$name = clean_input($input['name'] ?? '');
$phone = clean_input($input['phone'] ?? '');
$email = clean_input($input['email'] ?? '');
$password = $input['password'] ?? '';
$role = clean_input($input['role'] ?? '');
$blood_group = clean_input($input['blood_group'] ?? '');
$location = clean_input($input['location'] ?? '');

$form_data = [
    'name' => $name,
    'phone' => $phone,
    'email' => $email,
    'password' => $password,
    'role' => $role,
    'blood_group' => $blood_group,
    'location' => $location
];

$errors = validate_registration($form_data);

if (!empty($errors)) {
    json_response(false, "Validation failed.", ['errors' => $errors], 422);
}

$checkStmt = $conn->prepare("SELECT id FROM users WHERE phone = ? LIMIT 1");
$checkStmt->bind_param("s", $phone);
$checkStmt->execute();
$checkStmt->store_result();

if ($checkStmt->num_rows > 0) {
    json_response(false, "User already exists with this phone number.", [], 409);
}

$hashed_password = password_hash($password, PASSWORD_DEFAULT);

$stmt = $conn->prepare("INSERT INTO users (name, phone, email, password, role, blood_group, location) VALUES (?, ?, ?, ?, ?, ?, ?)");
$stmt->bind_param("sssssss", $name, $phone, $email, $hashed_password, $role, $blood_group, $location);

if ($stmt->execute()) {
    json_response(true, "Successfully Registered!!!", [
        'user_id' => $stmt->insert_id,
        'name' => $name,
        'phone' => $phone,
        'role' => $role
    ], 201);
} else {
    json_response(false, "Submission Not Completed.", [], 500);
}
?>