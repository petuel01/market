<?php
require_once 'config.php';
if (!isset($_SESSION['user_id'])) { header('Location: login.php'); exit; }
$user_id = $_SESSION['user_id'];
// Get all users who have chatted with this user (sent or received)
$stmt = $pdo->prepare('SELECT u.id, u.name, u.email, u.role, MAX(m.created_at) as last_time, MAX(m.id) as last_msg_id
    FROM users u
    JOIN messages m ON (u.id = m.sender_id AND m.receiver_id = ?) OR (u.id = m.receiver_id AND m.sender_id = ?)
    WHERE u.id != ?
    GROUP BY u.id, u.name, u.email, u.role
    ORDER BY last_time DESC');
$stmt->execute([$user_id, $user_id, $user_id]);
$users = $stmt->fetchAll();
// Get last message for each conversation
$last_msgs = [];
if ($users) {
    $ids = array_column($users, 'last_msg_id');
    if ($ids) {
        $in = implode(',', array_fill(0, count($ids), '?'));
        $msg_stmt = $pdo->prepare('SELECT * FROM messages WHERE id IN (' . $in . ')');
        $msg_stmt->execute($ids);
        foreach ($msg_stmt->fetchAll() as $msg) {
            $last_msgs[$msg['id']] = $msg;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Messages</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            background: #0a174e;
            color: #f5f6fa;
        }
        .container {
            background: #132257;
            border-radius: 18px;
            box-shadow: 0 4px 24px rgba(10,23,78,0.12);
            padding: 32px 24px 24px 24px;
        }
        h2 {
            color: #f5f6fa;
            letter-spacing: 1px;
        }
        .list-group-item {
            background: #182c61;
            color: #f5f6fa;
            border: none;
            border-radius: 12px !important;
            margin-bottom: 12px;
            box-shadow: 0 2px 8px rgba(10,23,78,0.08);
            transition: background 0.2s;
        }
        .list-group-item:hover {
            background: #2541b2;
        }
        .fw-bold.text-decoration-none {
            color: #f5f6fa !important;
            font-size: 1.1rem;
        }
        .fw-bold.text-decoration-none:hover {
            color: #ffb400 !important;
        }
        .badge.bg-danger {
            background: #ff595e !important;
            font-size: 0.85rem;
            padding: 0.5em 0.8em;
            border-radius: 8px;
        }
        .small.text-muted {
            color: #bfc9d1 !important;
        }
        .fa-user-circle {
            color: #ffb400;
            font-size: 1.5rem;
            margin-right: 8px;
        }
        @media (max-width: 600px) {
            .container { padding: 12px 2px; }
            .list-group-item { font-size: 0.98rem; }
        }
    </style>
</head>
<body>
<?php include 'navbar.php'; ?>
<div class="container mt-4" style="max-width: 700px;">
    <h2 class="mb-4 text-center">Your Conversations</h2>
    <?php if (empty($users)): ?>
        <div class="alert alert-info" style="background:#2541b2;color:#fff;border:none;">No conversations yet.</div>
    <?php else: ?>
    <ul class="list-group">
        <?php foreach ($users as $u):
            $msg = $last_msgs[$u['last_msg_id']] ?? null;
            $is_read = isset($msg['is_read']) ? $msg['is_read'] : 0;
            $is_unread = $msg && $msg['receiver_id'] == $user_id && !$is_read;
        ?>
        <li class="list-group-item d-flex justify-content-between align-items-center px-3 py-2">
            <div class="d-flex flex-column">
                <a href="chat.php?user_id=<?=$u['id']?>" class="fw-bold text-decoration-none">
                    <i class="fas fa-user-circle"></i> <?=htmlspecialchars($u['name'])?>
                </a>
                <div class="small text-muted mt-1">
                    <?php if ($msg): ?>
                        <?=($msg['sender_id'] == $user_id ? 'You: ' : '') . htmlspecialchars($msg['content'])?>
                        <span class="ms-2 text-secondary">(<?=date('M d, H:i', strtotime($msg['created_at']))?>)</span>
                    <?php endif; ?>
                </div>
            </div>
            <?php if ($is_unread): ?>
                <span class="badge bg-danger">New</span>
            <?php endif; ?>
        </li>
        <?php endforeach; ?>
    </ul>
    <?php endif; ?>
</div>
</body>
</html>
