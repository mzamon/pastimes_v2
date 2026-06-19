<?php
/**
 * admin/dashboard.php
 * Stats dashboard (users, listings, orders, revenue, recent tables)
 */
$pageTitle = 'Admin Dashboard';
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/functions.php';

requireAdmin();

// Get statistics
$stats = [];

// User counts
$result = mysqli_query($conn, "SELECT 
    COUNT(*) as total,
    SUM(CASE WHEN role = 'buyer' THEN 1 ELSE 0 END) as buyers,
    SUM(CASE WHEN role = 'seller' THEN 1 ELSE 0 END) as sellers,
    SUM(CASE WHEN is_verified = 0 THEN 1 ELSE 0 END) as unverified
FROM tblUser");
$stats['users'] = mysqli_fetch_assoc($result);

// Product counts
$result = mysqli_query($conn, "SELECT 
    COUNT(*) as total,
    SUM(CASE WHEN status = 'active' THEN 1 ELSE 0 END) as active,
    SUM(CASE WHEN status = 'sold' THEN 1 ELSE 0 END) as sold
FROM tblProducts");
$stats['products'] = mysqli_fetch_assoc($result);

// Order counts
$result = mysqli_query($conn, "SELECT 
    COUNT(*) as total,
    SUM(total) as revenue
FROM tblOrders");
$stats['orders'] = mysqli_fetch_assoc($result);

// Seller requests pending
$result = mysqli_query($conn, "SELECT COUNT(*) as pending FROM tblSellerRequests WHERE status = 'pending'");
$stats['seller_requests'] = mysqli_fetch_assoc($result);

// Recent users
$result = mysqli_query($conn, "SELECT id, name, email, role, is_verified, created_at FROM tblUser ORDER BY created_at DESC LIMIT 5");
$recent_users = mysqli_fetch_all($result, MYSQLI_ASSOC);

// Recent products
$result = mysqli_query($conn, "SELECT p.id, p.title, u.name as seller_name, p.price, p.created_at, p.status FROM tblProducts p JOIN tblUser u ON p.seller_id = u.id ORDER BY p.created_at DESC LIMIT 5");
$recent_products = mysqli_fetch_all($result, MYSQLI_ASSOC);

// Recent orders
$result = mysqli_query($conn, "SELECT o.id, o.total, o.status, o.created_at, u.name as buyer_name FROM tblOrders o JOIN tblUser u ON o.buyer_id = u.id ORDER BY o.created_at DESC LIMIT 5");
$recent_orders = mysqli_fetch_all($result, MYSQLI_ASSOC);

require_once __DIR__ . '/../includes/header.php';
?>

<h1 class="page-title">Admin Dashboard</h1>

<div class="dashboard-grid">
    <div class="stat-card">
        <h3>Total Users</h3>
        <div class="stat-number"><?php echo (int)$stats['users']['total']; ?></div>
        <p><?php echo (int)$stats['users']['buyers']; ?> buyers, <?php echo (int)$stats['users']['sellers']; ?> sellers</p>
        <small><?php echo (int)$stats['users']['unverified']; ?> unverified</small>
    </div>

    <div class="stat-card">
        <h3>Products Listed</h3>
        <div class="stat-number"><?php echo (int)$stats['products']['total']; ?></div>
        <p><?php echo (int)$stats['products']['active']; ?> active, <?php echo (int)$stats['products']['sold']; ?> sold</p>
    </div>

    <div class="stat-card">
        <h3>Total Orders</h3>
        <div class="stat-number"><?php echo (int)$stats['orders']['total']; ?></div>
        <p>Revenue: R<?php echo number_format((float)($stats['orders']['revenue'] ?? 0), 2); ?></p>
    </div>

    <div class="stat-card">
        <h3>Seller Requests</h3>
        <div class="stat-number warning"><?php echo (int)$stats['seller_requests']['pending']; ?></div>
        <p><a href="<?php echo BASE_URL; ?>admin/verify_users.php">Review pending requests</a></p>
    </div>
</div>

<div class="dashboard-section">
    <h2>Recent Users</h2>
    <div class="table-wrap">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Verified</th>
                    <th>Joined</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($recent_users as $u): ?>
                    <tr>
                        <td><?php echo h($u['name']); ?></td>
                        <td><?php echo h($u['email']); ?></td>
                        <td><span class="badge role-<?php echo strtolower($u['role']); ?>"><?php echo h($u['role']); ?></span></td>
                        <td>
                            <?php if ((int)$u['is_verified'] === 1): ?>
                                <span class="badge verified">✓ Verified</span>
                            <?php else: ?>
                                <span class="badge pending">Pending</span>
                            <?php endif; ?>
                        </td>
                        <td><?php echo date('M d, Y', strtotime($u['created_at'])); ?></td>
                        <td><a href="<?php echo BASE_URL; ?>admin/edit_user.php?id=<?php echo $u['id']; ?>" class="btn btn-sm btn-primary">Edit</a></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <p class="text-center"><a href="<?php echo BASE_URL; ?>admin/users.php">View all users</a></p>
</div>

<div class="dashboard-section">
    <h2>Recent Products</h2>
    <div class="table-wrap">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Title</th>
                    <th>Seller</th>
                    <th>Price</th>
                    <th>Status</th>
                    <th>Listed</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($recent_products as $p): ?>
                    <tr>
                        <td><?php echo h(substr($p['title'], 0, 40)); ?></td>
                        <td><?php echo h($p['seller_name']); ?></td>
                        <td>R<?php echo number_format($p['price'], 2); ?></td>
                        <td><span class="badge status-<?php echo strtolower($p['status']); ?>"><?php echo h($p['status']); ?></span></td>
                        <td><?php echo date('M d, Y', strtotime($p['created_at'])); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <p class="text-center"><a href="<?php echo BASE_URL; ?>admin/products.php">View all products</a></p>
</div>

<div class="dashboard-section">
    <h2>Recent Orders</h2>
    <div class="table-wrap">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Order ID</th>
                    <th>Buyer</th>
                    <th>Total</th>
                    <th>Status</th>
                    <th>Date</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($recent_orders as $o): ?>
                    <tr>
                        <td>#<?php echo $o['id']; ?></td>
                        <td><?php echo h($o['buyer_name']); ?></td>
                        <td>R<?php echo number_format($o['total'], 2); ?></td>
                        <td><span class="badge status-<?php echo strtolower($o['status']); ?>"><?php echo h($o['status']); ?></span></td>
                        <td><?php echo date('M d, Y', strtotime($o['created_at'])); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <p class="text-center"><a href="<?php echo BASE_URL; ?>admin/orders.php">View all orders</a></p>
</div>

<style>
.dashboard-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 20px;
    margin-bottom: 40px;
}

.stat-card {
    background: var(--surface);
    border: 1px solid var(--border);
    border-radius: 8px;
    padding: 20px;
    text-align: center;
}

.stat-card h3 {
    margin: 0 0 10px 0;
    font-size: 0.9em;
    text-transform: uppercase;
    color: var(--text-muted);
}

.stat-number {
    font-size: 2.5em;
    font-weight: bold;
    color: var(--primary-red);
    margin: 10px 0;
}

.stat-number.warning {
    color: #f59e0b;
}

.stat-card p {
    margin: 8px 0;
    font-size: 0.9em;
}

.stat-card small {
    color: var(--text-muted);
}

.dashboard-section {
    background: var(--surface);
    border: 1px solid var(--border);
    border-radius: 8px;
    padding: 20px;
    margin-bottom: 20px;
}

.dashboard-section h2 {
    margin-top: 0;
    border-bottom: 2px solid var(--primary-red);
    padding-bottom: 10px;
}

.text-center {
    text-align: center;
}

@media (max-width: 768px) {
    .dashboard-grid {
        grid-template-columns: 1fr;
    }
    
    .stat-number {
        font-size: 2em;
    }
}
</style>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
