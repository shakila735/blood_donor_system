<?php
header('Content-Type: application/json');

require_once __DIR__ . '/../../app/config/db.php';
require_once __DIR__ . '/../../app/helpers/auth.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode([
        "success" => false,
        "message" => "Method not allowed.",
        "data" => []
    ]);
    exit;
}

if (!is_logged_in()) {
    http_response_code(401);
    echo json_encode([
        "success" => false,
        "message" => "Unauthorized. Please login first.",
        "data" => []
    ]);
    exit;
}

$user_id = current_user_id();
$requester_role = current_user_role();

if (!in_array($requester_role, ['requester', 'hospital'])) {
    http_response_code(403);
    echo json_encode([
        "success" => false,
        "message" => "Only requester and hospital can create blood requests.",
        "data" => []
    ]);
    exit;
}

$input = json_decode(file_get_contents("php://input"), true);

if (!$input) {
    http_response_code(400);
    echo json_encode([
        "success" => false,
        "message" => "Invalid JSON input.",
        "data" => []
    ]);
    exit;
}

$patient_name  = trim($input['patient_name'] ?? '');
$blood_group   = trim($input['blood_group'] ?? '');
$units_needed  = (int)($input['units_needed'] ?? 0);
$needed_date   = trim($input['needed_date'] ?? '');
$location      = trim($input['location'] ?? '');
$hospital_name = trim($input['hospital_name'] ?? '');
$contact_phone = trim($input['contact_phone'] ?? '');
$details       = trim($input['details'] ?? '');

$allowed_blood_groups = ['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'];
$errors = [];

if ($patient_name === '') {
    $errors[] = "Patient name is required.";
}

if (!in_array($blood_group, $allowed_blood_groups)) {
    $errors[] = "Valid blood group is required.";
}

if ($units_needed < 1) {
    $errors[] = "Units needed must be at least 1.";
}

if ($needed_date === '') {
    $errors[] = "Needed date is required.";
}

if ($location === '') {
    $errors[] = "Location is required.";
}

if ($contact_phone === '') {
    $errors[] = "Contact phone is required.";
}

if (!empty($errors)) {
    http_response_code(422);
    echo json_encode([
        "success" => false,
        "message" => "Validation failed.",
        "data" => $errors
    ]);
    exit;
}

$sql = "INSERT INTO blood_requests
        (user_id, requester_role, patient_name, blood_group, units_needed, needed_date, location, hospital_name, contact_phone, details)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

$stmt = $conn->prepare($sql);

if (!$stmt) {
    http_response_code(500);
    echo json_encode([
        "success" => false,
        "message" => "Database prepare failed.",
        "data" => [$conn->error]
    ]);
    exit;
}

$stmt->bind_param(
    "isssisssss",
    $user_id,
    $requester_role,
    $patient_name,
    $blood_group,
    $units_needed,
    $needed_date,
    $location,
    $hospital_name,
    $contact_phone,
    $details
);

if ($stmt->execute()) {
    http_response_code(201);
    echo json_encode([
        "success" => true,
        "message" => "Blood request posted successfully.",
        "data" => [
            "request_id" => $stmt->insert_id
        ]
    ]);
} else {
    http_response_code(500);
    echo json_encode([
        "success" => false,
        "message" => "Failed to save blood request.",
        "data" => [$stmt->error]
    ]);
}

$stmt->close();
$conn->close();
?>