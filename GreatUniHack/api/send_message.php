<?php
ini_set('display_errors', 0);
error_reporting(0);

// This include is the most likely thing to fail
include_once __DIR__ . '/chat_log_utils.php';

header('Content-Type: application/json');

// Check for the helper file, just in case
if (!function_exists('save_chat_message')) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Chat helper file is missing or corrupt.']);
    exit;
}

$logFile = __DIR__ . '/chat_log.json';
if (!is_writable($logFile)) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Chat log file (chat_log.json) is not writable. Check permissions.']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);

if (!$data || !isset($data['message']) || !isset($data['user'])) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Invalid data sent from browser.']);
    exit;
}

// Use our new helper function to safely save the message
save_chat_message(
    $data['user'],
    $data['message'],
    $data['is_admin'] ?? false
);

echo json_encode(['status' => 'success']);
?>