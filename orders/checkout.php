<?php
$pageTitle = 'Checkout';
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/functions.php';

requireLogin();

$cartObj = new ShoppingCart($conn, (int)$_SESSION['user_id']);
$cart    = $cartObj->GetItems();
if (empty($cart)) redirect(BASE_URL . 'products/index.php');

$errors   = [];
$subtotal = $cartObj->GetSubtotal();
$total    = $subtotal + 50.00;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $address        = sanitize($_POST['address']        ?? '');
    $city           = sanitize($_POST['city']           ?? '');
    $postal         = sanitize($_POST['postal']         ?? '');
    $payment_method = sanitize($_POST['payment_method'] ?? '');

    if (empty($address))                                            $errors[] = 'Address is required.';
    if (empty($city))                                               $errors[] = 'City is required.';
    if (empty($postal))                                             $errors[] = 'Postal code is required.';
    if (!in_array($payment_method, ['Credit Card', 'Debit Card'])) $errors[] = 'Select payment method.';

    if (empty($errors)) {
        try {
            $result = $cartObj->Checkout([
                'address'        => $address,
                'city'           => $city,
                'postal'         => $postal,
                'payment_method' => $payment_method,
            ]);
            redirect(BASE_URL . 'orders/confirm.php?id=' . $result['order_id']);
        } catch (Exception $e) {
            $errors[] = $e->getMessage();
        }
    }
}

require_once __DIR__ . '/../includes/header.php';
?>
<h1 class="page-title">Checkout</h1>
<?php foreach ($errors as $e) echo displayError($e); ?>
<div class="cart-layout">
    <div>
        <h2 class="section-title">Delivery Information</h2>
        <div class="card" style="padding:1.5rem;">
            <form method="POST" action="">
                <div class="form-group">
                    <label for="address">Street Address</label>
                    <textarea id="address" name="address" class="form-control" rows="2" required><?php echo h($_POST['address'] ?? ''); ?></textarea>
                </div>
                <div class="form-row cols-2">
                    <div class="form-group">
                        <label for="city">City</label>
                        <input type="text" id="city" name="city" class="form-control" required value="<?php echo h($_POST['city'] ?? ''); ?>">
                    </div>
                    <div class="form-group">
                        <label for="postal">Postal Code</label>
                        <input type="text" id="postal" name="postal" class="form-control" required value="<?php echo h($_POST['postal'] ?? ''); ?>">
                    </div>
                </div>
                <div class="form-group">
                    <label for="payment_method">Payment Method</label>
                    <select id="payment_method" name="payment_method" class="form-control" required>
                        <option value="">Select…</option>
                        <option value="Credit Card" <?php echo ($_POST['payment_method'] ?? '') === 'Credit Card' ? 'selected' : ''; ?>>Credit Card</option>
                        <option value="Debit Card"  <?php echo ($_POST['payment_method'] ?? '') === 'Debit Card'  ? 'selected' : ''; ?>>Debit Card</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary btn-full btn-lg">Place Order</button>
            </form>
        </div>
    </div>
    <div class="cart-summary">
        <h3>Order Summary</h3>
        <?php foreach ($cart as $item): ?>
            <div class="summary-row">
                <span><?php echo h($item['title']); ?> ×<?php echo $item['quantity']; ?></span>
                <span>R <?php echo number_format($item['price'] * $item['quantity'], 2); ?></span>
            </div>
        <?php endforeach; ?>
        <div class="summary-row"><span>Subtotal</span><span>R <?php echo number_format($subtotal, 2); ?></span></div>
        <div class="summary-row"><span>Delivery</span><span>R 50.00</span></div>
        <div class="summary-total"><span>Total</span><span>R <?php echo number_format($total, 2); ?></span></div>
    </div>
</div>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
