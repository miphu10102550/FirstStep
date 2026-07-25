<?php
$pageTitle = "Manage Users";
require_once __DIR__ . '/../config/database.php';

if (isset($_GET['ban'])) {
    $id = (int)$_GET['ban'];
    $u = $pdo->prepare("SELECT status FROM users WHERE id=?");
    $u->execute([$id]);
    $u = $u->fetch();
    if ($u) {
        $new = $u['status'] === 'banned' ? 'active' : 'banned';
        $pdo->prepare("UPDATE users SET status=? WHERE id=?")->execute([$new, $id]);
    }
    header('Location: users.php');
    exit;
}
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $pdo->prepare("DELETE FROM users WHERE id=? AND role != 'admin'")->execute([$id]);
    header('Location: users.php');
    exit;
}

$role = $_GET['role'] ?? '';
$sql = "SELECT * FROM users";
$params = [];
if ($role) { $sql .= " WHERE role=?"; $params[] = $role; }
$sql .= " ORDER BY created_at DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$users = $stmt->fetchAll();

require_once __DIR__ . '/header.php';
?>
<h1>Manage Users</h1>
<div style="margin-bottom:20px;">
  <a href="users.php" class="tag" style="<?= $role==''?'background:var(--green-dark);color:#fff;':'' ?>">All</a>
  <a href="users.php?role=jobseeker" class="tag" style="<?= $role=='jobseeker'?'background:var(--green-dark);color:#fff;':'' ?>">Job Seekers</a>
  <a href="users.php?role=employer" class="tag" style="<?= $role=='employer'?'background:var(--green-dark);color:#fff;':'' ?>">Employers</a>
  <a href="users.php?role=admin" class="tag" style="<?= $role=='admin'?'background:var(--green-dark);color:#fff;':'' ?>">Admins</a>
</div>

<table>
  <thead><tr><th>Name</th><th>Email</th><th>Role</th><th>Status</th><th>Joined</th><th>Actions</th></tr></thead>
  <tbody>
    <?php foreach ($users as $u): ?>
    <tr>
      <td><?= htmlspecialchars($u['full_name']) ?></td>
      <td><?= htmlspecialchars($u['email']) ?></td>
      <td><?= ucfirst($u['role']) ?></td>
      <td><span class="badge badge-<?= $u['status'] ?>"><?= ucfirst($u['status']) ?></span></td>
      <td><?= date('d M Y', strtotime($u['created_at'])) ?></td>
      <td>
        <?php if ($u['role'] !== 'admin'): ?>
          <a href="users.php?ban=<?= $u['id'] ?>" style="color:var(--blue);font-weight:600;"><?= $u['status']==='banned'?'Unban':'Ban' ?></a> ·
          <a href="users.php?delete=<?= $u['id'] ?>" onclick="return confirm('Delete this user?');" style="color:#b3261e;font-weight:600;">Delete</a>
        <?php else: ?>
          <span style="color:var(--muted);">—</span>
        <?php endif; ?>
      </td>
    </tr>
    <?php endforeach; ?>
    <?php if (empty($users)): ?>
      <tr><td colspan="6" class="empty-state">No users found.</td></tr>
    <?php endif; ?>
  </tbody>
</table>

<?php require_once __DIR__ . '/footer.php'; ?>
