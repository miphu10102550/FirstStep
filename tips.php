<?php
$pageTitle = "Tips & Insights";
require_once __DIR__ . '/config/database.php';

$category = trim($_GET['category'] ?? '');
$sql = "SELECT * FROM articles";
$params = [];
if ($category !== '') { $sql .= " WHERE category = :cat"; $params[':cat'] = $category; }
$sql .= " ORDER BY created_at DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$articles = $stmt->fetchAll();

$categories = $pdo->query("SELECT DISTINCT category FROM articles")->fetchAll(PDO::FETCH_COLUMN);

require_once __DIR__ . '/includes/header.php';
?>
<div class="page-header">
  <div class="container">
    <h1>Tips &amp; Insights</h1>
    <p>บทความและเคล็ดลับดี ๆ สำหรับการเริ่มต้นสายอาชีพ</p>
  </div>
</div>

<div class="container section">
  <div style="margin-bottom:24px;">
    <a href="tips.php" class="tag" style="<?= $category==''?'background:var(--green-dark);color:#fff;':'' ?>">All</a>
    <?php foreach ($categories as $c): ?>
      <a href="tips.php?category=<?= urlencode($c) ?>" class="tag" style="<?= $category==$c?'background:var(--green-dark);color:#fff;':'' ?>"><?= htmlspecialchars($c) ?></a>
    <?php endforeach; ?>
  </div>

  <div class="grid grid-3">
    <?php foreach ($articles as $a): ?>
    <a href="article-detail.php?id=<?= $a['id'] ?>" class="card article-card">
      <div class="tag"><?= htmlspecialchars($a['category']) ?></div>
      <div class="job-title"><?= htmlspecialchars($a['title']) ?></div>
      <p style="color:var(--muted);font-size:14px;"><?= htmlspecialchars(mb_substr($a['content'],0,110)) ?>...</p>
    </a>
    <?php endforeach; ?>
    <?php if (empty($articles)): ?>
      <p class="empty-state">No articles yet.</p>
    <?php endif; ?>
  </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
