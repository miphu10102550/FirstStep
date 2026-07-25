<?php
$pageTitle = "Companies";
require_once __DIR__ . '/config/database.php';

$q = trim($_GET['q'] ?? '');
$sql = "SELECT * FROM companies";
$params = [];
if ($q !== '') { $sql .= " WHERE name LIKE :q"; $params[':q'] = "%$q%"; }
$sql .= " ORDER BY created_at DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$companies = $stmt->fetchAll();

require_once __DIR__ . '/includes/header.php';
?>
<div class="page-header">
  <div class="container">
    <h1>Companies</h1>
    <p>สำรวจบริษัทที่กำลังเปิดรับสมัครงาน</p>
  </div>
</div>

<div class="container section">
  <form method="get" style="margin-bottom:26px;max-width:420px;">
    <div style="display:flex;gap:10px;">
      <input type="text" name="q" value="<?= htmlspecialchars($q) ?>" placeholder="Search companies..." style="flex:1;padding:11px 14px;border:1px solid var(--border);border-radius:8px;">
      <button class="btn btn-primary" type="submit">Search</button>
    </div>
  </form>

  <div class="grid grid-4">
    <?php foreach ($companies as $c):
        $jobCount = $pdo->prepare("SELECT COUNT(*) FROM jobs WHERE company_id=? AND status='open'");
        $jobCount->execute([$c['id']]);
        $count = $jobCount->fetchColumn();
    ?>
    <a href="company-detail.php?id=<?= $c['id'] ?>" class="card">
      <div class="job-title"><?= htmlspecialchars($c['name']) ?></div>
      <div class="company"><?= htmlspecialchars($c['location']) ?></div>
      <div class="job-meta"><?= $count ?> open position(s)</div>
    </a>
    <?php endforeach; ?>
    <?php if (empty($companies)): ?>
      <p class="empty-state">No companies found.</p>
    <?php endif; ?>
  </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
