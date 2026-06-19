<?php
/**
 * admin/users.php
 * List all users with edit/delete options
 */
$pageTitle = 'Manage Users';
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/functions.php';

requireAdmin();

$search = sanitize($_GET['search'] ?? '');
$role_filter = sanitize($_GET['role'] ?? '');

$sql = "SELECT id, name, email, role, is_verified, seller_request, created_at FROM tblUser WHERE 1=1";
$params = [];
$types = '';

if (!empty($search)) {
    $like = '%' . $search . '%';
    $sql .= " AND (name LIKE ? OR email LIKE ?)";
    $params[] = $like;
    $params[] = $like;
    $types .= 'ss';
}

if (!empty($role_filter)) {
    $sql .= " AND role = ?";
    $params[] = $role_filter;
    $types .= 's';
}

$sql .= " ORDER BY created_at DESC";

$stmt = mysqli_prepare($conn, $sql);
if (!empty($params)) mysqli_stmt_bind_param($stmt, $types, ...$params);
mysqli_stmt_execute($stmt);
$users = mysqli_fetch_all(mysqli_stmt_get_result($stmt), MYSQLI_ASSOC);
mysqli_stmt_close($stmt);

require_once __DIR__ . '/../includes/header.php';
?>

<h1 class="page-title">Manage Users</h1>

<div class="filter-bar">
    <form method="GET" action="" style="display:flex; gap:10px; flex-wrap:wrap;">
        <input type="text" name="search" placeholder="Search by name or email…" value="<?php echo h($search); ?>" class="form-control" style="flex:1; min-width:200px;">
        <select name="role" class="form-control">
            <option value="">All Roles</option>
            <option value="buyer" <?php echo $role_filter === 'buyer' ? 'selected' : ''; ?>>Buyers</option>
            <option value="seller" <?php echo $role_filter === 'seller' ? 'selected' : ''; ?>>Sellers</option>
            <option value="admin" <?php echo $role_filter === 'admin' ? 'selected' : ''; ?>>Admins</option>
        </select>
        <button type="submit" class="btn btn-primary">Filter</button>
        <?php if (!empty($search) || !empty($role_filter)): ?>
            <a href="<?php echo BASE_URL; ?>admin/users.php" class="btn btn-secondary">Clear</a>
        <?php endif; ?>
    </form>
</div>

<div class="user-actions">
    <a href="<?php echo BASE_URL; ?>admin/add_user.php" class="btn btn-primary">+ Add New User</a>
</div>

<?php if (empty($users)): ?>
    <div class="alert alert-info">No users found.</div>
<?php else: ?>
    <div class="table-wrap">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Verified</th>
                    <th>Seller Status</th>
                    <th>Joined</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $u): ?>
                    <tr>
                        <td><?php echo h($u['name']); ?></td>
                        <td><?php echo h($u['email']); ?></td>
                        <td><span class="badge role-<?php echo strtolower($u['role']); ?>"><?php echo h($u['role']); ?></span></td>
                        <td>
                            <?php if ((int)$u['is_verified'] === 1): ?>
                                <span class="badge verified">✓ Yes</span>
                            <?php else: ?>
                                <span class="badge pending">No</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if ($u['role'] === 'seller'): ?>
                                <span class="badge seller-<?php echo strtolower($u['seller_request']); ?>">
                                    <?php echo h($u['seller_request']); ?>
                                </span>
                            <?php else: ?>
                                <span class="badge disabled">—</span>
                            <?php endif; ?>
                        </td>
                        <td><?php echo date('M d, Y', strtotime($u['created_at'])); ?></td>
                        <td>
                            <a href="<?php echo BASE_URL; ?>admin/edit_user.php?id=<?php echo $u['id']; ?>" class="btn btn-sm btn-primary">Edit</a>
                            <form method="POST" action="<?php echo BASE_URL; ?>admin/delete_user.php" style="display:inline;" onsubmit="return confirm('Delete this user? This cannot be undone.');">
                                <input type="hidden" name="user_id" value="<?php echo $u['id']; ?>">
                                <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php endif; ?>

<style>
.filter-bar {
    background: var(--surface);
    border: 1px solid var(--border);
    border-radius: 8px;
    padding: 15px;
    margin-bottom: 20px;
}

.user-actions {
    margin-bottom: 20px;
}

.badge.disabled {
    background: var(--text-muted);
    opacity: 0.5;
}

.badge.seller-pending {
    background: #f59e0b;
    color: white;
}

.badge.seller-approved {
    background: #10b981;
    color: white;
}

.badge.seller-rejected {
    background: var(--primary-red);
    color: white;
}

.badge.seller-none {
    background: var(--text-muted);
    opacity: 0.5;
}
</style>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
