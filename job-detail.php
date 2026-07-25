<?php
$pageTitle = "Job Detail";
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/auth.php';

$id = (int)($_GET['id'] ?? 0);
$stmt = $pdo->prepare("SELECT jobs.*, companies.name AS company_name, companies.id AS cid, companies.description AS company_desc
                        FROM jobs JOIN companies ON jobs.company_id = companies.id WHERE jobs.id = ?");
$stmt->execute([$id]);
$job = $stmt->fetch();

if (!$job) { header('Location: jobs.php'); exit; }

$user = currentUser();
$alreadyApplied = false;
if ($user && $user['role'] === 'jobseeker') {
    $chk = $pdo->prepare("SELECT id FROM applications WHERE job_id=? AND jobseeker_id=?");
    $chk->execute([$id, $user['id']]);
    $alreadyApplied = (bool)$chk->fetch();
}

require_once __DIR__ . '/includes/header.php';
?>
<div class="page-header">
  <div class="container">
    <h1><?= htmlspecialchars($job['title']) ?></h1>
    <p><?= htmlspecialchars($job['company_name']) ?> · <?= htmlspecialchars($job['location']) ?></p>
  </div>
</div>

<div class="container section">
  <div class="two-col">
    <div>
      <div class="card" style="margin-bottom:20px;">
        <span class="tag"><?= htmlspecialchars($job['job_type']) ?></span>
        <span class="tag"><?= htmlspecialchars($job['category']) ?></span>
        <h3>Job Description</h3>
        <p><?= nl2br(htmlspecialchars($job['description'])) ?></p>
        <h3>Requirements</h3>
        <p><?= nl2br(htmlspecialchars($job['requirements'])) ?></p>
        <h3>About <?= htmlspecialchars($job['company_name']) ?></h3>
        <p><?= nl2br(htmlspecialchars($job['company_desc'])) ?></p>
      </div>
    </div>
    <aside class="filter-box">
      <div class="job-meta salary" style="font-size:20px;margin-bottom:14px;">฿<?= number_format($job['salary_min']) ?> - ฿<?= number_format($job['salary_max']) ?></div>

      <?php if (!$user): ?>
        <a href="login.php" class="btn btn-primary btn-block">Log In to Apply</a>
      <?php elseif ($user['role'] !== 'jobseeker'): ?>
        <p style="color:var(--muted);font-size:14px;">Only job seeker accounts can apply for jobs.</p>
      <?php elseif ($alreadyApplied): ?>
        <p class="alert alert-success">You already applied to this job.</p>
      <?php elseif ($job['status'] !== 'open'): ?>
        <p style="color:var(--muted);">This job is no longer accepting applications.</p>
      <?php else: ?>
        <a href="apply.php?job_id=<?= $job['id'] ?>" class="btn btn-primary btn-block">Apply Now</a>
      <?php endif; ?>

      <a href="company-detail.php?id=<?= $job['cid'] ?>" class="btn btn-outline btn-block" style="margin-top:10px;">View Company</a>
    </aside>
  </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
