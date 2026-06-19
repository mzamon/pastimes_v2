<?php
require_once __DIR__ . '/../includes/functions.php';
$_SESSION = [];
if (isset($_COOKIE[session_name()])) {
    setcookie(session_name(), '', time() - 3600, '/');
}
session_destroy();
redirect(BASE_URL . 'index.php');
?>
