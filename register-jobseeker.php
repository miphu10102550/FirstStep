<?php
$pageTitle = "Register - Job Seeker";
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/auth.php';

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['full_name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm = $_POST['confirm_password'] ?? '';

    if ($name === '' || $email === '' || $password === '') {
        $error = 'Please fill in all required fields.';
    } elseif ($password !== $confirm) {
        $error = 'Passwords do not match.';
    } elseif (strlen($password) < 6) {
        $error = 'Password must be at least 6 characters.';
    } else {
        $chk = $pdo->prepare("SELECT id FROM users WHERE email=?");
        $chk->execute([$email]);
        if ($chk->fetch()) {
            $error = 'This email is already registered.';
        } else {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO users (role, full_name, email, password, phone) VALUES ('jobseeker', ?, ?, ?, ?)");
            $stmt->execute([$name, $email, $hash, $phone]);
            $userId = $pdo->lastInsertId();
            $pdo->prepare("INSERT INTO jobseeker_profiles (user_id) VALUES (?)")->execute([$userId]);

            $_SESSION['user_id'] = $userId;
            $_SESSION['user_name'] = $name;
            $_SESSION['user_role'] = 'jobseeker';
            $_SESSION['user_email'] = $email;
            header('Location: dashboard-jobseeker.php');
            exit;
        }
    }
}

require_once __DIR__ . '/includes/header.php';
?>
<div class="form-wrap">
  <h2>Register as Job Seeker</h2>
  <?php if ($error): ?><div class="alert alert-error"><?= htmlspecialchars($error) ?></div><?php endif; ?>
  <form method="post">
    <div class="form-group">
      <label>Full Name</label>
      <input type="text" name="full_name" required value="<?= htmlspecialchars($_POST['full_name'] ?? '') ?>">
    </div>
    <div class="form-group">
      <label>Email</label>
      <input type="email" name="email" required value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
    </div>
    <div class="form-group">
      <label>Phone</label>
      <input type="text" name="phone" value="<?= htmlspecialchars($_POST['phone'] ?? '') ?>">
    </div>
    <div class="form-group">
      <label>Password</label>
      <input type="password" name="password" required>
    </div>
    <div class="form-group">
      <label>Confirm Password</label>
      <input type="password" name="confirm_password" required>
    </div>
    <button type="submit" class="btn btn-primary btn-block">Create Account</button>
  </form>
  <div class="form-note">Already have an account? <a href="login.php" style="color:var(--green-dark);font-weight:600;">Log In</a></div>
</div>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
