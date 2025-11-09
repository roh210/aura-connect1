<?php
ini_set('display_errors', 0);
error_reporting(0);
session_start(); // <-- We need the session

header('Content-Type: application/json');

$stateFile = 'state.json';
if (!file_exists($stateFile) || !is_readable($stateFile)) {
    echo json_encode(['status' => 'error', 'message' => 'State file not found or not readable. Check permissions.']);
    exit;
}

$state = json_decode(file_get_contents($stateFile), true);
if ($state === null) {
    echo json_encode(['status' => 'error', 'message' => 'State file is empty or corrupt.']);
    exit;
}

$response = ['status' => 'idle'];

// --- THIS IS THE NEW LOGIC ---
if (isset($state['chat_active']) && $state['chat_active']) {
    $response['status'] = 'start_chat';

} elseif (isset($state['senior_available']) && $state['senior_available'] && isset($state['student_request']) && $state['student_request']) {

    // This is the "collision"!

    // --- NEW: Save partner's name to session ---
    if (isset($_SESSION['user_type']) && $_SESSION['user_type'] === 'student') {
        $_SESSION['partner_name'] = $state['senior_name'] ?? 'Senior';
    } else {
        $_SESSION['partner_name'] = $state['student_name'] ?? 'Student';
    }
    // --- END NEW ---

    $state['chat_active'] = true;

    if (is_writable($stateFile)) {
        file_put_contents($stateFile, json_encode($state));
        $response['status'] = 'start_chat'; // Tell both clients to start
    } else {
        $response['status'] = 'error';
        $response['message'] = 'State file not writable. Check permissions.';
    }

} elseif (isset($state['senior_available']) && $state['senior_available'] || isset($state['student_request']) && $state['student_request']) {
    $response['status'] = 'searching';
}
// --- END NEW LOGIC ---

echo json_encode($response);
?>