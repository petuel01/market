<?php
require_once 'config.php';
if (!isset($_SESSION['user_id'])) { header('Location: login.php'); exit; }
$stmt = $pdo->prepare('SELECT * FROM users WHERE id = ?');
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();
$error = $success = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $password = $_POST['password'];
    if (!$email || !$phone) {
        $error = 'Email and phone are required!';
    } else {
        if ($password) {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare('UPDATE users SET email=?, phone=?, password=? WHERE id=?');
            $stmt->execute([$email, $phone, $hash, $_SESSION['user_id']]);
        } else {
            $stmt = $pdo->prepare('UPDATE users SET email=?, phone=? WHERE id=?');
            $stmt->execute([$email, $phone, $_SESSION['user_id']]);
        }
        $success = 'Profile updated!';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
<?php include 'navbar.php'; ?>
<div class="container mt-5" style="max-width: 500px;">
    <h2 class="mb-4 text-center">My Profile</h2>
    <?php if ($error): ?><div class="alert alert-danger"><?=$error?></div><?php endif; ?>
    <?php if ($success): ?><div class="alert alert-success"><?=$success?></div><?php endif; ?>
    <form method="post">
        <div class="mb-3">
            <label class="form-label">Name</label>
            <input type="text" class="form-control" value="<?=htmlspecialchars($user['name'])?>" disabled>
        </div>
        <div class="mb-3">
            <label class="form-label">Email</label>
            <input type="email" class="form-control" name="email" value="<?=htmlspecialchars($user['email'])?>" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Phone</label>
            <input type="text" class="form-control" name="phone" value="<?=htmlspecialchars($user['phone'])?>" required>
        </div>
        <div class="mb-3">
            <label class="form-label">New Password (leave blank to keep current)</label>
            <input type="password" class="form-control" name="password">
        </div>
        <button class="btn btn-success w-100"><i class="fas fa-save"></i> Update Profile</button>
    </form>
</div>
</body>
</html>
