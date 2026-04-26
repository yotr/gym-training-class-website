<?php
declare(strict_types=1);
require_once __DIR__ . '/../config.php';

header('Content-Type: application/json; charset=utf-8');

set_exception_handler(function (Throwable $e): void {
  http_response_code(500);
  $msg = 'Server error. Please try again.';
  // Log full details server-side
  error_log('[submit.php] ' . $e->getMessage() . ' in ' . $e->getFile() . ':' . $e->getLine());
  echo json_encode(['ok' => false, 'error' => $msg]);
  exit;
});

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  http_response_code(405);
  echo json_encode(['ok' => false, 'error' => 'Method not allowed']);
  exit;
}

$raw = file_get_contents('php://input') ?: '';
$data = json_decode($raw, true);
if (!is_array($data)) {
  http_response_code(400);
  echo json_encode(['ok' => false, 'error' => 'Invalid JSON']);
  exit;
}

$package = (string)($data['package'] ?? '');
$fullName = trim((string)($data['fullName'] ?? ''));
$whatsapp = trim((string)($data['whatsapp'] ?? ''));
$age = (int)($data['age'] ?? 0);
$la7Member = (string)($data['la7Member'] ?? '');
$trainingBackground = (string)($data['trainingBackground'] ?? '');
$medicalDisclaimer = !empty($data['medicalDisclaimer']) ? 1 : 0;
$paymentConfirmed = 0;

if (!in_array($package, ['member', 'nonmember'], true)) {
  http_response_code(422);
  echo json_encode(['ok' => false, 'error' => 'Invalid package']);
  exit;
}
if ($fullName === '' || mb_strlen($fullName) < 2) {
  http_response_code(422);
  echo json_encode(['ok' => false, 'error' => 'Invalid name']);
  exit;
}
$digits = preg_replace('/\D+/', '', $whatsapp);
if ($digits === null || strlen($digits) < 9) {
  http_response_code(422);
  echo json_encode(['ok' => false, 'error' => 'Invalid WhatsApp number']);
  exit;
}
if ($age < 16 || $age > 100) {
  http_response_code(422);
  echo json_encode(['ok' => false, 'error' => 'Invalid age']);
  exit;
}

if (!in_array($la7Member, ['yes', 'no'], true)) {
  http_response_code(422);
  echo json_encode(['ok' => false, 'error' => 'Invalid LA7 member answer']);
  exit;
}

if (!in_array($trainingBackground, ['beginner', 'returning', 'active'], true)) {
  http_response_code(422);
  echo json_encode(['ok' => false, 'error' => 'Invalid training background']);
  exit;
}

$pdo = db();
$stmt = $pdo->prepare('INSERT INTO submissions (package, full_name, whatsapp, age, la7_member, training_background, medical_disclaimer_confirmed, payment_confirmed)
                       VALUES (?,?,?,?,?,?,?,?)');
$stmt->execute([$package, $fullName, $digits, $age, $la7Member, $trainingBackground, $medicalDisclaimer, $paymentConfirmed]);

echo json_encode(['ok' => true, 'id' => (int)$pdo->lastInsertId()]);

