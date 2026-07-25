<?php
$pageTitle = "Apply for Job";
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/auth.php';
requireRole('jobseeker');
$user = currentUser();

$jobId = (int)($_GET['job_id'] ?? $_POST['job_id'] ?? 0);
$stmt = $pdo->prepare("SELECT jobs.*, companies.name AS company_name FROM jobs JOIN companies ON jobs.company_id=companies.id WHERE jobs.id=?");
$stmt->execute([$jobId]);
$job = $stmt->fetch();
if (!$job) { header('Location: jobs.php'); exit; }

$chk = $pdo->prepare("SELECT id FROM applications WHERE job_id=? AND jobseeker_id=?");
$chk->execute([$jobId, $user['id']]);
if ($chk->fetch()) { header("Location: job-detail.php?id=$jobId"); exit; }

$profileStmt = $pdo->prepare("SELECT * FROM jobseeker_profiles WHERE user_id=?");
$profileStmt->execute([$user['id']]);
$profile = $profileStmt->fetch();

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $coverLetter = trim($_POST['cover_letter'] ?? '');
    $resumeFile = $profile['resume_file'] ?? null;

    if (!empty($_FILES['resume']['name'])) {
        $ext = pathinfo($_FILES['resume']['name'], PATHINFO_EXTENSION);
        if (in_array(strtolower($ext), ['pdf','doc','docx'])) {
            $newName = 'resume_' . $user['id'] . '_' . time() . '.' . $ext;
            move_uploaded_file($_FILES['resume']['tmp_name'], __DIR__ . '/uploads/resumes/' . $newName);
            $resumeFile = $newName;
        }
    }

    if (!$resumeFile) {
        $error = 'Please upload a resume (or add one to your profile first).';
    } else {
        $pdo->prepare("INSERT INTO applications (job_id, jobseeker_id, cover_letter, resume_file) VALUES (?,?,?,?)")
            ->execute([$jobId, $user['id'], $coverLetter, $resumeFile]);
        header("Location: dashboard-jobseeker.php?applied=1");
        exit;
    }
}

require_once __DIR__ . '/includes/header.php';
?>
<div class="page-header">
  <div class="container">
    <h1>Apply for <?= htmlspecialchars($job['title']) ?></h1>
    <p><?= htmlspecialchars($job['company_name']) ?></p>
  </div>
</div>

<div class="container section">
  <div class="form-wrap">
    <?php if ($error): ?><div class="alert alert-error"><?= htmlspecialchars($error) ?></div><?php endif; ?>
    <form method="post" enctype="multipart/form-data">
      <input type="hidden" name="job_id" value="<?= $jobId ?>">
      <div class="form-group">
        <label>Cover Letter</label>
        <textarea name="cover_letter" rows="6" placeholder="Tell the employer why you're a great fit..."></textarea>
      </div>
      <div class="form-group">
        <label>Resume (PDF or Word)</label>
        <input type="file" name="resume" accept=".pdf,.doc,.docx" <?= empty($profile['resume_file']) ? 'required' : '' ?>>
        <?php if (!empty($profile['resume_file'])): ?>
          <p style="font-size:13px;color:var(--muted);margin-top:6px;">Leave empty to use your profile resume: <?= htmlspecialchars($profile['resume_file']) ?></p>
        <?php endif; ?>
      </div>
      <button type="submit" class="btn btn-primary btn-block">Submit Application</button>
    </form>
  </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
