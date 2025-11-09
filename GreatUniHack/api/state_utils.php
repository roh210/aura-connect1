<?php
// This is our new, safe way to read and write the state.

function get_state() {
    $stateFile = __DIR__ . '/state.json';
    $fp = fopen($stateFile, 'r+');

    // 1. Lock the file for reading
    flock($fp, LOCK_SH);

    $stateJson = file_get_contents($stateFile);

    // 2. Unlock the file
    flock($fp, LOCK_UN);
    fclose($fp);

    $state = json_decode($stateJson, true);

    if ($state === null) {
        // If file is corrupt, reset it
        return reset_state();
    }
    return $state;
}

function update_state($callback) {
    $stateFile = __DIR__ . '/state.json';
    $fp = fopen($stateFile, 'r+');

    // 1. Get an EXCLUSIVE lock (no one else can read or write)
    if (!flock($fp, LOCK_EX)) {
        fclose($fp);
        return; // Failed to get lock
    }

    // 2. Read the current state
    $stateJson = stream_get_contents($fp);
    $state = json_decode($stateJson, true);

    if ($state === null) {
        $state = ["senior_available" => false, "senior_name" => null, "senior_interest" => null, "student_request" => false, "student_name" => null, "student_interest" => null, "chat_active" => false];
    }

    // 3. Call the function that makes the change
    $state = $callback($state);

    // 4. Go back to the beginning, erase, and write new state
    ftruncate($fp, 0);
    rewind($fp);
    fwrite($fp, json_encode($state));

    // 5. Release the lock and close
    flock($fp, LOCK_UN);
    fclose($fp);

    return $state;
}

function reset_state() {
    $stateFile = __DIR__ . '/state.json';
    $state = [
        "senior_available" => false,
        "senior_name" => null,
        "senior_interest" => null,
        "student_request" => false,
        "student_name" => null,
        "student_interest" => null,
        "chat_active" => false
    ];
    file_put_contents($stateFile, json_encode($state), LOCK_EX);
    return $state;
}
?>