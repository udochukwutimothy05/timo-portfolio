<?php
require_once __DIR__ . '/../includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && verify_csrf($_POST['csrf_token'] ?? null)) {
    $_SESSION = [];
    if (ini_get('session.use_cookies')) {
        $p = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000, $p['path'], $p['domain'], $p['secure'], $p['httponly']);
    }
    session_destroy();
    session_start();                       // new clean session for the flash message
    set_flash('success', 'You have been logged out.');
}

redirect('admin/login.php');