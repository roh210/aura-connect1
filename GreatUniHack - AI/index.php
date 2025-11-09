<?php
// --- MUST BE AT THE VERY TOP ---
session_start();

// --- NEW KILL SWITCH ---
// If you add &logout=true to any URL, it will kill the session.
if (isset($_GET['logout'])) {
    session_unset();
    session_destroy();
    header('Location: index.php?view=login'); // Go to login
    exit;
}
// --- END KILL SWITCH ---


$ROOT_PATH = __DIR__;
$view = $_GET['view'] ?? 'home';
$action = $_GET['action'] ?? null;

// --- Get user info if logged in ---
$user_id = $_SESSION['user_id'] ?? null;
$user_type = $_SESSION['user_type'] ?? null;
$first_name = $_SESSION['first_name'] ?? null;

// --- API Routing ---
if ($view === 'api') {

    switch ($action) {
        case 'handle_register':
            include $ROOT_PATH . '/api/register_action.php';
            break;
        case 'handle_login':
            include $ROOT_PATH . '/api/login_action.php';
            break;

        // --- NEW LOGOUT LOGIC ---
        // We put the code here directly to be 100% sure it works.
        case 'handle_logout':
            session_unset();
            session_destroy();
            header('Location: index.php?view=login');
            exit;
        // --- END NEW LOGOUT LOGIC ---

        // --- FIX: ALL API PATHS NOW USE $ROOT_PATH ---
        case 'check_status':
            include $ROOT_PATH . '/api/check_status.php';
            break;
        case 'set_senior_state':
            include $ROOT_PATH . '/api/set_senior_state.php';
            break;
        case 'request_chat':
            include $ROOT_PATH . '/api/request_chat.php';
            break;
        case 'get_messages':
            include $ROOT_PATH . '/api/get_messages.php';
            break;
        case 'send_message':
            include $ROOT_PATH . '/api/send_message.php';
            break;
        case 'reset_chat':
            include $ROOT_PATH . '/api/reset_chat.php';
            break;
        default:
            header('Content-Type: application/json');
            http_response_code(400);
            echo json_encode(['status' => 'error', 'message' => 'Invalid API action']);
    }
    exit;
}

// --- Page (View) Routing ---

// --- NEW AUTH PROTECTION ---
if (!$user_id && $view !== 'login' && $view !== 'register' && $view !== 'home') {
    header('Location: index.php?view=login');
    exit;
}
if ($user_id && ($view === 'login' || $view === 'register')) {
    header('Location: index.php?view=' . $user_type);
    exit;
}
// --- END AUTH PROTECTION ---


switch ($view) {
    case 'home':
        if ($user_id) {
            header('Location: index.php?view=' . $user_type);
        } else {
            header('Location: index.php?view=login');
        }
        break;

    case 'login':
        include $ROOT_PATH . '/Views/login.phtml';
        break;
    case 'register':
        include $ROOT_PATH . '/Views/register.phtml';
        break;

    case 'student':
        if ($user_type !== 'student') {
            header('Location: index.php?view=senior');
            exit;
        }
        // --- FIX: This path was wrong ---
        include $ROOT_PATH . '/student.php';
        break;
    case 'senior':
        if ($user_type !== 'senior') {
            header('Location: index.php?view=student');
            exit;
        }
        include $ROOT_PATH . '/Views/senior.phtml';
        break;
    case 'chat':
        // --- FIX: This path was wrong ---
        include $ROOT_PATH . '/chat.php';
        break;
    default:
        header('Location: index.php?view=login');
        break;
}
?>