<?php
/**
 * Test database connection and setup
 */
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h2>Testing Pastimes v2 Database Setup</h2>";

// Test connection
$conn = mysqli_connect('127.0.0.1', 'root', '', 'ClothingStore');
if ($conn) {
    echo "<p style='color:green;'>✓ Connected to ClothingStore database</p>";
    
    // Check for key tables
    $tables = ['tblUser', 'tblProducts', 'tblOrders', 'categories'];
    foreach ($tables as $tbl) {
        $res = mysqli_query($conn, "SHOW TABLES LIKE '$tbl'");
        $exists = mysqli_num_rows($res) > 0;
        echo $exists 
            ? "<p style='color:green;'>✓ Table $tbl exists</p>"
            : "<p style='color:red;'>✗ Table $tbl missing - run loadClothingStore.php</p>";
    }
    
    mysqli_close($conn);
} else {
    echo "<p style='color:red;'>✗ Cannot connect to database. Make sure:</p>";
    echo "<ul>";
    echo "<li>XAMPP MySQL is running</li>";
    echo "<li>ClothingStore database exists</li>";
    echo "<li>Run: http://localhost/pastimes/loadClothingStore.php</li>";
    echo "</ul>";
}
?>
