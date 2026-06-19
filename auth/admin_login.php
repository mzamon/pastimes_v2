<?php
$pageTitle = 'Admin Sign In';
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/functions.php';

if (isAdmin()) redirect(BASE_URL . 'admin/dashboard.php');

$errors = [];
$post   = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email    = sanitize($_POST['email']    ?? '');
    $password = $_POST['password'] ?? '';
    $post['email'] = $email;

    if (empty($email) || empty($password)) {
        $errors[] = 'Please enter your admin email and password.';
    } else {
        $cart = new ShoppingCart($conn);
        $user = $cart->Login($email, $password);

        if ($user && $user['role'] === 'admin') {
            session_regenerate_id(true);
            $_SESSION['user_id']        = $user['id'];
            $_SESSION['user_name']      = $user['name'];
            $_SESSION['user_email']     = $user['email'];
            $_SESSION['role']           = 'admin';
            $_SESSION['is_verified']    = 1;
            $_SESSION['seller_request'] = 'none';

            redirect(BASE_URL . 'admin/dashboard.php');
        } else {
            $errors[] = 'Invalid admin credentials or account is not an admin.';
        }
    }
}

require_once __DIR__ . '/../includes/header.php';
?>
<h1 class="page-title">Admin Sign In</h1>
<div class="form-wrap">
    <?php foreach ($errors as $e) echo displayError($e); ?>
    <form method="POST" action="">
        <div class="form-group">
            <label for="email">Admin Email</label>
            <input type="email" id="email" name="email" class="form-control" required autofocus
                   value="<?php echo h($post['email'] ?? ''); ?>">
        </div>
        <div class="form-group">
            <label for="password">Password</label>
            <input type="password" id="password" name="password" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-primary btn-full">Sign In as Admin</button>
        <p class="mt-1 text-muted" style="font-size:0.9rem; text-align:center;">
            Regular user? <a href="<?php echo BASE_URL; ?>auth/login.php">Sign in here</a>
        </p>
    </form>
</div>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
