<?php
require_once 'config.php';
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin' || !isset($_GET['id'])) { header('Location: admin_dashboard.php'); exit; }
$id = intval($_GET['id']);
$stmt = $pdo->prepare('DELETE FROM listings WHERE id = ?');
$stmt->execute([$id]);
header('Location: admin_dashboard.php');
exit;
