<?php
// This handles the form submission from login.phtml

include_once __DIR__ . '/db.php';
$db = get_db();

$email = $_POST['email'];
$password = $_POST['password'];

// --- Validation ---
if (empty($email) || empty($password)) {
    header('Location: ../index.php?view=login&error=Email and password are required.');
    exit;
}

// --- Find the user ---
$stmt = $db->prepare("SELECT * FROM users WHERE email = ?");
$stmt->execute([$email]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if ($user && password_verify($password, $user['password_hash'])) {
    // --- Password is correct! Start a session ---
    session_start();
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['first_name'] = $user['first_name'];
    $_SESSION['user_type'] = $user['user_type'];

    // Redirect to the correct dashboard
    if ($user['user_type'] === 'student') {
        header('Location: ../index.php?view=student');
    } else {
        header('Location: ../index.php?view=senior');
    }

} else {
    // --- Invalid credentials ---
    header('Location: ../index.php?view=login&error=Invalid email or password.');
}
?>