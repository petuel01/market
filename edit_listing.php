<?php
require_once 'config.php';
if (!isset($_SESSION['user_id'])) { header('Location: login.php'); exit; }
if (!isset($_GET['id'])) { header('Location: my_listings.php'); exit; }
$id = intval($_GET['id']);
$stmt = $pdo->prepare('SELECT * FROM listings WHERE id = ? AND user_id = ?');
$stmt->execute([$id, $_SESSION['user_id']]);
$listing = $stmt->fetch();
if (!$listing) { header('Location: my_listings.php'); exit; }
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);
    $price = floatval($_POST['price']);
    $category = isset($_POST['category']) ? $_POST['category'] : $listing['category'];
    $school_type = isset($_POST['school_type']) ? $_POST['school_type'] : $listing['school_type'];
    $image = $listing['image'];
    if (!$title || !$description || !$price || !$category || !$school_type) {
        $error = 'All fields except image are required!';
    } else {
        if (isset($_FILES['image']) && $_FILES['image']['tmp_name']) {
            $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
            $image = uniqid('img_') . '.' . $ext;
            move_uploaded_file($_FILES['image']['tmp_name'], 'uploads/' . $image);
        }
        $stmt = $pdo->prepare('UPDATE listings SET title=?, description=?, price=?, image=?, category=?, school_type=? WHERE id=? AND user_id=?');
        $stmt->execute([$title, $description, $price, $image, $category, $school_type, $id, $_SESSION['user_id']]);
        header('Location: my_listings.php'); exit;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Listing</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
<?php include 'navbar.php'; ?>
<div class="container mt-5" style="max-width: 600px;">
    <h2 class="mb-4 text-center">Edit Listing</h2>
    <?php if ($error): ?><div class="alert alert-danger"><?=$error?></div><?php endif; ?>
    <form method="post" enctype="multipart/form-data">
        <div class="mb-3">
            <label class="form-label">Title</label>
            <input type="text" class="form-control" name="title" value="<?=htmlspecialchars($listing['title'])?>" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Description</label>
            <textarea class="form-control" name="description" rows="3" required><?=htmlspecialchars($listing['description'])?></textarea>
        </div>
        <div class="mb-3">
            <label class="form-label">Price (F CFA)</label>
            <input type="number" class="form-control" name="price" value="<?=htmlspecialchars($listing['price'])?>" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Category</label>
            <select class="form-select" name="category" required>
                <option value="Books" <?=($listing['category']==='Books'?'selected':'')?>>Books</option>
                <option value="Uniforms" <?=($listing['category']==='Uniforms'?'selected':'')?>>Uniforms</option>
                <option value="Electronics" <?=($listing['category']==='Electronics'?'selected':'')?>>Electronics</option>
                <option value="Notes" <?=($listing['category']==='Notes'?'selected':'')?>>Notes</option>
                <option value="Others" <?=($listing['category']==='Others'?'selected':'')?>>Others</option>
            </select>
        </div>
        <div class="mb-3">
            <label class="form-label">School Type</label>
            <select class="form-select" name="school_type" required>
                <option value="University" <?=($listing['school_type']==='University'?'selected':'')?>>University</option>
                <option value="Secondary School" <?=($listing['school_type']==='Secondary School'?'selected':'')?>>Secondary School</option>
            </select>
        </div>
        <div class="mb-3">
            <label class="form-label">Book Cover (optional)</label>
            <input type="file" class="form-control" name="image" accept="image/*">
            <?php if ($listing['image']): ?>
                <img src="uploads/<?=htmlspecialchars($listing['image'])?>" class="img-thumbnail mt-2" style="max-width:120px;">
            <?php endif; ?>
        </div>
        <button class="btn btn-success w-100"><i class="fas fa-save"></i> Save Changes</button>
    </form>
</div>
</body>
</html>
