<?php
/**
 * config/DBConn.php
 * Database connection handler
 * WEDE6021 POE — Pastimes
 */
ini_set('display_errors', 1);
error_reporting(E_ALL);

// ── Database Configuration ──────────────────────────────────
// Using 'localhost' for XAMPP (127.0.0.1 also works)
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');          // XAMPP default = no password
define('DB_NAME', 'ClothingStore');

// ── Create Connection ──────────────────────────────────────
$conn = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);

// ── Check Connection ──────────────────────────────────────
if (!$conn) {
    die('Database connection failed: ' . mysqli_connect_error() .
        '<br><br>Make sure XAMPP MySQL is running and the ClothingStore database exists.' .
        '<br>Run <a href="loadClothingStore.php">loadClothingStore.php</a> to create the database.');
}

// ── Set Charset ────────────────────────────────────────────
mysqli_set_charset($conn, 'utf8mb4');

// ── Helper Function ────────────────────────────────────────
function getDbConnection() {
    global $conn;
    return $conn;
}
?>