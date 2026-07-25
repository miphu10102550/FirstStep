<?php
require_once __DIR__ . '/auth.php';
$user = currentUser();
$current = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="th">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= isset($pageTitle) ? htmlspecialchars($pageTitle) . ' - FirstStep' : 'FirstStep - Take Your First Step to the Perfect Career' ?></title>
<link rel="stylesheet" href="<?= isset($base) ? $base : '' ?>assets/css/style.css?v=6">
</head>
<body class="<?= isset($bodyClass) ? htmlspecialchars($bodyClass) : '' ?>">
<header class="site-header">
  <div class="container header-inner">
    <a href="<?= isset($base) ? $base : '' ?>index.php" class="logo">
      <span class="logo-icon">
        <svg width="36" height="36" viewBox="0 0 64 64" fill="none" xmlns="http://www.w3.org/2000/svg">
          <rect width="64" height="64" rx="14" fill="#16213e"/>
          <!-- ascending stair steps -->
          <path d="M34 46 V38 H42 V30 H50 V22 H58 V46 Z" fill="#fff"/>
          <!-- sprouting plant -->
          <path d="M28 34 C28 28 28 24 28 19" stroke="#fff" stroke-width="2.6" stroke-linecap="round"/>
          <path d="M28 19c-7 -1 -12 -6 -11 -14 8 1 13 6 11 14z" fill="#fff"/>
          <path d="M28 19c7 -1 12 -6 11 -14 -8 1 -13 6 -11 14z" fill="#fff"/>
          <!-- open hand cupping the plant -->
          <path d="M9 40c1.5 -4 5 -6.5 9 -6.5 2 0 3.7 0.8 5 2 1.1 -1.6 3 -2.6 5.2 -2.6 2.6 0 4.8 1.4 5.9 3.5 1 -0.7 2.2 -1.1 3.5 -1.1 3.3 0 6 2.7 6 6v4.7c0 2 -1.6 3.6 -3.6 3.6H13c-2.8 0 -5 -2.2 -5 -5 0 -1.7 0.4 -3.3 1 -4.7z" fill="#fff"/>
        </svg>
      </span> FirstStep
    </a>
    <nav class="main-nav">
      <a href="<?= isset($base) ? $base : '' ?>index.php" class="<?= $current=='index.php'?'active':'' ?>">Home</a>
      <a href="<?= isset($base) ? $base : '' ?>jobs.php" class="<?= $current=='jobs.php'?'active':'' ?>">Find Jobs</a>
      <a href="<?= isset($base) ? $base : '' ?>companies.php" class="<?= $current=='companies.php'?'active':'' ?>">Companies</a>
      <a href="<?= isset($base) ? $base : '' ?>tips.php" class="<?= $current=='tips.php'?'active':'' ?>">Tips &amp; Insights</a>
      <a href="<?= isset($base) ? $base : '' ?>employers.php" class="<?= $current=='employers.php'?'active':'' ?>">For Employers</a>
    </nav>
    <div class="header-actions">
      <?php if ($user): ?>
        <div class="user-menu">
          <span class="hello">Hi, <?= htmlspecialchars($user['name']) ?></span>
          <?php if ($user['role']=='jobseeker'): ?>
            <a href="<?= isset($base) ? $base : '' ?>dashboard-jobseeker.php" class="btn btn-outline">Dashboard</a>
          <?php elseif ($user['role']=='employer'): ?>
            <a href="<?= isset($base) ? $base : '' ?>dashboard-employer.php" class="btn btn-outline">Dashboard</a>
          <?php elseif ($user['role']=='admin'): ?>
            <a href="<?= isset($base) ? $base : '' ?>admin/index.php" class="btn btn-outline">Admin</a>
          <?php endif; ?>
          <a href="<?= isset($base) ? $base : '' ?>logout.php" class="btn btn-primary">Log Out</a>
        </div>
      <?php else: ?>
        <a href="<?= isset($base) ? $base : '' ?>login.php" class="btn btn-login">Log In</a>
      <?php endif; ?>
    </div>
  </div>
</header>
<main>
