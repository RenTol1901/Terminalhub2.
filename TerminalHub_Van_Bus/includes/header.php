<?php
require_once __DIR__ . '/../config.php';
$title = $title ?? APP_NAME;
$user = $_SESSION['user'] ?? null;
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= e($title) ?> · <?= e(APP_NAME) ?></title>
  <link rel="stylesheet" href="<?= e(APP_BASE) ?>/assets/css/style.css">
  <script defer src="<?= e(APP_BASE) ?>/assets/js/app.js"></script>
</head>
<body>
<header class="topbar">
  <a class="brand" href="<?= e(APP_BASE) ?>/">Terminal<span>Hub</span></a>
  <nav>
    <?php if ($user): ?>
      <span class="welcome"><?= e($user['full_name']) ?> · <?= e(ucfirst($user['role'])) ?></span>
      <a href="<?= e(APP_BASE) ?>/<?= e(dashboard_for_role($user['role'])) ?>">Dashboard</a>
      <a class="nav-cta" href="<?= e(APP_BASE) ?>/logout.php">Log out</a>
    <?php else: ?>
      <a href="<?= e(APP_BASE) ?>/login.php">Log in</a>
      <a class="nav-cta" href="<?= e(APP_BASE) ?>/register.php">Passenger sign up</a>
    <?php endif; ?>
  </nav>
</header>
<main class="container">
