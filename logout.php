<?php
// ============================================================
//  TechSync Blog — Logout
//  logout.php
//
//  Destroys the PHP session and redirects to homepage.
// ============================================================
session_start();

// Destroy all session data
$_SESSION = [];

// Delete the session cookie from the browser
if (ini_get('session.use_cookies')) {
    $params = session_get_cookie_params();
    setcookie(
        session_name(), '',
        time() - 42000,
        $params['path'],
        $params['domain'],
        $params['secure'],
        $params['httponly']
    );
}

session_destroy();

// Redirect to homepage with a goodbye message
header('Location: index.php?success=You+have+been+signed+out+successfully');
exit;
