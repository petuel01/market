<?php
require_once 'config.php';
if (!isset($_SESSION['pending_user_id'])) {
    header('Location: register.php'); exit;
}
$user_id = $_SESSION['pending_user_id'];
$email = $_SESSION['pending_email'];
$otp = $_SESSION['pending_otp'];
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['resend_otp'])) {
        // Generate and send a new OTP
        $otp = rand(100000, 999999);
        $expires = date('Y-m-d H:i:s', time() + 600);
        $pdo->prepare('INSERT INTO verification_codes (user_id, code, expires_at) VALUES (?, ?, ?)')
            ->execute([$user_id, $otp, $expires]);
        $_SESSION['pending_otp'] = $otp;
        // Send OTP email
        require_once 'mail_helper.php';
        $subject = 'Your Campus Market OTP Code';
        $body_html = '<p>Hello,</p>' .
            '<p>Your new OTP code is: <b>' . $otp . '</b></p>' .
            '<p>This code will expire in 10 minutes.</p>' .
            '<p>Thank you,<br>Campus Market Team</p>';
        send_email($email, '', $subject, $body_html);
        $error = '<span style="color:green;">A new OTP has been sent to your email.</span>';
    } else {
        $input_otp = trim($_POST['otp']);
        $stmt = $pdo->prepare('SELECT code, expires_at FROM verification_codes WHERE user_id = ? ORDER BY id DESC LIMIT 1');
        $stmt->execute([$user_id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($row && $row['code'] === $input_otp && strtotime($row['expires_at']) > time()) {
            $pdo->prepare('UPDATE users SET is_verified = 1 WHERE id = ?')->execute([$user_id]);
            unset($_SESSION['pending_user_id'], $_SESSION['pending_email'], $_SESSION['pending_otp']);
            $_SESSION['success'] = 'Account verified! You can now login.';
            header('Location: login.php'); exit;
        } else {
            $error = 'Invalid or expired OTP.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify OTP</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
<?php include 'navbar.php'; ?>
<div class="container mt-5" style="max-width: 400px;">
    <h2 class="mb-4 text-center">Verify OTP</h2>
    <!-- OTP is sent to email, not shown here for security -->
    <div class="alert alert-info">An OTP has been sent to your email: <b><?=htmlspecialchars($email)?></b></div>
    <div class="mb-2 text-center">
        <form method="post" style="display:inline;">
            <input type="hidden" name="resend_otp" value="1">
            <button type="submit" class="btn btn-link p-0" style="color:#2541b2;">Resend OTP</button>
        </form>
    </div>
    <?php if ($error): ?><div class="alert alert-danger"><?=$error?></div><?php endif; ?>
    <form method="post">
        <div class="mb-3">
            <label class="form-label">Enter OTP sent to your email/phone</label>
            <input type="text" class="form-control" name="otp" required>
        </div>
        <button class="btn btn-success w-100"><i class="fas fa-check"></i> Verify</button>
    </form>
</div>
</body>
</html>
