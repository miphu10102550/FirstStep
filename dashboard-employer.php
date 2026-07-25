<?php
$pageTitle = "Employer Dashboard";
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/auth.php';
requireRole('employer');
$user = currentUser();

$companyStmt = $pdo->prepare("SELECT * FROM companies WHERE employer_id=?");
$companyStmt->execute([$user['id']]);
$company = $companyStmt->fetch();

$jobCount = 0; $applicantCount = 0; $openCount = 0;
if ($company) {
    $jobCount = $pdo->prepare("SELECT COUNT(*) FROM jobs WHERE company_id=?");
    $jobCount->execute([$company['id']]); $jobCount = $jobCount->fetchColumn();

    $openCount = $pdo->prepare("SELECT COUNT(*) FROM jobs WHERE company_id=? AND status='open'");
    $openCount->execute([$company['id']]); $openCount = $openCount->fetchColumn();

    $applicantCount = $pdo->prepare("SELECT COUNT(*) FROM applications a JOIN jobs j ON a.job_id=j.id WHERE j.company_id=?");
    $applicantCount->execute([$company['id']]); $applicantCount = $applicantCount->fetchColumn();

    $recentJobs = $pdo->prepare("SELECT * FROM jobs WHERE company_id=? ORDER BY created_at DESC LIMIT 5");
    $recentJobs->execute([$company['id']]);
    $recentJobs = $recentJobs->fetchAll();
} else {
    $recentJobs = [];
}

require_once __DIR__ . '/includes/header.php';
?>
<div class="page-header">
  <div class="container">
    <h1>Employer Dashboard</h1>
    <p><?= $company ? htmlspecialchars($company['name']) : 'Welcome, ' . htmlspecialchars($user['name']) ?></p>
  </div>
</div>

<div class="container section">
  <div class="dashboard-nav">
    <a href="dashboard-employer.php" class="active">Overview</a>
    <a href="post-job.php">Post a Job</a>
    <a href="manage-jobs.php">Manage Jobs</a>
    <a href="company-profile.php">Company Profile</a>
  </div>

  <div class="grid grid-3" style="margin-bottom:30px;">
    <div class="stat-box"><div class="num"><?= $jobCount ?></div><div class="label">Total Jobs Posted</div></div>
    <div class="stat-box"><div class="num"><?= $openCount ?></div><div class="label">Open Positions</div></div>
    <div class="stat-box"><div class="num"><?= $applicantCount ?></div><div class="label">Total Applicants</div></div>
  </div>

  <h3>Recent Job Postings</h3>
  <table>
    <thead><tr><th>Title</th><th>Location</th><th>Status</th><th>Posted</th><th></th></tr></thead>
    <tbody>
      <?php foreach ($recentJobs as $job): ?>
      <tr>
        <td><?= htmlspecialchars($job['title']) ?></td>
        <td><?= htmlspecialchars($job['location']) ?></td>
        <td><span class="badge badge-<?= $job['status'] ?>"><?= ucfirst($job['status']) ?></span></td>
        <td><?= date('d M Y', strtotime($job['created_at'])) ?></td>
        <td><a href="applicants.php?job_id=<?= $job['id'] ?>" style="color:var(--green-dark);font-weight:600;">View Applicants</a></td>
      </tr>
      <?php endforeach; ?>
      <?php if (empty($recentJobs)): ?>
        <tr><td colspan="5" class="empty-state">You haven't posted any jobs yet. <a href="post-job.php">Post your first job</a>.</td></tr>
      <?php endif; ?>
    </tbody>
  </table>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
