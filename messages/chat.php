<?php
/**
 * messages/chat.php
 * Display chat thread between two users for a product
 */
$pageTitle = 'Chat';
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/functions.php';

requireLogin();

$user_id   = intval($_GET['user_id'] ?? 0);
$product_id = intval($_GET['product_id'] ?? 0);
$current_user = $_SESSION['user_id'];

if ($user_id <= 0) {
    redirect(BASE_URL . 'messages/inbox.php');
}

// Verify user exists
$stmt = mysqli_prepare($conn, "SELECT id, name, email FROM tblUser WHERE id = ?");
mysqli_stmt_bind_param($stmt, 'i', $user_id);
mysqli_stmt_execute($stmt);
$other_user = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
mysqli_stmt_close($stmt);

if (!$other_user) {
    redirect(BASE_URL . 'messages/inbox.php');
}

// Mark messages as read
$stmt = mysqli_prepare($conn, "UPDATE tblMessages SET is_read = 1 WHERE receiver_id = ? AND sender_id = ?");
mysqli_stmt_bind_param($stmt, 'ii', $current_user, $user_id);
mysqli_stmt_execute($stmt);
mysqli_stmt_close($stmt);

// Fetch conversation
$sql = "SELECT m.*, 
               u.name AS sender_name
        FROM tblMessages m
        JOIN tblUser u ON m.sender_id = u.id
        WHERE (m.sender_id = ? AND m.receiver_id = ?) 
           OR (m.sender_id = ? AND m.receiver_id = ?)
        ORDER BY m.sent_at ASC";

$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, 'iiii', $current_user, $user_id, $user_id, $current_user);
mysqli_stmt_execute($stmt);
$messages = mysqli_fetch_all(mysqli_stmt_get_result($stmt), MYSQLI_ASSOC);
mysqli_stmt_close($stmt);

require_once __DIR__ . '/../includes/header.php';
?>

<h1 class="page-title">Chat with <?php echo h($other_user['name']); ?></h1>

<?php
$errors = $_SESSION['errors'] ?? [];
$success = $_SESSION['success'] ?? [];
unset($_SESSION['errors'], $_SESSION['success']);

foreach ($errors as $e) echo displayError($e);
foreach ($success as $s) echo displaySuccess($s);
?>

<div class="chat-container">
    <div class="chat-thread">
        <?php if (empty($messages)): ?>
            <div class="alert alert-info">No messages yet. Start the conversation!</div>
        <?php else: ?>
            <?php foreach ($messages as $msg): ?>
                <div class="chat-message <?php echo $msg['sender_id'] === $current_user ? 'sent' : 'received'; ?>">
                    <div class="chat-bubble">
                        <strong><?php echo h($msg['sender_name']); ?></strong>
                        <p><?php echo h($msg['message']); ?></p>
                        <small><?php echo date('M d, Y g:i A', strtotime($msg['sent_at'])); ?></small>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <form method="POST" action="<?php echo BASE_URL; ?>messages/send.php" class="chat-form">
        <input type="hidden" name="receiver_id" value="<?php echo $user_id; ?>">
        <?php if ($product_id > 0): ?>
            <input type="hidden" name="product_id" value="<?php echo $product_id; ?>">
        <?php endif; ?>
        <div class="form-group">
            <textarea name="message" class="form-control" placeholder="Type your message…" maxlength="1000" required></textarea>
        </div>
        <button type="submit" class="btn btn-primary">Send Message</button>
    </form>
</div>

<style>
.chat-container {
    max-width: 600px;
    margin: 0 auto;
    display: flex;
    flex-direction: column;
    gap: 20px;
}

.chat-thread {
    background: var(--surface);
    border-radius: 8px;
    padding: 20px;
    min-height: 300px;
    max-height: 500px;
    overflow-y: auto;
    display: flex;
    flex-direction: column;
    gap: 15px;
}

.chat-message {
    display: flex;
    margin-bottom: 10px;
}

.chat-message.sent {
    justify-content: flex-end;
}

.chat-message.received {
    justify-content: flex-start;
}

.chat-bubble {
    max-width: 70%;
    padding: 12px 16px;
    border-radius: 8px;
    background: var(--primary-red);
    color: white;
}

.chat-message.received .chat-bubble {
    background: var(--surface);
    border: 1px solid var(--primary-red);
    color: var(--text);
}

.chat-bubble strong {
    display: block;
    font-size: 0.9em;
    margin-bottom: 4px;
    opacity: 0.9;
}

.chat-bubble p {
    margin: 0 0 4px 0;
    word-wrap: break-word;
}

.chat-bubble small {
    display: block;
    font-size: 0.75em;
    opacity: 0.8;
    margin-top: 4px;
}

.chat-form {
    display: flex;
    flex-direction: column;
    gap: 10px;
}

.chat-form textarea {
    min-height: 80px;
    resize: vertical;
}

@media (max-width: 768px) {
    .chat-bubble {
        max-width: 90%;
    }
}
</style>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
