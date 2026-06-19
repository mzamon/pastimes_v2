<?php
$pageTitle = 'Order Confirmed';
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/functions.php';

requireLogin();

$id = intval($_GET['id'] ?? 0);
if ($id <= 0) redirect(BASE_URL . 'index.php');

$stmt = mysqli_prepare($conn, "SELECT * FROM tblOrders WHERE id = ? AND buyer_id = ?");
mysqli_stmt_bind_param($stmt, 'ii', $id, $_SESSION['user_id']);
mysqli_stmt_execute($stmt);
$order = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
mysqli_stmt_close($stmt);

if (!$order) redirect(BASE_URL . 'index.php');

$orderRef = 'ORD-' . str_pad($order['id'], 6, '0', STR_PAD_LEFT);

require_once __DIR__ . '/../includes/header.php';
?>
<div class="confirm-box">
    <div class="confirm-icon">&#10003;</div>
    <h1 style="font-size:1.5rem; margin-bottom:0.5rem;">Order Placed!</h1>
    <p class="text-muted" style="margin-bottom:1.5rem;">Thank you for your purchase.</p>
    <div style="text-align:left; border-top:1px solid var(--border); padding-top:1rem;">
        <p style="margin-bottom:0.4rem;"><strong>Order Reference:</strong> <?php echo h($orderRef); ?></p>
        <p style="margin-bottom:0.4rem;" class="text-muted"><strong>Session ID:</strong> <?php echo h(session_id()); ?></p>
        <p style="margin-bottom:0.4rem;"><strong>Total:</strong> R <?php echo number_format($order['total'], 2); ?></p>
        <p style="margin-bottom:0.4rem;">Status: <?php echo statusBadge($order['status']); ?></p>
        <p style="margin-bottom:0.4rem;" class="text-muted"><strong>Deliver to:</strong> <?php echo h($order['delivery_address']); ?></p>
    </div>
    <div style="display:flex; flex-direction:column; gap:0.6rem; margin-top:1.5rem;">
        <a href="<?php echo BASE_URL; ?>orders/track.php" class="btn btn-primary">View My Orders</a>
        <a href="<?php echo BASE_URL; ?>products/index.php" class="btn btn-secondary">Continue Shopping</a>
    </div>
</div>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
