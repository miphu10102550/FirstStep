<?php
$pageTitle = "Employer Dashboard";
require_once __DIR__ . '/includes/employer-header.php';
// $__company and $user are provided by employer-header.php
$company = $__company;

$openJobs = $pdo->prepare("SELECT COUNT(*) FROM jobs WHERE company_id=? AND status='open'");
$openJobs->execute([$company['id']]); $openJobs = $openJobs->fetchColumn();

$totalApplicants = $pdo->prepare("SELECT COUNT(*) FROM applications a JOIN jobs j ON a.job_id=j.id WHERE j.company_id=?");
$totalApplicants->execute([$company['id']]); $totalApplicants = $totalApplicants->fetchColumn();

$statusCounts = ['pending'=>0,'reviewed'=>0,'accepted'=>0,'rejected'=>0];
$rows = $pdo->prepare("SELECT a.status, COUNT(*) c FROM applications a JOIN jobs j ON a.job_id=j.id WHERE j.company_id=? GROUP BY a.status");
$rows->execute([$company['id']]);
foreach ($rows->fetchAll() as $r) { $statusCounts[$r['status']] = (int)$r['c']; }

$activeJobs = $pdo->prepare("
  SELECT jobs.*, (SELECT COUNT(*) FROM applications WHERE applications.job_id = jobs.id) AS app_count
  FROM jobs WHERE company_id=? AND status='open' ORDER BY created_at DESC LIMIT 6
");
$activeJobs->execute([$company['id']]);
$activeJobs = $activeJobs->fetchAll();

$recentApplicants = $pdo->prepare("
  SELECT applications.*, users.full_name, jobs.title AS job_title
  FROM applications
  JOIN users ON applications.jobseeker_id = users.id
  JOIN jobs ON applications.job_id = jobs.id
  WHERE jobs.company_id = ?
  ORDER BY applications.applied_at DESC LIMIT 5
");
$recentApplicants->execute([$company['id']]);
$recentApplicants = $recentApplicants->fetchAll();

$totalForDonut = max(array_sum($statusCounts), 1);
$pending = round($statusCounts['pending'] / $totalForDonut * 100);
$reviewed = round($statusCounts['reviewed'] / $totalForDonut * 100);
$accepted = round($statusCounts['accepted'] / $totalForDonut * 100);
$rejected = 100 - $pending - $reviewed - $accepted;

function empBadge($name) {
    $palette = ['#2f8fe0','#1f7a4d','#e08a1e','#8e44ad','#e0473f','#6c5ce7'];
    $sum = array_sum(array_map('ord', str_split($name)));
    $color = $palette[$sum % count($palette)];
    $words = preg_split('/\s+/', trim($name));
    $initials = mb_strtoupper(mb_substr($words[0] ?? '', 0, 1) . mb_substr($words[1] ?? '', 0, 1));
    return "<span class=\"widget-badge\" style=\"background:$color;\">" . htmlspecialchars($initials) . "</span>";
}
?>
<div class="dash-topbar">
  <h1><span class="dash-topbar-icon"><?= icon('building',18) ?></span> ตั้งค่าบริษัท — <?= htmlspecialchars($company['name']) ?></h1>
  <div class="dash-topbar-actions">
    <div class="dash-bell"><?= icon('bell',18) ?><span class="dot"></span></div>
    <div class="dash-avatar-sm"><?= htmlspecialchars(mb_substr($user['name'],0,1)) ?></div>
  </div>
</div>

<div class="stat-grid" style="grid-template-columns:repeat(5,1fr);">
  <div class="stat-card-v2">
    <div class="top-row"><div class="icon-box" style="background:#eef0ff;color:#6c5ce7;"><?= icon('briefcase',18) ?></div></div>
    <div class="num"><?= $openJobs ?></div>
    <div class="label">งานที่ประกาศอยู่</div>
  </div>
  <div class="stat-card-v2">
    <div class="top-row"><div class="icon-box" style="background:#e7f6ee;color:#1f9d55;"><?= icon('users',18) ?></div></div>
    <div class="num"><?= $totalApplicants ?></div>
    <div class="label">ผู้สมัครทั้งหมด</div>
  </div>
  <div class="stat-card-v2">
    <div class="top-row"><div class="icon-box" style="background:#fff4e5;color:#e08a1e;"><?= icon('clock',18) ?></div></div>
    <div class="num"><?= $statusCounts['pending'] ?></div>
    <div class="label">อยู่ระหว่างการคัดเลือก</div>
  </div>
  <div class="stat-card-v2">
    <div class="top-row"><div class="icon-box" style="background:#e7f0fe;color:#2f8fe0;"><?= icon('check',18) ?></div></div>
    <div class="num"><?= $statusCounts['reviewed'] ?></div>
    <div class="label">นัดสัมภาษณ์แล้ว</div>
  </div>
  <div class="stat-card-v2">
    <div class="top-row"><div class="icon-box" style="background:#fdecea;color:#1f9d55;"><?= icon('check',18) ?></div></div>
    <div class="num"><?= $statusCounts['accepted'] ?></div>
    <div class="label">รับเข้าทำงานแล้ว</div>
  </div>
</div>

<div class="dash-widgets" style="grid-template-columns:1.6fr 1fr;">
  <div class="widget-card">
    <h3>งานที่ประกาศอยู่ <a href="manage-jobs.php">ดูทั้งหมด →</a></h3>
    <?php foreach ($activeJobs as $job): ?>
      <div class="widget-row">
        <span class="widget-badge" style="background:#6c5ce7;"><?= icon('briefcase',18) ?></span>
        <div style="flex:1;min-width:0;">
          <div class="widget-title"><?= htmlspecialchars($job['title']) ?></div>
          <div class="widget-sub"><?= htmlspecialchars($job['location']) ?> · อัปเดต <?= date('d M Y', strtotime($job['created_at'])) ?></div>
        </div>
        <span class="widget-pill"><?= $job['app_count'] ?> ผู้สมัคร</span>
      </div>
    <?php endforeach; ?>
    <?php if (empty($activeJobs)): ?><p class="empty-state">ยังไม่มีงานที่เปิดรับสมัคร — <a href="post-job.php">ประกาศงานแรกของคุณ</a></p><?php endif; ?>
  </div>

  <div class="widget-card">
    <h3>การดำเนินการด่วน</h3>
    <div class="quick-actions">
      <a href="post-job.php" class="primary"><?= icon('plus',16) ?> สร้างประกาศรับสมัครงาน</a>
      <a href="applicants-all.php"><?= icon('clipboard',16) ?> ดูผู้สมัครทั้งหมด</a>
      <a href="manage-jobs.php"><?= icon('folder',16) ?> จัดการงานที่ประกาศ</a>
      <a href="company-profile.php"><?= icon('building',18) ?> แก้ไขข้อมูลบริษัท</a>
    </div>
  </div>
</div>

<div class="dash-widgets">
  <div class="widget-card">
    <h3>สถิติการรับสมัคร (แนวโน้ม)</h3>
    <div class="mini-chart-wrap">
      <svg viewBox="0 0 560 160" width="100%" height="160" preserveAspectRatio="none">
        <polyline fill="none" stroke="#6c5ce7" stroke-width="3" points="0,120 70,100 140,110 210,70 280,85 350,50 420,65 490,35 560,45"/>
        <polygon fill="rgba(108,92,231,.10)" points="0,120 70,100 140,110 210,70 280,85 350,50 420,65 490,35 560,45 560,160 0,160"/>
      </svg>
    </div>
  </div>

  <div class="widget-card">
    <h3>สัดส่วนสถานะผู้สมัคร</h3>
    <div class="donut-wrap">
      <div class="donut" style="background:conic-gradient(#e08a1e 0% <?= $pending ?>%, #2f8fe0 <?= $pending ?>% <?= $pending+$reviewed ?>%, #1f9d55 <?= $pending+$reviewed ?>% <?= $pending+$reviewed+$accepted ?>%, #e0473f <?= $pending+$reviewed+$accepted ?>% 100%);"></div>
      <div class="donut-legend">
        <div><span class="dot" style="background:#e08a1e;"></span> รอพิจารณา <?= $pending ?>%</div>
        <div><span class="dot" style="background:#2f8fe0;"></span> นัดสัมภาษณ์ <?= $reviewed ?>%</div>
        <div><span class="dot" style="background:#1f9d55;"></span> รับเข้าทำงาน <?= $accepted ?>%</div>
        <div><span class="dot" style="background:#e0473f;"></span> ปฏิเสธ <?= $rejected ?>%</div>
      </div>
    </div>
  </div>
</div>

<div class="widget-card">
  <h3>ผู้สมัครล่าสุด <a href="applicants-all.php">ดูทั้งหมด →</a></h3>
  <?php foreach ($recentApplicants as $a): ?>
    <div class="widget-row">
      <?= empBadge($a['full_name']) ?>
      <div style="flex:1;min-width:0;">
        <div class="widget-title"><?= htmlspecialchars($a['full_name']) ?></div>
        <div class="widget-sub"><?= htmlspecialchars($a['job_title']) ?> · <?= date('d M Y', strtotime($a['applied_at'])) ?></div>
      </div>
      <span class="widget-pill badge-<?= $a['status'] ?>"><?= ucfirst($a['status']) ?></span>
    </div>
  <?php endforeach; ?>
  <?php if (empty($recentApplicants)): ?><p class="empty-state">ยังไม่มีผู้สมัคร</p><?php endif; ?>
</div>

<?php require_once __DIR__ . '/includes/employer-footer.php'; ?>
