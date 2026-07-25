<?php
$pageTitle = "My Profile";
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/auth.php';
requireRole('jobseeker');
$user = currentUser();

$stmt = $pdo->prepare("SELECT * FROM jobseeker_profiles WHERE user_id=?");
$stmt->execute([$user['id']]);
$profile = $stmt->fetch();
if (!$profile) {
    $pdo->prepare("INSERT INTO jobseeker_profiles (user_id) VALUES (?)")->execute([$user['id']]);
    $stmt->execute([$user['id']]);
    $profile = $stmt->fetch();
}

$userRow = $pdo->prepare("SELECT * FROM users WHERE id=?");
$userRow->execute([$user['id']]);
$userRow = $userRow->fetch();

$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fullName = trim($_POST['full_name'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $education = trim($_POST['education'] ?? '');
    $skills = trim($_POST['skills'] ?? '');
    $bio = trim($_POST['bio'] ?? '');
    $prefLocation = trim($_POST['preferred_location'] ?? '');
    $salaryMin = (int)($_POST['expected_salary_min'] ?? 0);
    $salaryMax = (int)($_POST['expected_salary_max'] ?? 0);

    $resumeFile = $profile['resume_file'];
    if (!empty($_FILES['resume']['name'])) {
        $ext = pathinfo($_FILES['resume']['name'], PATHINFO_EXTENSION);
        $allowed = ['pdf','doc','docx'];
        if (in_array(strtolower($ext), $allowed)) {
            $newName = 'resume_' . $user['id'] . '_' . time() . '.' . $ext;
            $dest = __DIR__ . '/uploads/resumes/' . $newName;
            if (move_uploaded_file($_FILES['resume']['tmp_name'], $dest)) {
                $resumeFile = $newName;
            }
        } else {
            $error = 'Resume must be a PDF or Word document.';
        }
    }

    if (!$error) {
        $pdo->prepare("UPDATE users SET full_name=?, phone=? WHERE id=?")
            ->execute([$fullName, $phone, $user['id']]);

        $pdo->prepare("UPDATE jobseeker_profiles SET education=?, skills=?, bio=?, preferred_location=?, expected_salary_min=?, expected_salary_max=?, resume_file=? WHERE user_id=?")
            ->execute([$education, $skills, $bio, $prefLocation, $salaryMin, $salaryMax, $resumeFile, $user['id']]);

        $_SESSION['user_name'] = $fullName;
        $success = 'Profile updated successfully.';

        $stmt->execute([$user['id']]);
        $profile = $stmt->fetch();
        $userRow['full_name'] = $fullName;
        $userRow['phone'] = $phone;
    }
}

require_once __DIR__ . '/includes/header.php';
?>
<div class="page-header">
  <div class="container">
    <h1>My Profile</h1>
    <p>อัปเดตข้อมูลส่วนตัวและเรซูเม่ของคุณ</p>
  </div>
</div>

<div class="container section">
  <div class="dashboard-nav">
    <a href="dashboard-jobseeker.php">My Applications</a>
    <a href="profile.php" class="active">My Profile</a>
    <a href="jobs.php">Find Jobs</a>
  </div>

  <div class="card" style="max-width:650px;">
    <?php if ($success): ?><div class="alert alert-success"><?= htmlspecialchars($success) ?></div><?php endif; ?>
    <?php if ($error): ?><div class="alert alert-error"><?= htmlspecialchars($error) ?></div><?php endif; ?>
    <form method="post" enctype="multipart/form-data">
      <div class="form-group">
        <label>Full Name</label>
        <input type="text" name="full_name" value="<?= htmlspecialchars($userRow['full_name']) ?>" required>
      </div>
      <div class="form-group">
        <label>Email</label>
        <input type="email" value="<?= htmlspecialchars($userRow['email']) ?>" disabled>
      </div>
      <div class="form-group">
        <label>Phone</label>
        <input type="text" name="phone" value="<?= htmlspecialchars($userRow['phone']) ?>">
      </div>
      <div class="form-group">
        <label>Education</label>
        <input type="text" name="education" value="<?= htmlspecialchars($profile['education']) ?>" placeholder="e.g. B.Sc. Computer Science">
      </div>
      <div class="form-group">
        <label>Skills</label>
        <textarea name="skills" rows="3" placeholder="e.g. PHP, HTML, CSS, Teamwork"><?= htmlspecialchars($profile['skills']) ?></textarea>
      </div>
      <div class="form-group">
        <label>Short Bio</label>
        <textarea name="bio" rows="3"><?= htmlspecialchars($profile['bio']) ?></textarea>
      </div>
      <div class="form-group">
        <label>Preferred Location</label>
        <input type="text" name="preferred_location" value="<?= htmlspecialchars($profile['preferred_location']) ?>">
      </div>
      <div class="form-group" style="display:flex;gap:10px;">
        <div style="flex:1;">
          <label>Expected Salary (Min)</label>
          <input type="number" name="expected_salary_min" value="<?= (int)$profile['expected_salary_min'] ?>">
        </div>
        <div style="flex:1;">
          <label>Expected Salary (Max)</label>
          <input type="number" name="expected_salary_max" value="<?= (int)$profile['expected_salary_max'] ?>">
        </div>
      </div>
      <div class="form-group">
        <label>Resume (PDF or Word)</label>
        <input type="file" name="resume" accept=".pdf,.doc,.docx">
        <?php if ($profile['resume_file']): ?>
          <p style="font-size:13px;color:var(--muted);margin-top:6px;">Current file: <?= htmlspecialchars($profile['resume_file']) ?></p>
        <?php endif; ?>
      </div>
      <button type="submit" class="btn btn-primary btn-block">Save Profile</button>
    </form>
  </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
