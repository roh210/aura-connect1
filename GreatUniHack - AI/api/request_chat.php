<?php
ini_set('display_errors', 0);
error_reporting(0);
session_start(); // <-- We need the session

header('Content-Type: application/json');

$stateFile = 'state.json';
if (!file_exists($stateFile) || !is_writable($stateFile)) {
    echo json_encode(['status' => 'error', 'message' => 'State file not found or not writable. Check permissions.']);
    exit;
}

$state = json_decode(file_get_contents($stateFile), true);
if ($state === null) {
    $state = ["senior_available" => false, "senior_name" => null, "student_request" => false, "student_name" => null, "chat_active" => false];
}

// --- THIS IS THE NEW LOGIC ---
$state['student_request'] = true;
$state['student_name'] = $_SESSION['first_name'] ?? 'Student';
// --- END NEW LOGIC ---

file_put_contents($stateFile, json_encode($state));

echo json_encode(['status' => 'success', 'chatRequest' => true]);
?>