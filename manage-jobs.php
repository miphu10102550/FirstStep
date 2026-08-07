<?php
$pageTitle = "Manage Jobs";
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/auth.php';
requireRole('employer');
$user = currentUser();

$stmt = $pdo->prepare("SELECT * FROM companies WHERE employer_id=?");
$stmt->execute([$user['id']]);
$company = $stmt->fetch();

// Handle status toggle / delete
if (isset($_GET['toggle'])) {
    $jobId = (int)$_GET['toggle'];
    $job = $pdo->prepare("SELECT * FROM jobs WHERE id=? AND company_id=?");
    $job->execute([$jobId, $company['id']]);
    $job = $job->fetch();
    if ($job) {
        $newStatus = $job['status'] === 'open' ? 'closed' : 'open';
        $pdo->prepare("UPDATE jobs SET status=? WHERE id=?")->execute([$newStatus, $jobId]);
    }
    header('Location: manage-jobs.php');
    exit;
}
if (isset($_GET['delete'])) {
    $jobId = (int)$_GET['delete'];
    $pdo->prepare("DELETE FROM jobs WHERE id=? AND company_id=?")->execute([$jobId, $company['id']]);
    header('Location: manage-jobs.php');
    exit;
}

$jobsStmt = $pdo->prepare("SELECT jobs.*, (SELECT COUNT(*) FROM applications WHERE applications.job_id = jobs.id) AS app_count
                            FROM jobs WHERE company_id=? ORDER BY created_at DESC");
$jobsStmt->execute([$company['id']]);
$jobs = $jobsStmt->fetchAll();

require_once __DIR__ . '/includes/employer-header.php';
?>
<div class="dash-topbar"><h1><span class="dash-topbar-icon"><?= icon('document',18) ?></span> งานที่ประกาศไว้</h1></div>

  <?php if (isset($_GET['posted'])): ?>
    <div class="alert alert-success">Job posted successfully!</div>
  <?php endif; ?>

  <div class="widget-card">
  <table>
    <thead><tr><th>Title</th><th>Type</th><th>Status</th><th>Applicants</th><th>Actions</th></tr></thead>
    <tbody>
      <?php foreach ($jobs as $job): ?>
      <tr>
        <td><?= htmlspecialchars($job['title']) ?></td>
        <td><?= htmlspecialchars($job['job_type']) ?></td>
        <td><span class="badge badge-<?= $job['status'] ?>"><?= ucfirst($job['status']) ?></span></td>
        <td><a href="applicants.php?job_id=<?= $job['id'] ?>"><?= $job['app_count'] ?> applicant(s)</a></td>
        <td>
          <a href="edit-job.php?id=<?= $job['id'] ?>" style="color:var(--blue);font-weight:600;">Edit</a> ·
          <a href="manage-jobs.php?toggle=<?= $job['id'] ?>" style="color:var(--green-dark);font-weight:600;"><?= $job['status']==='open'?'Close':'Reopen' ?></a> ·
          <a href="manage-jobs.php?delete=<?= $job['id'] ?>" onclick="return confirm('Delete this job posting?');" style="color:#b3261e;font-weight:600;">Delete</a>
        </td>
      </tr>
      <?php endforeach; ?>
      <?php if (empty($jobs)): ?>
        <tr><td colspan="5" class="empty-state">No jobs posted yet. <a href="post-job.php">Post one now</a>.</td></tr>
      <?php endif; ?>
    </tbody>
  </table>
  </div>

<?php require_once __DIR__ . '/includes/employer-footer.php'; ?>
