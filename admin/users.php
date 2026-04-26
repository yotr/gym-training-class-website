<?php
declare(strict_types=1);
require_once __DIR__ . '/../auth.php';
require_admin();
require_once __DIR__ . '/_layout.php';

$pdo = db();

$flash = (string)($_GET['ok'] ?? '');
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $action = (string)($_POST['action'] ?? '');
  if ($action === 'create') {
    $username = trim((string)($_POST['username'] ?? ''));
    $password = (string)($_POST['password'] ?? '');
    $role = (string)($_POST['role'] ?? 'staff');
    $isActive = isset($_POST['is_active']) ? 1 : 0;

    if ($username === '' || $password === '') {
      $error = 'Username and password are required.';
    } elseif (!in_array($role, ['admin', 'staff'], true)) {
      $error = 'Invalid role.';
    } else {
      try {
        $stmt = $pdo->prepare('INSERT INTO users (username, password_hash, role, is_active) VALUES (?,?,?,?)');
        $stmt->execute([$username, password_hash($password, PASSWORD_BCRYPT), $role, $isActive]);
        header('Location: users.php?ok=User+created');
        exit;
      } catch (Throwable $e) {
        $error = 'Could not create user (maybe username already exists).';
      }
    }
  }
}

$users = $pdo->query('SELECT id, username, role, is_active, created_at FROM users ORDER BY created_at DESC')->fetchAll();

admin_header('Users · Admin', 'users');
?>

<div class="grid">
  <div class="card">
    <div class="card__head">
      <h1>Users</h1>
      <p>Create, update, and delete dashboard users.</p>
    </div>
    <div class="card__body">
      <?php if ($flash !== ''): ?>
        <div class="alert ok"><?= h($flash) ?></div>
        <div style="height:10px"></div>
      <?php endif; ?>
      <?php if ($error !== ''): ?>
        <div class="alert error"><?= h($error) ?></div>
        <div style="height:10px"></div>
      <?php endif; ?>

      <form method="post">
        <input type="hidden" name="action" value="create" />
        <div class="row">
          <div class="field">
            <label for="username">Username</label>
            <input id="username" name="username" required />
          </div>
          <div class="field">
            <label for="password">Password</label>
            <input id="password" name="password" type="password" required />
          </div>
          <div class="field">
            <label for="role">Role</label>
            <select id="role" name="role">
              <option value="staff">Staff</option>
              <option value="admin">Admin</option>
            </select>
          </div>
          <div class="field" style="min-width:220px">
            <label>Active</label>
            <div class="row" style="align-items:center; gap:10px">
              <input id="is_active" name="is_active" type="checkbox" checked style="width:18px; height:18px" />
              <label for="is_active" style="letter-spacing:.02em; text-transform:none; font-size:.95rem; color:var(--text); font-weight:800">Enabled</label>
            </div>
          </div>
          <div class="field" style="flex:0 0 auto; min-width:220px">
            <button class="btn" type="submit" style="width:100%">Add user</button>
          </div>
        </div>
      </form>
    </div>
  </div>

  <div class="card">
    <div class="card__head">
      <h2>All users</h2>
      <p><?= count($users) ?> user(s).</p>
    </div>
    <div class="card__body" style="padding:0">
      <div style="overflow:auto">
        <table>
          <thead>
            <tr>
              <th>ID</th>
              <th>Username</th>
              <th>Role</th>
              <th>Status</th>
              <th>Created</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($users as $u): ?>
              <tr>
                <td class="muted"><?= (int)$u['id'] ?></td>
                <td><strong><?= h((string)$u['username']) ?></strong></td>
                <td><span class="pill"><?= h((string)$u['role']) ?></span></td>
                <td>
                  <?php if ((int)$u['is_active'] === 1): ?>
                    <span class="pill ok">Active</span>
                  <?php else: ?>
                    <span class="pill no">Disabled</span>
                  <?php endif; ?>
                </td>
                <td class="muted">
                  <?php $dt = new DateTime((string)$u['created_at']); echo h($dt->format('d M Y')); ?>
                </td>
                <td>
                  <div class="actions">
                    <a class="btn secondary" href="users_edit.php?id=<?= (int)$u['id'] ?>" style="text-decoration:none">Edit</a>
                    <?php if ((int)$u['id'] !== (int)current_user()['id']): ?>
                      <a class="btn danger" href="users_delete.php?id=<?= (int)$u['id'] ?>" style="text-decoration:none">Delete</a>
                    <?php else: ?>
                      <button class="btn danger" type="button" disabled title="You can’t delete the logged in user">Delete</button>
                    <?php endif; ?>
                  </div>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>

<?php admin_footer(); ?>

