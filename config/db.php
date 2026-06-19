<?php
/**
 * config/db.php
 * Bootstrap DB connection + ShoppingCart class
 */
require_once __DIR__ . '/DBConn.php';

if ($conn) {
    mysqli_set_charset($conn, 'utf8mb4');
}

require_once __DIR__ . '/../includes/ShoppingCart.php';
?>
