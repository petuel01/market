<?php
require_once 'config.php';
$name = $email = $phone = $password = $error = '';
$school_type = 'University';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $password = $_POST['password'];
    $school_type = isset($_POST['school_type']) ? $_POST['school_type'] : 'University';
    $role = 'user';
    $banned = 0;
    if (!$name || !$email || !$phone || !$password || !$school_type) {
        $error = 'All fields are required!';
    } else {
        $stmt = $pdo->prepare('SELECT id FROM users WHERE email = ?');
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            $error = 'Email already registered!';
        } else {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $pdo->prepare('INSERT INTO users (name, email, phone, password, school_type, role, banned) VALUES (?, ?, ?, ?, ?, ?, ?)')
                ->execute([$name, $email, $phone, $hash, $school_type, $role, $banned]);
            $user_id = $pdo->lastInsertId();
            // Generate OTP
            $otp = rand(100000, 999999);
            $expires = date('Y-m-d H:i:s', time() + 600);
            $pdo->prepare('INSERT INTO verification_codes (user_id, code, expires_at) VALUES (?, ?, ?)')
                ->execute([$user_id, $otp, $expires]);
            $_SESSION['pending_user_id'] = $user_id;
            $_SESSION['pending_email'] = $email;
            $_SESSION['pending_otp'] = $otp; // For simulation
            header('Location: verify_otp.php');
            exit;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Campus Market</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
<?php include 'navbar.php'; ?>
<div class="container mt-5" style="max-width: 500px;">
    <h2 class="mb-4 text-center">Register</h2>
    <?php if ($error): ?><div class="alert alert-danger"><?=$error?></div><?php endif; ?>
    <form method="post">
        <div class="mb-3">
            <label class="form-label">Name</label>
            <input type="text" class="form-control" name="name" value="<?=htmlspecialchars($name)?>" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Email</label>
            <input type="email" class="form-control" name="email" value="<?=htmlspecialchars($email)?>" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Phone</label>
            <input type="text" class="form-control" name="phone" value="<?=htmlspecialchars($phone)?>" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Password</label>
            <input type="password" class="form-control" name="password" required>
        </div>
        <div class="mb-3">
            <label class="form-label">School Type</label>
            <select class="form-select" name="school_type" required>
                <option value="University" <?=($school_type==='University'?'selected':'')?>>University</option>
                <option value="Secondary School" <?=($school_type==='Secondary School'?'selected':'')?>>Secondary School</option>
            </select>
        </div>
        <input type="hidden" name="role" value="user">
        <input type="hidden" name="banned" value="0">
        <button class="btn btn-primary w-100"><i class="fas fa-user-plus"></i> Register</button>
    </form>
    <div class="mt-3 text-center">
        Already have an account? <a href="login.php">Login</a>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
