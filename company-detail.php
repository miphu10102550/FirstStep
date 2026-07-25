<?php
$pageTitle = "Company";
require_once __DIR__ . '/config/database.php';

$id = (int)($_GET['id'] ?? 0);
$stmt = $pdo->prepare("SELECT * FROM companies WHERE id=?");
$stmt->execute([$id]);
$company = $stmt->fetch();
if (!$company) { header('Location: companies.php'); exit; }

$jobsStmt = $pdo->prepare("SELECT * FROM jobs WHERE company_id=? AND status='open' ORDER BY created_at DESC");
$jobsStmt->execute([$id]);
$jobs = $jobsStmt->fetchAll();

require_once __DIR__ . '/includes/header.php';
?>
<div class="page-header">
  <div class="container">
    <h1><?= htmlspecialchars($company['name']) ?></h1>
    <p><?= htmlspecialchars($company['location']) ?><?= $company['website'] ? ' · ' . htmlspecialchars($company['website']) : '' ?></p>
  </div>
</div>

<div class="container section">
  <div class="card" style="margin-bottom:30px;">
    <h3>About the Company</h3>
    <p><?= nl2br(htmlspecialchars($company['description'])) ?></p>
  </div>

  <h3>Open Positions</h3>
  <div class="grid grid-3">
    <?php foreach ($jobs as $job): ?>
    <a href="job-detail.php?id=<?= $job['id'] ?>" class="card job-card">
      <div class="job-title"><?= htmlspecialchars($job['title']) ?></div>
      <div class="company"><?= htmlspecialchars($job['location']) ?></div>
      <span class="tag"><?= htmlspecialchars($job['job_type']) ?></span>
      <div class="job-meta salary">฿<?= number_format($job['salary_min']) ?> - ฿<?= number_format($job['salary_max']) ?></div>
    </a>
    <?php endforeach; ?>
    <?php if (empty($jobs)): ?>
      <p class="empty-state">No open positions right now.</p>
    <?php endif; ?>
  </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
