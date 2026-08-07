<?php
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/auth.php';
requireRole('employer');
$user = currentUser();

$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['full_name'] ?? '');
    $newPassword = $_POST['new_password'] ?? '';
    $confirm = $_POST['confirm_password'] ?? '';

    if ($name === '') {
        $error = 'กรุณากรอกชื่อ';
    } elseif ($newPassword !== '' && $newPassword !== $confirm) {
        $error = 'รหัสผ่านใหม่ไม่ตรงกัน';
    } elseif ($newPassword !== '' && strlen($newPassword) < 6) {
        $error = 'รหัสผ่านต้องมีอย่างน้อย 6 ตัวอักษร';
    } else {
        if ($newPassword !== '') {
            $hash = password_hash($newPassword, PASSWORD_DEFAULT);
            $pdo->prepare("UPDATE users SET full_name=?, password=? WHERE id=?")->execute([$name, $hash, $user['id']]);
        } else {
            $pdo->prepare("UPDATE users SET full_name=? WHERE id=?")->execute([$name, $user['id']]);
        }
        $_SESSION['user_name'] = $name;
        $success = 'บันทึกการตั้งค่าเรียบร้อยแล้ว';
    }
}

$pageTitle = "Settings";
require_once __DIR__ . '/includes/employer-header.php';

$userRow = $pdo->prepare("SELECT * FROM users WHERE id=?");
$userRow->execute([$user['id']]);
$userRow = $userRow->fetch();
?>
<div class="dash-topbar"><h1><span class="dash-topbar-icon"><?= icon('settings',18) ?></span> ตั้งค่าระบบ</h1></div>

<div class="widget-card" style="max-width:520px;">
  <h3>ข้อมูลบัญชีนายจ้าง</h3>
  <?php if ($success): ?><div class="alert alert-success"><?= htmlspecialchars($success) ?></div><?php endif; ?>
  <?php if ($error): ?><div class="alert alert-error"><?= htmlspecialchars($error) ?></div><?php endif; ?>
  <form method="post">
    <div class="form-group">
      <label>ชื่อ</label>
      <input type="text" name="full_name" required value="<?= htmlspecialchars($userRow['full_name']) ?>">
    </div>
    <div class="form-group">
      <label>อีเมล</label>
      <input type="email" value="<?= htmlspecialchars($userRow['email']) ?>" disabled>
    </div>
    <div class="form-group">
      <label>รหัสผ่านใหม่ (เว้นว่างหากไม่เปลี่ยน)</label>
      <input type="password" name="new_password">
    </div>
    <div class="form-group">
      <label>ยืนยันรหัสผ่านใหม่</label>
      <input type="password" name="confirm_password">
    </div>
    <button type="submit" class="btn btn-primary btn-block">บันทึกการตั้งค่า</button>
  </form>
</div>

<?php require_once __DIR__ . '/includes/employer-footer.php'; ?>
