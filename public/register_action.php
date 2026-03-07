<?php
session_start();

require_once __DIR__ . '/../app/config/db.php';
require_once __DIR__ . '/../app/helpers/validation.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: register.php");
    exit;
}

$name = clean_input($_POST['name'] ?? '');
$phone = clean_input($_POST['phone'] ?? '');
$email = clean_input($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';
$role = clean_input($_POST['role'] ?? '');
$blood_group = clean_input($_POST['blood_group'] ?? '');
$location = clean_input($_POST['location'] ?? '');

$data = [
    'name' => $name,
    'phone' => $phone,
    'email' => $email,
    'password' => $password,
    'role' => $role,
    'blood_group' => $blood_group,
    'location' => $location
];

$errors = validate_registration($data);

if (!empty($errors)) {
    header("Location: register.php?error=" . urlencode(implode(" ", $errors)));
    exit;
}

$check = $conn->prepare("SELECT id FROM users WHERE phone = ?");
$check->bind_param("s", $phone);
$check->execute();
$check->store_result();

if ($check->num_rows > 0) {
    header("Location: register.php?error=" . urlencode("Phone number already registered."));
    exit;
}

$hashed_password = password_hash($password, PASSWORD_DEFAULT);

$stmt = $conn->prepare("INSERT INTO users (name, phone, email, password, role, blood_group, location) VALUES (?, ?, ?, ?, ?, ?, ?)");

$stmt->bind_param(
    "sssssss",
    $name,
    $phone,
    $email,
    $hashed_password,
    $role,
    $blood_group,
    $location
);

if ($stmt->execute()) {

    header("Location: register.php?success=" . urlencode("Successfully Registered!!!"));
    exit;

} else {

    header("Location: register.php?error=" . urlencode("Registration failed."));
    exit;
}
?>