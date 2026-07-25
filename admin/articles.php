<?php
$pageTitle = "Manage Articles";
require_once __DIR__ . '/../config/database.php';

if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $pdo->prepare("DELETE FROM articles WHERE id=?")->execute([$id]);
    header('Location: articles.php');
    exit;
}

$editArticle = null;
if (isset($_GET['edit'])) {
    $id = (int)$_GET['edit'];
    $stmt = $pdo->prepare("SELECT * FROM articles WHERE id=?");
    $stmt->execute([$id]);
    $editArticle = $stmt->fetch();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = (int)($_POST['id'] ?? 0);
    $title = trim($_POST['title'] ?? '');
    $category = trim($_POST['category'] ?? '');
    $content = trim($_POST['content'] ?? '');

    if ($title !== '' && $content !== '') {
        if ($id > 0) {
            $pdo->prepare("UPDATE articles SET title=?, category=?, content=? WHERE id=?")
                ->execute([$title, $category, $content, $id]);
        } else {
            $pdo->prepare("INSERT INTO articles (title, category, content) VALUES (?,?,?)")
                ->execute([$title, $category, $content]);
        }
    }
    header('Location: articles.php');
    exit;
}

$articles = $pdo->query("SELECT * FROM articles ORDER BY created_at DESC")->fetchAll();

require_once __DIR__ . '/header.php';
?>
<h1>Manage Articles</h1>

<div class="two-col">
  <div>
    <table>
      <thead><tr><th>Title</th><th>Category</th><th>Date</th><th>Actions</th></tr></thead>
      <tbody>
        <?php foreach ($articles as $a): ?>
        <tr>
          <td><?= htmlspecialchars($a['title']) ?></td>
          <td><?= htmlspecialchars($a['category']) ?></td>
          <td><?= date('d M Y', strtotime($a['created_at'])) ?></td>
          <td>
            <a href="articles.php?edit=<?= $a['id'] ?>" style="color:var(--blue);font-weight:600;">Edit</a> ·
            <a href="articles.php?delete=<?= $a['id'] ?>" onclick="return confirm('Delete this article?');" style="color:#b3261e;font-weight:600;">Delete</a>
          </td>
        </tr>
        <?php endforeach; ?>
        <?php if (empty($articles)): ?>
          <tr><td colspan="4" class="empty-state">No articles yet.</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>

  <div class="filter-box">
    <h4><?= $editArticle ? 'Edit Article' : 'Add New Article' ?></h4>
    <form method="post">
      <input type="hidden" name="id" value="<?= $editArticle['id'] ?? 0 ?>">
      <div class="form-group">
        <label>Title</label>
        <input type="text" name="title" required value="<?= htmlspecialchars($editArticle['title'] ?? '') ?>">
      </div>
      <div class="form-group">
        <label>Category</label>
        <input type="text" name="category" value="<?= htmlspecialchars($editArticle['category'] ?? '') ?>" placeholder="e.g. Career Tips">
      </div>
      <div class="form-group">
        <label>Content</label>
        <textarea name="content" rows="8" required><?= htmlspecialchars($editArticle['content'] ?? '') ?></textarea>
      </div>
      <button type="submit" class="btn btn-primary btn-block"><?= $editArticle ? 'Update Article' : 'Publish Article' ?></button>
    </form>
  </div>
</div>

<?php require_once __DIR__ . '/footer.php'; ?>
