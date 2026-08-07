<?php
// Run this ONCE in your browser: http://localhost/firststep/migrate-profile-fields.php
// Adds new columns needed for the redesigned profile & dashboard pages.
// Safe to re-run — it checks for existing columns first.

require_once __DIR__ . '/config/database.php';

function columnExists($pdo, $table, $column) {
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ? AND COLUMN_NAME = ?");
    $stmt->execute([$table, $column]);
    return $stmt->fetchColumn() > 0;
}

$log = [];

$userCols = [
    'first_name' => "VARCHAR(100) NULL",
    'last_name' => "VARCHAR(100) NULL",
    'avatar' => "VARCHAR(255) NULL",
];
foreach ($userCols as $col => $def) {
    if (!columnExists($pdo, 'users', $col)) {
        $pdo->exec("ALTER TABLE users ADD COLUMN $col $def");
        $log[] = "Added users.$col";
    }
}

$profileCols = [
    'birthdate' => "DATE NULL",
    'gender' => "VARCHAR(20) NULL",
    'address' => "VARCHAR(255) NULL",
    'province' => "VARCHAR(100) NULL",
    'district' => "VARCHAR(100) NULL",
    'subdistrict' => "VARCHAR(100) NULL",
    'postal_code' => "VARCHAR(10) NULL",
    'work_experience' => "TEXT NULL",
];
foreach ($profileCols as $col => $def) {
    if (!columnExists($pdo, 'jobseeker_profiles', $col)) {
        $pdo->exec("ALTER TABLE jobseeker_profiles ADD COLUMN $col $def");
        $log[] = "Added jobseeker_profiles.$col";
    }
}

if (empty($log)) { $log[] = "Nothing to do — all columns already exist."; }
?>
<!DOCTYPE html>
<html><head><meta charset="UTF-8"><title>Migration Complete</title>
<style>body{font-family:sans-serif;max-width:600px;margin:60px auto;line-height:1.7;}</style>
</head>
<body>
<h2>✅ Migration Complete</h2>
<ul><?php foreach ($log as $l): ?><li><?= htmlspecialchars($l) ?></li><?php endforeach; ?></ul>
<p style="color:#b3261e;"><strong>You can delete this file now for security.</strong></p>
<p><a href="index.php">Go to Homepage &rarr;</a></p>
</body></html>
