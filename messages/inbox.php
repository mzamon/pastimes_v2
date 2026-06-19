<?php
/**
 * messages/inbox.php
 * List all conversations with unread badge
 */
$pageTitle = 'Messages';
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/functions.php';

requireLogin();

$current_user = $_SESSION['user_id'];

// Get conversations (last message from each user)
$sql = "SELECT DISTINCT
            CASE 
                WHEN sender_id = ? THEN receiver_id
                ELSE sender_id
            END AS conversation_user_id,
            (SELECT name FROM tblUser WHERE id = CASE 
                WHEN sender_id = ? THEN receiver_id
                ELSE sender_id
            END) AS other_name,
            (SELECT message FROM tblMessages m2
             WHERE (m2.sender_id = tblMessages.sender_id AND m2.receiver_id = tblMessages.receiver_id)
                OR (m2.sender_id = tblMessages.receiver_id AND m2.receiver_id = tblMessages.sender_id)
             ORDER BY m2.sent_at DESC LIMIT 1) AS last_message,
            (SELECT COUNT(*) FROM tblMessages m3
             WHERE m3.receiver_id = ? AND m3.sender_id = CASE 
                WHEN tblMessages.sender_id = ? THEN tblMessages.receiver_id
                ELSE tblMessages.sender_id
            END AND m3.is_read = 0) AS unread_count,
            (SELECT MAX(sent_at) FROM tblMessages m4
             WHERE (m4.sender_id = tblMessages.sender_id AND m4.receiver_id = tblMessages.receiver_id)
                OR (m4.sender_id = tblMessages.receiver_id AND m4.receiver_id = tblMessages.sender_id)) AS last_sent
        FROM tblMessages
        WHERE sender_id = ? OR receiver_id = ?
        ORDER BY last_sent DESC";

$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, 'iiiiiiii', $current_user, $current_user, $current_user, $current_user, $current_user, $current_user, $current_user, $current_user);
mysqli_stmt_execute($stmt);
$conversations = mysqli_fetch_all(mysqli_stmt_get_result($stmt), MYSQLI_ASSOC);
mysqli_stmt_close($stmt);

require_once __DIR__ . '/../includes/header.php';
?>

<h1 class="page-title">Messages</h1>

<?php if (empty($conversations)): ?>
    <div class="alert alert-info">No messages yet. <a href="<?php echo BASE_URL; ?>products/index.php">Browse products</a> to start chatting!</div>
<?php else: ?>
    <div class="messages-list">
        <?php foreach ($conversations as $conv): ?>
            <a href="<?php echo BASE_URL; ?>messages/chat.php?user_id=<?php echo $conv['conversation_user_id']; ?>" class="message-item">
                <div class="message-user">
                    <strong><?php echo h($conv['other_name']); ?></strong>
                    <?php if ((int)$conv['unread_count'] > 0): ?>
                        <span class="badge unread"><?php echo (int)$conv['unread_count']; ?></span>
                    <?php endif; ?>
                </div>
                <p class="message-preview"><?php echo h(substr($conv['last_message'], 0, 100)); ?></p>
                <small><?php echo date('M d, Y g:i A', strtotime($conv['last_sent'])); ?></small>
            </a>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<style>
.messages-list {
    display: flex;
    flex-direction: column;
    gap: 10px;
}

.message-item {
    background: var(--surface);
    border: 1px solid var(--border);
    border-radius: 8px;
    padding: 16px;
    text-decoration: none;
    color: var(--text);
    transition: background 0.2s, transform 0.2s;
    display: block;
}

.message-item:hover {
    background: var(--bg);
    transform: translateX(4px);
}

.message-user {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 8px;
}

.message-user strong {
    color: var(--primary-red);
}

.badge.unread {
    background: var(--primary-red);
    color: white;
    border-radius: 50%;
    width: 24px;
    height: 24px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.75em;
    font-weight: bold;
}

.message-preview {
    color: var(--text-muted);
    margin: 8px 0 0 0;
    font-size: 0.9em;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.message-item small {
    color: var(--text-muted);
    font-size: 0.8em;
}
</style>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
