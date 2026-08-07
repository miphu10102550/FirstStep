<?php
$pageTitle = "Notifications";
require_once __DIR__ . '/includes/employer-header.php';
$company = $__company;

$items = [];

$newApps = $pdo->prepare("
  SELECT users.full_name, jobs.title AS job_title, applications.applied_at
  FROM applications
  JOIN users ON applications.jobseeker_id = users.id
  JOIN jobs ON applications.job_id = jobs.id
  WHERE jobs.company_id = ?
  ORDER BY applications.applied_at DESC LIMIT 10
");
$newApps->execute([$company['id']]);
foreach ($newApps->fetchAll() as $a) {
    $items[] = ['icon' => '<?= icon('clipboard',18) ?>', 'text' => $a['full_name'] . ' สมัครงาน ' . $a['job_title'], 'time' => $a['applied_at']];
}

$myJobs = $pdo->prepare("SELECT title, created_at FROM jobs WHERE company_id=? ORDER BY created_at DESC LIMIT 5");
$myJobs->execute([$company['id']]);
foreach ($myJobs->fetchAll() as $j) {
    $items[] = ['icon' => '<?= icon('briefcase',16) ?>', 'text' => 'คุณประกาศงาน: ' . $j['title'], 'time' => $j['created_at']];
}

usort($items, fn($a, $b) => strtotime($b['time']) <=> strtotime($a['time']));
$items = array_slice($items, 0, 15);
?>
<div class="dash-topbar"><h1><span class="dash-topbar-icon"><?= icon('bell',18) ?></span> แจ้งเตือน</h1></div>

<div class="widget-card">
  <?php foreach ($items as $it): ?>
    <div class="widget-row">
      <span class="widget-badge" style="background:var(--green-light);color:var(--green-dark);"><?= $it['icon'] ?></span>
      <div style="flex:1;">
        <div class="widget-title" style="font-weight:600;"><?= htmlspecialchars($it['text']) ?></div>
        <div class="widget-sub"><?= date('d M Y, H:i', strtotime($it['time'])) ?></div>
      </div>
    </div>
  <?php endforeach; ?>
  <?php if (empty($items)): ?><p class="empty-state">ไม่มีการแจ้งเตือน</p><?php endif; ?>
</div>

<?php require_once __DIR__ . '/includes/employer-footer.php'; ?>
