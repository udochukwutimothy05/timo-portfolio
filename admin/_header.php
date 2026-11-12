<?php
$adminTitle  = $adminTitle ?? 'Admin';
$adminActive = $adminActive ?? '';
$unread      = (int) $pdo->query("SELECT COUNT(*) FROM messages WHERE is_read = 0")->fetchColumn();
$flash       = get_flash();

$links = [
    'dashboard' => ['Dashboard', 'admin/dashboard.php', 'bi-speedometer2'],
    'projects'  => ['Projects',  'admin/projects.php',  'bi-folder2-open'],
    'services'  => ['Services',  'admin/services.php',  'bi-tools'],
    'courses'   => ['Courses',   'admin/courses.php',   'bi-mortarboard'],
    'messages'  => ['Messages',  'admin/messages.php',  'bi-envelope'],
];

$adminName = $_SESSION['admin_name'] ?? 'admin';
$initial   = mb_strtoupper(mb_substr($adminName, 0, 1));
?>
<!DOCTYPE html>
<html lang="en" data-bs-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow">
    <meta name="theme-color" content="#0b1220">
    <title><?= e($adminTitle) ?> | Admin</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="<?= url('assets/css/style.css') ?>" rel="stylesheet">
    <link href="<?= url('assets/css/admin.css') ?>" rel="stylesheet">
</head>
<body class="admin-body">

<div class="sidebar-backdrop" id="sidebarBackdrop"></div>

<!-- ============ SIDEBAR ============ -->
<aside class="admin-sidebar" id="adminSidebar" aria-label="Admin navigation">
    <div class="sidebar-brand">
        <a href="<?= url('admin/dashboard.php') ?>"><span class="brand-mark">&lt;/&gt;</span> Admin</a>
        <button type="button" class="sidebar-close d-lg-none" id="sidebarClose" aria-label="Close menu">
            <i class="bi bi-x-lg"></i>
        </button>
    </div>

    <nav class="sidebar-nav">
        <div class="sidebar-label">Manage</div>
        <?php foreach ($links as $key => [$label, $path, $icon]): ?>
            <a class="sidebar-link<?= $adminActive === $key ? ' active' : '' ?>" href="<?= url($path) ?>">
                <i class="bi <?= e($icon) ?>"></i>
                <span><?= e($label) ?></span>
                <?php if ($key === 'messages' && $unread > 0): ?>
                    <span class="badge rounded-pill text-bg-danger"><?= $unread ?></span>
                <?php endif; ?>
            </a>
        <?php endforeach; ?>
    </nav>

    <div class="sidebar-footer">
        <div class="sidebar-user">
            <span class="sidebar-avatar"><?= e($initial) ?></span>
            <div>
                <div class="sidebar-user-name"><?= e($adminName) ?></div>
                <div class="sidebar-user-role">Administrator</div>
            </div>
        </div>
        <div class="d-grid gap-2">
            <a class="btn btn-sm btn-outline-secondary" href="<?= url('index.php') ?>" target="_blank" rel="noopener">
                <i class="bi bi-box-arrow-up-right me-1"></i> View website
            </a>
            <form action="<?= url('admin/logout.php') ?>" method="post" class="m-0 d-grid">
                <?= csrf_field() ?>
                <button type="submit" class="btn btn-sm btn-brand">
                    <i class="bi bi-box-arrow-right me-1"></i> Logout
                </button>
            </form>
        </div>
    </div>
</aside>

<!-- ============ MAIN AREA ============ -->
<div class="admin-main">

    <!-- Top bar, phones and tablets only -->
    <header class="admin-topbar d-lg-none">
        <button type="button" class="topbar-btn" id="sidebarToggle" aria-label="Open menu" aria-controls="adminSidebar">
            <i class="bi bi-list"></i>
        </button>
        <span class="fw-semibold"><?= e($adminTitle) ?></span>
    </header>

    <main class="admin-content">
    <?php if ($flash): ?>
        <div class="alert alert-<?= e($flash['type']) ?> alert-dismissible fade show" role="alert">
            <?= e($flash['message']) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>