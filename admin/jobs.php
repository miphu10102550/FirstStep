<?php
$pageTitle = "Manage Jobs";
require_once __DIR__ . '/../config/database.php';

if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $pdo->prepare("DELETE FROM jobs WHERE id=?")->execute([$id]);
    header('Location: jobs.php');
    exit;
}
if (isset($_GET['toggle'])) {
    $id = (int)$_GET['toggle'];
    $job = $pdo->prepare("SELECT status FROM jobs WHERE id=?");
    $job->execute([$id]);
    $job = $job->fetch();
    if ($job) {
        $new = $job['status'] === 'open' ? 'closed' : 'open';
        $pdo->prepare("UPDATE jobs SET status=? WHERE id=?")->execute([$new, $id]);
    }
    header('Location: jobs.php');
    exit;
}

$jobs = $pdo->query("
  SELECT jobs.*, companies.name AS company_name,
  (SELECT COUNT(*) FROM applications WHERE applications.job_id = jobs.id) AS app_count
  FROM jobs JOIN companies ON jobs.company_id = companies.id
  ORDER BY jobs.created_at DESC
")->fetchAll();

require_once __DIR__ . '/header.php';
?>
<h1>Manage Jobs</h1>
<table>
  <thead><tr><th>Title</th><th>Company</th><th>Status</th><th>Applicants</th><th>Posted</th><th>Actions</th></tr></thead>
  <tbody>
    <?php foreach ($jobs as $job): ?>
    <tr>
      <td><?= htmlspecialchars($job['title']) ?></td>
      <td><?= htmlspecialchars($job['company_name']) ?></td>
      <td><span class="badge badge-<?= $job['status'] ?>"><?= ucfirst($job['status']) ?></span></td>
      <td><?= $job['app_count'] ?></td>
      <td><?= date('d M Y', strtotime($job['created_at'])) ?></td>
      <td>
        <a href="../job-detail.php?id=<?= $job['id'] ?>" target="_blank" style="color:var(--blue);font-weight:600;">View</a> ·
        <a href="jobs.php?toggle=<?= $job['id'] ?>" style="color:var(--green-dark);font-weight:600;"><?= $job['status']==='open'?'Close':'Reopen' ?></a> ·
        <a href="jobs.php?delete=<?= $job['id'] ?>" onclick="return confirm('Delete this job?');" style="color:#b3261e;font-weight:600;">Delete</a>
      </td>
    </tr>
    <?php endforeach; ?>
    <?php if (empty($jobs)): ?>
      <tr><td colspan="6" class="empty-state">No jobs found.</td></tr>
    <?php endif; ?>
  </tbody>
</table>

<?php require_once __DIR__ . '/footer.php'; ?>
