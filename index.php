<?php
require_once 'config.php';
// Fetch latest listings
$stmt = $pdo->query("SELECT l.*, u.name FROM listings l JOIN users u ON l.user_id = u.id ORDER BY l.created_at DESC LIMIT 12");
$listings = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Campus Market</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
<?php include 'navbar.php'; ?>
<div class="container mt-4">
    <h1 class="mb-4 text-center">Campus Market</h1>
    <form class="row g-3 mb-4" method="get" action="search.php">
        <div class="col-md-10">
            <input type="text" class="form-control" name="q" placeholder="Search listings...">
        </div>
        <div class="col-md-2 text-end">
            <button class="btn btn-primary w-100"><i class="fas fa-search"></i> Search</button>
        </div>
        <div class="col-12 text-end">
            <button class="btn btn-primary"><i class="fas fa-search"></i> Search</button>
        </div>
    </form>
    <div class="row">
        <?php foreach ($listings as $listing): ?>
        <div class="col-md-4 mb-4">
            <div class="listing-card">
                <?php if ($listing['image']): ?>
                <img src="uploads/<?=htmlspecialchars($listing['image'])?>" class="listing-img" alt="Book Cover">
                <?php endif; ?>
                <div class="listing-details">
                    <div class="listing-title"><?=htmlspecialchars($listing['title'])?></div>
                    <div class="listing-meta">Category: <?=htmlspecialchars($listing['category'])?> | School Type: <?=htmlspecialchars($listing['school_type'])?></div>
                    <div class="listing-price">F CFA <?=number_format($listing['price'],2)?></div>
                    <div class="listing-meta"><small>By <?=htmlspecialchars($listing['name'])?> | <?=date('M d, Y', strtotime($listing['created_at']))?></small></div>
                </div>
                <div class="listing-actions">
                    <a href="chat.php?user_id=<?=$listing['user_id']?>" class="btn btn-listing"><i class="fas fa-comments"></i> Chat Seller</a>
                    <a href="swap_request.php?listing_id=<?= $listing['id'] ?>" class="btn btn-listing" style="background:linear-gradient(90deg,#36cfc9 60%,#2a4a7c 100%);color:#fff;"><i class="fas fa-exchange-alt"></i> Swap Book</a>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="assets/scripts.js"></script>
</body>
</html>
