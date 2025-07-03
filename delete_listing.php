<?php
require_once 'config.php';
if (!isset($_SESSION['user_id']) || !isset($_GET['id'])) { header('Location: my_listings.php'); exit; }
$id = intval($_GET['id']);
$stmt = $pdo->prepare('DELETE FROM listings WHERE id = ? AND user_id = ?');
$stmt->execute([$id, $_SESSION['user_id']]);
header('Location: my_listings.php');
exit;
