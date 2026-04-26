<?php
declare(strict_types=1);
require_once __DIR__ . '/../auth.php';
require_admin();
require_once __DIR__ . '/_layout.php';

$id = (int)($_GET['id'] ?? 0);
if ($id <= 0) { header('Location: users.php'); exit; }

$pdo = db();
$stmt = $pdo->prepare('SELECT id, username, role, is_active FROM users WHERE id = ?');
$stmt->execute([$id]);
$user = $stmt->fetch();
if (!$user) { header('Location: users.php'); exit; }

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $username = trim((string)($_POST['username'] ?? ''));
  $role = (string)($_POST['role'] ?? 'staff');
  $isActive = isset($_POST['is_active']) ? 1 : 0;
  $newPassword = (string)($_POST['new_password'] ?? '');

  if ($username === '') {
    $error = 'Username is required.';
  } elseif (!in_array($role, ['admin', 'staff'], true)) {
    $error = 'Invalid role.';
  } else {
    try {
      if ($newPassword !== '') {
        $stmt = $pdo->prepare('UPDATE users SET username=?, role=?, is_active=?, password_hash=? WHERE id=?');
        $stmt->execute([$username, $role, $isActive, password_hash($newPassword, PASSWORD_BCRYPT), $id]);
      } else {
        $stmt = $pdo->prepare('UPDATE users SET username=?, role=?, is_active=? WHERE id=?');
        $stmt->execute([$username, $role, $isActive, $id]);
      }
      header('Location: users.php?ok=User+updated');
      exit;
    } catch (Throwable $e) {
      $error = 'Could not update user (maybe username already exists).';
    }
  }
}

admin_header('Edit user · Admin', 'users');
?>

<div class="grid">
  <div class="card">
    <div class="card__head">
      <h1>Edit user</h1>
      <p>Update username, role, status, and optionally reset the password.</p>
    </div>
    <div class="card__body">
      <?php if ($error !== ''): ?>
        <div class="alert error"><?= h($error) ?></div>
        <div style="height:10px"></div>
      <?php endif; ?>

      <form method="post">
        <div class="row">
          <div class="field">
            <label for="username">Username</label>
            <input id="username" name="username" value="<?= h((string)$user['username']) ?>" required />
          </div>

          <div class="field">
            <label for="role">Role</label>
            <select id="role" name="role">
              <option value="staff" <?= (string)$user['role'] === 'staff' ? 'selected' : '' ?>>Staff</option>
              <option value="admin" <?= (string)$user['role'] === 'admin' ? 'selected' : '' ?>>Admin</option>
            </select>
          </div>

          <div class="field" style="min-width:220px">
            <label>Active</label>
            <div class="row" style="align-items:center; gap:10px">
              <input id="is_active" name="is_active" type="checkbox" <?= (int)$user['is_active'] === 1 ? 'checked' : '' ?> style="width:18px; height:18px" />
              <label for="is_active" style="letter-spacing:.02em; text-transform:none; font-size:.95rem; color:var(--text); font-weight:800">Enabled</label>
            </div>
          </div>
        </div>

        <div style="height:10px"></div>

        <div class="row">
          <div class="field">
            <label for="new_password">New password (optional)</label>
            <input id="new_password" name="new_password" type="password" placeholder="Leave empty to keep current password" />
          </div>
          <div class="field" style="flex:0 0 auto; min-width:220px">
            <button class="btn" type="submit" style="width:100%">Save changes</button>
            <div style="height:8px"></div>
            <a class="btn secondary" href="users.php" style="width:100%; text-decoration:none">Back</a>
          </div>
        </div>
      </form>
    </div>
  </div>
</div>

<?php admin_footer(); ?>

