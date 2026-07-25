<?php
$pageTitle = "Find Jobs";
require_once __DIR__ . '/config/database.php';

$q = trim($_GET['q'] ?? '');
$location = trim($_GET['location'] ?? '');
$category = trim($_GET['category'] ?? '');
$type = trim($_GET['type'] ?? '');
$salary = trim($_GET['salary'] ?? '');

$sql = "SELECT jobs.*, companies.name AS company_name FROM jobs
        JOIN companies ON jobs.company_id = companies.id WHERE jobs.status='open'";
$params = [];

if ($q !== '') { $sql .= " AND jobs.title LIKE :q"; $params[':q'] = "%$q%"; }
if ($location !== '') { $sql .= " AND jobs.location LIKE :loc"; $params[':loc'] = "%$location%"; }
if ($category !== '') { $sql .= " AND jobs.category = :cat"; $params[':cat'] = $category; }
if ($type !== '') { $sql .= " AND jobs.job_type = :type"; $params[':type'] = $type; }
if ($salary !== '' && strpos($salary,'-') !== false) {
    [$min,$max] = explode('-', $salary);
    $sql .= " AND jobs.salary_max >= :smin AND jobs.salary_min <= :smax";
    $params[':smin'] = $min; $params[':smax'] = $max;
}
$sql .= " ORDER BY jobs.created_at DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$jobs = $stmt->fetchAll();

$categories = $pdo->query("SELECT DISTINCT category FROM jobs")->fetchAll(PDO::FETCH_COLUMN);

require_once __DIR__ . '/includes/header.php';
?>
<div class="page-header">
  <div class="container">
    <h1>Find Jobs</h1>
    <p>ค้นหาตำแหน่งงานที่ใช่สำหรับคุณ</p>
  </div>
</div>

<div class="container section">
  <div class="two-col">
    <aside class="filter-box">
      <form method="get">
        <h4>Filter Jobs</h4>
        <div class="form-group">
          <label>Keyword</label>
          <input type="text" name="q" value="<?= htmlspecialchars($q) ?>" placeholder="Job title">
        </div>
        <div class="form-group">
          <label>Location</label>
          <input type="text" name="location" value="<?= htmlspecialchars($location) ?>" placeholder="e.g. Bangkok">
        </div>
        <div class="form-group">
          <label>Category</label>
          <select name="category">
            <option value="">All</option>
            <?php foreach ($categories as $c): ?>
              <option value="<?= htmlspecialchars($c) ?>" <?= $category==$c?'selected':'' ?>><?= htmlspecialchars($c) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="form-group">
          <label>Job Type</label>
          <select name="type">
            <option value="">All</option>
            <?php foreach (['Full-time','Part-time','Internship','Contract'] as $t): ?>
              <option value="<?= $t ?>" <?= $type==$t?'selected':'' ?>><?= $t ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="form-group">
          <label>Expected Salary</label>
          <select name="salary">
            <option value="">Any</option>
            <option value="0-20000" <?= $salary=='0-20000'?'selected':'' ?>>0 - 20K THB</option>
            <option value="20000-30000" <?= $salary=='20000-30000'?'selected':'' ?>>20K - 30K THB</option>
            <option value="30000-50000" <?= $salary=='30000-50000'?'selected':'' ?>>30K - 50K THB</option>
            <option value="50000-999999" <?= $salary=='50000-999999'?'selected':'' ?>>50K+ THB</option>
          </select>
        </div>
        <button type="submit" class="btn btn-primary btn-block">Apply Filters</button>
      </form>
    </aside>

    <div>
      <p style="color:var(--muted);"><?= count($jobs) ?> jobs found</p>
      <div class="grid" style="grid-template-columns:1fr;gap:16px;">
        <?php foreach ($jobs as $job): ?>
        <a href="job-detail.php?id=<?= $job['id'] ?>" class="card job-card">
          <div class="job-title"><?= htmlspecialchars($job['title']) ?></div>
          <div class="company"><?= htmlspecialchars($job['company_name']) ?> · <?= htmlspecialchars($job['location']) ?></div>
          <span class="tag"><?= htmlspecialchars($job['job_type']) ?></span>
          <span class="tag"><?= htmlspecialchars($job['category']) ?></span>
          <div class="job-meta salary">฿<?= number_format($job['salary_min']) ?> - ฿<?= number_format($job['salary_max']) ?></div>
        </a>
        <?php endforeach; ?>
        <?php if (empty($jobs)): ?>
          <p class="empty-state">No jobs match your search. Try different filters.</p>
        <?php endif; ?>
      </div>
    </div>
  </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
