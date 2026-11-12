<?php
require_once __DIR__ . '/config/database.php';

$services = $pdo->query("SELECT * FROM services ORDER BY sort_order ASC")->fetchAll();

$pageTitle       = 'Services';
$pageDescription = 'Website development, website fixing and redesign, and web development training by Timothy Udochukwu.';
$activePage      = 'services';

$steps = [
    ['Discuss',  'You tell me what you need. We agree on scope, timeline and price.',   'bi-chat-dots'],
    ['Design',   'I plan the pages and layout so you can see the direction early.',     'bi-palette'],
    ['Build',    'I code the site with clean, secure, responsive PHP, MySQL and Bootstrap.', 'bi-code-slash'],
    ['Launch',   'We test everything, go live, and I stay available for support.',       'bi-rocket-takeoff'],
];

$faqs = [
    ['How long does a website take?',
     'A simple website usually takes 1 to 2 weeks. Larger sites with an admin panel or database features take longer. I give you a timeline before we start.'],
    ['Can you fix my existing website?',
     'Yes. I can fix layout problems, bugs, slow pages, contact forms and mobile issues without rebuilding the whole site.'],
    ['Will my website work on phones?',
     'Yes. Every site I build is mobile-first and responsive, so it works on phones, tablets and computers.'],
    ['Do you teach beginners?',
     'Yes. My training starts from zero. You learn by building real projects, and I explain each step clearly.'],
    ['How do I pay?',
     'Pricing depends on the project. Contact me with your requirements and I will send a clear quote.'],
];

require_once __DIR__ . '/includes/header.php';
?>

<!-- ============ PAGE BANNER ============ -->
<section class="page-banner">
    <div class="container text-center">
        <h1 class="fw-bold">Services</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb justify-content-center mb-0">
                <li class="breadcrumb-item"><a href="<?= url('index.php') ?>">Home</a></li>
                <li class="breadcrumb-item active" aria-current="page">Services</li>
            </ol>
        </nav>
    </div>
</section>

<!-- ============ SERVICES ============ -->
<section class="section">
    <div class="container">
        <div class="section-title reveal">
            <span class="eyebrow">What I Offer</span>
            <h2>How I Can Help You</h2>
            <p>Three ways to work with me, depending on what you need.</p>
        </div>

        <div class="row g-4">
            <?php foreach ($services as $service): ?>
                <div class="col-md-6 col-lg-4 reveal">
                    <div class="card-soft card-hover p-4 h-100 d-flex flex-column">
                        <div class="icon-box mb-3"><i class="bi <?= e($service['icon']) ?>"></i></div>
                        <h4 class="fw-semibold h5"><?= e($service['title']) ?></h4>
                        <p class="text-secondary flex-grow-1"><?= e($service['description']) ?></p>
                        <a href="<?= url('contact.php?subject=' . urlencode($service['title'])) ?>"
                           class="fw-medium link-brand">Request this service <i class="bi bi-arrow-right"></i></a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- ============ PROCESS ============ -->
<section class="section section-alt">
    <div class="container">
        <div class="section-title reveal">
            <span class="eyebrow">Process</span>
            <h2>How I Work</h2>
            <p>A simple process so you always know what is happening.</p>
        </div>

        <div class="row g-4">
            <?php foreach ($steps as $i => [$title, $text, $icon]): ?>
                <div class="col-sm-6 col-lg-3 reveal">
                    <div class="card-soft p-4 h-100 text-center position-relative">
                        <span class="step-number"><?= $i + 1 ?></span>
                        <div class="icon-box mx-auto mb-3"><i class="bi <?= e($icon) ?>"></i></div>
                        <h5 class="fw-semibold"><?= e($title) ?></h5>
                        <p class="text-secondary small mb-0"><?= e($text) ?></p>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- ============ FAQ ============ -->
<section class="section">
    <div class="container">
        <div class="section-title reveal">
            <span class="eyebrow">FAQ</span>
            <h2>Common Questions</h2>
        </div>

        <div class="accordion faq mx-auto reveal" id="faqAccordion" style="max-width: 760px;">
            <?php foreach ($faqs as $i => [$question, $answer]): ?>
                <div class="accordion-item">
                    <h3 class="accordion-header">
                        <button class="accordion-button<?= $i > 0 ? ' collapsed' : '' ?>" type="button"
                                data-bs-toggle="collapse" data-bs-target="#faq<?= $i ?>"
                                aria-expanded="<?= $i === 0 ? 'true' : 'false' ?>">
                            <?= e($question) ?>
                        </button>
                    </h3>
                    <div id="faq<?= $i ?>" class="accordion-collapse collapse<?= $i === 0 ? ' show' : '' ?>"
                         data-bs-parent="#faqAccordion">
                        <div class="accordion-body text-secondary"><?= e($answer) ?></div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- ============ CTA ============ -->
<section class="section pt-0">
    <div class="container">
        <div class="cta-band text-center reveal">
            <h2 class="fw-bold mb-3">Ready to start?</h2>
            <p class="mb-4 mx-auto" style="max-width: 520px;">Tell me about your project and I'll reply with a clear plan and quote.</p>
            <a href="<?= url('contact.php') ?>" class="btn btn-light btn-lg rounded-pill px-5">Contact Me</a>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>