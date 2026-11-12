<?php
http_response_code(404);

$pageTitle       = 'Page Not Found';
$pageDescription = 'The page you were looking for could not be found.';
$activePage      = '';

require_once __DIR__ . '/includes/header.php';
?>

<section class="section">
    <div class="container text-center py-5">
        <div class="error-code">404</div>
        <h1 class="fw-bold mb-3">Page not found</h1>
        <p class="text-secondary mb-4 mx-auto" style="max-width: 460px;">
            The page you are looking for does not exist, was moved, or the link is wrong.
        </p>
        <div class="d-flex flex-wrap justify-content-center gap-2">
            <a href="<?= url('index.php') ?>" class="btn btn-brand px-4"><i class="bi bi-house me-1"></i> Go Home</a>
            <a href="<?= url('portfolio.php') ?>" class="btn btn-outline-brand px-4">See My Work</a>
            <a href="<?= url('contact.php') ?>" class="btn btn-outline-brand px-4">Contact Me</a>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>