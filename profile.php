<?php
$pageTitle = "My Profile";
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/auth.php';
requireRole('jobseeker');
$user = currentUser();
$provinces = require __DIR__ . '/includes/provinces.php';

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
    $firstName = trim($_POST['first_name'] ?? '');
    $lastName = trim($_POST['last_name'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $birthdate = trim($_POST['birthdate'] ?? '') ?: null;
    $gender = trim($_POST['gender'] ?? '');
    $address = trim($_POST['address'] ?? '');
    $province = trim($_POST['province'] ?? '');
    $district = trim($_POST['district'] ?? '');
    $subdistrict = trim($_POST['subdistrict'] ?? '');
    $postalCode = trim($_POST['postal_code'] ?? '');
    $bio = mb_substr(trim($_POST['bio'] ?? ''), 0, 300);
    $education = trim($_POST['education'] ?? '');
    $skills = trim($_POST['skills'] ?? '');
    $prefLocation = trim($_POST['preferred_location'] ?? '');
    $salaryMin = (int)($_POST['expected_salary_min'] ?? 0);
    $salaryMax = (int)($_POST['expected_salary_max'] ?? 0);
    $workExperience = trim($_POST['work_experience'] ?? '');

    $avatarFile = $userRow['avatar'];
    if (!empty($_FILES['avatar']['name'])) {
        $ext = strtolower(pathinfo($_FILES['avatar']['name'], PATHINFO_EXTENSION));
        if (in_array($ext, ['jpg','jpeg','png']) && $_FILES['avatar']['size'] <= 5 * 1024 * 1024) {
            $newName = 'avatar_' . $user['id'] . '_' . time() . '.' . $ext;
            if (move_uploaded_file($_FILES['avatar']['tmp_name'], __DIR__ . '/uploads/logos/' . $newName)) {
                $avatarFile = $newName;
            }
        } else {
            $error = 'รูปโปรไฟล์ต้องเป็น JPG/PNG และขนาดไม่เกิน 5MB';
        }
    }

    $resumeFile = $profile['resume_file'];
    if (!empty($_FILES['resume']['name'])) {
        $ext = strtolower(pathinfo($_FILES['resume']['name'], PATHINFO_EXTENSION));
        if (in_array($ext, ['pdf','doc','docx'])) {
            $newName = 'resume_' . $user['id'] . '_' . time() . '.' . $ext;
            if (move_uploaded_file($_FILES['resume']['tmp_name'], __DIR__ . '/uploads/resumes/' . $newName)) {
                $resumeFile = $newName;
            }
        }
    }

    if (!$error) {
        $fullName = trim($firstName . ' ' . $lastName) ?: $userRow['full_name'];
        $pdo->prepare("UPDATE users SET full_name=?, first_name=?, last_name=?, phone=?, avatar=? WHERE id=?")
            ->execute([$fullName, $firstName, $lastName, $phone, $avatarFile, $user['id']]);

        $pdo->prepare("UPDATE jobseeker_profiles SET education=?, skills=?, bio=?, preferred_location=?, expected_salary_min=?, expected_salary_max=?, resume_file=?, birthdate=?, gender=?, address=?, province=?, district=?, subdistrict=?, postal_code=?, work_experience=? WHERE user_id=?")
            ->execute([$education, $skills, $bio, $prefLocation, $salaryMin, $salaryMax, $resumeFile, $birthdate, $gender, $address, $province, $district, $subdistrict, $postalCode, $workExperience, $user['id']]);

        $_SESSION['user_name'] = $fullName;
        $success = 'บันทึกโปรไฟล์เรียบร้อยแล้ว';

        $stmt->execute([$user['id']]);
        $profile = $stmt->fetch();
        $userRow = $pdo->prepare("SELECT * FROM users WHERE id=?");
        $userRow->execute([$user['id']]);
        $userRow = $userRow->fetch();
    }
}

require_once __DIR__ . '/includes/header.php';
?>
<div class="page-header">
  <div class="container">
    <h1>สร้างโปรไฟล์ผู้ใช้</h1>
    <p>กรอกข้อมูลให้ครบถ้วนเพื่อเพิ่มโอกาสในการหางาน</p>
  </div>
</div>

<div class="container section">
  <?php if ($success): ?><div class="alert alert-success" style="max-width:700px;"><?= htmlspecialchars($success) ?></div><?php endif; ?>
  <?php if ($error): ?><div class="alert alert-error" style="max-width:700px;"><?= htmlspecialchars($error) ?></div><?php endif; ?>

  <div class="profile-shell">
    <div>
      <div class="profile-tab active" data-tab="personal">
        <span class="tab-icon"><?= icon('user',18) ?></span> ข้อมูลส่วนตัว
      </div>
      <div class="profile-tab" data-tab="education">
        <span class="tab-icon"><?= icon('graduation',18) ?></span> การศึกษา
      </div>
      <div class="profile-tab" data-tab="experience">
        <span class="tab-icon"><?= icon('briefcase',18) ?></span> ประสบการณ์ทำงาน
      </div>
    </div>

    <form method="post" enctype="multipart/form-data">
      <!-- Panel 1: Personal Info -->
      <div class="profile-panel active card" id="panel-personal">
        <div class="avatar-upload">
          <div class="preview">
            <?php if (!empty($userRow['avatar'])): ?>
              <img src="uploads/logos/<?= htmlspecialchars($userRow['avatar']) ?>" alt="Avatar">
            <?php else: ?><?= icon('camera',22) ?><?php endif; ?>
          </div>
          <div>
            <label for="avatarInput" class="btn btn-outline" style="cursor:pointer;">อัปโหลดรูปโปรไฟล์</label>
            <input type="file" id="avatarInput" name="avatar" accept=".jpg,.jpeg,.png" style="display:none;">
            <p style="font-size:12px;color:var(--muted);margin:8px 0 0;">ขนาดแนะนำ 400x400px<br>ไฟล์ JPG, PNG (ไม่เกิน 5MB)</p>
          </div>
        </div>

        <div class="form-row-2">
          <div class="form-group">
            <label>* ชื่อจริง</label>
            <input type="text" name="first_name" required placeholder="กรอกชื่อจริง" value="<?= htmlspecialchars($userRow['first_name'] ?? '') ?>">
          </div>
          <div class="form-group">
            <label>* นามสกุล</label>
            <input type="text" name="last_name" required placeholder="กรอกนามสกุล" value="<?= htmlspecialchars($userRow['last_name'] ?? '') ?>">
          </div>
        </div>

        <div class="form-row-2">
          <div class="form-group">
            <label>* อีเมล</label>
            <input type="email" value="<?= htmlspecialchars($userRow['email']) ?>" disabled>
          </div>
          <div class="form-group">
            <label>* เบอร์โทรศัพท์</label>
            <input type="text" name="phone" required placeholder="0xx-xxx-xxxx" value="<?= htmlspecialchars($userRow['phone']) ?>">
          </div>
        </div>

        <div class="form-row-2">
          <div class="form-group">
            <label>วันเกิด</label>
            <input type="date" name="birthdate" value="<?= htmlspecialchars($profile['birthdate'] ?? '') ?>">
          </div>
          <div class="form-group">
            <label>เพศ</label>
            <select name="gender">
              <option value="">เลือกเพศ</option>
              <?php foreach (['ชาย','หญิง','ไม่ระบุ'] as $g): ?>
                <option value="<?= $g ?>" <?= ($profile['gender'] ?? '')==$g?'selected':'' ?>><?= $g ?></option>
              <?php endforeach; ?>
            </select>
          </div>
        </div>

        <div class="form-group">
          <label>* ที่อยู่ปัจจุบัน</label>
          <input type="text" name="address" required placeholder="กรอกที่อยู่ปัจจุบัน" value="<?= htmlspecialchars($profile['address'] ?? '') ?>">
        </div>

        <div class="form-row-3" style="grid-template-columns:1fr 1fr 1fr 0.8fr;">
          <div class="form-group">
            <label>จังหวัด</label>
            <select name="province">
              <option value="">เลือกจังหวัด</option>
              <?php foreach ($provinces as $p): ?>
                <option value="<?= htmlspecialchars($p) ?>" <?= ($profile['province'] ?? '')==$p?'selected':'' ?>><?= htmlspecialchars($p) ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="form-group">
            <label>อำเภอ/เขต</label>
            <input type="text" name="district" placeholder="เลือกอำเภอ/เขต" value="<?= htmlspecialchars($profile['district'] ?? '') ?>">
          </div>
          <div class="form-group">
            <label>ตำบล/แขวง</label>
            <input type="text" name="subdistrict" placeholder="เลือกตำบล/แขวง" value="<?= htmlspecialchars($profile['subdistrict'] ?? '') ?>">
          </div>
          <div class="form-group">
            <label>รหัสไปรษณีย์</label>
            <input type="text" name="postal_code" placeholder="รหัสไปรษณีย์" value="<?= htmlspecialchars($profile['postal_code'] ?? '') ?>">
          </div>
        </div>

        <div class="form-row-2">
          <div class="form-group">
            <label>เงินเดือนที่คาดหวัง (บาท)</label>
            <div style="display:flex;gap:8px;">
              <input type="number" name="expected_salary_min" placeholder="ต่ำสุด" value="<?= (int)($profile['expected_salary_min'] ?? 0) ?>">
              <input type="number" name="expected_salary_max" placeholder="สูงสุด" value="<?= (int)($profile['expected_salary_max'] ?? 0) ?>">
            </div>
          </div>
          <div class="form-group">
            <label>พื้นที่ที่สนใจทำงาน</label>
            <input type="text" name="preferred_location" placeholder="เช่น กรุงเทพฯ" value="<?= htmlspecialchars($profile['preferred_location'] ?? '') ?>">
          </div>
        </div>

        <div class="form-group">
          <label>แนะนำตัวเอง (ไม่เกิน 300 ตัวอักษร)</label>
          <textarea name="bio" id="bioInput" maxlength="300" rows="4" placeholder="แนะนำตัวเองสั้น ๆ เกี่ยวกับตัวคุณ..."><?= htmlspecialchars($profile['bio'] ?? '') ?></textarea>
          <div class="char-count"><span id="bioCount"><?= mb_strlen($profile['bio'] ?? '') ?></span>/300</div>
        </div>
      </div>

      <!-- Panel 2: Education -->
      <div class="profile-panel card" id="panel-education">
        <div class="form-group">
          <label>ระดับการศึกษา / สถาบันการศึกษา</label>
          <input type="text" name="education" placeholder="เช่น ปริญญาตรี วิทยาการคอมพิวเตอร์ มหาวิทยาลัย..." value="<?= htmlspecialchars($profile['education'] ?? '') ?>">
        </div>
        <div class="form-group">
          <label>ทักษะ (Skills)</label>
          <textarea name="skills" rows="4" placeholder="เช่น PHP, HTML, CSS, การทำงานเป็นทีม"><?= htmlspecialchars($profile['skills'] ?? '') ?></textarea>
        </div>
      </div>

      <!-- Panel 3: Work Experience -->
      <div class="profile-panel card" id="panel-experience">
        <div class="form-group">
          <label>ประสบการณ์ทำงาน</label>
          <textarea name="work_experience" rows="6" placeholder="อธิบายประสบการณ์การทำงาน ฝึกงาน หรือกิจกรรมที่เกี่ยวข้อง..."><?= htmlspecialchars($profile['work_experience'] ?? '') ?></textarea>
        </div>
        <div class="form-group">
          <label>เรซูเม่ (PDF หรือ Word)</label>
          <input type="file" name="resume" accept=".pdf,.doc,.docx">
          <?php if (!empty($profile['resume_file'])): ?>
            <p style="font-size:12px;color:var(--muted);margin-top:6px;">ไฟล์ปัจจุบัน: <?= htmlspecialchars($profile['resume_file']) ?></p>
          <?php endif; ?>
        </div>
      </div>

      <div style="margin-top:6px;">
        <button type="submit" class="btn btn-primary btn-block">บันทึกโปรไฟล์ทั้งหมด</button>
      </div>
    </form>
  </div>
</div>

<script>
document.querySelectorAll('.profile-tab').forEach(function(tab){
  tab.addEventListener('click', function(){
    document.querySelectorAll('.profile-tab').forEach(t => t.classList.remove('active'));
    document.querySelectorAll('.profile-panel').forEach(p => p.classList.remove('active'));
    tab.classList.add('active');
    document.getElementById('panel-' + tab.dataset.tab).classList.add('active');
  });
});
var bioInput = document.getElementById('bioInput');
var bioCount = document.getElementById('bioCount');
if (bioInput) {
  bioInput.addEventListener('input', function(){ bioCount.textContent = bioInput.value.length; });
}
var avatarInput = document.getElementById('avatarInput');
if (avatarInput) {
  avatarInput.addEventListener('change', function(){
    if (this.files && this.files[0]) {
      var reader = new FileReader();
      reader.onload = function(e){
        document.querySelector('.avatar-upload .preview').innerHTML = '<img src="' + e.target.result + '" alt="Avatar">';
      };
      reader.readAsDataURL(this.files[0]);
    }
  });
}
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
