<?php
ini_set('display_errors', 0);
error_reporting(0);
session_start();

include_once __DIR__ . '/ai_service.php';
include_once __DIR__ . '/chat_log_utils.php';
// --- NEW ---
include_once __DIR__ . '/state_utils.php';

header('Content-Type: application/json');

// --- THIS IS THE NEW "AGENT BRAIN" ---

// We wrap the *entire* check in our update function
// This locks the file so no one else can touch it while we "think"
$response = null;
$finalState = update_state(function($state) use (&$response) {

    if (isset($state['chat_active']) && $state['chat_active']) {
        $response = ['status' => 'start_chat'];
        return $state; // No changes needed

    } elseif (isset($state['senior_available']) && $state['senior_available'] && isset($state['student_request']) && $state['student_request']) {

        // This is the "collision"!

        if (isset($_SESSION['user_type']) && $_SESSION['user_type'] === 'student') {
            $_SESSION['partner_name'] = $state['senior_name'] ?? 'Senior';

            $icebreaker = generateIcebreaker(
                $state['student_name'],
                $state['student_interest'],
                $state['senior_name'],
                $state['senior_interest']
            );

            save_chat_message('Aura', "You're connected with " . $state['senior_name'] . "! " . $icebreaker, true);

        } else {
            $_SESSION['partner_name'] = $state['student_name'] ?? 'Student';
        }

        // Update the state to "lock" the chat
        $state['chat_active'] = true;
        $response = ['status' => 'start_chat'];
        return $state; // Return the *modified* state

    } elseif (isset($state['senior_available']) && $state['senior_available'] || isset($state['student_request']) && $state['student_request']) {
        $response = ['status' => 'searching'];
        return $state; // No changes needed
    }

    // Default
    $response = ['status' => 'idle'];
    return $state; // No changes needed
});

echo json_encode($response);
?>