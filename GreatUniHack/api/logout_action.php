<?php
// We do NOT need session_start() here,
// because index.php (which includes this file) has already started it.

// 1. Unset all session variables
session_unset();

// 2. Destroy the session file on the server
session_destroy();

// 3. Redirect the user to the login page
// The path is simple because this file is being "run" by index.php
header('Location: index.php?view=login');
exit;
?>