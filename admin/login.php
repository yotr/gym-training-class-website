<?php
declare(strict_types=1);
require_once __DIR__ . '/../auth.php';
ensure_session();

if (!empty($_SESSION['user'])) {
  header('Location: dashboard.php');
  exit;
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $username = trim((string)($_POST['username'] ?? ''));
  $password = (string)($_POST['password'] ?? '');

  if ($username === '' || $password === '') {
    $error = 'Enter your username and password.';
  } else {
    $stmt = db()->prepare('SELECT id, username, password_hash, role, is_active FROM users WHERE username = ? LIMIT 1');
    $stmt->execute([$username]);
    $u = $stmt->fetch();

    if (!$u || !(int)$u['is_active'] || !password_verify($password, (string)$u['password_hash'])) {
      $error = 'Invalid credentials.';
    } else {
      session_regenerate_id(true);
      $_SESSION['user'] = [
        'id' => (int)$u['id'],
        'username' => (string)$u['username'],
        'role' => (string)$u['role'],
      ];
      header('Location: dashboard.php');
      exit;
    }
  }
}
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Login · Fit Over 40</title>
  <link rel="stylesheet" href="../assets/admin/admin.css" />
</head>
<body>
  <div class="login-wrap">
    <div class="card login-card">
      <div class="card__body">
        <div class="login-title">
          <strong>Welcome back</strong>
          <span>Sign in to the Fit Over 40 admin dashboard.</span>
        </div>

        <?php if ($error !== ''): ?>
          <div class="alert error"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></div>
          <div style="height:10px"></div>
        <?php endif; ?>

        <form method="post" autocomplete="on">
          <div class="row">
            <div class="field" style="min-width:100%">
              <label for="username">Username</label>
              <input id="username" name="username" required autocomplete="username" />
            </div>
            <div class="field" style="min-width:100%">
              <label for="password">Password</label>
              <input id="password" name="password" type="password" required autocomplete="current-password" />
            </div>
          </div>
          <div style="height:12px"></div>
          <button class="btn" type="submit" style="width:100%">Login</button>
        </form>
      </div>
    </div>
  </div>
</body>
</html>

