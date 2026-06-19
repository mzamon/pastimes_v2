<?php
$pageTitle = 'Become a Seller';
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/functions.php';

requireLogin();

$errors  = [];
$success = '';
$post    = [];

$stmt = mysqli_prepare($conn,
    "SELECT u.seller_request FROM tblUser u WHERE u.id = ? LIMIT 1");
mysqli_stmt_bind_param($stmt, 'i', $_SESSION['user_id']);
mysqli_stmt_execute($stmt);
$current = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
mysqli_stmt_close($stmt);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $post['reason'] = sanitize($_POST['reason'] ?? '');
    if (empty($post['reason'])) $errors[] = 'Please describe what you want to sell.';

    if (empty($errors)) {
        $upd = mysqli_prepare($conn,
            "UPDATE tblUser SET seller_request = 'pending' WHERE id = ?");
        mysqli_stmt_bind_param($upd, 'i', $_SESSION['user_id']);
        mysqli_stmt_execute($upd);
        mysqli_stmt_close($upd);

        $_SESSION['seller_request'] = 'pending';
        $success = 'Your seller request has been submitted and is awaiting admin approval.';
        $current['seller_request'] = 'pending';
    }
}

require_once __DIR__ . '/../includes/header.php';
?>
<h1 class="page-title">Become a Seller</h1>
<div class="form-wrap">
    <div class="mb-2">Current status: <?php echo sellerRequestBadge($current['seller_request'] ?? 'none'); ?></div>
    <?php foreach ($errors as $e) echo displayError($e); ?>
    <?php if ($success) echo displaySuccess($success); ?>
    <form method="POST" action="">
        <div class="form-group">
            <label for="reason">What would you like to sell?</label>
            <textarea id="reason" name="reason" class="form-control" rows="4" required
                      placeholder="e.g. vintage denim, branded sneakers, designer handbags"><?php echo h($post['reason'] ?? ''); ?></textarea>
        </div>
        <button type="submit" class="btn btn-primary btn-full">Submit Request</button>
    </form>
</div>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
