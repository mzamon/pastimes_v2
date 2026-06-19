<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/functions.php';

requireLogin();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $cart = new ShoppingCart($conn, (int)$_SESSION['user_id']);
    $cart->UpdateQuantity(intval($_POST['id'] ?? 0), intval($_POST['quantity'] ?? 0));
}

redirect(BASE_URL . 'cart/index.php');
?>
