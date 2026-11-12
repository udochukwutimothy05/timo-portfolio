<?php
$pageTitle       = 'About Me';
$pageDescription = 'Learn about Timothy Udochukwu, a Nigerian university student and full stack web developer who builds websites, fixes existing ones and teaches web development.';
$activePage      = 'about';
require_once __DIR__ . '/includes/header.php';

$photo = get_profile_photo();

$skills = [
    ['HTML5 & CSS3', 85],
    ['Bootstrap 5',  90],
    ['JavaScript',   75],
    ['PHP',          80],
    ['MySQL',        80],
    ['Teaching & Mentoring', 85],
];

$timeline = [
    ['Now',   'Web Developer & Instructor', 'Building websites for clients, fixing existing sites, and teaching web development to beginners.'],
    ['Now',   'Pharmacy Student',   'Studying Pharmacy at Enugu State University of Science and Technology (ESUT), Nigeria.'],
    ['Earlier', 'Started Learning to Code',  'Began with HTML and CSS, then moved on to JavaScript, PHP and MySQL.'],
];
?>

<!-- ============ PAGE BANNER ============ -->
<section class="page-banner">
    <div class="container text-center">
        <h1 class="fw-bold">About Me</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb justify-content-center mb-0">
                <li class="breadcrumb-item"><a href="<?= url('index.php') ?>">Home</a></li>
                <li class="breadcrumb-item active" aria-current="page">About</li>
            </ol>
        </nav>
    </div>
</section>

<!-- ============ STORY ============ -->
<section class="section">
    <div class="container">
        <div class="row align-items-center g-5">
            <div class="col-lg-5 text-center reveal">
                <?php if ($photo): ?>
                    <img src="<?= url('assets/img/' . $photo) ?>" alt="<?= e(SITE_NAME) ?>" class="hero-photo">
                <?php else: ?>
                    <div class="hero-photo hero-photo-fallback">TU</div>
                <?php endif; ?>
            </div>

            <div class="col-lg-7 reveal">
                <span class="eyebrow-inline">Who I Am</span>
                <h2 class="fw-bold mb-3">A student developer who builds, fixes and teaches</h2>
                <p class="text-secondary">
                    I'm <strong><?= e(SITE_NAME) ?></strong>, a Nigerian university student and full stack
                    web developer. I build modern websites with PHP, MySQL, Bootstrap 5 and JavaScript.
                </p>
                <p class="text-secondary">
                    Many businesses and individuals already have websites that are slow, broken or
                    outdated, so I also correct and improve existing sites without making clients start over.
                </p>
                <p class="text-secondary">
                    I love teaching as much as building. I help beginners and fellow students
                    learn web development step by step, with real projects instead of just theory.
                </p>

                <div class="row g-3 mt-2">
                    <div class="col-sm-6"><i class="bi bi-geo-alt-fill text-brand me-2"></i>Nigeria</div>
                    <div class="col-sm-6"><i class="bi bi-envelope-fill text-brand me-2"></i><?= e(SITE_EMAIL) ?></div>
                    <div class="col-sm-6"><i class="bi bi-mortarboard-fill text-brand me-2"></i>Pharmacy Student, ESUT</div>
                    <div class="col-sm-6"><i class="bi bi-circle-fill text-success me-2 small"></i>Available for projects</div>
                </div>

                <div class="mt-4 d-flex flex-wrap gap-2">
                    <a href="<?= url('contact.php') ?>" class="btn btn-brand px-4">Hire Me</a>
                    <a href="<?= url('portfolio.php') ?>" class="btn btn-outline-brand px-4">See My Work</a>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ============ SKILLS ============ -->
<section class="section section-alt">
    <div class="container">
        <div class="section-title reveal">
            <span class="eyebrow">Skills</span>
            <h2>What I'm Good At</h2>
            <p>My skill levels, where I am today and where I'm still growing.</p>
        </div>

        <div class="row g-4 justify-content-center">
            <?php foreach ($skills as [$name, $level]): ?>
                <div class="col-md-6 reveal">
                    <div class="d-flex justify-content-between mb-1">
                        <span class="fw-medium"><?= e($name) ?></span>
                        <span class="text-secondary small"><?= (int) $level ?>%</span>
                    </div>
                    <div class="progress skill-progress" role="progressbar"
                         aria-valuenow="<?= (int) $level ?>" aria-valuemin="0" aria-valuemax="100">
                        <div class="progress-bar" data-width="<?= (int) $level ?>" style="width:0"></div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- ============ JOURNEY ============ -->
<section class="section">
    <div class="container">
        <div class="section-title reveal">
            <span class="eyebrow">Journey</span>
            <h2>My Journey</h2>
        </div>

        <div class="timeline mx-auto">
            <?php foreach ($timeline as [$when, $title, $text]): ?>
                <div class="timeline-item reveal">
                    <span class="timeline-when"><?= e($when) ?></span>
                    <h5 class="fw-semibold mb-1"><?= e($title) ?></h5>
                    <p class="text-secondary mb-0"><?= e($text) ?></p>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>