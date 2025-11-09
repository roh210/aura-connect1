<?php
ini_set('display_errors', 0);
error_reporting(0);

// --- NEW ---
include_once __DIR__ . '/state_utils.php';

header('Content-Type: application/json');

// Reset the main state using our new function
reset_state();

// Also clear the chat log
$logFile = __DIR__ . '/chat_log.json';
if (is_writable($logFile)) {
    file_put_contents($logFile, json_encode([]));
}

echo json_encode(['status' => 'reset_success']);
?>