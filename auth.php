<?php
declare(strict_types=1);

require_once __DIR__ . '/config.php';

function ensure_session(): void {
  if (session_status() === PHP_SESSION_NONE) {
    session_start();
  }
}

function require_login(): void {
  ensure_session();
  if (empty($_SESSION['user'])) {
    header('Location: /admin/login.php');
    exit;
  }
}

function require_admin(): void {
  require_login();
  if (($_SESSION['user']['role'] ?? '') !== 'admin') {
    http_response_code(403);
    echo 'Forbidden';
    exit;
  }
}

function current_user(): ?array {
  ensure_session();
  return $_SESSION['user'] ?? null;
}

