<?php
$pageTitle = 'Delete Listing';
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/functions.php';

requireSellerOrAdmin();

$id = intval($_GET['id'] ?? 0);
if ($id <= 0) redirect(BASE_URL . 'products/index.php');

$chk = mysqli_prepare($conn, "SELECT seller_id FROM tblProducts WHERE id = ?");
mysqli_stmt_bind_param($chk, 'i', $id);
mysqli_stmt_execute($chk);
mysqli_stmt_bind_result($chk, $ownerId);
mysqli_stmt_fetch($chk);
mysqli_stmt_close($chk);

if ($ownerId != ($_SESSION['user_id'] ?? 0) && !isAdmin()) {
    redirect(BASE_URL . 'products/index.php');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['confirm_delete'])) {
    $del = mysqli_prepare($conn, "DELETE FROM tblProducts WHERE id = ?");
    mysqli_stmt_bind_param($del, 'i', $id);
    mysqli_stmt_execute($del);
    mysqli_stmt_close($del);
    redirect(BASE_URL . 'products/index.php');
}

require_once __DIR__ . '/../includes/header.php';
?>
<div class="confirm-box">
    <div class="confirm-icon" style="color:var(--danger);">&#9888;</div>
    <h1 style="font-size:1.5rem; margin-bottom:0.5rem;">Delete Listing?</h1>
    <p class="text-muted" style="margin-bottom:1.5rem;">This cannot be undone.</p>
    <form method="POST" action="">
        <div style="display:flex;justify-content:center;gap:0.75rem;">
            <button type="submit" name="confirm_delete" class="btn btn-danger">Yes, Delete</button>
            <a href="<?php echo BASE_URL; ?>products/index.php" class="btn btn-secondary">Cancel</a>
        </div>
    </form>
</div>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
