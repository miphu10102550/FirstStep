<?php
$pageTitle = "Manage Companies";
require_once __DIR__ . '/../config/database.php';

if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $pdo->prepare("DELETE FROM companies WHERE id=?")->execute([$id]);
    header('Location: companies.php');
    exit;
}

$companies = $pdo->query("
  SELECT companies.*, users.email AS employer_email,
  (SELECT COUNT(*) FROM jobs WHERE jobs.company_id = companies.id) AS job_count
  FROM companies JOIN users ON companies.employer_id = users.id
  ORDER BY companies.created_at DESC
")->fetchAll();

require_once __DIR__ . '/header.php';
?>
<h1>Manage Companies</h1>
<table>
  <thead><tr><th>Company</th><th>Employer</th><th>Location</th><th>Jobs Posted</th><th>Actions</th></tr></thead>
  <tbody>
    <?php foreach ($companies as $c): ?>
    <tr>
      <td><?= htmlspecialchars($c['name']) ?></td>
      <td><?= htmlspecialchars($c['employer_email']) ?></td>
      <td><?= htmlspecialchars($c['location']) ?></td>
      <td><?= $c['job_count'] ?></td>
      <td>
        <a href="../company-detail.php?id=<?= $c['id'] ?>" target="_blank" style="color:var(--blue);font-weight:600;">View</a> ·
        <a href="companies.php?delete=<?= $c['id'] ?>" onclick="return confirm('Delete this company and all its jobs?');" style="color:#b3261e;font-weight:600;">Delete</a>
      </td>
    </tr>
    <?php endforeach; ?>
    <?php if (empty($companies)): ?>
      <tr><td colspan="5" class="empty-state">No companies found.</td></tr>
    <?php endif; ?>
  </tbody>
</table>

<?php require_once __DIR__ . '/footer.php'; ?>
