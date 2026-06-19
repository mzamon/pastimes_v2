<?php
/**
 * admin/verify_users.php
 * Approve/reject user registrations and seller requests
 */
$pageTitle = 'Verify Users';
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/functions.php';

requireAdmin();

$errors = [];

// Handle approval/rejection
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $user_id = intval($_POST['user_id'] ?? 0);

    if ($action === 'verify_user') {
        $stmt = mysqli_prepare($conn, "UPDATE tblUser SET is_verified = 1 WHERE id = ?");
        mysqli_stmt_bind_param($stmt, 'i', $user_id);
        if (mysqli_stmt_execute($stmt)) {
            $_SESSION['success'] = ['User verified successfully'];
        } else {
            $errors[] = 'Failed to verify user';
        }
        mysqli_stmt_close($stmt);
    } elseif ($action === 'reject_user') {
        $stmt = mysqli_prepare($conn, "UPDATE tblUser SET is_verified = 0 WHERE id = ?");
        mysqli_stmt_bind_param($stmt, 'i', $user_id);
        if (mysqli_stmt_execute($stmt)) {
            $_SESSION['success'] = ['User rejected'];
        } else {
            $errors[] = 'Failed to reject user';
        }
        mysqli_stmt_close($stmt);
    } elseif ($action === 'approve_seller') {
        $stmt = mysqli_prepare($conn, "UPDATE tblUser SET seller_request = 'approved', is_verified = 1 WHERE id = ?");
        mysqli_stmt_bind_param($stmt, 'i', $user_id);
        if (mysqli_stmt_execute($stmt)) {
            $_SESSION['success'] = ['Seller request approved'];
        } else {
            $errors[] = 'Failed to approve seller';
        }
        mysqli_stmt_close($stmt);
    } elseif ($action === 'reject_seller') {
        $note = sanitize($_POST['rejection_note'] ?? '');
        $stmt = mysqli_prepare($conn, "UPDATE tblUser SET seller_request = 'rejected', seller_request_note = ? WHERE id = ?");
        mysqli_stmt_bind_param($stmt, 'si', $note, $user_id);
        if (mysqli_stmt_execute($stmt)) {
            $_SESSION['success'] = ['Seller request rejected'];
        } else {
            $errors[] = 'Failed to reject seller request';
        }
        mysqli_stmt_close($stmt);
    }

    if (!empty($_SESSION['success'])) {
        redirect(BASE_URL . 'admin/verify_users.php');
    }
}

// Get unverified users
$result = mysqli_query($conn, "SELECT id, name, email, role, is_verified, created_at FROM tblUser WHERE is_verified = 0 ORDER BY created_at ASC");
$unverified = mysqli_fetch_all($result, MYSQLI_ASSOC);

// Get pending seller requests
$result = mysqli_query($conn, "SELECT u.id, u.name, u.email, u.seller_request_note, sr.requested_at FROM tblUser u LEFT JOIN tblSellerRequests sr ON u.id = sr.user_id WHERE u.seller_request = 'pending' ORDER BY u.created_at ASC");
$pending_sellers = mysqli_fetch_all($result, MYSQLI_ASSOC);

require_once __DIR__ . '/../includes/header.php';

$success = $_SESSION['success'] ?? [];
unset($_SESSION['success']);
foreach ($errors as $e) echo displayError($e);
foreach ($success as $s) echo displaySuccess($s);
?>

<h1 class="page-title">Verify Users & Seller Requests</h1>

<div class="verify-section">
    <h2>Unverified Users (<?php echo count($unverified); ?>)</h2>
    
    <?php if (empty($unverified)): ?>
        <div class="alert alert-success">All users are verified!</div>
    <?php else: ?>
        <div class="table-wrap">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Joined</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($unverified as $u): ?>
                        <tr>
                            <td><?php echo h($u['name']); ?></td>
                            <td><?php echo h($u['email']); ?></td>
                            <td><?php echo h($u['role']); ?></td>
                            <td><?php echo date('M d, Y', strtotime($u['created_at'])); ?></td>
                            <td>
                                <form method="POST" style="display:contents;">
                                    <input type="hidden" name="user_id" value="<?php echo $u['id']; ?>">
                                    <input type="hidden" name="action" value="verify_user">
                                    <button type="submit" class="btn btn-sm btn-success">Verify</button>
                                </form>
                                <form method="POST" style="display:contents;" onsubmit="return confirm('Reject this user?');">
                                    <input type="hidden" name="user_id" value="<?php echo $u['id']; ?>">
                                    <input type="hidden" name="action" value="reject_user">
                                    <button type="submit" class="btn btn-sm btn-danger">Reject</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>

<div class="verify-section">
    <h2>Pending Seller Requests (<?php echo count($pending_sellers); ?>)</h2>
    
    <?php if (empty($pending_sellers)): ?>
        <div class="alert alert-success">No pending seller requests!</div>
    <?php else: ?>
        <div class="table-wrap">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Motivation</th>
                        <th>Requested</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($pending_sellers as $s): ?>
                        <tr>
                            <td><?php echo h($s['name']); ?></td>
                            <td><?php echo h($s['email']); ?></td>
                            <td><small><?php echo h(substr($s['seller_request_note'] ?? 'No motivation provided', 0, 50)); ?></small></td>
                            <td><?php echo date('M d, Y', strtotime($s['requested_at'])); ?></td>
                            <td>
                                <form method="POST" style="display:contents;">
                                    <input type="hidden" name="user_id" value="<?php echo $s['id']; ?>">
                                    <input type="hidden" name="action" value="approve_seller">
                                    <button type="submit" class="btn btn-sm btn-success">Approve</button>
                                </form>
                                <button type="button" class="btn btn-sm btn-danger" onclick="showRejectForm(<?php echo $s['id']; ?>)">Reject</button>
                            </td>
                        </tr>
                        <tr id="reject-form-<?php echo $s['id']; ?>" style="display:none;">
                            <td colspan="5">
                                <form method="POST">
                                    <input type="hidden" name="user_id" value="<?php echo $s['id']; ?>">
                                    <input type="hidden" name="action" value="reject_seller">
                                    <textarea name="rejection_note" placeholder="Rejection reason..." class="form-control" required></textarea>
                                    <div style="margin-top:10px;">
                                        <button type="submit" class="btn btn-danger">Confirm Rejection</button>
                                        <button type="button" class="btn btn-secondary" onclick="hideRejectForm(<?php echo $s['id']; ?>)">Cancel</button>
                                    </div>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>

<style>
.verify-section {
    background: var(--surface);
    border: 1px solid var(--border);
    border-radius: 8px;
    padding: 20px;
    margin-bottom: 20px;
}

.verify-section h2 {
    margin-top: 0;
    border-bottom: 2px solid var(--primary-red);
    padding-bottom: 10px;
}
</style>

<script>
function showRejectForm(id) {
    document.getElementById('reject-form-' + id).style.display = '';
}
function hideRejectForm(id) {
    document.getElementById('reject-form-' + id).style.display = 'none';
}
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
