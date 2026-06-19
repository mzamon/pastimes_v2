<?php
/**
 * loadClothingStore.php
 * SAFE to run multiple times. Drops, recreates and seeds the whole DB
 * from database.sql, then adds enough demo records for the 30-entry
 * requirement without dropping anything if you run it again.
 */
ini_set('display_errors', 1);
error_reporting(E_ALL);

$host = '127.0.0.1';
$user = 'root';
$pass = '';
$name = 'ClothingStore';

echo "<pre>⚙  loadClothingStore.php — starting\n";

$mysqli = new mysqli($host, $user, $pass);
if ($mysqli->connect_error) {
    die("❌  Cannot connect to MySQL: " . $mysqli->connect_error .
        "\nMake sure XAMPP MySQL is running.\n</pre>");
}

$sqlFile = __DIR__ . '/database.sql';
if (!file_exists($sqlFile)) {
    die("❌  database.sql not found in " . __DIR__ . "\n</pre>");
}

$sql = file_get_contents($sqlFile);

// Remove the CREATE DATABASE + USE statements — we handle the DB
// selection separately so multi_query doesn't choke on it
$sql = preg_replace('/^CREATE DATABASE.*?;\s*/ims', '', $sql);
$sql = preg_replace('/^USE\s+\w+\s*;\s*/ims', '', $sql);

// Create DB if needed, then select it
$mysqli->query("CREATE DATABASE IF NOT EXISTS `$name` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
$mysqli->select_db($name);

echo "✓  Database selected: $name\n";

if ($mysqli->multi_query($sql)) {
    do { if ($r = $mysqli->store_result()) $r->free(); } while ($mysqli->next_result());
    echo "✓  Schema and sample data loaded from database.sql\n";
} else {
    die("❌  SQL error: " . $mysqli->error . "\n</pre>");
}

// ── Seed 30 clothing-appropriate demo products ───────────────
$hash = '{{BCRYPT_HASH}}';  // <-- REPLACE with your generated hash for Kookemooi10!

// Ensure demo seller exists
$mysqli->query(
    "INSERT IGNORE INTO tblUser (name, email, password_hash, role, is_verified, seller_request)
     VALUES ('Demo Seller', 'demo@pastimes.co.za', '$hash', 'seller', 1, 'approved')"
);
$sellerResult = $mysqli->query("SELECT id FROM tblUser WHERE email='demo@pastimes.co.za' LIMIT 1");
$sellerRow    = $sellerResult->fetch_assoc();
$sellerId     = $sellerRow['id'];

// Ensure demo buyer exists
$mysqli->query(
    "INSERT IGNORE INTO tblUser (name, email, password_hash, role, is_verified)
     VALUES ('Demo Buyer', 'buyer@pastimes.co.za', '$hash', 'buyer', 1)"
);

// Demo products with BRAND included (index 2)
$demoProducts = [
    [1, 'Levi\'s 501 Blue Denim Jeans',       'Levi\'s',     'Straight cut 501s, size 34x32. Faded authentically.', 380, 'Good'],
    [2, 'Cotton On Floral Wrap Dress',        'Cotton On',   'Size S/M. Worn once. Excellent condition.', 120, 'Like New'],
    [3, 'Stüssy Bucket Hat',                  'Stüssy',      'Black with embroidered logo. One size fits all.', 250, 'Good'],
    [4, 'Outerwear Puffer Vest',              'Outerwear',   'Size M. Light down fill. Perfect for layering.', 320, 'Good'],
    [5, 'Vans Old Skool Black',               'Vans',        'Size 8. Used but clean. Classic skate silhouette.', 450, 'Fair'],
    [6, 'Coach Leather Crossbody Bag',        'Coach',       'Tan leather, silver hardware. Minor wear on strap.', 780, 'Good'],
    [7, 'Vintage Lee Riders Jacket',          'Lee',         'Stonewash denim, size L. 1980s authenticity.', 520, 'Fair'],
    [8, 'Under Armour Compression Set',       'Under Armour','Shirt + shorts. Size M. Washed and ready to use.', 200, 'Good'],
    [3, 'Palace Tri-Ferg Tee',                'Palace',      'White with red/blue logo, size L. Authentic.', 350, 'Good'],
    [2, 'Mango Satin Slip Skirt',             'Mango',       'Champagne colour, size S. Worn twice for events.', 180, 'Like New'],
    [1, 'Tommy Hilfiger Chino Pants',         'Tommy Hilfiger','Khaki, size 32x30. Classic straight cut.', 260, 'Good'],
    [4, 'Dr Martens 1460 Boots',              'Dr Martens',  'Size 7. Black with yellow stitching. Minor heel wear.', 920, 'Fair'],
    [6, 'Ray-Ban Wayfarer Sunglasses',        'Ray-Ban',     'Classic black frame. Comes with original case.', 680, 'Good'],
    [3, 'Carhartt WIP Work Jacket',           'Carhartt',    'Washed black duck canvas, size L. Heavy-duty.', 850, 'Good'],
    [7, 'Zara Tailored Blazer',               'Zara',        'Charcoal grey, size 38. Worn 3 times for work.', 390, 'Like New'],
    [5, 'Converse Chuck Taylor High',         'Converse',    'Red, size 9. All-star classic. Slightly worn.', 380, 'Fair'],
    [2, 'Woolworths Linen Trousers',          'Woolworths',  'White wide-leg trousers, size 12. Summer essential.', 150, 'Good'],
    [8, 'Asics Gel-Nimbus 24',                'Asics',       'Size 10. Used for 3 months of running. Still supportive.', 720, 'Fair'],
    [1, 'Polo Ralph Lauren Oxford Shirt',     'Ralph Lauren','Blue stripe, size M. Classic American style.', 280, 'Good'],
    [4, 'Stone Island Nylon Jacket',          'Stone Island','Dark navy, size L. Minor badge wear. Authentic.', 1800, 'Good'],
    [7, 'Diesel Regular Denim Jacket',        'Diesel',      'Distressed wash, size M. Lots of character.', 430, 'Fair'],
    [2, 'H&M Knit Cardigan',                  'H&M',         'Cream open-front cardigan, size S. Very cosy.', 90, 'Like New'],
    [3, 'Puma RS-X Sneakers',                 'Puma',        'White/blue colourway, size 9. Chunky 90s runner.', 510, 'Good'],
    [6, 'Michael Kors Tote Bag',              'Michael Kors','Black leather tote. Pen mark on inner lining.', 950, 'Fair'],
    [1, 'Ben Sherman Mod Shirt',              'Ben Sherman', 'Paisley print, size L. Great for weekend casual.', 140, 'Good'],
    [8, 'Reebok Classic Leather',             'Reebok',      'White, size 8.5. Cleaned and whitened sole.', 340, 'Good'],
    [5, 'Sperry Topsider Boat Shoes',         'Sperry',      'Tan leather, size 10. Worn seaside only.', 420, 'Good'],
    [4, 'Columbia Fleece Jacket',             'Columbia',    'Blue zip-up fleece, size XL. Ideal for hiking.', 290, 'Good'],
    [2, 'Topshop Denim Cut-offs',             'Topshop',     'Frayed hem, size 10. Festival-ready.', 110, 'Good'],
    [3, 'Dickies 874 Work Pants',             'Dickies',     'Khaki, size 34x32. Classic straight leg workwear.', 220, 'Like New'],
];

// Get category IDs
$catRes = $mysqli->query("SELECT id FROM categories ORDER BY id");
$catIds = [];
while ($row = $catRes->fetch_assoc()) $catIds[] = (int)$row['id'];

$images = [
    'vintage-clothing/denim-jacket-1.jpg',
    'vintage-clothing/leather-jacket-1.jpg',
    'vintage-clothing/denim-jacket-2.jpg',
    'streetwear/hoodie-black-1.jpg',
    'streetwear/sneakers-hightop-1.jpg',
    'sports-gear/running-shorts-1.jpg',
    'sports-gear/gym-tanktop-1.jpg',
    'sports-gear/yoga-mat-1.jpg',
];

$inserted = 0;
foreach ($demoProducts as $i => $dp) {
    $catOffset = $dp[0] - 1;
    $catId     = $catIds[$catOffset % count($catIds)];
    $img       = $images[$i % count($images)];
    $title     = $dp[1];
    $brand     = $dp[2];
    $desc      = $dp[3];
    $price     = $dp[4];
    $cond      = $dp[5];

    $stmt = $mysqli->prepare(
        "INSERT IGNORE INTO tblProducts (seller_id, category_id, title, brand, description, price, `condition`, image, quantity, status)
         VALUES (?, ?, ?, ?, ?, ?, ?, ?, 1, 'active')"
    );
    $stmt->bind_param('iisssdss', $sellerId, $catId, $title, $brand, $desc, $price, $cond, $img);
    if ($stmt->execute()) $inserted++;
    $stmt->close();
}

echo "✓  $inserted demo products inserted\n";
echo "✓  Setup complete!\n\n";
echo "ACCOUNTS (password: <b>Kookemooi10!</b>)\n";
echo "  Admin:  admin@pastimes.co.za\n";
echo "  Buyer:  koos@gmail.com\n";
echo "  Seller: sarah@example.com\n";
echo "  Seller: mike@example.com\n";
echo "\n<a href='index.php'>→ Visit your website</a>\n";
echo "</pre>";

$mysqli->close();
?>