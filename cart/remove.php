<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/functions.php';

requireLogin();

$cart = new ShoppingCart($conn, (int)$_SESSION['user_id']);
$cart->RemoveItem(intval($_GET['id'] ?? 0));

redirect(BASE_URL . 'cart/index.php');
?>
