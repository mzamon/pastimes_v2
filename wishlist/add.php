<?php
/**
 * wishlist/add.php
 * Add product to wishlist (accepts GET and POST)
 */
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/functions.php';

requireLogin();

// Get product ID from GET or POST
$product_id = intval($_GET['id'] ?? $_POST['product_id'] ?? 0);
$user_id    = $_SESSION['user_id'];

if ($product_id <= 0) {
    $_SESSION['errors'] = ['Invalid product'];
    redirect(BASE_URL . 'products/index.php');
}

// Verify product exists
$stmt = mysqli_prepare($conn, "SELECT id FROM tblProducts WHERE id = ?");
mysqli_stmt_bind_param($stmt, 'i', $product_id);
mysqli_stmt_execute($stmt);
if (!mysqli_stmt_get_result($stmt)->fetch_assoc()) {
    $_SESSION['errors'] = ['Product not found'];
    redirect(BASE_URL . 'products/index.php');
}
mysqli_stmt_close($stmt);

// Add to wishlist (INSERT IGNORE handles duplicate)
$stmt = mysqli_prepare($conn, "INSERT IGNORE INTO tblWishlist (user_id, product_id) VALUES (?, ?)");
mysqli_stmt_bind_param($stmt, 'ii', $user_id, $product_id);

if (mysqli_stmt_execute($stmt)) {
    $_SESSION['success'] = ['Added to wishlist!'];
} else {
    $_SESSION['errors'] = ['Failed to add to wishlist'];
}
mysqli_stmt_close($stmt);

// Redirect back to product page
redirect(BASE_URL . 'products/view.php?id=' . $product_id);
?>