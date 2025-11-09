<?php
ini_set('display_errors', 0);
error_reporting(0);

// --- NEW: Include our safe helper file ---
include_once __DIR__ . '/chat_log_utils.php';

header('Content-Type: application/json');

// --- THIS IS THE FIX ---
// Replace all the old, unsafe logic with this one line
$messages = get_chat_log();
// --- END FIX ---

echo json_encode($messages);
?>