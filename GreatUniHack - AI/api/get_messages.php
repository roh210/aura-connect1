<?php
// This file is in /api/
// chat_log.json is in the same folder.
header('Content-Type: application/json');

$logFile = 'chat_log.json';

if (!file_exists($logFile)) {
    echo json_encode([]);
    exit;
}

$messages = json_decode(file_get_contents($logFile), true);

if (json_last_error() !== JSON_ERROR_NONE) {
    echo json_encode([]);
    exit;
}

echo json_encode($messages);
?>