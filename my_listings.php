<?php
require_once 'config.php';
if (!isset($_SESSION['user_id'])) { header('Location: login.php'); exit; }
$stmt = $pdo->prepare('SELECT * FROM listings WHERE user_id = ? ORDER BY created_at DESC');
$stmt->execute([$_SESSION['user_id']]);
$listings = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Listings</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
<?php include 'navbar.php'; ?>
<div class="container mt-4">
    <h2 class="mb-4 text-center">My Listings</h2>
    <div class="mb-3 text-end">
        <a href="create_listing.php" class="btn btn-primary"><i class="fas fa-plus"></i> New Listing</a>
    </div>
    <div class="row">
        <?php foreach ($listings as $listing): ?>
        <div class="col-md-4 mb-4">
            <div class="listing-card">
                <?php if ($listing['image']): ?>
                <img src="uploads/<?=htmlspecialchars($listing['image'])?>" class="listing-img" alt="Book Cover">
                <?php endif; ?>
                <div class="listing-details">
                    <div class="listing-title"><?=htmlspecialchars($listing['title'])?></div>
                    <!-- Removed Department, Course, and Condition fields as per new table structure -->
                    <div class="listing-price">F CFA <?=number_format($listing['price'],2)?></div>
                </div>
                <div class="listing-actions">
                    <a href="edit_listing.php?id=<?=$listing['id']?>" class="btn btn-listing"><i class="fas fa-edit"></i> Edit</a>
                    <a href="delete_listing.php?id=<?=$listing['id']?>" class="btn btn-listing" onclick="return confirm('Delete this listing?')"><i class="fas fa-trash"></i> Delete</a>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</div>
</body>
</html>
