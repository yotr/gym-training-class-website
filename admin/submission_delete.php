<?php
declare(strict_types=1);
require_once __DIR__ . '/../auth.php';
require_login();
require_once __DIR__ . '/_layout.php';

$id = (int)($_GET['id'] ?? 0);
if ($id <= 0) { header('Location: dashboard.php'); exit; }

$pdo = db();
$stmt = $pdo->prepare('SELECT id, full_name, whatsapp, created_at FROM submissions WHERE id = ?');
$stmt->execute([$id]);
$row = $stmt->fetch();
if (!$row) { header('Location: dashboard.php'); exit; }

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $pdo->prepare('DELETE FROM submissions WHERE id = ?')->execute([$id]);
  header('Location: dashboard.php?ok=Submission+deleted');
  exit;
}

admin_header('Delete submission · Admin', 'dashboard');
?>

<div class="grid">
  <div class="card">
    <div class="card__head">
      <h1>Delete submission</h1>
      <p>This action can’t be undone.</p>
    </div>
    <div class="card__body">
      <div class="alert error">
        Delete submission <strong>#<?= (int)$row['id'] ?></strong> for
        <strong><?= h((string)$row['full_name']) ?></strong>
        (<?= h((string)$row['whatsapp']) ?>)?
      </div>
      <div style="height:12px"></div>
      <form method="post">
        <div class="actions">
          <button class="btn danger" type="submit">Yes, delete</button>
          <a class="btn secondary" href="dashboard.php" style="text-decoration:none">Cancel</a>
        </div>
      </form>
    </div>
  </div>
</div>

<?php admin_footer(); ?>

