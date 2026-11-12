</main>

<footer class="site-footer pt-5 pb-4">
    <div class="container">
        <div class="row g-4">
            <div class="col-lg-5">
                <h5 class="fw-bold"><span class="brand-mark">&lt;/&gt;</span> <?= e(SITE_NAME) ?></h5>
                <p class="footer-text mb-3">
                    Full stack web developer and instructor. I build websites, fix broken ones,
                    and teach others how to do the same.
                </p>
                <div class="d-flex gap-2">
                    <a class="social-link" href="https://wa.me/<?= e(SITE_WHATSAPP) ?>" target="_blank" rel="noopener" aria-label="WhatsApp"><i class="bi bi-whatsapp"></i></a>
                    <a class="social-link" href="mailto:<?= e(SITE_EMAIL) ?>" aria-label="Email"><i class="bi bi-envelope-fill"></i></a>
                    <a class="social-link" href="https://github.com/" target="_blank" rel="noopener" aria-label="GitHub"><i class="bi bi-github"></i></a>
                    <a class="social-link" href="https://linkedin.com/" target="_blank" rel="noopener" aria-label="LinkedIn"><i class="bi bi-linkedin"></i></a>
                </div>
            </div>

            <div class="col-6 col-lg-3">
                <h6 class="fw-semibold mb-3">Quick Links</h6>
                <ul class="list-unstyled footer-links">
                    <li><a href="<?= url('about.php') ?>">About</a></li>
                    <li><a href="<?= url('services.php') ?>">Services</a></li>
                    <li><a href="<?= url('portfolio.php') ?>">Portfolio</a></li>
                    <li><a href="<?= url('training.php') ?>">Training</a></li>
                    <li><a href="<?= url('contact.php') ?>">Contact</a></li>
                </ul>
            </div>

            <div class="col-6 col-lg-4">
                <h6 class="fw-semibold mb-3">Get In Touch</h6>
                <ul class="list-unstyled footer-links">
                    <li><i class="bi bi-envelope me-2"></i><?= e(SITE_EMAIL) ?></li>
                    <li><i class="bi bi-whatsapp me-2"></i>+<?= e(SITE_WHATSAPP) ?></li>
                    <li><i class="bi bi-geo-alt me-2"></i>Nigeria</li>
                </ul>
            </div>
        </div>

        <hr class="footer-divider my-4">
        <p class="text-center footer-text small mb-0">
            &copy; <span id="year"><?= date('Y') ?></span> <?= e(SITE_NAME) ?>. All rights reserved.
        </p>
    </div>
</footer>

<!-- Floating buttons -->
<a href="https://wa.me/<?= e(SITE_WHATSAPP) ?>" class="whatsapp-float" target="_blank" rel="noopener" aria-label="Chat on WhatsApp">
    <i class="bi bi-whatsapp"></i>
</a>
<button type="button" class="back-to-top" id="backToTop" aria-label="Back to top">
    <i class="bi bi-arrow-up"></i>
</button>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="<?= url('assets/js/main.js') ?>"></script>
</body>
</html>