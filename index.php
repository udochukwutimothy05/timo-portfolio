<?php
require_once __DIR__ . '/config/database.php';

// ---- Data from the database ----
$services = $pdo->query("SELECT * FROM services ORDER BY sort_order ASC LIMIT 6")->fetchAll();
$projects = $pdo->query("SELECT * FROM projects WHERE featured = 1 ORDER BY created_at DESC LIMIT 3")->fetchAll();
$projectCount = (int) $pdo->query("SELECT COUNT(*) FROM projects")->fetchColumn();
$courseCount  = (int) $pdo->query("SELECT COUNT(*) FROM courses")->fetchColumn();

// Optional profile photo: save yours as assets/img/profile.jpg
$hasPhoto = file_exists(__DIR__ . '/assets/img/profile.jpg');

$activePage = 'home';
require_once __DIR__ . '/includes/header.php';
?>

<!-- ============ HERO ============ -->
<section class="hero">
    <div class="container">
        <div class="row align-items-center g-5">
            <div class="col-lg-7 order-2 order-lg-1">
                <span class="badge rounded-pill text-bg-light border mb-3 px-3 py-2">
                    <i class="bi bi-circle-fill text-success me-1 small"></i> Available for new projects
                </span>
                <h1>Hi, I'm <span class="highlight"><?= e(SITE_NAME) ?></span>.<br>
                    <span class="highlight" id="typed"
                        data-words='["Website Developer","Full Stack Developer","Website Fixer","Web Development Teacher"]'></span><span class="cursor">|</span>
                </h1>
                <p class="lead my-4">
                    I build fast, modern websites with PHP, MySQL, Bootstrap 5 and JavaScript,
                    fix broken ones, and teach others how to do the same.
                </p>
                <div class="d-flex flex-wrap gap-2">
                    <a href="<?= url('portfolio.php') ?>" class="btn btn-brand px-4 py-2">
                        View My Work <i class="bi bi-arrow-right ms-1"></i>
                    </a>
                    <a href="<?= url('contact.php') ?>" class="btn btn-outline-brand px-4 py-2">Contact Me</a>
                </div>

                <div class="row g-3 mt-4 pt-2">
                    <div class="col-4">
                        <div class="stat-num"><?= $projectCount ?>+</div>
                        <div class="stat-label">Projects</div>
                    </div>
                    <div class="col-4">
                        <div class="stat-num"><?= $courseCount ?></div>
                        <div class="stat-label">Courses</div>
                    </div>
                    <div class="col-4">
                        <div class="stat-num">3</div>
                        <div class="stat-label">Services</div>
                    </div>
                </div>
            </div>

            <div class="col-lg-5 order-1 order-lg-2 text-center">
                <?php if ($hasPhoto): ?>
                    <img src="<?= url('assets/img/profile.jpg') ?>" alt="<?= e(SITE_NAME) ?>" class="hero-photo">
                <?php else: ?>
                    <div class="hero-photo hero-photo-fallback">TU</div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<!--