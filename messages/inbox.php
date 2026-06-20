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

// Get all messages where current user is sender or receiver
$sql = "SELECT m.*, 
               u_sender.name AS sender_name,
               u_receiver.name AS receiver_name,
               p.title AS product_title
        FROM tblMessages m
        LEFT JOIN tblUser u_sender ON m.sender_id = u_sender.id
        LEFT JOIN tblUser u_receiver ON m.receiver_id = u_receiver.id
        LEFT JOIN tblProducts p ON m.product_id = p.id
        WHERE m.sender_id = ? OR m.receiver_id = ?
        ORDER BY m.sent_at DESC";

$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, 'ii', $current_user, $current_user);
mysqli_stmt_execute($stmt);
$all_messages = mysqli_fetch_all(mysqli_stmt_get_result($stmt), MYSQLI_ASSOC);
mysqli_stmt_close($stmt);

// Group messages by conversation (other user + product)
$conversations = [];
foreach ($all_messages as $msg) {
    // Determine the other user
    $other_id = ($msg['sender_id'] == $current_user) ? $msg['receiver_id'] : $msg['sender_id'];
    $key = $other_id . '_' . ($msg['product_id'] ?? 0);
    
    if (!isset($conversations[$key])) {
        // Determine the other user's name
        $other_name = ($msg['sender_id'] == $current_user) ? $msg['receiver_name'] : $msg['sender_name'];
        
        $conversations[$key] = [
            'other_user_id'   => $other_id,
            'other_user_name' => $other_name,
            'product_id'      => $msg['product_id'],
            'product_title'   => $msg['product_title'] ?? 'General',
            'last_message'    => $msg['message'],
            'last_sent_at'    => $msg['sent_at'],
            'unread'          => ($msg['receiver_id'] == $current_user && $msg['is_read'] == 0),
        ];
    }
    
    // If this message is unread for current user, mark conversation as unread
    if ($msg['receiver_id'] == $current_user && $msg['is_read'] == 0) {
        $conversations[$key]['unread'] = true;
    }
}

// Sort conversations by last sent time (newest first)
usort($conversations, function($a, $b) {
    return strtotime($b['last_sent_at']) - strtotime($a['last_sent_at']);
});

require_once __DIR__ . '/../includes/header.php';
?>

<h1 class="page-title">Messages</h1>

<?php if (empty($conversations)): ?>
    <div class="alert alert-info">No messages yet. Browse a listing and message a seller to get started.</div>
<?php else: ?>
    <?php foreach ($conversations as $conv): ?>
        <div class="conv-card">
            <div class="conv-info">
                <h3>
                    <?php echo h($conv['product_title']); ?>
                    <?php if ($conv['unread']): ?>
                        <span class="status-badge status-pending" style="margin-left:0.4rem;">New</span>
                    <?php endif; ?>
                </h3>
                <p class="text-muted" style="font-size:0.85rem; margin-bottom:0.25rem;">
                    With <strong><?php echo h($conv['other_user_name']); ?></strong>
                </p>
                <p class="conv-preview"><?php echo h(mb_strimwidth($conv['last_message'], 0, 80, '…')); ?></p>
                <small class="text-muted"><?php echo date('d M Y H:i', strtotime($conv['last_sent_at'])); ?></small>
            </div>
            <?php
                $chatUrl = BASE_URL . 'messages/chat.php?user_id=' . $conv['other_user_id'];
                if (!empty($conv['product_id'])) $chatUrl .= '&product_id=' . $conv['product_id'];
            ?>
            <a href="<?php echo $chatUrl; ?>" class="btn btn-primary btn-sm">Open Chat</a>
        </div>
    <?php endforeach; ?>
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