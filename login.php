<?php
$pageTitle = "Log In";
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/auth.php';

if (isLoggedIn()) { redirectByRole(); }

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    $stmt = $pdo->prepare("SELECT * FROM users WHERE email=?");
    $stmt->execute([$email]);
    $u = $stmt->fetch();

    if ($u && password_verify($password, $u['password'])) {
        if ($u['status'] === 'banned') {
            $error = 'Your account has been suspended.';
        } else {
            $_SESSION['user_id'] = $u['id'];
            $_SESSION['user_name'] = $u['full_name'];
            $_SESSION['user_role'] = $u['role'];
            $_SESSION['user_email'] = $u['email'];
            redirectByRole();
        }
    } else {
        $error = 'Invalid email or password.';
    }
}

require_once __DIR__ . '/includes/header.php';
?>
<div class="form-wrap">
  <h2>Log In</h2>
  <?php if ($error): ?><div class="alert alert-error"><?= htmlspecialchars($error) ?></div><?php endif; ?>
  <form method="post">
    <div class="form-group">
      <label>Email</label>
      <input type="email" name="email" required value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
    </div>
    <div class="form-group">
      <label>Password</label>
      <input type="password" name="password" required>
    </div>
    <button type="submit" class="btn btn-primary btn-block">Log In</button>
  </form>
  <div class="form-note">Don't have an account? <a href="register.php" style="color:var(--green-dark);font-weight:600;">Register</a></div>
</div>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
