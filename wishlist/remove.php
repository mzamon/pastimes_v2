<?php
/**
 * wishlist/remove.php
 * Remove from wishlist
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

// Remove from wishlist
$stmt = mysqli_prepare($conn, "DELETE FROM tblWishlist WHERE user_id = ? AND product_id = ?");
mysqli_stmt_bind_param($stmt, 'ii', $user_id, $product_id);

if (mysqli_stmt_execute($stmt)) {
    $_SESSION['success'] = ['Removed from wishlist'];
    http_response_code(200);
    echo json_encode(['status' => 'success', 'message' => 'Removed from wishlist']);
} else {
    $_SESSION['errors'] = ['Failed to remove from wishlist'];
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Failed to remove']);
}
mysqli_stmt_close($stmt);
?>
