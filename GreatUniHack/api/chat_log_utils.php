<?php
// This is our new, safe way to read and write the chat log.

function get_chat_log() {
    $logFile = __DIR__ . '/chat_log.json';
    if (!file_exists($logFile) || !is_readable($logFile)) {
        return []; // File doesn't exist, return empty array
    }

    $messagesJson = file_get_contents($logFile);
    if (empty($messagesJson)) {
        return []; // File is empty, return empty array
    }

    $messages = json_decode($messagesJson, true);

    // --- THIS IS THE FIX ---
    // If the file contained "null" or was corrupt, json_decode returns null.
    // We must check for this and return an empty array.
    if (!is_array($messages)) {
        return [];
    }
    // --- END FIX ---

    return $messages;
}

function save_chat_message($user, $message, $isAdmin = false) {
    $logFile = __DIR__ . '/chat_log.json';

    if (!file_exists($logFile) || !is_writable($logFile)) {
        // Fail silently if we can't write
        return;
    }

    // This will now always return a valid array
    $messages = get_chat_log();

    $newMessage = [
        'user' => $user,
        'message' => $message,
        'timestamp' => time(),
        'is_admin' => $isAdmin
    ];
    $messages[] = $newMessage;

    // Save the updated messages, with an exclusive lock to prevent errors
    file_put_contents($logFile, json_encode($messages), LOCK_EX);
}
?>