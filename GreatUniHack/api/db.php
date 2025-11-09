<?php
// This is our new Database Connection file.

function get_db() {
    // __DIR__ is the 'api' folder. We want the DB file in here.
    $db_path = __DIR__ . '/aura.db';

    try {
        // 1. Create (or connect to) the database
        // This creates a new PDO (PHP Data Object) connection
        $db = new PDO('sqlite:' . $db_path);

        // Set attributes for error reporting
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // 2. Create the 'users' table if it doesn't already exist
        $create_table_query = "
        CREATE TABLE IF NOT EXISTS users (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            first_name TEXT NOT NULL,
            email TEXT NOT NULL UNIQUE,
            password_hash TEXT NOT NULL,
            user_type TEXT NOT NULL,
            interest TEXT
        );
        ";

        $db->exec($create_table_query);

        return $db;

    } catch (PDOException $e) {
        // Handle connection error
        die("Database connection failed: " . $e->getMessage());
    }
}

?>
