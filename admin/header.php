<?php
require_once __DIR__ . '/../includes/auth.php';
requireRole('admin');
$user = currentUser();
$current = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="th">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= isset($pageTitle) ? htmlspecialchars($pageTitle) . ' - Admin' : 'Admin' ?> - FirstStep</title>
<link rel="stylesheet" href="../assets/css/style.css?v=6">
</head>
<body>
<header class="site-header">
  <div class="container header-inner">
    <a href="index.php" class="logo">
      <span class="logo-icon">
        <svg width="30" height="30" viewBox="0 0 64 64" fill="none" xmlns="http://www.w3.org/2000/svg">
          <rect width="64" height="64" rx="14" fill="#16213e"/>
          <path d="M34 46 V38 H42 V30 H50 V22 H58 V46 Z" fill="#fff"/>
          <path d="M28 34 C28 28 28 24 28 19" stroke="#fff" stroke-width="2.6" stroke-linecap="round"/>
          <path d="M28 19c-7 -1 -12 -6 -11 -14 8 1 13 6 11 14z" fill="#fff"/>
          <path d="M28 19c7 -1 12 -6 11 -14 -8 1 -13 6 -11 14z" fill="#fff"/>
          <path d="M9 40c1.5 -4 5 -6.5 9 -6.5 2 0 3.7 0.8 5 2 1.1 -1.6 3 -2.6 5.2 -2.6 2.6 0 4.8 1.4 5.9 3.5 1 -0.7 2.2 -1.1 3.5 -1.1 3.3 0 6 2.7 6 6v4.7c0 2 -1.6 3.6 -3.6 3.6H13c-2.8 0 -5 -2.2 -5 -5 0 -1.7 0.4 -3.3 1 -4.7z" fill="#fff"/>
        </svg>
      </span> FirstStep <span style="color:var(--muted);font-weight:400;font-size:14px;">Admin</span>
    </a>
    <nav class="main-nav">
      <a href="index.php" class="<?= $current=='index.php'?'active':'' ?>">Dashboard</a>
      <a href="users.php" class="<?= $current=='users.php'?'active':'' ?>">Users</a>
      <a href="companies.php" class="<?= $current=='companies.php'?'active':'' ?>">Companies</a>
      <a href="jobs.php" class="<?= $current=='jobs.php'?'active':'' ?>">Jobs</a>
      <a href="articles.php" class="<?= $current=='articles.php'?'active':'' ?>">Articles</a>
    </nav>
    <div class="header-actions">
      <span class="hello">Hi, <?= htmlspecialchars($user['name']) ?></span>
      <a href="../logout.php" class="btn btn-primary">Log Out</a>
    </div>
  </div>
</header>
<main>
<div class="container section">
