<?php
require_once 'config.php';
if (!isset($_SESSION['user_id'])) { header('Location: login.php'); exit; }
$user_id = $_SESSION['user_id'];

// Fetch swaps where user is requester or owner
$stmt = $pdo->prepare('SELECT s.*, l1.title AS my_title, l2.title AS their_title, u1.name AS owner_name, u2.name AS requester_name
    FROM swap_requests s
    JOIN listings l1 ON s.requester_listing_id = l1.id
    JOIN listings l2 ON s.owner_listing_id = l2.id
    JOIN users u1 ON s.owner_id = u1.id
    JOIN users u2 ON s.requester_id = u2.id
    WHERE s.requester_id = ? OR s.owner_id = ?
    ORDER BY s.created_at DESC');
$stmt->execute([$user_id, $user_id]);
$swaps = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Swaps</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
<?php include 'navbar.php'; ?>
<div class="container mt-4" style="max-width:800px;">
    <h2 class="mb-4 text-center">My Swap Requests</h2>
    <?php if (empty($swaps)): ?>
        <div class="alert alert-info">No swap requests yet.</div>
    <?php else: ?>
    <table class="table table-dark table-striped table-bordered">
        <thead>
            <tr>
                <th>Requested By</th>
                <th>Offered</th>
                <th>Requested</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($swaps as $swap): ?>
            <tr>
                <td><?=htmlspecialchars($swap['requester_name'])?></td>
                <td><?=htmlspecialchars($swap['my_title'])?></td>
                <td><?=htmlspecialchars($swap['their_title'])?></td>
                <td><?=ucfirst($swap['status'])?></td>
                <td>
                <?php if ($swap['owner_id'] == $user_id && $swap['status'] == 'pending'): ?>
                    <form method="post" action="swap_action.php" style="display:inline;">
                        <input type="hidden" name="swap_id" value="<?=$swap['id']?>">
                        <button name="action" value="accept" class="btn btn-success btn-sm">Accept</button>
                        <button name="action" value="decline" class="btn btn-danger btn-sm">Decline</button>
                    </form>
                <?php else: ?>
                    -
                <?php endif; ?>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
    <?php endif; ?>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
