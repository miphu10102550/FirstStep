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

// Data for the top overview section (recommended jobs + career tips)
$recommendedJobs = $pdo->query("
  SELECT jobs.*, companies.name AS company_name FROM jobs
  JOIN companies ON jobs.company_id = companies.id
  WHERE jobs.status='open' ORDER BY jobs.created_at DESC LIMIT 4
")->fetchAll();
$tipArticles = $pdo->query("SELECT * FROM articles ORDER BY created_at DESC LIMIT 3")->fetchAll();

// Generate a consistent colored initials "logo" for a company (no real trademarked logos used)
function companyBadge($name) {
    $palette = ['#2f8fe0','#1f7a4d','#e08a1e','#8e44ad','#e0473f','#16213e'];
    $sum = array_sum(array_map('ord', str_split($name)));
    $color = $palette[$sum % count($palette)];
    $words = preg_split('/\s+/', trim($name));
    $initials = mb_strtoupper(mb_substr($words[0] ?? '', 0, 1) . mb_substr($words[1] ?? '', 0, 1));
    return "<span class=\"reco-logo\" style=\"background:$color;\">" . htmlspecialchars($initials) . "</span>";
}

require_once __DIR__ . '/includes/header.php';
?>
<div class="page-header">
  <div class="container">
    <h1>Find Jobs</h1>
    <p>ค้นหาตำแหน่งงานที่ใช่สำหรับคุณ</p>
  </div>
</div>

<div class="container section" style="padding-bottom:0;">
  <div class="grid grid-4" style="margin-bottom:36px;">
    <a href="<?= isLoggedIn() && currentUser()['role']=='jobseeker' ? 'profile.php' : 'register-jobseeker.php' ?>" class="feature-card">
      <div class="feature-icon"><?= icon('user',18) ?></div>
      <div class="feature-title">สร้างโปรไฟล์</div>
      <p class="feature-sub">เพิ่มข้อมูลเรซูเม่เพื่อโอกาสในการหางานของคุณ</p>
      <span class="feature-cta">สร้างโปรไฟล์ →</span>
    </a>
    <a href="#job-search" class="feature-card">
      <div class="feature-icon"><?= icon('briefcase',18) ?></div>
      <div class="feature-title">ค้นหางาน</div>
      <p class="feature-sub">ค้นหางานที่ใช่จากบริษัทชั้นนำ</p>
      <span class="feature-cta">ค้นหางาน →</span>
    </a>
    <a href="employers.php" class="feature-card">
      <div class="feature-icon"><?= icon('building',18) ?></div>
      <div class="feature-title">บริษัทหาคนทำงาน</div>
      <p class="feature-sub">รวมบริษัทที่เปิดรับสมัครคนทำงาน</p>
      <span class="feature-cta">สมัครนายจ้าง →</span>
    </a>
    <a href="login.php" class="feature-card">
      <div class="feature-icon"><?= icon('wrench',18) ?></div>
      <div class="feature-title">แอดมิน</div>
      <p class="feature-sub">สำหรับแอดมินจัดการระบบต่าง ๆ</p>
      <span class="feature-cta">เข้าระบบ →</span>
    </a>
  </div>

  <div class="two-col" style="grid-template-columns:2fr 1fr;margin-bottom:40px;">
    <div class="card" style="padding:0;overflow:hidden;">
      <h3 style="padding:20px 24px 0;">ตำแหน่งงานแนะนำ</h3>
      <div class="reco-list">
        <?php foreach ($recommendedJobs as $job): ?>
        <a href="job-detail.php?id=<?= $job['id'] ?>" class="reco-item">
          <?= companyBadge($job['company_name']) ?>
          <div class="reco-info">
            <div class="reco-title"><?= htmlspecialchars($job['title']) ?></div>
            <div class="reco-company"><?= htmlspecialchars($job['company_name']) ?></div>
          </div>
          <div class="reco-location"><?= icon('pin',13) ?> <?= htmlspecialchars($job['location']) ?></div>
          <div class="reco-salary">฿<?= number_format($job['salary_min']/1000) ?>K - <?= number_format($job['salary_max']/1000) ?>K THB</div>
        </a>
        <?php endforeach; ?>
        <?php if (empty($recommendedJobs)): ?>
          <p class="empty-state">No jobs yet.</p>
        <?php endif; ?>
      </div>
      <div style="padding:16px 24px;text-align:center;">
        <a href="#job-search" class="btn btn-outline">ดูตำแหน่งงานทั้งหมด →</a>
      </div>
    </div>

    <div class="card" style="padding:0;overflow:hidden;">
      <h3 style="padding:20px 24px 0;">เคล็ดลับอาชีพ</h3>
      <div class="tips-list">
        <?php foreach ($tipArticles as $a): ?>
        <a href="article-detail.php?id=<?= $a['id'] ?>" class="tip-item">
          <div class="tip-thumb"><?= icon('note',22) ?></div>
          <div>
            <div class="tip-title"><?= htmlspecialchars($a['title']) ?></div>
            <div class="tip-date"><?= date('d M Y', strtotime($a['created_at'])) ?></div>
          </div>
        </a>
        <?php endforeach; ?>
        <?php if (empty($tipArticles)): ?>
          <p class="empty-state">No articles yet.</p>
        <?php endif; ?>
      </div>
    </div>
  </div>
</div>

<div class="container section" id="job-search" style="padding-top:0;">
  <h2 style="color:var(--green-dark);margin-bottom:20px;">ค้นหางานแบบละเอียด</h2>
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
