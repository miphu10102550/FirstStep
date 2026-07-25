<?php
$pageTitle = "Company Profile";
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

$success = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $location = trim($_POST['location'] ?? '');
    $website = trim($_POST['website'] ?? '');
    $description = trim($_POST['description'] ?? '');

    $pdo->prepare("UPDATE companies SET name=?, location=?, website=?, description=? WHERE id=?")
        ->execute([$name, $location, $website, $description, $company['id']]);

    $success = 'Company profile updated.';
    $stmt->execute([$user['id']]);
    $company = $stmt->fetch();
}

require_once __DIR__ . '/includes/header.php';
?>
<div class="page-header">
  <div class="container">
    <h1>Company Profile</h1>
    <p>ข้อมูลบริษัทที่จะแสดงให้ผู้สมัครเห็น</p>
  </div>
</div>

<div class="container section">
  <div class="dashboard-nav">
    <a href="dashboard-employer.php">Overview</a>
    <a href="post-job.php">Post a Job</a>
    <a href="manage-jobs.php">Manage Jobs</a>
    <a href="company-profile.php" class="active">Company Profile</a>
  </div>

  <div class="card" style="max-width:650px;">
    <?php if ($success): ?><div class="alert alert-success"><?= htmlspecialchars($success) ?></div><?php endif; ?>
    <form method="post">
      <div class="form-group">
        <label>Company Name</label>
        <input type="text" name="name" required value="<?= htmlspecialchars($company['name']) ?>">
      </div>
      <div class="form-group">
        <label>Location</label>
        <input type="text" name="location" value="<?= htmlspecialchars($company['location']) ?>">
      </div>
      <div class="form-group">
        <label>Website</label>
        <input type="text" name="website" value="<?= htmlspecialchars($company['website']) ?>">
      </div>
      <div class="form-group">
        <label>Description</label>
        <textarea name="description" rows="5"><?= htmlspecialchars($company['description']) ?></textarea>
      </div>
      <button type="submit" class="btn btn-primary btn-block">Save Company Profile</button>
    </form>
  </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
