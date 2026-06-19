<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/functions.php';

requireLogin();

$id = intval($_GET['id'] ?? 0);
if ($id <= 0) redirect(BASE_URL . 'products/index.php');

$chk = mysqli_prepare($conn, "SELECT seller_id FROM tblProducts WHERE id = ?");
mysqli_stmt_bind_param($chk, 'i', $id);
mysqli_stmt_execute($chk);
mysqli_stmt_bind_result($chk, $ownerId);
mysqli_stmt_fetch($chk);
mysqli_stmt_close($chk);

if ($ownerId == ($_SESSION['user_id'] ?? 0)) {
    redirect(BASE_URL . 'products/view.php?id=' . $id);
}

$cart = new ShoppingCart($conn, (int)$_SESSION['user_id']);
$cart->AddItem($id, 1);

redirect(BASE_URL . 'cart/index.php');
?>
