<?php
/**
 * Loaded at the top of every protected admin page.
 * Connects to the database, loads helpers and blocks anyone who is not logged in.
 */
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';

require_admin();

// Make sure the admin account still exists and the username is in the session
$stmt = $pdo->prepare("SELECT username FROM admins WHERE id = :id LIMIT 1");
$stmt->execute([':id' => (int) $_SESSION['admin_id']]);
$adminRow = $stmt->fetch();

if (!$adminRow) {
    $_SESSION = [];
    session_destroy();
    session_start();
    set_flash('danger', 'Please log in again.');
    redirect('admin/login.php');
}
$_SESSION['admin_name'] = $adminRow['username'];

// Admin pages must never be cached or indexed
header('X-Robots-Tag: noindex, nofollow');
header('Cache-Control: no-store');