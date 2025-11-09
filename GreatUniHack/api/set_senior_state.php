<?php
ini_set('display_errors', 0);
error_reporting(0);
session_start();

// --- NEW ---
include_once __DIR__ . '/state_utils.php';

header('Content-Type: application/json');

$isAvailable = $_GET['available'] === 'true';

// Use our new, safe update function
$newState = update_state(function($currentState) use ($isAvailable) {

    $currentState['senior_available'] = $isAvailable;

    if ($isAvailable) {
        $currentState['senior_name'] = $_SESSION['first_name'] ?? 'Senior';
        $currentState['senior_interest'] = $_SESSION['interest'] ?? 'life';
    } else {
        // If they cancel, reset just their part
        $currentState['senior_available'] = false;
        $currentState['senior_name'] = null;
        $currentState['senior_interest'] = null;
        // Don't reset the student, they might still be searching
    }

    return $currentState;
});

echo json_encode(['status' => 'success', 'seniorAvailable' => $newState['senior_available']]);
?>