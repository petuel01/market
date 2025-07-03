<?php
require_once 'config.php';
session_start();
if (!isset($_SESSION['user_id']) || ($_SESSION['role'] ?? 'user') !== 'admin') {
    header('Location: login.php');
    exit;
}

// Fetch stats
$user_count = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
$listing_count = $pdo->query("SELECT COUNT(*) FROM listings")->fetchColumn();
$university_count = $pdo->query("SELECT COUNT(*) FROM users WHERE school_type='University'")->fetchColumn();
$secondary_count = $pdo->query("SELECT COUNT(*) FROM users WHERE school_type='Secondary School'")->fetchColumn();

// Fetch recent users and listings
$recent_users = $pdo->query("SELECT * FROM users ORDER BY created_at DESC LIMIT 5")->fetchAll();
$recent_listings = $pdo->query("SELECT * FROM listings ORDER BY created_at DESC LIMIT 5")->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Portal</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
<?php include 'navbar.php'; ?>
<div class="container mt-4">
    <h2 class="mb-4 text-center">Admin Portal</h2>
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card text-center bg-dark text-white mb-3">
                <div class="card-body">
                    <h5 class="card-title">Total Users</h5>
                    <p class="display-6 fw-bold"><?=$user_count?></p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center bg-primary text-white mb-3">
                <div class="card-body">
                    <h5 class="card-title">Total Listings</h5>
                    <p class="display-6 fw-bold"><?=$listing_count?></p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center bg-info text-dark mb-3">
                <div class="card-body">
                    <h5 class="card-title">University Users</h5>
                    <p class="display-6 fw-bold"><?=$university_count?></p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center bg-warning text-dark mb-3">
                <div class="card-body">
                    <h5 class="card-title">Secondary School Users</h5>
                    <p class="display-6 fw-bold"><?=$secondary_count?></p>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-6">
            <div class="card mb-3">
                <div class="card-header bg-secondary text-white">Recent Users</div>
                <ul class="list-group list-group-flush">
                    <?php foreach ($recent_users as $u): ?>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <span><?=htmlspecialchars($u['name'])?> (<?=$u['school_type']?>)</span>
                        <span class="badge bg-primary"><?=htmlspecialchars($u['role'])?></span>
                    </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card mb-3">
                <div class="card-header bg-secondary text-white">Recent Listings</div>
                <ul class="list-group list-group-flush">
                    <?php foreach ($recent_listings as $l): ?>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <span><?=htmlspecialchars($l['title'])?> (<?=$l['category']?>, <?=$l['school_type']?>)</span>
                        <span class="badge bg-info text-dark">F CFA <?=number_format($l['price'],2)?></span>
                    </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>
    </div>
    <div class="text-center mt-4">
        <a href="logout.php" class="btn btn-danger">Logout</a>
    </div>
</div>
</body>
</html>
