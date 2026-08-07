<?php
$pageTitle = "Edit Job";
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/auth.php';
requireRole('employer');
$user = currentUser();

$company = $pdo->prepare("SELECT * FROM companies WHERE employer_id=?");
$company->execute([$user['id']]);
$company = $company->fetch();

$id = (int)($_GET['id'] ?? 0);
$stmt = $pdo->prepare("SELECT * FROM jobs WHERE id=? AND company_id=?");
$stmt->execute([$id, $company['id']]);
$job = $stmt->fetch();
if (!$job) { header('Location: manage-jobs.php'); exit; }

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
        $pdo->prepare("UPDATE jobs SET title=?, description=?, requirements=?, location=?, job_type=?, category=?, salary_min=?, salary_max=? WHERE id=?")
            ->execute([$title, $description, $requirements, $location, $jobType, $category, $salaryMin, $salaryMax, $id]);
        header('Location: manage-jobs.php?updated=1');
        exit;
    }
}

require_once __DIR__ . '/includes/employer-header.php';
?>
<div class="dash-topbar"><h1><span class="dash-topbar-icon"><?= icon('edit',18) ?></span> Edit Job — <?= htmlspecialchars($job['title']) ?></h1></div>

  <div class="widget-card" style="max-width:700px;">
    <?php if ($error): ?><div class="alert alert-error"><?= htmlspecialchars($error) ?></div><?php endif; ?>
    <form method="post">
      <div class="form-group">
        <label>Job Title</label>
        <input type="text" name="title" required value="<?= htmlspecialchars($job['title']) ?>">
      </div>
      <div class="form-group">
        <label>Description</label>
        <textarea name="description" rows="5" required><?= htmlspecialchars($job['description']) ?></textarea>
      </div>
      <div class="form-group">
        <label>Requirements</label>
        <textarea name="requirements" rows="4"><?= htmlspecialchars($job['requirements']) ?></textarea>
      </div>
      <div class="form-group">
        <label>Location</label>
        <input type="text" name="location" value="<?= htmlspecialchars($job['location']) ?>">
      </div>
      <div class="form-group">
        <label>Category</label>
        <input type="text" name="category" value="<?= htmlspecialchars($job['category']) ?>">
      </div>
      <div class="form-group">
        <label>Job Type</label>
        <select name="job_type">
          <?php foreach (['Full-time','Part-time','Internship','Contract'] as $t): ?>
            <option <?= $job['job_type']==$t?'selected':'' ?>><?= $t ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="form-group" style="display:flex;gap:10px;">
        <div style="flex:1;">
          <label>Salary Min (THB)</label>
          <input type="number" name="salary_min" value="<?= (int)$job['salary_min'] ?>">
        </div>
        <div style="flex:1;">
          <label>Salary Max (THB)</label>
          <input type="number" name="salary_max" value="<?= (int)$job['salary_max'] ?>">
        </div>
      </div>
      <button type="submit" class="btn btn-primary btn-block">Save Changes</button>
    </form>
  </div>

<?php require_once __DIR__ . '/includes/employer-footer.php'; ?>
