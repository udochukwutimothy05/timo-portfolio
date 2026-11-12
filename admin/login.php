<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';

if (is_admin_logged_in()) {
    redirect('admin/dashboard.php');
}

// Clear an expired lockout
if (!empty($_SESSION['login_lock_until']) && time() >= $_SESSION['login_lock_until']) {
    unset($_SESSION['login_lock_until']);
}
$locked = !empty($_SESSION['login_lock_until']);

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($locked) {
        $error = 'Too many failed attempts. Please wait a few minutes and try again.';
    } elseif (!verify_csrf($_POST['csrf_token'] ?? null)) {
        $error = 'Session expired. Refresh the page and try again.';
    } else {
        $stmt = $pdo->prepare("SELECT id, username, password_hash FROM admins WHERE username = :u LIMIT 1");
        $stmt->execute([':u' => $username]);
        $admin = $stmt->fetch();

        if ($admin && password_verify($password, $admin['password_hash'])) {
            session_regenerate_id(true);                 // stops session fixation
            unset($_SESSION['login_fails'], $_SESSION['login_lock_until']);
            $_SESSION['admin_id']   = (int) $admin['id'];
            $_SESSION['admin_name'] = $admin['username'];
            redirect('admin/dashboard.php');
        }

        $_SESSION['login_fails'] = ($_SESSION['login_fails'] ?? 0) + 1;
        if ($_SESSION['login_fails'] >= 5) {
            $_SESSION['login_lock_until'] = time() + 300;   // 5 minute lockout
            $_SESSION['login_fails'] = 0;
        }
        $error = 'Incorrect username or password.';
    }
}

$flash = get_flash();
?>
<!DOCTYPE html>
<html lang="en" data-bs-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow">
    <title>Admin Login</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="<?= url('assets/css/style.css') ?>" rel="stylesheet">
    <link href="<?= url('assets/css/admin.css') ?>" rel="stylesheet">
</head>
<body class="admin-body">
<div class="auth-wrap">
    <div class="admin-card auth-card">
        <div class="text-center mb-4">
            <div class="icon-box mb-2"><i class="bi bi-shield-lock-fill"></i></div>
            <h1 class="h4 fw-bold mb-0">Admin Login</h1>
        </div>

        <?php if ($flash): ?><div class="alert alert-<?= e($flash['type']) ?>"><?= e($flash['message']) ?></div><?php endif; ?>
        <?php if ($error): ?><div class="alert alert-danger"><?= e($error) ?></div><?php endif; ?>

        <form method="post" novalidate>
            <?= csrf_field() ?>
            <div class="mb-3">
                <label class="form-label" for="username">Username</label>
                <input class="form-control" id="username" name="username" required autofocus autocomplete="username">
            </div>
            <div class="mb-4">
                <label class="form-label" for="password">Password</label>
                <input class="form-control" type="password" id="password" name="password" required autocomplete="current-password">
            </div>
            <button class="btn btn-brand w-100 py-2" type="submit">Log in</button>
        </form>

        <p class="text-center small mt-4 mb-0"><a href="<?= url('index.php') ?>" class="link-brand">&larr; Back to website</a></p>
    </div>
</div>
</body>
</html>