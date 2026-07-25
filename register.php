<?php
$pageTitle = "Register";
require_once __DIR__ . '/includes/header.php';
?>
<div class="form-wrap">
  <h2>Create an Account</h2>
  <p style="text-align:center;color:var(--muted);margin-bottom:24px;">เลือกประเภทบัญชีของคุณ</p>
  <a href="register-jobseeker.php" class="btn btn-primary btn-block" style="margin-bottom:12px;">I'm a Job Seeker</a>
  <a href="register-employer.php" class="btn btn-outline btn-block">I'm an Employer</a>
  <div class="form-note">Already have an account? <a href="login.php" style="color:var(--green-dark);font-weight:600;">Log In</a></div>
</div>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
