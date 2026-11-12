<?php
require_once __DIR__ . '/config/database.php';

$projects   = $pdo->query("SELECT * FROM projects ORDER BY featured DESC, created_at DESC")->fetchAll();
$categories = $pdo->query("SELECT DISTINCT category FROM projects ORDER BY category ASC")->fetchAll(PDO::FETCH_COLUMN);

$pageTitle       = 'Portfolio';
$pageDescription = 'Websites built and fixed by Timothy Udochukwu, with PHP, MySQL, Bootstrap 5 and JavaScript.';
$activePage      = 'portfolio';

require_once __DIR__ . '/includes/header.php';
?>

<!-- ============ PAGE BANNER ============ -->
<section class="page-banner">
    <div class="container text-center">
        <h1 class="fw-bold">Portfolio</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb justify-content-center mb-0">
                <li class="breadcrumb-item"><a href="<?= url('index.php') ?>">Home</a></li>
                <li class="breadcrumb-item active" aria-current="page">Portfolio</li>
            </ol>
        </nav>
    </div>
</section>

<!-- ============ PROJECTS ============ -->
<section class="section">
    <div class="container">

        <?php if (empty($projects)): ?>
            <p class="text-center text-secondary">Projects are coming soon.</p>
        <?php else: ?>

            <!-- Filter buttons -->
            <div class="filter-bar text-center mb-5 reveal" role="group" aria-label="Filter projects by category">
                <button type="button" class="filter-btn active" data-filter="all">All</button>
                <?php foreach ($categories as $category): ?>
                    <button type="button" class="filter-btn" data-filter="<?= e($category) ?>"><?= e($category) ?></button>
                <?php endforeach; ?>
            </div>

            <!-- Project grid -->
            <div class="row g-4" id="projectGrid">
                <?php foreach ($projects as $project): ?>
                    <?php
                    $hasImage = !empty($project['image']) && file_exists(__DIR__ . '/uploads/' . $project['image']);
                    $techs    = array_filter(array_map('trim', explode(',', (string) $project['tech_stack'])));
                    ?>
                    <div class="col-md-6 col-lg-4 project-item" data-category="<?= e($project['category']) ?>">
                        <article class="card-soft card-hover project-card h-100">
                            <div class="project-thumb">
                                <?php if ($hasImage): ?>
                                    <img src="<?= url('uploads/' . $project['image']) ?>"
                                         alt="<?= e($project['title']) ?>" loading="lazy">
                                <?php else: ?>
                                    <i class="bi bi-window-stack"></i>
                                <?php endif; ?>
                            </div>
                            <div class="p-4">
                                <span class="badge badge-soft mb-2"><?= e($project['category']) ?></span>
                                <h5 class="fw-semibold"><?= e($project['title']) ?></h5>
                                <p class="text-secondary small">
                                    <?= e(mb_strimwidth($project['description'], 0, 120, '...')) ?>
                                </p>
                                <div class="d-flex flex-wrap gap-1 mb-3">
                                    <?php foreach (array_slice($techs, 0, 4) as $tech): ?>
                                        <span class="tech-tag"><?= e($tech) ?></span>
                                    <?php endforeach; ?>
                                </div>
                                <a href="<?= url('project.php?slug=' . urlencode($project['slug'])) ?>"
                                   class="fw-medium link-brand">View details <i class="bi bi-arrow-right"></i></a>
                            </div>
                        </article>
                    </div>
                <?php endforeach; ?>
            </div>

            <p class="text-center text-secondary mt-4 d-none" id="noResults">No projects in this category yet.</p>

        <?php endif; ?>
    </div>
</section>

<!-- ============ CTA ============ -->
<section class="section pt-0">
    <div class="container">
        <div class="cta-band text-center reveal">
            <h2 class="fw-bold mb-3">Want something like this?</h2>
            <p class="mb-4 mx-auto" style="max-width: 520px;">Tell me about your idea and I'll help you build it.</p>
            <a href="<?= url('contact.php') ?>" class="btn btn-light btn-lg rounded-pill px-5">Start a Project</a>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>