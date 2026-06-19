<?php
/**
 * admin/products.php
 * Admin view/manage all product listings with search, filter, and brand column
 */
$pageTitle = 'Manage Products';
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/functions.php';

requireAdmin();

$search = sanitize($_GET['search'] ?? '');
$status_filter = sanitize($_GET['status'] ?? '');

$sql = "SELECT p.*, c.name AS category_name, u.name AS seller_name 
        FROM tblProducts p
        JOIN categories c ON p.category_id = c.id
        JOIN tblUser u ON p.seller_id = u.id
        WHERE 1=1";

$params = [];
$types = '';

if (!empty($search)) {
    $like = '%' . $search . '%';
    $sql .= " AND (p.title LIKE ? OR p.description LIKE ?)";
    $params[] = $like;
    $params[] = $like;
    $types .= 'ss';
}

if (!empty($status_filter)) {
    $sql .= " AND p.status = ?";
    $params[] = $status_filter;
    $types .= 's';
}

$sql .= " ORDER BY p.created_at DESC";

$stmt = mysqli_prepare($conn, $sql);
if (!empty($params)) mysqli_stmt_bind_param($stmt, $types, ...$params);
mysqli_stmt_execute($stmt);
$products = mysqli_fetch_all(mysqli_stmt_get_result($stmt), MYSQLI_ASSOC);
mysqli_stmt_close($stmt);

require_once __DIR__ . '/../includes/header.php';
?>

<h1 class="page-title">Manage Products</h1>

<div class="filter-bar">
    <form method="GET" action="" style="display:flex; gap:10px; flex-wrap:wrap;">
        <input type="text" name="search" placeholder="Search by title or description…" value="<?php echo h($search); ?>" class="form-control" style="flex:1; min-width:200px;">
        <select name="status" class="form-control">
            <option value="">All Status</option>
            <option value="active" <?php echo $status_filter === 'active' ? 'selected' : ''; ?>>Active</option>
            <option value="sold" <?php echo $status_filter === 'sold' ? 'selected' : ''; ?>>Sold</option>
        </select>
        <button type="submit" class="btn btn-primary">Filter</button>
        <?php if (!empty($search) || !empty($status_filter)): ?>
            <a href="<?php echo BASE_URL; ?>admin/products.php" class="btn btn-secondary">Clear</a>
        <?php endif; ?>
    </form>
</div>

<?php if (empty($products)): ?>
    <div class="alert alert-info">No products found.</div>
<?php else: ?>
    <div class="table-wrap">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Image</th>
                    <th>Title</th>
                    <th>Brand</th>
                    <th>Category</th>
                    <th>Seller</th>
                    <th>Price</th>
                    <th>Qty</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($products as $p): ?>
                    <tr>
                        <td><img src="<?php echo getProductImage($p['image']); ?>" alt="" style="width:48px;height:48px;object-fit:cover;border-radius:6px;"></td>
                        <td><?php echo h($p['title']); ?></td>
                        <td><?php echo h($p['brand'] ?? '—'); ?></td>
                        <td><?php echo h($p['category_name']); ?></td>
                        <td><?php echo h($p['seller_name']); ?></td>
                        <td>R <?php echo number_format($p['price'], 2); ?></td>
                        <td><?php echo (int)$p['quantity']; ?></td>
                        <td><span class="status-badge <?php echo $p['status'] === 'active' ? 'status-delivered' : 'status-transit'; ?>"><?php echo ucfirst($p['status']); ?></span></td>
                        <td style="display:flex; gap:0.5rem; flex-wrap:wrap;">
                            <a href="<?php echo BASE_URL; ?>products/edit.php?id=<?php echo $p['id']; ?>" class="btn btn-secondary btn-sm">Edit</a>
                            <a href="<?php echo BASE_URL; ?>products/delete.php?id=<?php echo $p['id']; ?>" class="btn btn-danger btn-sm">Delete</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php endif; ?>

<style>
.filter-bar {
    background: var(--surface);
    border: 1px solid var(--border);
    border-radius: 8px;
    padding: 15px;
    margin-bottom: 20px;
}
</style>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>