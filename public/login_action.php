<?php
session_start();
require_once __DIR__ . '/../app/config/db.php';
require_once __DIR__ . '/../app/helpers/validation.php';
require_once __DIR__ . '/../app/helpers/auth.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: login.php");
    exit;
}

$phone = clean_input($_POST['phone'] ?? '');
$password = $_POST['password'] ?? '';

$form_data = [
    'phone' => $phone,
    'password' => $password
];

$errors = validate_login($form_data);

if (!empty($errors)) {
    header("Location: login.php?error=" . urlencode(implode(" ", $errors)));
    exit;
}

$stmt = $conn->prepare("SELECT id, name, phone, password, role, is_active FROM users WHERE phone = ? LIMIT 1");
$stmt->bind_param("s", $phone);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows !== 1) {
    header("Location: login.php?error=" . urlencode("Account Not Found."));
    exit;
}

$user = $result->fetch_assoc();

if ((int)$user['is_active'] !== 1) {
    header("Location: login.php?error=" . urlencode("Your account is deactivated."));
    exit;
}

if (!password_verify($password, $user['password'])) {
    header("Location: login.php?error=" . urlencode("Invalid Password."));
    exit;
}

login_user($user);

switch ($user['role']) {
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
        header("Location: dashboard.php");
        break;
}
exit;
?>