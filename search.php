<?php
require_once 'config.php';
$q = isset($_GET['q']) ? trim($_GET['q']) : '';
$min_price = isset($_GET['min_price']) ? floatval($_GET['min_price']) : 0;
$max_price = isset($_GET['max_price']) ? floatval($_GET['max_price']) : 0;
$category = isset($_GET['category']) ? $_GET['category'] : '';
$school_type = isset($_GET['school_type']) ? $_GET['school_type'] : '';
$sql = "SELECT l.*, u.name FROM listings l JOIN users u ON l.user_id = u.id WHERE 1";
$params = [];
if ($q) {
    $sql .= " AND (l.title LIKE ? OR l.description LIKE ?)";
    $params[] = "%$q%";
    $params[] = "%$q%";
}
if ($category) {
    $sql .= " AND l.category = ?";
    $params[] = $category;
}
if ($school_type) {
    $sql .= " AND l.school_type = ?";
    $params[] = $school_type;
}
if ($min_price) {
    $sql .= " AND l.price >= ?";
    $params[] = $min_price;
}
if ($max_price) {
    $sql .= " AND l.price <= ?";
    $params[] = $max_price;
}
$sql .= " ORDER BY l.created_at DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$listings = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Search Listings</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
<?php include 'navbar.php'; ?>
<div class="container mt-4">
    <h2 class="mb-4 text-center">Search Results</h2>
    <form class="row g-3 mb-4" method="get" action="search.php">
        <div class="col-md-8">
            <input type="text" class="form-control" name="q" value="<?=htmlspecialchars($q)?>" placeholder="Search listings...">
        </div>
        <div class="col-md-3">
            <div class="dropdown">
                <button class="btn btn-outline-secondary dropdown-toggle w-100" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                    Filters
                </button>
                <ul class="dropdown-menu w-100 p-3">
                    <li>
                        <label>Category</label>
                        <select class="form-select mb-2" name="category">
                            <option value="">All Categories</option>
                            <option value="Books" <?=(@$_GET['category']==='Books'?'selected':'')?>>Books</option>
                            <option value="Uniforms" <?=(@$_GET['category']==='Uniforms'?'selected':'')?>>Uniforms</option>
                            <option value="Electronics" <?=(@$_GET['category']==='Electronics'?'selected':'')?>>Electronics</option>
                            <option value="Notes" <?=(@$_GET['category']==='Notes'?'selected':'')?>>Notes</option>
                            <option value="Others" <?=(@$_GET['category']==='Others'?'selected':'')?>>Others</option>
                        </select>
                        <label>School Type</label>
                        <select class="form-select mb-2" name="school_type">
                            <option value="">All School Types</option>
                            <option value="University" <?=(@$_GET['school_type']==='University'?'selected':'')?>>University</option>
                            <option value="Secondary School" <?=(@$_GET['school_type']==='Secondary School'?'selected':'')?>>Secondary School</option>
                        </select>
                        <label>Min Price</label>
                        <input type="number" class="form-control mb-2" name="min_price" value="<?=htmlspecialchars($min_price)?>" placeholder="Min Price">
                        <label>Max Price</label>
                        <input type="number" class="form-control mb-2" name="max_price" value="<?=htmlspecialchars($max_price)?>" placeholder="Max Price">
                    </li>
                </ul>
            </div>
        </div>
        <div class="col-md-1 text-end">
            <button class="btn btn-primary w-100"><i class="fas fa-search"></i> Search</button>
        </div>
    </form>
    <div class="row">
        <?php if (count($listings) === 0): ?>
            <div class="col-12"><div class="alert alert-warning">No listings found.</div></div>
        <?php endif; ?>
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
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</div>
</body>
</html>
