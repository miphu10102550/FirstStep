<?php
$pageTitle = "My Dashboard";
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/auth.php';
requireRole('jobseeker');
$user = currentUser();

$apps = $pdo->prepare("
  SELECT applications.*, jobs.title AS job_title, companies.name AS company_name
  FROM applications
  JOIN jobs ON applications.job_id = jobs.id
  JOIN companies ON jobs.company_id = companies.id
  WHERE applications.jobseeker_id = ?
  ORDER BY applications.applied_at DESC
");
$apps->execute([$user['id']]);
$applications = $apps->fetchAll();

$stats = ['pending'=>0,'reviewed'=>0,'accepted'=>0,'rejected'=>0];
foreach ($applications as $a) { $stats[$a['status']] = ($stats[$a['status']] ?? 0) + 1; }

require_once __DIR__ . '/includes/header.php';
?>
<div class="page-header">
  <div class="container">
    <h1>My Dashboard</h1>
    <p>Welcome back, <?= htmlspecialchars($user['name']) ?></p>
  </div>
</div>

<div class="container section">
  <div class="dashboard-nav">
    <a href="dashboard-jobseeker.php" class="active">My Applications</a>
    <a href="profile.php">My Profile</a>
    <a href="jobs.php">Find Jobs</a>
  </div>

  <div class="grid grid-4" style="margin-bottom:30px;">
    <div class="stat-box"><div class="num"><?= count($applications) ?></div><div class="label">Total Applications</div></div>
    <div class="stat-box"><div class="num"><?= $stats['pending'] ?></div><div class="label">Pending</div></div>
    <div class="stat-box"><div class="num"><?= $stats['accepted'] ?></div><div class="label">Accepted</div></div>
    <div class="stat-box"><div class="num"><?= $stats['rejected'] ?></div><div class="label">Rejected</div></div>
  </div>

  <table>
    <thead>
      <tr><th>Job Title</th><th>Company</th><th>Applied On</th><th>Status</th></tr>
    </thead>
    <tbody>
      <?php foreach ($applications as $a): ?>
      <tr>
        <td><?= htmlspecialchars($a['job_title']) ?></td>
        <td><?= htmlspecialchars($a['company_name']) ?></td>
        <td><?= date('d M Y', strtotime($a['applied_at'])) ?></td>
        <td><span class="badge badge-<?= $a['status'] ?>"><?= ucfirst($a['status']) ?></span></td>
      </tr>
      <?php endforeach; ?>
      <?php if (empty($applications)): ?>
        <tr><td colspan="4" class="empty-state">You haven't applied to any jobs yet.</td></tr>
      <?php endif; ?>
    </tbody>
  </table>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
