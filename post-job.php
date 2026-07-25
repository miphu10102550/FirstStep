<?php
$pageTitle = "Post a Job";
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/auth.php';
requireRole('employer');
$user = currentUser();

$stmt = $pdo->prepare("SELECT * FROM companies WHERE employer_id=?");
$stmt->execute([$user['id']]);
$company = $stmt->fetch();
if (!$company) {
    $pdo->prepare("INSERT INTO companies (employer_id, name) VALUES (?, 'My Company')")->execute([$user['id']]);
    $stmt->execute([$user['id']]);
    $company = $stmt->fetch();
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $requirements = trim($_POST['requirements'] ?? '');
    $location = trim($_POST['location'] ?? '');
    $jobType = $_POST['job_type'] ?? 'Full-time';
    $category = trim($_POST['category'] ?? '');
    $salaryMin = (int)($_POST['salary_min'] ?? 0);
    $salaryMax = (int)($_POST['salary_max'] ?? 0);

    if ($title === '' || $description === '') {
        $error = 'Please fill in the job title and description.';
    } else {
        $pdo->prepare("INSERT INTO jobs (company_id, title, description, requirements, location, job_type, category, salary_min, salary_max) VALUES (?,?,?,?,?,?,?,?,?)")
            ->execute([$company['id'], $title, $description, $requirements, $location, $jobType, $category, $salaryMin, $salaryMax]);
        header('Location: manage-jobs.php?posted=1');
        exit;
    }
}

require_once __DIR__ . '/includes/header.php';
?>
<div class="page-header">
  <div class="container">
    <h1>Post a Job</h1>
    <p>สร้างประกาศงานใหม่สำหรับ <?= htmlspecialchars($company['name']) ?></p>
  </div>
</div>

<div class="container section">
  <div class="dashboard-nav">
    <a href="dashboard-employer.php">Overview</a>
    <a href="post-job.php" class="active">Post a Job</a>
    <a href="manage-jobs.php">Manage Jobs</a>
    <a href="company-profile.php">Company Profile</a>
  </div>

  <div class="card" style="max-width:700px;">
    <?php if ($error): ?><div class="alert alert-error"><?= htmlspecialchars($error) ?></div><?php endif; ?>
    <form method="post">
      <div class="form-group">
        <label>Job Title</label>
        <input type="text" name="title" required placeholder="e.g. Junior Web Developer">
      </div>
      <div class="form-group">
        <label>Description</label>
        <textarea name="description" rows="5" required></textarea>
      </div>
      <div class="form-group">
        <label>Requirements</label>
        <textarea name="requirements" rows="4"></textarea>
      </div>
      <div class="form-group">
        <label>Location</label>
        <input type="text" name="location" placeholder="e.g. Bangkok">
      </div>
      <div class="form-group">
        <label>Category</label>
        <input type="text" name="category" placeholder="e.g. Developer, Design, Marketing">
      </div>
      <div class="form-group">
        <label>Job Type</label>
        <select name="job_type">
          <option>Full-time</option>
          <option>Part-time</option>
          <option>Internship</option>
          <option>Contract</option>
        </select>
      </div>
      <div class="form-group" style="display:flex;gap:10px;">
        <div style="flex:1;">
          <label>Salary Min (THB)</label>
          <input type="number" name="salary_min" value="0">
        </div>
        <div style="flex:1;">
          <label>Salary Max (THB)</label>
          <input type="number" name="salary_max" value="0">
        </div>
      </div>
      <button type="submit" class="btn btn-primary btn-block">Publish Job</button>
    </form>
  </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
