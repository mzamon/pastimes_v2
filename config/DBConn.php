<?php
/**
 * config/DBConn.php
 * Database connection handler
 * WEDE6021 POE — Pastimes
 */
ini_set('display_errors', 1);
error_reporting(E_ALL);

define('DB_HOST', '127.0.0.1');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'ClothingStore');

$conn = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);

if (!$conn) {
    die('Database connection failed: ' . mysqli_connect_error() .
        '<br>Make sure XAMPP MySQL is running and the ClothingStore database exists.' .
        '<br>Run loadClothingStore.php to create the database.');
}

function getDbConnection() {
    global $conn;
    return $conn;
}
?>
