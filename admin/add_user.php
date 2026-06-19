<?php
/**
 * admin/add_user.php
 * Admin create new user (8-char password validation)
 */
$pageTitle = 'Add User';
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/functions.php';

requireAdmin();

$errors = [];
$post = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = sanitize($_POST['name'] ?? '');
    $email = sanitize($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $password_confirm = $_POST['password_confirm'] ?? '';
    $role = sanitize($_POST['role'] ?? 'buyer');
    $is_verified = intval($_POST['is_verified'] ?? 0);

    $post = compact('name', 'email', 'role');

    // Validation
    if (empty($name)) $errors[] = 'Name is required';
    if (empty($email)) $errors[] = 'Email is required';
    if (empty($password)) $errors[] = 'Password is required';
    if (strlen($password) < 8) $errors[] = 'Password must be at least 8 characters';
    if ($password !== $password_confirm) $errors[] = 'Passwords do not match';
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Invalid email address';
    if (!in_array($role, ['buyer', 'seller', 'admin'])) $errors[] = 'Invalid role';

    // Check email uniqueness
    if (empty($errors)) {
        $stmt = mysqli_prepare($conn, "SELECT id FROM tblUser WHERE email = ?");
        mysqli_stmt_bind_param($stmt, 's', $email);
        mysqli_stmt_execute($stmt);
        if (mysqli_stmt_get_result($stmt)->fetch_assoc()) {
            $errors[] = 'Email already exists';
        }
        mysqli_stmt_close($stmt);
    }

    // Insert user
    if (empty($errors)) {
        $password_hash = password_hash($password, PASSWORD_BCRYPT);
        $seller_request = ($role === 'seller') ? 'approved' : 'none';

        $stmt = mysqli_prepare($conn, "INSERT INTO tblUser (name, email, password_hash, role, is_verified, seller_request) VALUES (?, ?, ?, ?, ?, ?)");
        mysqli_stmt_bind_param($stmt, 'ssssis', $name, $email, $password_hash, $role, $is_verified, $seller_request);

        if (mysqli_stmt_execute($stmt)) {
            $_SESSION['success'] = ['User created successfully'];
            redirect(BASE_URL . 'admin/users.php');
        } else {
            $errors[] = 'Failed to create user';
        }
        mysqli_stmt_close($stmt);
    }
}

require_once __DIR__ . '/../includes/header.php';
?>

<h1 class="page-title">Add New User</h1>

<div class="form-wrap" style="max-width:600px;">
    <?php foreach ($errors as $e) echo displayError($e); ?>

    <form method="POST" action="">
        <div class="form-group">
            <label>Full Name *</label>
            <input type="text" name="name" class="form-control" value="<?php echo h($post['name'] ?? ''); ?>" required>
        </div>

        <div class="form-group">
            <label>Email Address *</label>
            <input type="email" name="email" class="form-control" value="<?php echo h($post['email'] ?? ''); ?>" required>
        </div>

        <div class="form-group">
            <label>Role *</label>
            <select name="role" class="form-control" required>
                <option value="buyer" <?php echo ($post['role'] ?? 'buyer') === 'buyer' ? 'selected' : ''; ?>>Buyer</option>
                <option value="seller" <?php echo ($post['role'] ?? '') === 'seller' ? 'selected' : ''; ?>>Seller</option>
                <option value="admin" <?php echo ($post['role'] ?? '') === 'admin' ? 'selected' : ''; ?>>Admin</option>
            </select>
        </div>

        <div class="form-group">
            <label>Password (minimum 8 characters) *</label>
            <input type="password" name="password" class="form-control" minlength="8" required>
        </div>

        <div class="form-group">
            <label>Confirm Password *</label>
            <input type="password" name="password_confirm" class="form-control" minlength="8" required>
        </div>

        <div class="form-group">
            <label>
                <input type="checkbox" name="is_verified" value="1">
                Verified immediately
            </label>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary">Create User</button>
            <a href="<?php echo BASE_URL; ?>admin/users.php" class="btn btn-secondary">Cancel</a>
        </div>
    </form>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
