<?php
declare(strict_types=1);
require_once __DIR__ . '/../auth.php';
require_admin();
require_once __DIR__ . '/_layout.php';

$id = (int)($_GET['id'] ?? 0);
if ($id <= 0) { header('Location: users.php'); exit; }

if ($id === (int)current_user()['id']) {
  header('Location: users.php?ok=You+cannot+delete+your+own+user');
  exit;
}

$pdo = db();
$stmt = $pdo->prepare('SELECT id, username, role FROM users WHERE id = ?');
$stmt->execute([$id]);
$u = $stmt->fetch();
if (!$u) { header('Location: users.php'); exit; }

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $pdo->prepare('DELETE FROM users WHERE id = ?')->execute([$id]);
  header('Location: users.php?ok=User+deleted');
  exit;
}

admin_header('Delete user · Admin', 'users');
?>

<div class="grid">
  <div class="card">
    <div class="card__head">
      <h1>Delete user</h1>
      <p>This action can’t be undone.</p>
    </div>
    <div class="card__body">
      <div class="alert error">
        You are about to delete <strong><?= h((string)$u['username']) ?></strong>.
      </div>
      <div style="height:12px"></div>
      <form method="post">
        <div class="actions">
          <button class="btn danger" type="submit">Yes, delete</button>
          <a class="btn secondary" href="users.php" style="text-decoration:none">Cancel</a>
        </div>
      </form>
    </div>
  </div>
</div>

<?php admin_footer(); ?>

