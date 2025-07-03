<?php
require_once 'config.php';
if (!isset($_SESSION['user_id']) || !isset($_GET['user_id'])) exit('[]');
$user_id = $_SESSION['user_id'];
$other_id = intval($_GET['user_id']);

// Mark all messages sent to the current user by the other user as read
$mark = $pdo->prepare('UPDATE messages SET is_read = 1 WHERE sender_id = ? AND receiver_id = ? AND is_read = 0');
$mark->execute([$other_id, $user_id]);

$stmt = $pdo->prepare('SELECT * FROM messages WHERE (sender_id=? AND receiver_id=?) OR (sender_id=? AND receiver_id=?) ORDER BY created_at ASC');
$stmt->execute([$user_id, $other_id, $other_id, $user_id]);
$messages = $stmt->fetchAll();
$data = [];
foreach ($messages as $msg) {
    $data[] = [
        'sender_id' => $msg['sender_id'],
        'message' => htmlspecialchars($msg['content']),
        'time' => date('H:i d/m', strtotime($msg['created_at']))
    ];
}
header('Content-Type: application/json');
echo json_encode($data);
