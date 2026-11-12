<?php
require_once __DIR__ . '/includes/functions.php';

// Values kept from a failed non-JavaScript submit, plus a subject sent from other pages
$old = $_SESSION['old'] ?? [];
unset($_SESSION['old']);

$subjectPrefill = $old['subject'] ?? mb_substr(trim($_GET['subject'] ?? ''), 0, 200);

$pageTitle       = 'Contact';
$pageDescription = 'Contact Timothy Udochukwu to hire a web developer, get your website fixed, or enrol for web development training.';
$activePage      = 'contact';

require_once __DIR__ . '/includes/header.php';
?>

<!-- ============ PAGE BANNER ============ -->
<section class="page-banner">
    <div class="container text-center">
        <h1 class="fw-bold">Contact Me</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb justify-content-center mb-0">
                <li class="breadcrumb-item"><a href="<?= url('index.php') ?>">Home</a></li>
                <li class="breadcrumb-item active" aria-current="page">Contact</li>
            </ol>
        </nav>
    </div>
</section>

<!-- ============ CONTACT ============ -->
<section class="section">
    <div class="container">
        <div class="row g-5">

            <!-- Left: contact details -->
            <div class="col-lg-5 reveal">
                <span class="eyebrow-inline">Get In Touch</span>
                <h2 class="fw-bold mb-3">Let's talk about your project</h2>
                <p class="text-secondary mb-4">
                    Need a new website, a fix for an existing one, or lessons in web development?
                    Send a message and I will reply as soon as I can.
                </p>

                <a class="contact-card" href="mailto:<?= e(SITE_EMAIL) ?>">
                    <span class="icon-box"><i class="bi bi-envelope-fill"></i></span>
                    <span>
                        <small class="text-secondary d-block">Email</small>
                        <strong><?= e(SITE_EMAIL) ?></strong>
                    </span>
                </a>

                <a class="contact-card" href="https://wa.me/<?= e(SITE_WHATSAPP) ?>" target="_blank" rel="noopener">
                    <span class="icon-box"><i class="bi bi-whatsapp"></i></span>
                    <span>
                        <small class="text-secondary d-block">WhatsApp</small>
                        <strong>+<?= e(SITE_WHATSAPP) ?></strong>
                    </span>
                </a>

                <div class="contact-card">
                    <span class="icon-box"><i class="bi bi-geo-alt-fill"></i></span>
                    <span>
                        <small class="text-secondary d-block">Location</small>
                        <strong>Nigeria</strong>
                    </span>
                </div>
            </div>

            <!-- Right: form -->
            <div class="col-lg-7 reveal">
                <div class="card-soft p-4 p-md-5">
                    <h3 class="h4 fw-semibold mb-4">Send me a message</h3>

                    <div id="formAlert" role="alert"></div>

                    <form id="contactForm" action="<?= url('process_contact.php') ?>" method="post" novalidate>
                        <?= csrf_field() ?>

                        <!-- Honeypot: real people never see or fill this -->
                        <div class="hp-field" aria-hidden="true">
                            <label for="website">Leave this empty</label>
                            <input type="text" id="website" name="website" tabindex="-1" autocomplete="off">
                        </div>

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="name" class="form-label">Your name</label>
                                <input type="text" class="form-control" id="name" name="name"
                                       minlength="2" maxlength="100" required
                                       value="<?= e($old['name'] ?? '') ?>">
                                <div class="invalid-feedback">Please enter your name.</div>
                            </div>
                            <div class="col-md-6">
                                <label for="email" class="form-label">Email address</label>
                                <input type="email" class="form-control" id="email" name="email"
                                       maxlength="150" required
                                       value="<?= e($old['email'] ?? '') ?>">
                                <div class="invalid-feedback">Please enter a valid email.</div>
                            </div>
                            <div class="col-12">
                                <label for="subject" class="form-label">Subject</label>
                                <input type="text" class="form-control" id="subject" name="subject"
                                       maxlength="200" value="<?= e($subjectPrefill) ?>">
                            </div>
                            <div class="col-12">
                                <label for="message" class="form-label">Message</label>
                                <textarea class="form-control" id="message" name="message" rows="6"
                                          minlength="10" maxlength="3000" required><?= e($old['message'] ?? '') ?></textarea>
                                <div class="invalid-feedback">Please write at least 10 characters.</div>
                            </div>
                            <div class="col-12">
                                <button type="submit" class="btn btn-brand px-4 py-2" id="sendBtn">
                                    <span class="btn-label"><i class="bi bi-send me-1"></i> Send Message</span>
                                    <span class="btn-loading d-none">
                                        <span class="spinner-border spinner-border-sm me-1"></span> Sending...
                                    </span>
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>s