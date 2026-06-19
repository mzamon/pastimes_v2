<?php
/**
 * index.php
 * Homepage with hero section, categories, featured listings
 */
$pageTitle = 'Home';
require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/includes/functions.php';

// Get categories
$categories = mysqli_fetch_all(mysqli_query($conn, "SELECT * FROM categories ORDER BY name LIMIT 8"), MYSQLI_ASSOC);

// Get featured products (newest active)
$featured = mysqli_fetch_all(mysqli_query($conn, "
    SELECT p.*, c.name AS category_name, u.name AS seller_name
    FROM tblProducts p
    JOIN categories c ON p.category_id = c.id
    JOIN tblUser u ON p.seller_id = u.id
    WHERE p.status = 'active'
    ORDER BY p.created_at DESC
    LIMIT 12
"), MYSQLI_ASSOC);

// Get stats
$stats = [];
$result = mysqli_query($conn, "SELECT COUNT(*) as count FROM tblProducts WHERE status = 'active'");
$stats['products'] = mysqli_fetch_assoc($result)['count'];

$result = mysqli_query($conn, "SELECT COUNT(*) as count FROM tblUser WHERE role = 'seller' AND is_verified = 1");
$stats['sellers'] = mysqli_fetch_assoc($result)['count'];

$result = mysqli_query($conn, "SELECT COUNT(*) as count FROM tblOrders");
$stats['orders'] = mysqli_fetch_assoc($result)['count'];

require_once __DIR__ . '/includes/header.php';
?>

<!-- Hero Section -->
<section class="hero">
    <div class="hero-content">
        <h1>Pastimes</h1>
        <p>Discover authentic vintage and pre-loved clothing</p>
        <div class="hero-actions">
            <a href="<?php echo BASE_URL; ?>products/index.php" class="btn btn-primary btn-lg">Browse Now</a>

            <?php if (isLoggedIn()): ?>
                <!-- Logged In: Show Logout -->
                <a href="<?php echo BASE_URL; ?>auth/logout.php" class="btn btn-outline btn-lg">Logout</a>
                <?php if (isBuyer()): ?>
                    <a href="<?php echo BASE_URL; ?>auth/request_seller.php" class="btn btn-outline btn-lg">Sell With Us</a>
                <?php endif; ?>
            <?php else: ?>
                <!-- Not Logged In: Show Login & Register -->
                <a href="<?php echo BASE_URL; ?>auth/login.php" class="btn btn-outline btn-lg">Sign In</a>
                <a href="<?php echo BASE_URL; ?>auth/register.php" class="btn btn-outline btn-lg">Register</a>
            <?php endif; ?>
        </div>
    </div>
</section>

<!-- Stats Section -->
<section class="stats-section container">
    <div class="stats-grid">
        <div class="stat">
            <div class="stat-number"><?php echo number_format($stats['products']); ?></div>
            <div class="stat-label">Items Listed</div>
        </div>
        <div class="stat">
            <div class="stat-number"><?php echo number_format($stats['sellers']); ?></div>
            <div class="stat-label">Sellers</div>
        </div>
        <div class="stat">
            <div class="stat-number"><?php echo number_format($stats['orders']); ?></div>
            <div class="stat-label">Orders</div>
        </div>
    </div>
</section>

<!-- Categories Section -->
<section class="container">
    <h2 class="section-title">Shop by Category</h2>
    <div class="category-grid">
        <?php foreach ($categories as $cat): ?>
            <a href="<?php echo BASE_URL; ?>products/index.php?category=<?php echo $cat['id']; ?>" class="category-card">
                <div class="category-icon">📦</div>
                <h3><?php echo h($cat['name']); ?></h3>
            </a>
        <?php endforeach; ?>
        <a href="<?php echo BASE_URL; ?>products/index.php" class="category-card all">
            <div class="category-icon">🔍</div>
            <h3>See All</h3>
        </a>
    </div>
</section>

<!-- Featured Products Section -->
<section class="container">
    <h2 class="section-title">Featured Listings</h2>
    <?php if (empty($featured)): ?>
        <div class="alert alert-info">No products available yet. Check back soon!</div>
    <?php else: ?>
        <div class="product-grid">
            <?php foreach ($featured as $p): ?>
                <div class="product-card">
                    <a href="<?php echo BASE_URL; ?>products/view.php?id=<?php echo $p['id']; ?>" class="product-image">
                        <img src="<?php echo h(getProductImage($p['image'])); ?>" alt="<?php echo h($p['title']); ?>">
                        <span class="condition-badge <?php echo strtolower($p['condition']); ?>">
                            <?php echo h($p['condition']); ?>
                        </span>
                    </a>
                    <div class="product-info">
                        <h3><?php echo h($p['title']); ?></h3>
                        <p class="category"><?php echo h($p['category_name']); ?></p>
                        <p class="seller">by <?php echo h($p['seller_name']); ?></p>
                        <p class="price">R<?php echo number_format($p['price'], 2); ?></p>
                        <div class="product-actions">
                            <a href="<?php echo BASE_URL; ?>products/view.php?id=<?php echo $p['id']; ?>" class="btn btn-secondary">View</a>
                            <?php if (isLoggedIn()): ?>
                                <form method="POST" action="<?php echo BASE_URL; ?>wishlist/add.php">
                                    <input type="hidden" name="product_id" value="<?php echo $p['id']; ?>">
                                    <button type="submit" class="btn btn-outline" title="Add to wishlist">❤</button>
                                </form>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</section>

<!-- CTA Section -->
<section class="cta-section">
    <div class="container cta-content">
        <h2>Ready to Sell?</h2>
        <p>Join our community of sellers and start earning</p>
        <?php if (isLoggedIn() && isBuyer()): ?>
            <a href="<?php echo BASE_URL; ?>auth/request_seller.php" class="btn btn-primary btn-lg">Become a Seller</a>
        <?php elseif (!isLoggedIn()): ?>
            <a href="<?php echo BASE_URL; ?>auth/register.php" class="btn btn-primary btn-lg">Get Started</a>
        <?php endif; ?>
    </div>
</section>

<style>
.hero {
    background: linear-gradient(135deg, var(--primary) 0%, #8b0000 100%);
    color: white;
    padding: 60px 20px;
    text-align: center;
    margin-bottom: 40px;
}

.hero-content h1 {
    font-size: 3em;
    margin: 0 0 10px 0;
    font-weight: 900;
    text-transform: uppercase;
    letter-spacing: 2px;
}

.hero-content p {
    font-size: 1.2em;
    margin-bottom: 30px;
    opacity: 0.95;
}

.hero-actions {
    display: flex;
    gap: 20px;
    justify-content: center;
    flex-wrap: wrap;
}

.btn-lg {
    padding: 14px 40px;
    font-size: 1.1em;
}

.btn-outline {
    background: transparent;
    border: 2px solid white;
    color: white;
}

.btn-outline:hover {
    background: white;
    color: var(--primary);
}

.stats-section {
    margin-bottom: 60px;
}

.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 30px;
    text-align: center;
}

.stat {
    background: var(--surface);
    padding: 30px;
    border-radius: 8px;
    border: 1px solid var(--border);
}

.stat-number {
    font-size: 2.5em;
    font-weight: bold;
    color: var(--primary);
    margin-bottom: 10px;
}

.stat-label {
    font-size: 0.95em;
    color: var(--text-muted);
    text-transform: uppercase;
}

.section-title {
    font-size: 2em;
    margin: 60px 0 30px 0;
    text-align: center;
    border-bottom: 3px solid var(--primary);
    padding-bottom: 15px;
}

.category-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
    gap: 20px;
    margin-bottom: 60px;
}

.category-card {
    background: var(--surface);
    border: 2px solid var(--border);
    border-radius: 8px;
    padding: 20px;
    text-align: center;
    text-decoration: none;
    color: var(--text);
    transition: all 0.3s;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    min-height: 140px;
}

.category-card:hover {
    border-color: var(--primary);
    background: var(--bg);
    transform: translateY(-4px);
}

.category-card.all {
    border-color: var(--primary);
    background: var(--primary);
    color: white;
}

.category-icon {
    font-size: 2.5em;
    margin-bottom: 10px;
}

.category-card h3 {
    margin: 0;
    font-size: 1em;
}

.cta-section {
    background: linear-gradient(135deg, var(--primary) 0%, #8b0000 100%);
    color: white;
    padding: 60px 20px;
    margin-top: 60px;
}

.cta-content {
    text-align: center;
}

.cta-content h2 {
    font-size: 2.5em;
    margin-bottom: 15px;
}

.cta-content p {
    font-size: 1.1em;
    margin-bottom: 30px;
    opacity: 0.95;
}

@media (max-width: 768px) {
    .hero-content h1 {
        font-size: 2em;
    }

    .hero-content p {
        font-size: 1em;
    }

    .hero-actions {
        flex-direction: column;
    }

    .stats-grid {
        grid-template-columns: 1fr;
    }

    .section-title {
        font-size: 1.5em;
    }

    .category-grid {
        grid-template-columns: repeat(auto-fill, minmax(120px, 1fr));
    }

    .cta-content h2 {
        font-size: 1.8em;
    }
}
</style>

<?php require_once __DIR__ . '/includes/footer.php'; ?>