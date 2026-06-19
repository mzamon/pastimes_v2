<?php
/**
 * admin/edit_user.php
 * Admin edit user role/verification status
 */
$pageTitle = 'Edit User';
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/functions.php';

requireAdmin();

$user_id = intval($_GET['id'] ?? 0);

if ($user_id <= 0) {
    redirect(BASE_URL . 'admin/users.php');
}

// Fetch user
$stmt = mysqli_prepare($conn, "SELECT id, name, email, role, is_verified, seller_request FROM tblUser WHERE id = ?");
mysqli_stmt_bind_param($stmt, 'i', $user_id);
mysqli_stmt_execute($stmt);
$user = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
mysqli_stmt_close($stmt);

if (!$user) {
    redirect(BASE_URL . 'admin/users.php');
}

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = sanitize($_POST['name'] ?? '');
    $role = sanitize($_POST['role'] ?? 'buyer');
    $is_verified = intval($_POST['is_verified'] ?? 0);
    $seller_request = sanitize($_POST['seller_request'] ?? 'none');

    if (empty($name)) {
        $errors[] = 'Name is required';
    }

    if (!in_array($role, ['buyer', 'seller', 'admin'])) {
        $errors[] = 'Invalid role';
    }

    if (empty($errors)) {
        $stmt = mysqli_prepare($conn, "UPDATE tblUser SET name = ?, role = ?, is_verified = ?, seller_request = ? WHERE id = ?");
        mysqli_stmt_bind_param($stmt, 'ssisi', $name, $role, $is_verified, $seller_request, $user_id);

        if (mysqli_stmt_execute($stmt)) {
            $_SESSION['success'] = ['User updated successfully'];
            redirect(BASE_URL . 'admin/users.php');
        } else {
            $errors[] = 'Failed to update user';
        }
        mysqli_stmt_close($stmt);
    }
}

require_once __DIR__ . '/../includes/header.php';
?>

<h1 class="page-title">Edit User</h1>

<div class="form-wrap" style="max-width:600px;">
    <?php foreach ($errors as $e) echo displayError($e); ?>

    <form method="POST" action="">
        <div class="form-group">
            <label>Full Name *</label>
            <input type="text" name="name" class="form-control" value="<?php echo h($user['name']); ?>" required>
        </div>

        <div class="form-group">
            <label>Email Address</label>
            <input type="email" class="form-control" value="<?php echo h($user['email']); ?>" disabled>
            <small>Email cannot be changed</small>
        </div>

        <div class="form-group">
            <label>Role *</label>
            <select name="role" class="form-control" required>
                <option value="buyer" <?php echo $user['role'] === 'buyer' ? 'selected' : ''; ?>>Buyer</option>
                <option value="seller" <?php echo $user['role'] === 'seller' ? 'selected' : ''; ?>>Seller</option>
                <option value="admin" <?php echo $user['role'] === 'admin' ? 'selected' : ''; ?>>Admin</option>
            </select>
        </div>

        <div class="form-group">
            <label>Verification Status *</label>
            <select name="is_verified" class="form-control" required>
                <option value="0" <?php echo (int)$user['is_verified'] === 0 ? 'selected' : ''; ?>>Unverified</option>
                <option value="1" <?php echo (int)$user['is_verified'] === 1 ? 'selected' : ''; ?>>Verified</option>
            </select>
        </div>

        <div class="form-group">
            <label>Seller Request Status *</label>
            <select name="seller_request" class="form-control" required>
                <option value="none" <?php echo $user['seller_request'] === 'none' ? 'selected' : ''; ?>>None</option>
                <option value="pending" <?php echo $user['seller_request'] === 'pending' ? 'selected' : ''; ?>>Pending</option>
                <option value="approved" <?php echo $user['seller_request'] === 'approved' ? 'selected' : ''; ?>>Approved</option>
                <option value="rejected" <?php echo $user['seller_request'] === 'rejected' ? 'selected' : ''; ?>>Rejected</option>
            </select>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary">Save Changes</button>
            <a href="<?php echo BASE_URL; ?>admin/users.php" class="btn btn-secondary">Cancel</a>
        </div>
    </form>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
