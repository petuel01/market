<?php
require_once 'config.php';
if (!isset($_SESSION['user_id'])) { header('Location: login.php'); exit; }
$title = $description = $condition = $department = $course = $price = '';
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);
    $price = floatval($_POST['price']);
    $category = isset($_POST['category']) ? $_POST['category'] : 'General';
    $school_type = isset($_POST['school_type']) ? $_POST['school_type'] : 'University';
    $image = '';
    if (!$title || !$description || !$price || !$category || !$school_type) {
        $error = 'All fields except image are required!';
    } else {
        if (isset($_FILES['image']) && $_FILES['image']['tmp_name']) {
            $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
            $image = uniqid('img_') . '.' . $ext;
            move_uploaded_file($_FILES['image']['tmp_name'], 'uploads/' . $image);
        }
        $stmt = $pdo->prepare('INSERT INTO listings (user_id, title, description, price, image, category, school_type) VALUES (?, ?, ?, ?, ?, ?, ?)');
        $stmt->execute([$_SESSION['user_id'], $title, $description, $price, $image, $category, $school_type]);
        header('Location: my_listings.php'); exit;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Listing</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
<?php include 'navbar.php'; ?>
<div class="container mt-5" style="max-width: 600px;">
    <h2 class="mb-4 text-center">Create Listing</h2>
    <?php if ($error): ?><div class="alert alert-danger"><?=$error?></div><?php endif; ?>
    <form method="post" enctype="multipart/form-data">
        <div class="mb-3">
            <label class="form-label">Title</label>
            <input type="text" class="form-control" name="title" value="<?=htmlspecialchars($title)?>" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Description</label>
            <textarea class="form-control" name="description" rows="3" required><?=htmlspecialchars($description)?></textarea>
        </div>
        <div class="mb-3">
            <label class="form-label">Price (F CFA)</label>
            <input type="number" class="form-control" name="price" value="<?=htmlspecialchars($price)?>" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Category</label>
            <select class="form-select" name="category" required>
                <option value="Books">Books</option>
                <option value="Uniforms">Uniforms</option>
                <option value="Electronics">Electronics</option>
                <option value="Notes">Notes</option>
                <option value="Others">Others</option>
            </select>
        </div>
        <div class="mb-3">
            <label class="form-label">School Type</label>
            <select class="form-select" name="school_type" required>
                <option value="University">University</option>
                <option value="Secondary School">Secondary School</option>
            </select>
        </div>
        <div class="mb-3">
            <label class="form-label">Book Cover (optional)</label>
            <input type="file" class="form-control" name="image" accept="image/*">
        </div>
        <button class="btn btn-success w-100"><i class="fas fa-plus"></i> Create Listing</button>
    </form>
</div>
</body>
</html>
