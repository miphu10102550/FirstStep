<?php
$pageTitle = "Dashboard";
require_once __DIR__ . '/../config/database.php';

$totalUsers = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
$totalJobSeekers = $pdo->query("SELECT COUNT(*) FROM users WHERE role='jobseeker'")->fetchColumn();
$totalEmployers = $pdo->query("SELECT COUNT(*) FROM users WHERE role='employer'")->fetchColumn();
$totalJobs = $pdo->query("SELECT COUNT(*) FROM jobs")->fetchColumn();
$totalCompanies = $pdo->query("SELECT COUNT(*) FROM companies")->fetchColumn();
$totalApplications = $pdo->query("SELECT COUNT(*) FROM applications")->fetchColumn();

$recentUsers = $pdo->query("SELECT * FROM users ORDER BY created_at DESC LIMIT 5")->fetchAll();

require_once __DIR__ . '/header.php';
?>
<h1>Admin Dashboard</h1>
<p style="color:var(--muted);margin-bottom:26px;">ภาพรวมระบบ FirstStep</p>

<div class="grid grid-4" style="margin-bottom:30px;">
  <div class="stat-box"><div class="num"><?= $totalUsers ?></div><div class="label">Total Users</div></div>
  <div class="stat-box"><div class="num"><?= $totalJobSeekers ?></div><div class="label">Job Seekers</div></div>
  <div class="stat-box"><div class="num"><?= $totalEmployers ?></div><div class="label">Employers</div></div>
  <div class="stat-box"><div class="num"><?= $totalCompanies ?></div><div class="label">Companies</div></div>
  <div class="stat-box"><div class="num"><?= $totalJobs ?></div><div class="label">Jobs Posted</div></div>
  <div class="stat-box"><div class="num"><?= $totalApplications ?></div><div class="label">Applications</div></div>
</div>

<h3>Recently Registered Users</h3>
<table>
  <thead><tr><th>Name</th><th>Email</th><th>Role</th><th>Registered</th></tr></thead>
  <tbody>
    <?php foreach ($recentUsers as $u): ?>
    <tr>
      <td><?= htmlspecialchars($u['full_name']) ?></td>
      <td><?= htmlspecialchars($u['email']) ?></td>
      <td><?= ucfirst($u['role']) ?></td>
      <td><?= date('d M Y', strtotime($u['created_at'])) ?></td>
    </tr>
    <?php endforeach; ?>
  </tbody>
</table>

<?php require_once __DIR__ . '/footer.php'; ?>
