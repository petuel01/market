<?php
require_once 'config.php';
session_start();
if (!isset($_SESSION['user_id'])) { header('Location: login.php'); exit; }

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['swap_id'], $_POST['action'])) {
    $swap_id = intval($_POST['swap_id']);
    $action = $_POST['action'];
    $user_id = $_SESSION['user_id'];
    // Only the owner can accept/decline
    $stmt = $pdo->prepare('SELECT * FROM swap_requests WHERE id = ? AND owner_id = ? AND status = "pending"');
    $stmt->execute([$swap_id, $user_id]);
    $swap = $stmt->fetch();
    if ($swap) {
        if ($action === 'accept') {
            $pdo->prepare('UPDATE swap_requests SET status = "accepted" WHERE id = ?')->execute([$swap_id]);
            // Optionally mark listings as swapped or unavailable here
        } elseif ($action === 'decline') {
            $pdo->prepare('UPDATE swap_requests SET status = "declined" WHERE id = ?')->execute([$swap_id]);
        }
    }
}
header('Location: my_swaps.php');
exit;
