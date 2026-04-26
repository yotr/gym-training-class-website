<?php
declare(strict_types=1);
require_once __DIR__ . '/../auth.php';
require_login();

$ageSort = (string)($_GET['age_sort'] ?? 'newest'); // newest|oldest|age_asc|age_desc
$la7 = (string)($_GET['la7'] ?? 'all');            // all|yes|no
$training = (string)($_GET['training'] ?? 'all');  // all|beginner|returning|active
$medical = (string)($_GET['medical'] ?? 'all');    // all|yes|no

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

$stmt = db()->prepare("SELECT id, package, full_name, whatsapp, age, la7_member, training_background, medical_disclaimer_confirmed, created_at
                       FROM submissions
                       $whereSql
                       ORDER BY $orderBy
                       LIMIT 10000");
$stmt->execute($params);
$rows = $stmt->fetchAll();

$filename = 'submissions_' . date('Y-m-d_H-i') . '.csv';
header('Content-Type: text/csv; charset=UTF-8');
header('Content-Disposition: attachment; filename="' . $filename . '"');
header('Pragma: no-cache');
header('Expires: 0');

// UTF-8 BOM for Excel (Arabic-safe)
echo "\xEF\xBB\xBF";

$out = fopen('php://output', 'w');
fputcsv($out, ['ID', 'Name', 'WhatsApp', 'Age', 'LA7 member', 'Package', 'Training background', 'Medical disclaimer', 'Date']);

foreach ($rows as $r) {
  $dt = new DateTime((string)$r['created_at']);
  fputcsv($out, [
    (int)$r['id'],
    (string)$r['full_name'],
    (string)$r['whatsapp'],
    (int)$r['age'],
    (string)$r['la7_member'],
    (string)$r['package'],
    (string)$r['training_background'],
    ((int)$r['medical_disclaimer_confirmed'] === 1) ? 'Yes' : 'No',
    $dt->format('d M Y, h:i A'),
  ]);
}

fclose($out);
exit;

