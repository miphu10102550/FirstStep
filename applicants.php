<?php
$pageTitle = "Applicants";
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/auth.php';
requireRole('employer');
$user = currentUser();

$company = $pdo->prepare("SELECT * FROM companies WHERE employer_id=?");
$company->execute([$user['id']]);
$company = $company->fetch();

$jobId = (int)($_GET['job_id'] ?? 0);
$job = $pdo->prepare("SELECT * FROM jobs WHERE id=? AND company_id=?");
$job->execute([$jobId, $company['id']]);
$job = $job->fetch();
if (!$job) { header('Location: manage-jobs.php'); exit; }

// Handle status update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['app_id'])) {
    $appId = (int)$_POST['app_id'];
    $status = $_POST['status'];
    if (in_array($status, ['pending','reviewed','accepted','rejected'])) {
        $pdo->prepare("UPDATE applications SET status=? WHERE id=? AND job_id=?")->execute([$status, $appId, $jobId]);
    }
    header("Location: applicants.php?job_id=$jobId");
    exit;
}

$apps = $pdo->prepare("
  SELECT applications.*, users.full_name, users.email, users.phone, jobseeker_profiles.education, jobseeker_profiles.skills, jobseeker_profiles.bio
  FROM applications
  JOIN users ON applications.jobseeker_id = users.id
  LEFT JOIN jobseeker_profiles ON jobseeker_profiles.user_id = users.id
  WHERE applications.job_id = ?
  ORDER BY applications.applied_at DESC
");
$apps->execute([$jobId]);
$applicants = $apps->fetchAll();

require_once __DIR__ . '/includes/header.php';
?>
<div class="page-header">
  <div class="container">
    <h1>Applicants for <?= htmlspecialchars($job['title']) ?></h1>
    <p><?= count($applicants) ?> application(s) received</p>
  </div>
</div>

<div class="container section">
  <div class="dashboard-nav">
    <a href="dashboard-employer.php">Overview</a>
    <a href="post-job.php">Post a Job</a>
    <a href="manage-jobs.php" class="active">Manage Jobs</a>
    <a href="company-profile.php">Company Profile</a>
  </div>

  <?php foreach ($applicants as $a): ?>
  <div class="card" style="margin-bottom:16px;">
    <div style="display:flex;justify-content:space-between;flex-wrap:wrap;gap:10px;">
      <div>
        <div class="job-title"><?= htmlspecialchars($a['full_name']) ?></div>
        <div class="company"><?= htmlspecialchars($a['email']) ?> · <?= htmlspecialchars($a['phone']) ?></div>
      </div>
      <span class="badge badge-<?= $a['status'] ?>"><?= ucfirst($a['status']) ?></span>
    </div>
    <?php if ($a['education']): ?><p><strong>Education:</strong> <?= htmlspecialchars($a['education']) ?></p><?php endif; ?>
    <?php if ($a['skills']): ?><p><strong>Skills:</strong> <?= htmlspecialchars($a['skills']) ?></p><?php endif; ?>
    <?php if ($a['cover_letter']): ?><p><strong>Cover Letter:</strong><br><?= nl2br(htmlspecialchars($a['cover_letter'])) ?></p><?php endif; ?>
    <?php if ($a['resume_file']): ?>
      <p><a href="uploads/resumes/<?= htmlspecialchars($a['resume_file']) ?>" target="_blank" class="btn btn-outline">View Resume</a></p>
    <?php endif; ?>

    <form method="post" style="display:flex;gap:10px;align-items:center;margin-top:10px;">
      <input type="hidden" name="app_id" value="<?= $a['id'] ?>">
      <label style="margin:0;">Update Status:</label>
      <select name="status" onchange="this.form.submit()">
        <?php foreach (['pending','reviewed','accepted','rejected'] as $s): ?>
          <option value="<?= $s ?>" <?= $a['status']==$s?'selected':'' ?>><?= ucfirst($s) ?></option>
        <?php endforeach; ?>
      </select>
    </form>
  </div>
  <?php endforeach; ?>
  <?php if (empty($applicants)): ?>
    <p class="empty-state">No applicants yet for this job.</p>
  <?php endif; ?>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
