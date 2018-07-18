<?php
// dump(__FILE__);
defined('IS_VALID') or define('IS_VALID', 1);
require_once("main.php");

// ------------------------------------------------------------ //

// Initialize the session.
// If you are using session_name("something"), don't forget it now!
session_start();

// Unset all of the session variables.
$_SESSION = array();

// If it's desired to kill the session, also delete the session cookie.
// Note: This will destroy the session, and not just the session data!
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Finally, destroy the session.
session_destroy();

if (isset($_SESSION['user_name']))
	unset($_SESSION['user_name']);

echo('Redirecting ...');
\Utility::func_redirect('login.php', 1);
exit;