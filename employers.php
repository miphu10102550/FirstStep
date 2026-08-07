<?php
$pageTitle = "For Employers";
require_once __DIR__ . '/includes/header.php';
?>
<div class="page-header">
  <div class="container">
    <h1>For Employers</h1>
    <p>ค้นหาคนรุ่นใหม่ไฟแรงสำหรับทีมของคุณ</p>
  </div>
</div>

<div class="container section">
  <div class="grid grid-3">
    <div class="card">
      <h3><?= icon('note',22) ?> Post a Job</h3>
      <p>สร้างประกาศงานได้ง่าย ๆ และเข้าถึงผู้สมัครที่ใช่ในไม่กี่นาที</p>
    </div>
    <div class="card">
      <h3><?= icon('search',22) ?> Search Candidates</h3>
      <p>ค้นหาโปรไฟล์ผู้สมัครงานที่ตรงกับความต้องการของบริษัทคุณ</p>
    </div>
    <div class="card">
      <h3><?= icon('chart',22) ?> Manage Applicants</h3>
      <p>จัดการใบสมัครและติดตามสถานะผู้สมัครได้ในที่เดียว</p>
    </div>
  </div>

  <div style="text-align:center;margin-top:40px;">
    <?php require_once __DIR__ . '/includes/auth.php'; $user = currentUser(); ?>
    <?php if ($user && $user['role']=='employer'): ?>
      <a href="post-job.php" class="btn btn-primary">Post a Job</a>
      <a href="dashboard-employer.php" class="btn btn-outline">Go to Dashboard</a>
    <?php else: ?>
      <a href="register-employer.php" class="btn btn-primary">Register as Employer</a>
      <a href="login.php" class="btn btn-outline">Log In</a>
    <?php endif; ?>
  </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
