<?php
$pageTitle = 'Sign In';
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/functions.php';

if (isLoggedIn()) redirect(BASE_URL . 'index.php');

$errors      = [];
$post        = [];
$loggedInUser = null;

if (isset($_GET['pending'])) {
    $errors[] = 'Your account is pending administrator approval. Please wait for verification.';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email    = sanitize($_POST['email']    ?? '');
    $password = $_POST['password'] ?? '';
    $post['email'] = $email;

    if (empty($email) || empty($password)) {
        $errors[] = 'Please enter your email and password.';
    } else {
        $cart = new ShoppingCart($conn);
        $user = $cart->Login($email, $password);

        if ($user) {
            if ((int)$user['is_verified'] !== 1) {
                $errors[] = 'Your account is pending administrator approval.';
            } else {
                session_regenerate_id(true);
                $_SESSION['user_id']        = $user['id'];
                $_SESSION['user_name']      = $user['name'];
                $_SESSION['user_email']     = $user['email'];
                $_SESSION['role']           = $user['role'];
                $_SESSION['is_verified']    = (int)$user['is_verified'];
                $_SESSION['seller_request'] = $user['seller_request'] ?? 'none';

                $loggedInUser = $user;
            }
        } else {
            $errors[] = 'Invalid email or password.';
        }
    }
}

require_once __DIR__ . '/../includes/header.php';
?>
<h1 class="page-title">Sign In</h1>
<div class="form-wrap">
    <?php foreach ($errors as $e) echo displayError($e); ?>

    <?php if ($loggedInUser): ?>
        <div class="alert alert-success">
            User <strong><?php echo h($loggedInUser['name']); ?></strong> is logged in
        </div>
        <div class="table-wrap mb-2">
            <table class="data-table">
                <thead>
                    <tr><th>ID</th><th>Name</th><th>Email</th><th>Role</th><th>Verified</th></tr>
                </thead>
                <tbody>
                    <tr>
                        <td><?php echo h($loggedInUser['id']); ?></td>
                        <td><?php echo h($loggedInUser['name']); ?></td>
                        <td><?php echo h($loggedInUser['email']); ?></td>
                        <td><?php echo h($loggedInUser['role']); ?></td>
                        <td><?php echo verificationBadge($loggedInUser['is_verified']); ?></td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div class="alert alert-success" style="text-align:center;">
            Redirecting to homepage in <span id="countdown">3</span> seconds…
        </div>
        <a href="<?php echo BASE_URL; ?>index.php" class="btn btn-primary btn-full mb-2">Continue to Homepage</a>
        <script>
            (function(){
                var n = 3, el = document.getElementById('countdown');
                var t = setInterval(function(){
                    n--;
                    if (el) el.textContent = n;
                    if (n <= 0) { clearInterval(t); window.location = '<?php echo BASE_URL; ?>index.php'; }
                }, 1000);
            })();
        </script>
    <?php endif; ?>

    <?php if (!$loggedInUser): ?>
    <form method="POST" action="">
        <div class="form-group">
            <label for="email">Email Address</label>
            <input type="email" id="email" name="email" class="form-control" required autofocus
                   value="<?php echo h($post['email'] ?? ''); ?>">
        </div>
        <div class="form-group">
            <label for="password">Password</label>
            <input type="password" id="password" name="password" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-primary btn-full">Sign In</button>
        <p class="mt-1 text-muted" style="font-size:0.9rem; text-align:center;">
            No account? <a href="<?php echo BASE_URL; ?>auth/register.php">Register here</a>
        </p>
    </form>
    <?php endif; ?>
</div>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
