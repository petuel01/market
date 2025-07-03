<?php
require_once 'config.php';
session_start();
if (!isset($_SESSION['user_id'])) { echo 0; exit; }
$user_id = $_SESSION['user_id'];
// Count pending swaps where user is the owner
$stmt = $pdo->prepare('SELECT COUNT(*) FROM swap_requests WHERE owner_id = ? AND status = "pending"');
$stmt->execute([$user_id]);
echo $stmt->fetchColumn();
