<?php
/**
 * auth/logout.php
 * Destroy session and redirect to homepage.
 */
require_once __DIR__ . '/../includes/functions.php';

// Clear all session variables
$_SESSION = [];

// Delete the session cookie if it exists
if (isset($_COOKIE[session_name()])) {
    setcookie(session_name(), '', time() - 3600, '/');
}

// Destroy the session
session_destroy();

// Redirect to home
redirect(BASE_URL . 'index.php');
?>