<?php
require_once 'config.php';
if (!isset($_SESSION['user_id']) || !isset($_POST['receiver_id']) || !isset($_POST['message'])) exit('0');
$sender_id = $_SESSION['user_id'];
$receiver_id = intval($_POST['receiver_id']);
$message = trim($_POST['message']);
if ($receiver_id == $sender_id || !$message) exit('0');
$stmt = $pdo->prepare('INSERT INTO messages (sender_id, receiver_id, content, is_read) VALUES (?, ?, ?, 0)');
$stmt->execute([$sender_id, $receiver_id, $message]);
echo '1';
