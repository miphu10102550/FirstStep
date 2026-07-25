<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function currentUser() {
    if (!isLoggedIn()) return null;
    return [
        'id' => $_SESSION['user_id'],
        'name' => $_SESSION['user_name'],
        'role' => $_SESSION['user_role'],
        'email' => $_SESSION['user_email'],
    ];
}

function requireRole($role) {
    if (!isLoggedIn() || $_SESSION['user_role'] !== $role) {
        header('Location: login.php');
        exit;
    }
}

function requireLogin() {
    if (!isLoggedIn()) {
        header('Location: login.php');
        exit;
    }
}

function redirectByRole() {
    if (!isLoggedIn()) return;
    switch ($_SESSION['user_role']) {
        case 'jobseeker':
            header('Location: dashboard-jobseeker.php'); break;
        case 'employer':
            header('Location: dashboard-employer.php'); break;
        case 'admin':
            header('Location: admin/index.php'); break;
    }
    exit;
}
