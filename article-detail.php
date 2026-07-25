<?php
$pageTitle = "Article";
require_once __DIR__ . '/config/database.php';

$id = (int)($_GET['id'] ?? 0);
$stmt = $pdo->prepare("SELECT * FROM articles WHERE id=?");
$stmt->execute([$id]);
$article = $stmt->fetch();
if (!$article) { header('Location: tips.php'); exit; }

require_once __DIR__ . '/includes/header.php';
?>
<div class="page-header">
  <div class="container">
    <span class="tag"><?= htmlspecialchars($article['category']) ?></span>
    <h1><?= htmlspecialchars($article['title']) ?></h1>
    <p><?= date('d M Y', strtotime($article['created_at'])) ?></p>
  </div>
</div>

<div class="container section">
  <div class="card" style="max-width:800px;margin:0 auto;">
    <p style="font-size:16px;line-height:1.8;"><?= nl2br(htmlspecialchars($article['content'])) ?></p>
  </div>
  <div style="text-align:center;margin-top:24px;">
    <a href="tips.php" class="btn btn-outline">Back to Tips &amp; Insights</a>
  </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
