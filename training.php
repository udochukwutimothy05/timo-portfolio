<?php
require_once __DIR__ . '/config/database.php';

$courses = $pdo->query("SELECT * FROM courses ORDER BY id ASC")->fetchAll();

$pageTitle       = 'Training';
$pageDescription = 'Learn web development step by step: HTML, CSS, Bootstrap 5, JavaScript, PHP and MySQL, taught by Timothy Udochukwu.';
$activePage      = 'training';

$benefits = [
    ['bi-hammer',       'Learn by building', 'Every lesson ends with something you build, so you practise instead of only reading theory.'],
    ['bi-emoji-smile',  'Beginner friendly', 'No experience needed. I explain each step in simple language and go at your pace.'],
    ['bi-laptop',       'Real tools',        'You use the same tools professionals use: VS Code, Bootstrap 5, PHP and MySQL.'],
    ['bi-chat-heart',   'Ongoing support',   'Stuck on a bug? Message me and I will help you get unstuck.'],
];

require_once __DIR__ . '/includes/header.php';
?>

<!-- ============ PAGE BANNER ============ -->
<section class="page-banner">
    <div class="container text-center">
        <h1 class="fw-bold">Training</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb justify-content-center mb-0">
                <li class="breadcrumb-item"><a href="<?= url('index.php') ?>">Home</a></li>
                <li class="breadcrumb-item active" aria-current="page">Training</li>
            </ol>
        </nav>
    </div>
</section>

<!-- ============ COURSES ============ -->
<section class="section">
    <div class="container">
        <div class="section-title reveal">
            <span class="eyebrow">Learn With Me</span>
            <h2>Web Development Courses</h2>
            <p>Start from zero and build real websites, one skill at a time.</p>
        </div>

        <?php if (empty($courses)): ?>
            <p class="text-center text-secondary">New courses are coming soon.</p>
        <?php else: ?>
            <div class="row g-4">
                <?php foreach ($courses as $course): ?>
                    <div class="col-md-6 col-lg-4 reveal">
                        <div class="card-soft card-hover p-4 h-100 d-flex flex-column">
                            <div class="d-flex justify-content-between align-items-start mb-3">
                                <div class="icon-box"><i class="bi bi-mortarboard"></i></div>
                                <span class="badge badge-soft"><?= e($course['level']) ?></span>
                            </div>
                            <h4 class="h5 fw-semibold"><?= e($course['title']) ?></h4>
                            <p class="text-secondary flex-grow-1"><?= e($course['description']) ?></p>

                            <ul class="list-unstyled course-meta mb-3">
                                <?php if (!empty($course['duration'])): ?>
                                    <li><i class="bi bi-clock me-2"></i><?= e($course['duration']) ?></li>
                                <?php endif; ?>
                                <?php if (!empty($course['price'])): ?>
                                    <li><i class="bi bi-tag me-2"></i><?= e($course['price']) ?></li>
                                <?php endif; ?>
                            </ul>

                            <a href="<?= url('contact.php?subject=' . urlencode('Training enquiry: ' . $course['title'])) ?>"
                               class="btn btn-brand w-100">Enquire / Enrol</a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</section>

<!-- ============ WHY LEARN WITH ME ============ -->
<section class="section section-alt">
    <div class="container">
        <div class="section-title reveal">
            <span class="eyebrow">Why Me</span>
            <h2>How I Teach</h2>
        </div>

        <div class="row g-4">
            <?php foreach ($benefits as [$icon, $title, $text]): ?>
                <div class="col-sm-6 col-lg-3 reveal">
                    <div class="card-soft p-4 h-100 text-center">
                        <div class="icon-box mx-auto mb-3"><i class="bi <?= e($icon) ?>"></i></div>
                        <h5 class="fw-semibold"><?= e($title) ?></h5>
                        <p class="text-secondary small mb-0"><?= e($text) ?></p>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- ============ CTA ============ -->
<section class="section">
    <div class="container">
        <div class="cta-band text-center reveal">
            <h2 class="fw-bold mb-3">Ready to start learning?</h2>
            <p class="mb-4 mx-auto" style="max-width: 520px;">Message me to ask about class times, fees and which course fits you best.</p>
            <a href="<?= url('contact.php?subject=' . urlencode('Training enquiry')) ?>" class="btn btn-light btn-lg rounded-pill px-5 me-2 mb-2">Contact Me</a>
            <a href="https://wa.me/<?= e(SITE_WHATSAPP) ?>" target="_blank" rel="noopener"
               class="btn btn-outline-light btn-lg rounded-pill px-4 mb-2">
                <i class="bi bi-whatsapp me-1"></i> WhatsApp
            </a>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>