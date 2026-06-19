<?php
/**
 * admin/delete_user.php
 * Delete user with FK constraint handling
 */
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/functions.php';

requireAdmin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit('Method not allowed');
}

$user_id = intval($_POST['user_id'] ?? 0);

if ($user_id <= 0) {
    $_SESSION['errors'] = ['Invalid user'];
    redirect(BASE_URL . 'admin/users.php');
}

// Prevent deleting self
if ($user_id === $_SESSION['user_id']) {
    $_SESSION['errors'] = ['Cannot delete your own account'];
    redirect(BASE_URL . 'admin/users.php');
}

// Verify user exists
$stmt = mysqli_prepare($conn, "SELECT id FROM tblUser WHERE id = ?");
mysqli_stmt_bind_param($stmt, 'i', $user_id);
mysqli_stmt_execute($stmt);
if (!mysqli_stmt_get_result($stmt)->fetch_assoc()) {
    $_SESSION['errors'] = ['User not found'];
    redirect(BASE_URL . 'admin/users.php');
}
mysqli_stmt_close($stmt);

// Delete user (cascading deletes via FK)
$stmt = mysqli_prepare($conn, "DELETE FROM tblUser WHERE id = ?");
mysqli_stmt_bind_param($stmt, 'i', $user_id);

if (mysqli_stmt_execute($stmt)) {
    $_SESSION['success'] = ['User deleted successfully'];
} else {
    $_SESSION['errors'] = ['Failed to delete user: ' . mysqli_error($conn)];
}
mysqli_stmt_close($stmt);

redirect(BASE_URL . 'admin/users.php');
?>
