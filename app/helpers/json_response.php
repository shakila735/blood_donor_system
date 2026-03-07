<?php
function json_response($success, $message, $data = [], $status_code = 200) {
    http_response_code($status_code);
    header('Content-Type: application/json');

    echo json_encode([
        'success' => $success,
        'message' => $message,
        'data' => $data
    ], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    exit;
}
?>