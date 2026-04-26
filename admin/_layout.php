<?php
declare(strict_types=1);
require_once __DIR__ . '/../auth.php';

function h(string $s): string { return htmlspecialchars($s, ENT_QUOTES, 'UTF-8'); }

function admin_header(string $title, string $active = ''): void {
  $user = current_user();
  $who = $user ? ($user['username'] . ' · ' . $user['role']) : 'Guest';
  ?>
  <!doctype html>
  <html lang="en">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title><?= h($title) ?></title>
    <link rel="stylesheet" href="../assets/admin/admin.css" />
  </head>
  <body>
    <div class="container">
      <div class="topbar">
        <div class="brand">
          <strong>Fit Over 40 Foundations</strong>
          <span><?= h($who) ?></span>
        </div>
        <div class="nav">
          <a class="chip <?= $active === 'dashboard' ? 'is-active' : '' ?>" href="dashboard.php">Dashboard</a>
          <a class="chip <?= $active === 'users' ? 'is-active' : '' ?>" href="users.php">Users</a>
          <a class="chip danger" href="logout.php">Logout</a>
        </div>
      </div>
  <?php
}

function admin_footer(): void {
  ?>
      <div class="footer">© <?= date('Y') ?> Fit Over 40 Foundations</div>
    </div>
  </body>
  </html>
  <?php
}

