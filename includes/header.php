<?php
require_once __DIR__ . '/functions.php';

$fullTitle   = isset($pageTitle) ? $pageTitle . ' | ' . SITE_NAME : SITE_NAME . ' | ' . SITE_TAGLINE;
$description = $pageDescription ?? SITE_DESCRIPTION;
$activePage  = $activePage ?? '';

$navItems = [
    'home'      => ['Home',      'index.php'],
    'about'     => ['About',     'about.php'],
    'services'  => ['Services',  'services.php'],
    'portfolio' => ['Portfolio', 'portfolio.php'],
    'training'  => ['Training',  'training.php'],
    'contact'   => ['Contact',   'contact.php'],
];

$flash = get_flash();
?>
<!DOCTYPE html>
<html lang="en" data-bs-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e($fullTitle) ?></title>
    <meta name="description" content="<?= e($description) ?>">
    <meta name="author" content="<?= e(SITE_NAME) ?>">
    <meta name="theme-color" content="#2563eb">

    <!-- Open Graph (link previews on WhatsApp, Facebook, LinkedIn) -->
    <meta property="og:title" content="<?= e($fullTitle) ?>">
    <meta property="og:description" content="<?= e($description) ?>">
    <meta property="og:type" content="website">

    <!-- Apply saved theme before the page paints, so there is no flash -->
    <script>
        try {
            var t = localStorage.getItem('theme');
            if (t) document.documentElement.setAttribute('data-bs-theme', t);
        } catch (err) {}
    </script>

    <!-- Fonts, Bootstrap 5, Bootstrap Icons -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">

    <!-- Your styles (always last so they can override Bootstrap) -->
    <link href="<?= url('assets/css/style.css') ?>" rel="stylesheet">
</head>
<body>

<nav class="navbar navbar-expand-lg fixed-top site-nav" id="siteNav">
    <div class="container">
        <a class="navbar-brand fw-bold" href="<?= url('index.php') ?>">
            <span class="brand-mark">&lt;/&gt;</span> <?= e(SITE_NAME) ?>
        </a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                data-bs-target="#mainNav" aria-controls="mainNav"
                aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="mainNav">
            <ul class="navbar-nav ms-auto align-items-lg-center gap-lg-1">
                <?php foreach ($navItems as $key => [$label, $file]): ?>
                    <li class="nav-item">
                        <a class="nav-link<?= $activePage === $key ? ' active' : '' ?>"
                           href="<?= url($file) ?>"><?= e($label) ?></a>
                    </li>
                <?php endforeach; ?>
                <li class="nav-item ms-lg-2">
                    <button class="btn btn-sm theme-toggle" id="themeToggle" type="button"
                            aria-label="Switch light or dark theme">
                        <i class="bi bi-moon-stars-fill"></i>
                    </button>
                </li>
                <li class="nav-item ms-lg-2 mt-2 mt-lg-0">
                    <a class="btn btn-brand btn-sm px-3" href="<?= url('contact.php') ?>">Hire Me</a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<main id="content">
<?php if ($flash): ?>
    <div class="container flash-wrap">
        <div class="alert alert-<?= e($flash['type']) ?> alert-dismissible fade show" role="alert">
            <?= e($flash['message']) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    </div>
<?php endif; ?>