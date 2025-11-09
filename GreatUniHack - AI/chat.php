<?php
//var_dump($_GET);
//die; // This stops the script
$view = new stdClass();
$view->pageTitle = 'Homepage';

// This page is now loaded by index.php, which already started the session
$user_type = $_SESSION['user_type'] ?? 'student';
$user_name = $_SESSION['first_name'] ?? 'User';

// --- THIS IS THE FIX ---
// We get the partner's name from the session, which was set by check_status.php
$chat_partner_name = $_SESSION['partner_name'] ?? 'Partner';


require_once('Views/chat.phtml');
