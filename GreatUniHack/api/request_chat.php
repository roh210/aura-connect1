<?php
ini_set('display_errors', 0);
error_reporting(0);
session_start();

// --- NEW ---
include_once __DIR__ . '/state_utils.php';

header('Content-Type: application/json');

// Use our new, safe update function
$newState = update_state(function($currentState) {

    $currentState['student_request'] = true;
    $currentState['student_name'] = $_SESSION['first_name'] ?? 'Student';
    $currentState['student_interest'] = $_SESSION['interest'] ?? 'school';

    return $currentState;
});

echo json_encode(['status' => 'success', 'chatRequest' => true]);
?>