<?php
$pageTitle = "All Applicants";
require_once __DIR__ . '/includes/employer-header.php';
$company = $__company;

$status = $_GET['status'] ?? '';
$sql = "
  SELECT applications.*, users.full_name, users.email, users.phone, jobs.title AS job_title
  FROM applications
  JOIN users ON applications.jobseeker_id = users.id
  JOIN jobs ON applications.job_id = jobs.id
  WHERE jobs.company_id = ?
";
$params = [$company['id']];
if ($status) { $sql .= " AND applications.status = ?"; $params[] = $status; }
$sql .= " ORDER BY applications.applied_at DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$applications = $stmt->fetchAll();
?>
<div class="dash-topbar"><h1><span class="dash-topbar-icon"><?= icon('clipboard',18) ?></span> รายชื่อผู้สมัครทั้งหมด</h1></div>

<div style="margin-bottom:20px;">
  <a href="applicants-all.php" class="tag" style="<?= $status==''?'background:var(--green-dark);color:#fff;':'' ?>">All</a>
  <?php foreach (['pending','reviewed','accepted','rejected'] as $s): ?>
    <a href="applicants-all.php?status=<?= $s ?>" class="tag" style="<?= $status==$s?'background:var(--green-dark);color:#fff;':'' ?>"><?= ucfirst($s) ?></a>
  <?php endforeach; ?>
</div>

<div class="widget-card">
<table>
  <thead><tr><th>ผู้สมัคร</th><th>ติดต่อ</th><th>ตำแหน่งงาน</th><th>วันที่สมัคร</th><th>สถานะ</th><th>Resume</th></tr></thead>
  <tbody>
    <?php foreach ($applications as $a): ?>
    <tr>
      <td><?= htmlspecialchars($a['full_name']) ?></td>
      <td><?= htmlspecialchars($a['email']) ?><br><span style="color:var(--muted);font-size:12px;"><?= htmlspecialchars($a['phone']) ?></span></td>
      <td><?= htmlspecialchars($a['job_title']) ?></td>
      <td><?= date('d M Y', strtotime($a['applied_at'])) ?></td>
      <td><span class="badge badge-<?= $a['status'] ?>"><?= ucfirst($a['status']) ?></span></td>
      <td><?php if ($a['resume_file']): ?><a href="uploads/resumes/<?= htmlspecialchars($a['resume_file']) ?>" target="_blank">View</a><?php else: ?>—<?php endif; ?></td>
    </tr>
    <?php endforeach; ?>
    <?php if (empty($applications)): ?>
      <tr><td colspan="6" class="empty-state">No applicants found.</td></tr>
    <?php endif; ?>
  </tbody>
</table>
</div>

<?php require_once __DIR__ . '/includes/employer-footer.php'; ?>
