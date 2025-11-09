<?php
// This handles the form submission from register.phtml

// We must include our db connection file
include_once __DIR__ . '/db.php';

// Get the database connection
$db = get_db();

// Get form data
$first_name = $_POST['first_name'];
$email = $_POST['email'];
$password = $_POST['password'];
$user_type = $_POST['user_type'];
$interest = $_POST['interest'];

// --- Validation ---
if (empty($first_name) || empty($email) || empty($password) || empty($user_type) || empty($interest)) {
    // Redirect back with an error
    header('Location: ../index.php?view=register&error=All fields are required.');
    exit;
}

// Hash the password for security
$password_hash = password_hash($password, PASSWORD_DEFAULT);

// --- Check if user already exists ---
$stmt = $db->prepare("SELECT id FROM users WHERE email = ?");
$stmt->execute([$email]);
if ($stmt->fetch()) {
    // Redirect back with an error
    header('Location: ../index.php?view=register&error=Email already in use.');
    exit;
}

// --- Insert new user into the database ---
try {
    $stmt = $db->prepare("INSERT INTO users (first_name, email, password_hash, user_type, interest) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([$first_name, $email, $password_hash, $user_type, $interest]);

    // --- Start a session and log them in ---
    session_start();
    $_SESSION['user_id'] = $db->lastInsertId();
    $_SESSION['first_name'] = $first_name;
    $_SESSION['user_type'] = $user_type;
    $_SESSION['interest'] = $interest;

    // Redirect to the correct dashboard
    if ($user_type === 'student') {
        header('Location: ../index.php?view=student');
    } else {
        header('Location: ../index.php?view=senior');
    }

} catch (PDOException $e) {
    header('Location: ../index.php?view=register&error=Database error: ' . $e->getMessage());
}
?>