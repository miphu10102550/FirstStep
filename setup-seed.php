<?php
// Run this file ONCE in your browser (e.g. http://localhost/firststep/setup-seed.php)
// after importing database.sql, to create sample login accounts.
// Delete this file afterwards for security.

require_once __DIR__ . '/config/database.php';

function createUserIfNotExists($pdo, $role, $name, $email, $plainPassword, $phone) {
    $chk = $pdo->prepare("SELECT id FROM users WHERE email=?");
    $chk->execute([$email]);
    if ($chk->fetch()) {
        return null; // already exists
    }
    $hash = password_hash($plainPassword, PASSWORD_DEFAULT);
    $pdo->prepare("INSERT INTO users (role, full_name, email, password, phone) VALUES (?,?,?,?,?)")
        ->execute([$role, $name, $email, $hash, $phone]);
    return $pdo->lastInsertId();
}

$log = [];

// 1. Admin account
$adminId = createUserIfNotExists($pdo, 'admin', 'Site Admin', 'admin@firststep.com', 'admin123', '0800000000');
$log[] = $adminId ? "Created admin: admin@firststep.com / admin123" : "Admin already exists.";

// 2. Employer account + company + sample jobs
$employerId = createUserIfNotExists($pdo, 'employer', 'HR TechCorp', 'hr@techcorp.com', 'password123', '0811111111');
if ($employerId) {
    $pdo->prepare("INSERT INTO companies (employer_id, name, description, location, website) VALUES (?,?,?,?,?)")
        ->execute([$employerId, 'TechCorp Co., Ltd.', 'A leading software company in Bangkok focused on building products for startups.', 'Bangkok', 'https://techcorp.example.com']);
    $companyId = $pdo->lastInsertId();

    $pdo->prepare("INSERT INTO jobs (company_id, title, description, requirements, location, job_type, category, salary_min, salary_max) VALUES (?,?,?,?,?,?,?,?,?)")
        ->execute([$companyId, 'Junior Web Developer', 'Build and maintain web applications using PHP and JavaScript.', 'New graduates welcome. Basic knowledge of HTML/CSS/PHP.', 'Bangkok', 'Full-time', 'Developer', 20000, 30000]);
    $pdo->prepare("INSERT INTO jobs (company_id, title, description, requirements, location, job_type, category, salary_min, salary_max) VALUES (?,?,?,?,?,?,?,?,?)")
        ->execute([$companyId, 'UI/UX Designer Intern', 'Assist the design team with mockups and prototypes.', 'Currently studying design or related field.', 'Bangkok (Remote OK)', 'Internship', 'Design', 12000, 15000]);

    $log[] = "Created employer: hr@techcorp.com / password123 (with company + 2 sample jobs)";
} else {
    $log[] = "Employer already exists.";
}

// 3. Job seeker account
$seekerId = createUserIfNotExists($pdo, 'jobseeker', 'Somchai Jaidee', 'somchai@example.com', 'password123', '0822222222');
if ($seekerId) {
    $pdo->prepare("INSERT INTO jobseeker_profiles (user_id) VALUES (?)")->execute([$seekerId]);
    $log[] = "Created job seeker: somchai@example.com / password123";
} else {
    $log[] = "Job seeker already exists.";
}
?>
<!DOCTYPE html>
<html><head><meta charset="UTF-8"><title>Seed Complete</title>
<style>body{font-family:sans-serif;max-width:600px;margin:60px auto;line-height:1.7;}
code{background:#eee;padding:2px 6px;border-radius:4px;}</style>
</head>
<body>
<h2>✅ Setup Complete</h2>
<ul>
<?php foreach ($log as $l): ?>
  <li><?= htmlspecialchars($l) ?></li>
<?php endforeach; ?>
</ul>
<p><strong>Login accounts you can now use:</strong></p>
<table border="1" cellpadding="8" cellspacing="0">
<tr><th>Role</th><th>Email</th><th>Password</th></tr>
<tr><td>Admin</td><td>admin@firststep.com</td><td>admin123</td></tr>
<tr><td>Employer</td><td>hr@techcorp.com</td><td>password123</td></tr>
<tr><td>Job Seeker</td><td>somchai@example.com</td><td>password123</td></tr>
</table>
<p style="color:#b3261e;"><strong>Important:</strong> Delete this file (setup-seed.php) now for security.</p>
<p><a href="index.php">Go to Homepage &rarr;</a></p>
</body></html>
