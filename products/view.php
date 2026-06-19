<?php
$pageTitle = 'Product';
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/functions.php';

$id = intval($_GET['id'] ?? 0);
if ($id <= 0) redirect(BASE_URL . 'products/index.php');

$stmt = mysqli_prepare($conn,
    "SELECT p.*, c.name AS category_name, u.name AS seller_name, u.id AS seller_id
     FROM tblProducts p
     JOIN categories c ON p.category_id = c.id
     JOIN tblUser u    ON p.seller_id = u.id
     WHERE p.id = ?");
mysqli_stmt_bind_param($stmt, 'i', $id);
mysqli_stmt_execute($stmt);
$product = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
mysqli_stmt_close($stmt);

if (!$product) redirect(BASE_URL . 'products/index.php');

$pageTitle = $product['title'];
$isOwner   = isLoggedIn() && ($_SESSION['user_id'] ?? 0) == $product['seller_id'];

$inWishlist = false;
if (isLoggedIn() && !$isOwner) {
    $wst = mysqli_prepare($conn, "SELECT 1 FROM tblWishlist WHERE user_id = ? AND product_id = ?");
    mysqli_stmt_bind_param($wst, 'ii', $_SESSION['user_id'], $id);
    mysqli_stmt_execute($wst);
    mysqli_stmt_store_result($wst);
    $inWishlist = mysqli_stmt_num_rows($wst) > 0;
    mysqli_stmt_close($wst);
}

require_once __DIR__ . '/../includes/header.php';
?>
<div class="product-detail">
    <div>
        <img src="<?php echo getProductImage($product['image']); ?>"
             alt="<?php echo h($product['title']); ?>" class="product-detail-img">
    </div>
    <div class="product-info">
        <p class="product-meta"><?php echo h($product['category_name']); ?> · <?php echo h($product['condition']); ?></p>
        <h1><?php echo h($product['title']); ?></h1>
        <div class="product-price">R <?php echo number_format($product['price'], 2); ?></div>
        <p class="product-meta">In stock: <strong><?php echo (int)$product['quantity']; ?></strong></p>
        <p style="margin-bottom:1rem;"><?php echo nl2br(h($product['description'])); ?></p>

        <div class="product-actions">
            <?php if ($product['status'] === 'sold'): ?>
                <span class="btn btn-secondary" style="cursor:default; opacity:0.6;">Sold Out</span>
            <?php elseif ($isOwner): ?>
                <a href="<?php echo BASE_URL; ?>products/edit.php?id=<?php echo $product['id']; ?>" class="btn btn-secondary">Edit Listing</a>
                <a href="<?php echo BASE_URL; ?>products/delete.php?id=<?php echo $product['id']; ?>" class="btn btn-danger">Delete</a>
            <?php else: ?>
                <a href="<?php echo BASE_URL; ?>cart/add.php?id=<?php echo $product['id']; ?>" class="btn btn-primary btn-lg">Add to Cart</a>
                <?php if (isLoggedIn()): ?>
                    <?php if ($inWishlist): ?>
                        <a href="<?php echo BASE_URL; ?>wishlist/remove.php?id=<?php echo $product['id']; ?>&back=product" class="btn btn-secondary">♥ Remove from Wishlist</a>
                    <?php else: ?>
                        <a href="<?php echo BASE_URL; ?>wishlist/add.php?id=<?php echo $product['id']; ?>" class="btn btn-secondary">♡ Add to Wishlist</a>
                    <?php endif; ?>
                <?php endif; ?>
            <?php endif; ?>
        </div>
        <p class="text-muted" style="margin-top:1rem;">Sold by <strong><?php echo h($product['seller_name']); ?></strong></p>
        <p style="margin-top:0.5rem;"><a href="<?php echo BASE_URL; ?>products/index.php">← Continue shopping</a></p>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
