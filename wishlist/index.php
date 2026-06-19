<?php
/**
 * wishlist/index.php
 * Display wishlist items in product grid with brand
 */
$pageTitle = 'Wishlist';
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/functions.php';

requireLogin();

$user_id = $_SESSION['user_id'];

// Fetch wishlist items – p.* includes brand column
$sql = "SELECT p.*, c.name AS category_name, u.name AS seller_name, w.id AS wishlist_id
        FROM tblWishlist w
        JOIN tblProducts p ON w.product_id = p.id
        JOIN categories c  ON p.category_id = c.id
        JOIN tblUser u     ON p.seller_id = u.id
        WHERE w.user_id = ? AND p.status = 'active'
        ORDER BY w.added_at DESC";

$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, 'i', $user_id);
mysqli_stmt_execute($stmt);
$products = mysqli_fetch_all(mysqli_stmt_get_result($stmt), MYSQLI_ASSOC);
mysqli_stmt_close($stmt);

require_once __DIR__ . '/../includes/header.php';
?>

<h1 class="page-title">My Wishlist</h1>

<?php if (empty($products)): ?>
    <div class="alert alert-info">
        Your wishlist is empty. <a href="<?php echo BASE_URL; ?>products/index.php">Browse products</a>
    </div>
<?php else: ?>
    <div class="product-grid">
        <?php foreach ($products as $p): ?>
            <div class="product-card">
                <a href="<?php echo BASE_URL; ?>products/view.php?id=<?php echo $p['id']; ?>" class="product-image">
                    <img src="<?php echo h(getProductImage($p['image'])); ?>" alt="<?php echo h($p['title']); ?>">
                    <span class="condition-badge <?php echo strtolower($p['condition']); ?>">
                        <?php echo h($p['condition']); ?>
                    </span>
                </a>
                <div class="product-info">
                    <h3><?php echo h($p['title']); ?></h3>
                    <?php if (!empty($p['brand'])): ?>
                        <p class="brand"><strong>Brand:</strong> <?php echo h($p['brand']); ?></p>
                    <?php endif; ?>
                    <p class="category"><?php echo h($p['category_name']); ?></p>
                    <p class="seller">by <?php echo h($p['seller_name']); ?></p>
                    <p class="price">R<?php echo number_format($p['price'], 2); ?></p>
                    <div class="product-actions">
                        <a href="<?php echo BASE_URL; ?>products/view.php?id=<?php echo $p['id']; ?>" class="btn btn-secondary">View</a>
                        <form method="POST" action="<?php echo BASE_URL; ?>wishlist/remove.php" style="flex:1;">
                            <input type="hidden" name="product_id" value="<?php echo $p['id']; ?>">
                            <button type="submit" class="btn btn-danger" style="width:100%;">Remove</button>
                        </form>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>