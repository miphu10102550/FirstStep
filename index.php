<?php
$pageTitle = "Home";
require_once __DIR__ . '/config/database.php';

$latestJobs = $pdo->query("
  SELECT jobs.*, companies.name AS company_name
  FROM jobs JOIN companies ON jobs.company_id = companies.id
  WHERE jobs.status='open' ORDER BY jobs.created_at DESC LIMIT 6
")->fetchAll();

$articles = $pdo->query("SELECT * FROM articles ORDER BY created_at DESC LIMIT 3")->fetchAll();
$companies = $pdo->query("SELECT * FROM companies ORDER BY created_at DESC LIMIT 4")->fetchAll();

$bodyClass = 'has-hero';
require_once __DIR__ . '/includes/header.php';
?>

<section class="hero">
  <div class="container">
    <h1>Take Your First Step to<br>the Perfect Career.</h1>
    <p>แพลตฟอร์มหางานที่สุดยอดสุดสำหรับเด็กจบใหม่ ไม่ต้องมีประสบการณ์—เริ่มอนาคตของคุณวันนี้</p>
    <form action="jobs.php" method="get" class="search-box">
      <div class="search-field">
        <label>📍 Location</label>
        <input type="text" name="location" placeholder="e.g. Bangkok">
      </div>
      <div class="search-field">
        <label>🗂️ Job Title</label>
        <input type="text" name="q" placeholder="e.g. Developer">
      </div>
      <div class="search-field">
        <label>💰 Expected Salary</label>
        <select name="salary">
          <option value="">Any</option>
          <option value="0-20000">0 - 20K THB</option>
          <option value="20000-30000">20K - 30K THB</option>
          <option value="30000-50000">30K - 50K THB</option>
          <option value="50000-999999">50K+ THB</option>
        </select>
      </div>
      <button type="submit" class="search-submit">🔍 Search</button>
    </form>
  </div>
</section>

<section class="section">
  <div class="container">
    <div class="section-title">
      <h2>Latest Job Openings</h2>
      <p>ตำแหน่งงานใหม่ล่าสุดที่เหมาะกับเด็กจบใหม่</p>
    </div>
    <div class="grid grid-3">
      <?php foreach ($latestJobs as $job): ?>
      <a href="job-detail.php?id=<?= $job['id'] ?>" class="card job-card">
        <div class="job-title"><?= htmlspecialchars($job['title']) ?></div>
        <div class="company"><?= htmlspecialchars($job['company_name']) ?> · <?= htmlspecialchars($job['location']) ?></div>
        <span class="tag"><?= htmlspecialchars($job['job_type']) ?></span>
        <span class="tag"><?= htmlspecialchars($job['category']) ?></span>
        <div class="job-meta salary">฿<?= number_format($job['salary_min']) ?> - ฿<?= number_format($job['salary_max']) ?></div>
      </a>
      <?php endforeach; ?>
      <?php if (empty($latestJobs)): ?>
        <p class="empty-state">No jobs posted yet.</p>
      <?php endif; ?>
    </div>
    <div style="text-align:center;margin-top:30px;">
      <a href="jobs.php" class="btn btn-outline">View All Jobs</a>
    </div>
  </div>
</section>

<section class="section" style="background:#fff;">
  <div class="container">
    <div class="section-title">
      <h2>Featured Companies</h2>
      <p>บริษัทชั้นนำที่กำลังเปิดรับสมัคร</p>
    </div>
    <div class="grid grid-4">
      <?php foreach ($companies as $c): ?>
      <a href="company-detail.php?id=<?= $c['id'] ?>" class="card">
        <div class="job-title"><?= htmlspecialchars($c['name']) ?></div>
        <div class="company"><?= htmlspecialchars($c['location']) ?></div>
      </a>
      <?php endforeach; ?>
      <?php if (empty($companies)): ?>
        <p class="empty-state">No companies yet.</p>
      <?php endif; ?>
    </div>
  </div>
</section>

<section class="section">
  <div class="container">
    <div class="section-title">
      <h2>Tips &amp; Insights</h2>
      <p>บทความและเคล็ดลับสำหรับการหางานครั้งแรก</p>
    </div>
    <div class="grid grid-3">
      <?php foreach ($articles as $a): ?>
      <a href="article-detail.php?id=<?= $a['id'] ?>" class="card article-card">
        <div class="tag"><?= htmlspecialchars($a['category']) ?></div>
        <div class="job-title"><?= htmlspecialchars($a['title']) ?></div>
        <p style="color:var(--muted);font-size:14px;"><?= htmlspecialchars(mb_substr($a['content'],0,90)) ?>...</p>
      </a>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<section class="section" style="background:var(--green-dark);color:#fff;text-align:center;">
  <div class="container">
    <h2 style="margin-top:0;">Are You Hiring?</h2>
    <p>โพสต์ประกาศงานและค้นหาผู้สมัครที่ใช่สำหรับบริษัทของคุณ</p>
    <a href="employers.php" class="btn" style="background:#fff;color:var(--green-dark);">For Employers</a>
  </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
