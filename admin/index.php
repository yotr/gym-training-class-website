<?php
declare(strict_types=1);
require_once __DIR__ . '/../auth.php';
ensure_session();
if (empty($_SESSION['user'])) {
  header('Location: login.php');
  exit;
}
header('Location: dashboard.php');
exit;

