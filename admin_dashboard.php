<?php
require_once 'config.php';
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') { header('Location: index.php'); exit; }
// Users and Listings
$users = $pdo->query('SELECT * FROM users')->fetchAll();
$listings = $pdo->query('SELECT l.*, u.name FROM listings l JOIN users u ON l.user_id = u.id')->fetchAll();

// Messages and Stats
$messages = $pdo->query('SELECT m.*, s.name AS sender_name, r.name AS receiver_name FROM messages m
    LEFT JOIN users s ON m.sender_id = s.id
    LEFT JOIN users r ON m.receiver_id = r.id
    ORDER BY m.timestamp DESC')->fetchAll();
$total_messages = count($messages);
$unique_users = count(array_unique(array_merge(array_column($messages, 'sender_id'), array_column($messages, 'receiver_id'))));
$user_message_counts = array_count_values(array_column($messages, 'sender_id'));
$most_active_user_id = $user_message_counts ? array_search(max($user_message_counts), $user_message_counts) : null;
$most_active_user = '';
if ($most_active_user_id) {
    foreach ($users as $u) {
        if ($u['id'] == $most_active_user_id) { $most_active_user = $u['name']; break; }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
<?php include 'navbar.php'; ?>
<div class="container mt-4">
    <h2 class="mb-4 text-center">Admin Dashboard</h2>
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card text-bg-primary mb-3">
                <div class="card-body">
                    <h5 class="card-title"><i class="fas fa-comments"></i> Total Messages</h5>
                    <p class="card-text fs-3 fw-bold">$total_messages</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card text-bg-success mb-3">
                <div class="card-body">
                    <h5 class="card-title"><i class="fas fa-users"></i> Unique Chat Users</h5>
                    <p class="card-text fs-3 fw-bold">$unique_users</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card text-bg-warning mb-3">
                <div class="card-body">
                    <h5 class="card-title"><i class="fas fa-user"></i> Most Active User</h5>
                    <p class="card-text fs-5">$most_active_user</p>
                </div>
            </div>
        </div>
    </div>
    <h4 class="mt-4">Users</h4>
    <table class="table table-bordered table-sm">
        <thead><tr><th>ID</th><th>Name</th><th>Email</th><th>Phone</th><th>Role</th><th>Verified</th><th>Action</th></tr></thead>
        <tbody>
        <?php foreach ($users as $user): ?>
            <tr>
                <td><?=$user['id']?></td>
                <td><?=htmlspecialchars($user['name'])?></td>
                <td><?=htmlspecialchars($user['email'])?></td>
                <td><?=htmlspecialchars($user['phone'])?></td>
                <td><?=$user['role']?></td>
                <td><?=$user['is_verified'] ? 'Yes' : 'No'?></td>
                <td>
                    <?php if ($user['role'] !== 'admin'): ?>
                    <a href="delete_user.php?id=<?=$user['id']?>" class="btn btn-danger btn-sm" onclick="return confirm('Delete user?')"><i class="fas fa-user-slash"></i> Delete</a>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
    <h4 class="mt-5">Listings</h4>
    <table class="table table-bordered table-sm">
        <thead><tr><th>ID</th><th>Title</th><th>Owner</th><th>Department</th><th>Course</th><th>Price</th><th>Action</th></tr></thead>
        <tbody>
        <?php foreach ($listings as $listing): ?>
            <tr>
                <td><?=$listing['id']?></td>
                <td><?=htmlspecialchars($listing['title'])?></td>
                <td><?=htmlspecialchars($listing['name'])?></td>
                <td><?=htmlspecialchars($listing['department'])?></td>
                <td><?=htmlspecialchars($listing['course'])?></td>
                <td>F CFA <?=number_format($listing['price'],2)?></td>
                <td><a href="remove_listing.php?id=<?=$listing['id']?>" class="btn btn-danger btn-sm" onclick="return confirm('Remove listing?')"><i class="fas fa-trash"></i> Remove</a></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>

    <h4 class="mt-5">Messages</h4>
    <div class="table-responsive">
    <table class="table table-bordered table-striped table-sm align-middle">
        <thead class="table-dark">
            <tr>
                <th>ID</th>
                <th>Sender</th>
                <th>Receiver</th>
                <th>Message</th>
                <th>Time</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($messages as $msg): ?>
            <tr>
                <td><?= $msg['id'] ?></td>
                <td><?= htmlspecialchars($msg['sender_name']) ?></td>
                <td><?= htmlspecialchars($msg['receiver_name']) ?></td>
                <td><?= htmlspecialchars($msg['message']) ?></td>
                <td><?= date('Y-m-d H:i', strtotime($msg['timestamp'])) ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
    </div>
</div>
</body>
</html>
