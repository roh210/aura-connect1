<?php
ini_set('display_errors', 0);
error_reporting(0);

header('Content-Type: application/json');

// Reset the main state
$stateFile = 'state.json';
if (is_writable($stateFile)) {
    // --- THIS IS THE UPDATE ---
    $state = [
        "senior_available" => false,
        "senior_name" => null,
        "student_request" => false,
        "student_name" => null,
        "chat_active" => false
    ];
    // --- END UPDATE ---
    file_put_contents($stateFile, json_encode($state));
}

// Also clear the chat log
$logFile = 'chat_log.json';
if (is_writable($logFile)) {
    file_put_contents($logFile, json_encode([]));
}

echo json_encode(['status' => 'reset_success']);
?>