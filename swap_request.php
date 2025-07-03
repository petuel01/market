<?php
require_once 'config.php';
if (!isset($_SESSION['user_id'])) { header('Location: login.php'); exit; }
$user_id = $_SESSION['user_id'];

$target_listing_id = isset($_GET['listing_id']) ? intval($_GET['listing_id']) : 0;
if (!$target_listing_id) { echo '<div class="alert alert-danger">Invalid listing.</div>'; exit; }

// Fetch the target listing
$stmt = $pdo->prepare('SELECT l.*, u.name FROM listings l JOIN users u ON l.user_id = u.id WHERE l.id = ?');
$stmt->execute([$target_listing_id]);
$target_listing = $stmt->fetch();
if (!$target_listing) { echo '<div class="alert alert-danger">Listing not found.</div>'; exit; }

// Fetch user's own listings
$my_listings = $pdo->prepare('SELECT * FROM listings WHERE user_id = ? AND id != ?');
$my_listings->execute([$user_id, $target_listing_id]);
$my_listings = $my_listings->fetchAll();

// Handle swap request submission
$success = '';
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['my_listing_id'])) {
    $my_listing_id = intval($_POST['my_listing_id']);
    // Insert a swap request (store in swap_requests table)
    $insert = $pdo->prepare('INSERT INTO swap_requests (requester_id, owner_id, requester_listing_id, owner_listing_id, status) VALUES (?, ?, ?, ?, "pending")');
    $insert->execute([$user_id, $target_listing['user_id'], $my_listing_id, $target_listing_id]);
    $success = 'Swap request sent! The owner will be notified.';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Swap Request</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
<?php include 'navbar.php'; ?>
<div class="container mt-4" style="max-width:600px;">
    <h2 class="mb-4 text-center">Request a Swap</h2>
    <?php if ($success): ?><div class="alert alert-success"><?=$success?></div><?php endif; ?>
    <?php if ($error): ?><div class="alert alert-danger"><?=$error?></div><?php endif; ?>
    <div class="card mb-4">
        <div class="card-body">
            <h5 class="card-title">Swap for: <?=htmlspecialchars($target_listing['title'])?></h5>
            <div class="listing-meta">Owner: <?=htmlspecialchars($target_listing['name'])?></div>
            <div class="listing-meta">Category: <?=htmlspecialchars($target_listing['category'])?></div>
        </div>
    </div>
    <form method="post">
        <div class="mb-3">
            <label class="form-label">Select one of your listings to offer for swap:</label>
            <select class="form-select" name="my_listing_id" required>
                <option value="">-- Select Listing --</option>
                <?php foreach ($my_listings as $l): ?>
                <option value="<?=$l['id']?>"><?=htmlspecialchars($l['title'])?> (<?=$l['category']?>)</option>
                <?php endforeach; ?>
            </select>
        </div>
        <button class="btn btn-listing w-100"><i class="fas fa-exchange-alt"></i> Send Swap Request</button>
    </form>
</div>
</body>
</html>
