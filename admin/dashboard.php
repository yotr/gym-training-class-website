<?php
declare(strict_types=1);
require_once __DIR__ . '/../auth.php';
require_login();
require_once __DIR__ . '/_layout.php';

$ageSort = (string)($_GET['age_sort'] ?? 'newest'); // newest|oldest|age_asc|age_desc
$la7 = (string)($_GET['la7'] ?? 'all');            // all|yes|no
$training = (string)($_GET['training'] ?? 'all');  // all|beginner|returning|active
$medical = (string)($_GET['medical'] ?? 'all');    // all|yes|no

$exportQuery = http_build_query([
  'age_sort' => $ageSort,
  'la7' => $la7,
  'training' => $training,
  'medical' => $medical,
]);

$flash = (string)($_GET['ok'] ?? '');

$where = [];
$params = [];

if ($la7 === 'yes') {
  $where[] = 'la7_member = ?';
  $params[] = 'yes';
} elseif ($la7 === 'no') {
  $where[] = 'la7_member = ?';
  $params[] = 'no';
}

if (in_array($training, ['beginner', 'returning', 'active'], true)) {
  $where[] = 'training_background = ?';
  $params[] = $training;
}

if ($medical === 'yes') {
  $where[] = 'medical_disclaimer_confirmed = 1';
} elseif ($medical === 'no') {
  $where[] = 'medical_disclaimer_confirmed = 0';
}

$orderBy = 'created_at DESC';
if ($ageSort === 'oldest') $orderBy = 'created_at ASC';
if ($ageSort === 'age_asc') $orderBy = 'age ASC, created_at DESC';
if ($ageSort === 'age_desc') $orderBy = 'age DESC, created_at DESC';

$whereSql = $where ? ('WHERE ' . implode(' AND ', $where)) : '';

$pdo = db();
$stmt = $pdo->prepare("SELECT id, package, full_name, whatsapp, age, la7_member, training_background, medical_disclaimer_confirmed, created_at
                       FROM submissions
                       $whereSql
                       ORDER BY $orderBy
                       LIMIT 1000");
$stmt->execute($params);
$rows = $stmt->fetchAll();

admin_header('Dashboard · Submissions', 'dashboard');
?>

<div class="grid">
  <div class="card">
    <div class="card__head">
      <h1>Submissions</h1>
      <p>Filter by LA7 membership, training background, medical disclaimer, and age sorting.</p>
    </div>
    <div class="card__body">
      <?php if ($flash !== ''): ?>
        <div class="alert ok"><?= h($flash) ?></div>
        <div style="height:10px"></div>
      <?php endif; ?>
      <form method="get">
        <div class="row">
          <div class="field">
            <label for="la7">LA7 member</label>
            <select id="la7" name="la7">
              <option value="all" <?= $la7 === 'all' ? 'selected' : '' ?>>All</option>
              <option value="yes" <?= $la7 === 'yes' ? 'selected' : '' ?>>Yes</option>
              <option value="no" <?= $la7 === 'no' ? 'selected' : '' ?>>No</option>
            </select>
          </div>

          <div class="field">
            <label for="training">Training background</label>
            <select id="training" name="training">
              <option value="all" <?= $training === 'all' ? 'selected' : '' ?>>All</option>
              <option value="beginner" <?= $training === 'beginner' ? 'selected' : '' ?>>Beginner</option>
              <option value="returning" <?= $training === 'returning' ? 'selected' : '' ?>>Returning</option>
              <option value="active" <?= $training === 'active' ? 'selected' : '' ?>>Currently active</option>
            </select>
          </div>

          <div class="field">
            <label for="medical">Medical disclaimer</label>
            <select id="medical" name="medical">
              <option value="all" <?= $medical === 'all' ? 'selected' : '' ?>>All</option>
              <option value="yes" <?= $medical === 'yes' ? 'selected' : '' ?>>Yes</option>
              <option value="no" <?= $medical === 'no' ? 'selected' : '' ?>>No</option>
            </select>
          </div>

          <div class="field">
            <label for="age_sort">Sort</label>
            <select id="age_sort" name="age_sort">
              <option value="newest" <?= $ageSort === 'newest' ? 'selected' : '' ?>>Newest</option>
              <option value="oldest" <?= $ageSort === 'oldest' ? 'selected' : '' ?>>Oldest</option>
              <option value="age_desc" <?= $ageSort === 'age_desc' ? 'selected' : '' ?>>Age: older → younger</option>
              <option value="age_asc" <?= $ageSort === 'age_asc' ? 'selected' : '' ?>>Age: younger → older</option>
            </select>
          </div>

          <div class="field" style="flex:0 0 auto; min-width:220px">
            <button class="btn" type="submit" style="width:100%">Apply filters</button>
            <div style="height:8px"></div>
            <a class="btn secondary" style="width:100%; text-decoration:none" href="dashboard.php">Reset</a>
            <div style="height:8px"></div>
            <a class="btn secondary" style="width:100%; text-decoration:none" href="export.php?<?= h($exportQuery) ?>">Export Excel (CSV)</a>
          </div>
        </div>
      </form>
    </div>
  </div>

  <div class="card">
    <div class="card__head">
      <h2>Results</h2>
      <p><?= count($rows) ?> row(s) shown (max 1000).</p>
    </div>
    <div class="card__body" style="padding:0">
      <div style="overflow:auto">
        <table>
          <thead>
            <tr>
              <th>ID</th>
              <th>Name</th>
              <th>WhatsApp</th>
              <th>Age</th>
              <th>LA7</th>
              <th>Package</th>
              <th>Training</th>
              <th>Medical</th>
              <th>Date</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
          <?php if (!$rows): ?>
            <tr><td colspan="10" class="muted" style="padding:16px">No results for the selected filters.</td></tr>
          <?php else: ?>
            <?php foreach ($rows as $r): ?>
              <tr>
                <td class="muted"><?= (int)$r['id'] ?></td>
                <td><strong><?= h((string)$r['full_name']) ?></strong></td>
                <td><?= h((string)$r['whatsapp']) ?></td>
                <td><?= (int)$r['age'] ?></td>
                <td class="muted"><?= h((string)$r['la7_member']) ?></td>
                <td><span class="pill"><?= h((string)$r['package']) ?></span></td>
                <td class="muted"><?= h((string)$r['training_background']) ?></td>
                <td>
                  <?php if ((int)$r['medical_disclaimer_confirmed'] === 1): ?>
                    <span class="pill ok">Yes</span>
                  <?php else: ?>
                    <span class="pill no">No</span>
                  <?php endif; ?>
                </td>
                <td class="muted">
                  <?php
                    $dt = new DateTime((string)$r['created_at']);
                    echo h($dt->format('d M Y, h:i A'));
                  ?>
                </td>
                <td>
                  <div class="actions">
                    <a class="btn danger" href="submission_delete.php?id=<?= (int)$r['id'] ?>" style="text-decoration:none">Delete</a>
                  </div>
                </td>
              </tr>
            <?php endforeach; ?>
          <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>

<?php admin_footer(); ?>

