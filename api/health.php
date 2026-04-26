<?php
declare(strict_types=1);
require_once __DIR__ . '/../config.php';

header('Content-Type: application/json; charset=utf-8');

try {
  $pdo = db();
  $v = $pdo->query('SELECT VERSION() AS v')->fetch();
  $db = $pdo->query('SELECT DATABASE() AS d')->fetch();
  echo json_encode([
    'ok' => true,
    'php' => PHP_VERSION,
    'pdo_mysql' => extension_loaded('pdo_mysql'),
    'db' => $db['d'] ?? null,
    'mysql_version' => $v['v'] ?? null,
  ]);
} catch (Throwable $e) {
  http_response_code(500);
  echo json_encode([
    'ok' => false,
    'pdo_mysql' => extension_loaded('pdo_mysql'),
    'error' => $e->getMessage(),
  ]);
}

