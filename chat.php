<?php
require_once 'config.php';
if (!isset($_SESSION['user_id']) || !isset($_GET['user_id'])) { header('Location: login.php'); exit; }
$other_id = intval($_GET['user_id']);
if ($other_id == $_SESSION['user_id']) { header('Location: index.php'); exit; }
$stmt = $pdo->prepare('SELECT * FROM users WHERE id = ?');
$stmt->execute([$other_id]);
$other = $stmt->fetch();
if (!$other) { header('Location: index.php'); exit; }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chat with <?=htmlspecialchars($other['name'])?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        #chat-box { height: 400px; overflow-y: auto; background: #fff; border: 1px solid #ddd; padding: 1rem; }
        .msg-me { text-align: right; }
        .msg-other { text-align: left; }
    </style>
</head>
<body>
<?php include 'navbar.php'; ?>
<div class="container mt-4" style="max-width: 600px;">
    <h3 class="mb-3">Chat with <?=htmlspecialchars($other['name'])?></h3>
    <div class="card shadow-sm mb-3">
        <div class="card-header bg-primary text-white d-flex align-items-center">
            <i class="fas fa-comments me-2"></i>
            <span>Conversation</span>
            <span id="msg-count" class="badge bg-warning text-dark ms-auto"></span>
        </div>
        <div id="chat-box" class="card-body p-3" style="height:400px; overflow-y:auto; background:#f8f9fa;"></div>
        <div class="card-footer bg-light">
            <form id="chat-form" class="input-group">
                <input type="hidden" id="receiver_id" value="<?=$other_id?>">
                <input type="text" id="message" class="form-control" placeholder="Type your message..." autocomplete="off">
                <button class="btn btn-primary" type="submit"><i class="fas fa-paper-plane"></i></button>
            </form>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
let msgCount = 0;
function fetchMessages() {
    var xhr = new XMLHttpRequest();
    xhr.open('GET', 'fetch_messages.php?user_id='+encodeURIComponent(<?=$other_id?>), true);
    xhr.onload = function() {
        if (xhr.status === 200) {
            var data = JSON.parse(xhr.responseText);
            var box = document.getElementById('chat-box');
            var count = data.length;
            msgCount = count;
            document.getElementById('msg-count').textContent = count + ' messages';
            box.innerHTML = '';
            data.forEach(function(msg) {
                var div = document.createElement('div');
                div.className = (msg.sender_id == <?= $_SESSION['user_id'] ?>) ? 'msg-me mb-2' : 'msg-other mb-2';
                div.innerHTML = '<span class="badge bg-' + ((msg.sender_id == <?= $_SESSION['user_id'] ?>) ? 'primary' : 'secondary') + '">' + msg.message + '</span><br><small class="text-muted">' + msg.time + '</small>';
                box.appendChild(div);
            });
            box.scrollTop = box.scrollHeight;
        }
    };
    xhr.send();
}
fetchMessages();
setInterval(fetchMessages, 2000);
document.getElementById('chat-form').onsubmit = function(e) {
    e.preventDefault();
    var msg = document.getElementById('message').value.trim();
    if (!msg) return;
    var xhr = new XMLHttpRequest();
    xhr.open('POST', 'send_message.php', true);
    xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
    xhr.onload = function() {
        if (xhr.status === 200) {
            document.getElementById('message').value = '';
            fetchMessages();
        }
    };
    xhr.send('receiver_id='+encodeURIComponent(<?=$other_id?>)+'&message='+encodeURIComponent(msg));
};
</script>
</body>
</html>
