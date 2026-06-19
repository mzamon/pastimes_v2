<?php
/**
 * admin/orders.php
 * Admin view all orders with status updates
 */
$pageTitle = 'Manage Orders';
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/functions.php';

requireAdmin();

$status_filter = sanitize($_GET['status'] ?? '');
$errors = [];

// Handle status update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $order_id = intval($_POST['order_id'] ?? 0);
    $new_status = sanitize($_POST['status'] ?? '');

    if (!in_array($new_status, ['Pending', 'Packed', 'In Transit', 'Delivered'])) {
        $errors[] = 'Invalid status';
    } else {
        $stmt = mysqli_prepare($conn, "UPDATE tblOrders SET status = ? WHERE id = ?");
        mysqli_stmt_bind_param($stmt, 'si', $new_status, $order_id);

        if (mysqli_stmt_execute($stmt)) {
            $_SESSION['success'] = ['Order status updated'];
            redirect(BASE_URL . 'admin/orders.php' . (!empty($status_filter) ? '?status=' . $status_filter : ''));
        } else {
            $errors[] = 'Failed to update order';
        }
        mysqli_stmt_close($stmt);
    }
}

// Fetch orders
$sql = "SELECT o.*, u.name AS buyer_name, u.email AS buyer_email,
               COUNT(oi.id) AS item_count
        FROM tblOrders o
        JOIN tblUser u ON o.buyer_id = u.id
        LEFT JOIN order_items oi ON o.id = oi.order_id
        WHERE 1=1";

$params = [];
$types = '';

if (!empty($status_filter)) {
    $sql .= " AND o.status = ?";
    $params[] = $status_filter;
    $types .= 's';
}

$sql .= " GROUP BY o.id ORDER BY o.created_at DESC";

$stmt = mysqli_prepare($conn, $sql);
if (!empty($params)) mysqli_stmt_bind_param($stmt, $types, ...$params);
mysqli_stmt_execute($stmt);
$orders = mysqli_fetch_all(mysqli_stmt_get_result($stmt), MYSQLI_ASSOC);
mysqli_stmt_close($stmt);

require_once __DIR__ . '/../includes/header.php';

foreach ($errors as $e) echo displayError($e);
$success = $_SESSION['success'] ?? [];
unset($_SESSION['success']);
foreach ($success as $s) echo displaySuccess($s);
?>

<h1 class="page-title">Manage Orders</h1>

<div class="filter-bar">
    <form method="GET" action="" style="display:flex; gap:10px;">
        <select name="status" class="form-control">
            <option value="">All Status</option>
            <option value="Pending" <?php echo $status_filter === 'Pending' ? 'selected' : ''; ?>>Pending</option>
            <option value="Packed" <?php echo $status_filter === 'Packed' ? 'selected' : ''; ?>>Packed</option>
            <option value="In Transit" <?php echo $status_filter === 'In Transit' ? 'selected' : ''; ?>>In Transit</option>
            <option value="Delivered" <?php echo $status_filter === 'Delivered' ? 'selected' : ''; ?>>Delivered</option>
        </select>
        <button type="submit" class="btn btn-primary">Filter</button>
        <?php if (!empty($status_filter)): ?>
            <a href="<?php echo BASE_URL; ?>admin/orders.php" class="btn btn-secondary">Clear</a>
        <?php endif; ?>
    </form>
</div>

<?php if (empty($orders)): ?>
    <div class="alert alert-info">No orders found.</div>
<?php else: ?>
    <div class="table-wrap">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Order ID</th>
                    <th>Buyer</th>
                    <th>Items</th>
                    <th>Total</th>
                    <th>Status</th>
                    <th>Date</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($orders as $o): ?>
                    <tr>
                        <td><strong>#<?php echo $o['id']; ?></strong></td>
                        <td><?php echo h($o['buyer_name']); ?></td>
                        <td><?php echo (int)$o['item_count']; ?></td>
                        <td>R<?php echo number_format($o['total'], 2); ?></td>
                        <td><span class="badge status-<?php echo strtolower($o['status']); ?>"><?php echo h($o['status']); ?></span></td>
                        <td><?php echo date('M d, Y', strtotime($o['created_at'])); ?></td>
                        <td>
                            <form method="POST" style="display:inline;">
                                <input type="hidden" name="order_id" value="<?php echo $o['id']; ?>">
                                <select name="status" class="form-control" style="width:auto; display:inline-block;">
                                    <option value="<?php echo h($o['status']); ?>" selected>— Change —</option>
                                    <option value="Pending">Pending</option>
                                    <option value="Packed">Packed</option>
                                    <option value="In Transit">In Transit</option>
                                    <option value="Delivered">Delivered</option>
                                </select>
                                <button type="submit" class="btn btn-sm btn-primary">Update</button>
                            </form>
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

.badge.status-pending {
    background: #f59e0b;
    color: white;
}

.badge.status-packed {
    background: #3b82f6;
    color: white;
}

.badge.status-in\ transit {
    background: #8b5cf6;
    color: white;
}

.badge.status-delivered {
    background: #10b981;
    color: white;
}
</style>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
