<?php
// This file is in /api/
// chat_log.json is in the same folder.
header('Content-Type: application/json');

$logFile = 'chat_log.json';

// Get the message from the POST body
$data = json_decode(file_get_contents('php://input'), true);

if (!$data || !isset($data['message']) || !isset($data['user'])) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Invalid data']);
    exit;
}

// Load current messages
$messages = json_decode(file_get_contents($logFile), true);
if (json_last_error() !== JSON_ERROR_NONE) {
    $messages = [];
}

// Add new message
$newMessage = [
    'user' => $data['user'],
    'message' => $data['message'],
    'timestamp' => time(),
    'is_admin' => $data['is_admin'] ?? false
];
$messages[] = $newMessage;

// Save messages
file_put_contents($logFile, json_encode($messages));

echo json_encode(['status' => 'success']);
?>