<?php
/**
 * loadClothingStore.php
 * Creates and seeds the complete ClothingStore database
 * Run this ONCE via: http://localhost/pastimes/loadClothingStore.php
 */
ini_set('display_errors', 1);
error_reporting(E_ALL);

$host = '127.0.0.1';
$user = 'root';
$pass = '';
$name = 'ClothingStore';

echo "<pre style='background:#1a1a1a;color:#0f0;padding:20px;font-family:monospace;'>";
echo "⚙️  loadClothingStore.php — Starting Database Setup\n\n";

$mysqli = new mysqli($host, $user, $pass);
if ($mysqli->connect_error) {
    die("❌ Cannot connect to MySQL: " . $mysqli->connect_error .
        "\nMake sure XAMPP MySQL is running.\n</pre>");
}

// Create database
$mysqli->query("DROP DATABASE IF EXISTS `$name`");
$mysqli->query("CREATE DATABASE IF NOT EXISTS `$name` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
$mysqli->select_db($name);
echo "✅ Database created: $name\n";

// Read and execute schema
$sqlFile = __DIR__ . '/database.sql';
if (!file_exists($sqlFile)) {
    die("❌ database.sql not found in " . __DIR__ . "\n</pre>");
}

$sql = file_get_contents($sqlFile);
$queries = array_filter(array_map('trim', preg_split('/;[\s\n]+/', $sql)));

$count = 0;
foreach ($queries as $query) {
    if (!empty($query)) {
        if (!$mysqli->query($query)) {
            echo "⚠️  Query error: " . $mysqli->error . "\n";
        } else {
            $count++;
        }
    }
}

echo "✅ Schema loaded: $count queries executed\n";
echo "✅ Sample data inserted\n\n";

echo "═══════════════════════════════════════════════════════\n";
echo "TEST ACCOUNTS (password: 'password')\n";
echo "═══════════════════════════════════════════════════════\n";
echo "👤 ADMIN:        admin@pastimes.co.za\n";
echo "👤 BUYER:        john@example.com\n";
echo "👤 SELLER:       sarah@example.com\n";
echo "👤 SELLER:       mike@example.com\n";
echo "👤 DEMO SELLER:  demo@pastimes.co.za\n";
echo "👤 DEMO BUYER:   buyer@pastimes.co.za\n";
echo "═══════════════════════════════════════════════════════\n\n";

echo "✅ Database setup complete!\n";
echo "🚀 <a href='index.php' style='color:#0f0;'>→ Visit your website</a>\n";
echo "</pre>";

$mysqli->close();
?>
