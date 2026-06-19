<?php
$pageTitle = 'Manage Orders';
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/functions.php';

requireSeller();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'])) {
    $order_id  = intval($_POST['order_id'] ?? 0);
    $status    = sanitize($_POST['status'] ?? '');
    $allowed   = ['Pending','Packed','In Transit','Delivered'];

    if ($order_id > 0 && in_array($status, $allowed)) {
        $chk = mysqli_prepare($conn,
            "SELECT o.id FROM tblOrders o
             JOIN order_items oi ON o.id = oi.order_id
             JOIN tblProducts p  ON oi.product_id = p.id
             WHERE o.id = ? AND p.seller_id = ? LIMIT 1");
        mysqli_stmt_bind_param($chk, 'ii', $order_id, $_SESSION['user_id']);
        mysqli_stmt_execute($chk);
        mysqli_stmt_store_result($chk);
        if (mysqli_stmt_num_rows($chk) > 0) {
            $upd = mysqli_prepare($conn, "UPDATE tblOrders SET status=? WHERE id=?");
            mysqli_stmt_bind_param($upd, 'si', $status, $order_id);
            mysqli_stmt_execute($upd);
            mysqli_stmt_close($upd);
        }
        mysqli_stmt_close($chk);
    }
    redirect(BASE_URL . 'orders/manage.php');
}

$stmt = mysqli_prepare($conn,
    "SELECT DISTINCT o.*, u.name AS buyer_name,
            GROUP_CONCAT(p.title SEPARATOR ', ') AS product_titles
     FROM tblOrders o
     JOIN order_items oi ON o.id = oi.order_id
     JOIN tblProducts p  ON oi.product_id = p.id
     JOIN tblUser u      ON o.buyer_id = u.id
     WHERE p.seller_id = ?
     GROUP BY o.id
     ORDER BY o.created_at DESC");
mysqli_stmt_bind_param($stmt, 'i', $_SESSION['user_id']);
mysqli_stmt_execute($stmt);
$orders = mysqli_fetch_all(mysqli_stmt_get_result($stmt), MYSQLI_ASSOC);
mysqli_stmt_close($stmt);

require_once __DIR__ . '/../includes/header.php';
?>
<h1 class="page-title">Manage Orders</h1>
<?php if (empty($orders)): ?>
    <div class="alert alert-error">No orders for your products yet.</div>
<?php else: ?>
<div class="table-wrap">
    <table class="data-table">
        <thead>
            <tr><th>Order</th><th>Buyer</th><th>Items</th><th>Total</th><th>Status</th><th>Update</th></tr>
        </thead>
        <tbody>
            <?php foreach ($orders as $o): ?>
            <tr>
                <td>#<?php echo $o['id']; ?><br><small class="text-muted"><?php echo date('d M Y', strtotime($o['created_at'])); ?></small></td>
                <td><?php echo h($o['buyer_name']); ?></td>
                <td style="max-width:160px; font-size:0.85rem;"><?php echo h($o['product_titles']); ?></td>
                <td>R <?php echo number_format($o['total'], 2); ?></td>
                <td><?php echo statusBadge($o['status']); ?></td>
                <td>
                    <form method="POST" action="" style="display:flex;flex-direction:column;gap:0.4rem;min-width:140px;">
                        <input type="hidden" name="order_id" value="<?php echo $o['id']; ?>">
                        <select name="status" class="form-control">
                            <?php foreach (['Pending','Packed','In Transit','Delivered'] as $s): ?>
                                <option value="<?php echo $s; ?>" <?php echo $o['status'] === $s ? 'selected' : ''; ?>><?php echo $s; ?></option>
                            <?php endforeach; ?>
                        </select>
                        <button type="submit" name="update_status" class="btn btn-primary btn-sm">Update</button>
                    </form>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php endif; ?>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
