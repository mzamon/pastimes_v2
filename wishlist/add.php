<?php
/**
 * wishlist/add.php
 * Add product to wishlist (INSERT IGNORE into tblWishlist)
 */
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/functions.php';

requireLogin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit('Method not allowed');
}

$product_id = intval($_POST['product_id'] ?? 0);
$user_id    = $_SESSION['user_id'];

if ($product_id <= 0) {
    http_response_code(400);
    exit('Invalid product');
}

// Verify product exists
$stmt = mysqli_prepare($conn, "SELECT id FROM tblProducts WHERE id = ?");
mysqli_stmt_bind_param($stmt, 'i', $product_id);
mysqli_stmt_execute($stmt);
if (!mysqli_stmt_get_result($stmt)->fetch_assoc()) {
    http_response_code(404);
    exit('Product not found');
}
mysqli_stmt_close($stmt);

// Add to wishlist (INSERT IGNORE handles duplicate)
$stmt = mysqli_prepare($conn, "INSERT IGNORE INTO tblWishlist (user_id, product_id) VALUES (?, ?)");
mysqli_stmt_bind_param($stmt, 'ii', $user_id, $product_id);

if (mysqli_stmt_execute($stmt)) {
    $_SESSION['success'] = ['Added to wishlist!'];
    http_response_code(200);
    echo json_encode(['status' => 'success', 'message' => 'Added to wishlist']);
} else {
    $_SESSION['errors'] = ['Failed to add to wishlist'];
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Failed to add']);
}
mysqli_stmt_close($stmt);
?>
