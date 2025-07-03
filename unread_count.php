<?php
require_once 'config.php';
if (!isset($_SESSION['user_id'])) { echo '0'; exit; }
$user_id = $_SESSION['user_id'];
$stmt = $pdo->prepare('SELECT COUNT(*) FROM messages WHERE receiver_id = ? AND is_read = 0');
$stmt->execute([$user_id]);
echo $stmt->fetchColumn();
