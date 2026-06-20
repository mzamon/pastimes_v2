<?php
/**
 * wishlist/remove.php
 * Remove from wishlist (accepts GET and POST)
 */
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/functions.php';

requireLogin();

// Get product ID from GET or POST
$product_id = intval($_GET['id'] ?? $_POST['product_id'] ?? 0);
$user_id    = $_SESSION['user_id'];

if ($product_id <= 0) {
    $_SESSION['errors'] = ['Invalid product'];
    redirect(BASE_URL . 'wishlist/index.php');
}

// Remove from wishlist
$stmt = mysqli_prepare($conn, "DELETE FROM tblWishlist WHERE user_id = ? AND product_id = ?");
mysqli_stmt_bind_param($stmt, 'ii', $user_id, $product_id);

if (mysqli_stmt_execute($stmt)) {
    $_SESSION['success'] = ['Removed from wishlist'];
} else {
    $_SESSION['errors'] = ['Failed to remove from wishlist'];
}
mysqli_stmt_close($stmt);

// Redirect back
$back = ($_GET['back'] ?? '') === 'product' ? 'products/view.php?id=' . $product_id : 'wishlist/index.php';
redirect(BASE_URL . $back);
?>