<?php
/**
 * messages/send.php
 * Handle message submission (POST only, insert into tblMessages)
 */
$pageTitle = 'Send Message';
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/functions.php';

requireLogin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit('Method not allowed');
}

$receiver_id = intval($_POST['receiver_id'] ?? 0);
$product_id  = intval($_POST['product_id'] ?? 0);
$message     = sanitize($_POST['message'] ?? '');
$sender_id   = $_SESSION['user_id'];

$errors = [];

if ($receiver_id <= 0) {
    $errors[] = 'Recipient not found.';
}

if ($sender_id === $receiver_id) {
    $errors[] = 'You cannot message yourself.';
}

if (empty($message)) {
    $errors[] = 'Message cannot be empty.';
} elseif (strlen($message) > 1000) {
    $errors[] = 'Message is too long (max 1000 characters).';
}

if (!empty($errors)) {
    $_SESSION['errors'] = $errors;
    redirect(BASE_URL . 'messages/chat.php?user_id=' . $receiver_id);
}

// Verify receiver exists
$stmt = mysqli_prepare($conn, "SELECT id FROM tblUser WHERE id = ?");
mysqli_stmt_bind_param($stmt, 'i', $receiver_id);
mysqli_stmt_execute($stmt);
if (!mysqli_stmt_get_result($stmt)->fetch_assoc()) {
    $_SESSION['errors'] = ['Recipient not found.'];
    redirect(BASE_URL . 'messages/inbox.php');
}
mysqli_stmt_close($stmt);

// Insert message
$product_id_insert = !empty($product_id) ? $product_id : null;
$stmt = mysqli_prepare($conn, "INSERT INTO tblMessages (sender_id, receiver_id, product_id, message, is_read) VALUES (?, ?, ?, ?, 0)");
mysqli_stmt_bind_param($stmt, 'iiii', $sender_id, $receiver_id, $product_id_insert, $message);

if (mysqli_stmt_execute($stmt)) {
    $_SESSION['success'] = ['Message sent successfully!'];
} else {
    $_SESSION['errors'] = ['Failed to send message.'];
}
mysqli_stmt_close($stmt);

redirect(BASE_URL . 'messages/chat.php?user_id=' . $receiver_id);
?>
