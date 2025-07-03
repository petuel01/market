<?php
// ...existing code...
?>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
  <div class="container-fluid">
    <a class="navbar-brand" href="index.php"><i class="fas fa-book"></i> Campus Market</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav ms-auto">
        <?php if (isset($_SESSION['user_id'])): ?>
          <li class="nav-item"><a class="nav-link" href="index.php"><i class="fas fa-home"></i> Home</a></li>
          <li class="nav-item"><a class="nav-link" href="my_listings.php"><i class="fas fa-list"></i> My Listings</a></li>
          <li class="nav-item"><a class="nav-link" href="profile.php"><i class="fas fa-user"></i> Profile</a></li>
          <li class="nav-item position-relative">
            <a class="nav-link" href="my_swaps.php"><i class="fas fa-exchange-alt"></i> My Swaps
              <span id="swap-badge" class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-warning text-dark" style="display:none;">0</span>
            </a>
          </li>
          <li class="nav-item position-relative">
            <a class="nav-link" href="messages.php"><i class="fas fa-comments"></i> Messages
              <span id="msg-badge" class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="display:none;">0</span>
            </a>
          </li>
          <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
            <li class="nav-item"><a class="nav-link" href="admin_dashboard.php"><i class="fas fa-tools"></i> Admin</a></li>
          <?php endif; ?>
          <li class="nav-item"><a class="nav-link" href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
        <?php else: ?>
          <li class="nav-item"><a class="nav-link" href="index.php"><i class="fas fa-home"></i> Home</a></li>
          <li class="nav-item"><a class="nav-link" href="login.php"><i class="fas fa-sign-in-alt"></i> Login</a></li>
          <li class="nav-item"><a class="nav-link" href="register.php"><i class="fas fa-user-plus"></i> Register</a></li>
        <?php endif; ?>
      </ul>
    </div>
  </div>
</nav>
<?php if (isset($_SESSION['user_id'])): ?>
<script>
// AJAX to fetch unread message and swap counts
function updateMsgBadge() {
  var xhr = new XMLHttpRequest();
  xhr.open('GET', 'unread_count.php', true);
  xhr.onload = function() {
    if (xhr.status === 200) {
      var count = parseInt(xhr.responseText);
      var badge = document.getElementById('msg-badge');
      if (badge) {
        if (count > 0) {
          badge.textContent = count;
          badge.style.display = 'inline-block';
        } else {
          badge.style.display = 'none';
        }
      }
    }
  };
  xhr.send();
}
function updateSwapBadge() {
  var xhr = new XMLHttpRequest();
  xhr.open('GET', 'unread_swaps.php', true);
  xhr.onload = function() {
    if (xhr.status === 200) {
      var count = parseInt(xhr.responseText);
      var badge = document.getElementById('swap-badge');
      if (badge) {
        if (count > 0) {
          badge.textContent = count;
          badge.style.display = 'inline-block';
        } else {
          badge.style.display = 'none';
        }
      }
    }
  };
  xhr.send();
}
updateMsgBadge();
updateSwapBadge();
setInterval(updateMsgBadge, 5000);
setInterval(updateSwapBadge, 5000);
</script>
<?php endif; ?>
