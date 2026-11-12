<?php
require_once __DIR__ . '/config/database.php';

$slug = trim($_GET['slug'] ?? '');

$stmt = $pdo->prepare("SELECT * FROM projects WHERE slug = :slug LIMIT 1");
$stmt->execute([':slug' => $slug]);
$project = $stmt->fetch();

$activePage = 'portfolio';

// ---- Project not found ----
if (!$project) {
    http_response_code(404);
    $pageTitle = 'Project Not Found';
    require_once __DIR__ . '/includes/header.php';
    ?>
    <section class="section">
        <div class="container text-center py-5">
            <div class="icon-box mb-3"><i class="bi bi-search"></i></div>
            <h1 class="fw-bold">Project not found</h1>
            <p class="text-secondary">That project does not exist or may have been removed.</p>
            <a href="<?= url('portfolio.php') ?>" class="btn btn-brand px-4">Back to Portfolio</a>
        </div>
    </section>
    <?php
    require_once __DIR__ . '/includes/footer.php';
    exit;
}

// ---- Related projects (same category) ----
$relStmt = $pdo->prepare("SELECT * FROM projects WHERE category = :cat AND id != :id ORDER BY created_at DESC LIMIT 3");
$relStmt->execute([':cat' => $project['category'], ':id' => $project['id']]);
$related = $relStmt->fetchAll();

// ---- Helpers for this page ----
$hasImage = !empty($project['image']) && file_exists(__DIR__ . '/uploads/' . $project['image']);
$techs    = array_filter(array_map('trim', explode(',', (string) $project['tech_stack'])));
$isUrl    = fn($v) => !empty($v) && preg_match('#^https?://#i', $v);

$pageTitle       = $project['title'];
$pageDescription = mb_strimwidth($project['description'], 0, 155, '...');

require_once __DIR__ . '/includes/header.php';
?>

<!-- ============ PAGE BANNER ============ -->
<section class="page-banner">
    <div class="container text-center">
        <h1 class="fw-bold"><?= e($project['title']) ?></h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb justify-content-center mb-0">
                <li class="breadcrumb-item"><a href="<?= url('index.php') ?>">Home</a></li>
                <li class="breadcrumb-item"><a href="<?= url('portfolio.php') ?>">Portfolio</a></li>
                <li class="breadcrumb-item active" aria-current="page"><?= e($project['title']) ?></li>
            </ol>
        </nav>
    </div>
</section>

<!-- ============ PROJECT DETAIL ============ -->
<section class="section">
    <div class="container">
        <div class="row g-5">

            <!-- Left: image and description -->
            <div class="col-lg-8">
                <div class="project-hero mb-4">
                    <?php if ($hasImage): ?>
                        <img src="<?= url('uploads/' . $project['image']) ?>" alt="<?= e($project['title']) ?>">
                    <?php else: ?>
                        <i class="bi bi-window-stack"></i>
                    <?php endif; ?>
                </div>

                <h2 class="h4 fw-semibold mb-3">About this project</h2>
                <div class="text-secondary project-text"><?= nl2br(e($project['description'])) ?></div>
            </div>

            <!-- Right: details -->
            <div class="col-lg-4">
                <div class="card-soft p-4 detail-card">
                    <h3 class="h5 fw-semibold mb-3">Project Details</h3>

                    <ul class="list-unstyled detail-list mb-4">
                        <li>
                            <span class="detail-label">Category</span>
                            <span class="badge badge-soft"><?= e($project['category']) ?></span>
                        </li>
                        <li>
                            <span class="detail-label">Date</span>
                            <?= e(date('F Y', strtotime($project['created_at']))) ?>
                        </li>
                        <?php if (!empty($techs)): ?>
                            <li>
                                <span class="detail-label">Technologies</span>
                                <div class="d-flex flex-wrap gap-1 mt-1">
                                    <?php foreach ($techs as $tech): ?>
                                        <span class="tech-tag"><?= e($tech) ?></span>
                                    <?php endforeach; ?>
                                </div>
                            </li>
                        <?php endif; ?>
                    </ul>

                    <div class="d-grid gap-2">
                        <?php if ($isUrl($project['live_url'])): ?>
                            <a href="<?= e($project['live_url']) ?>" target="_blank" rel="noopener noreferrer" class="btn btn-brand">
                                <i class="bi bi-box-arrow-up-right me-1"></i> Visit Live Site
                            </a>
                        <?php endif; ?>
                        <?php if ($isUrl($project['github_url'])): ?>
                            <a href="<?= e($project['github_url']) ?>" target="_blank" rel="noopener noreferrer" class="btn btn-outline-brand">
                                <i class="bi bi-github me-1"></i> View Source Code
                            </a>
                        <?php endif; ?>
                        <a href="<?= url('contact.php?subject=' . urlencode('Project like: ' . $project['title'])) ?>"
                           class="btn btn-outline-secondary rounded-pill">
                            Request something similar
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="mt-5">
            <a href="<?= url('portfolio.php') ?>" class="link-brand fw-medium">
                <i class="bi bi-arrow-left"></i> Back to all projects
            </a>
        </div>
    </div>
</section>

<!-- ============ RELATED PROJECTS ============ -->
<?php if (!empty($related)): ?>
<section class="section section-alt">
    <div class="container">
        <div class="section-title reveal">
            <span class="eyebrow">More Work</span>
            <h2>Related Projects</h2>
        </div>
        <div class="row g-4">
            <?php foreach ($related as $item): ?>
                <?php $relImage = !empty($item['image']) && file_exists(__DIR__ . '/uploads/' . $item['image']); ?>
                <div class="col-md-6 col-lg-4 reveal">
                    <article class="card-soft card-hover project-card h-100">
                        <div class="project-thumb">
                            <?php if ($relImage): ?>
                                <img src="<?= url('uploads/' . $item['image']) ?>" alt="<?= e($item['title']) ?>" loading="lazy">
                            <?php else: ?>
                                <i class="bi bi-window-stack"></i>
                            <?php endif; ?>
                        </div>
                        <div class="p-4">
                            <span class="badge badge-soft mb-2"><?= e($item['category']) ?></span>
                            <h5 class="fw-semibold"><?= e($item['title']) ?></h5>
                            <a href="<?= url('project.php?slug=' . urlencode($item['slug'])) ?>"
                               class="fw-medium link-brand">View details <i class="bi bi-arrow-right"></i></a>
                        </div>
                    </article>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<?php require_once __DIR__ . '/includes/footer.php'; ?>