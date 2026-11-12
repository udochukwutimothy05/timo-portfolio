/* Timothy Udochukwu Portfolio - shared JavaScript */
document.addEventListener('DOMContentLoaded', function () {

    // ---- Navbar shadow after scrolling ----
    var nav = document.getElementById('siteNav');
    var backToTop = document.getElementById('backToTop');

    function onScroll() {
        var y = window.scrollY || document.documentElement.scrollTop;
        if (nav) nav.classList.toggle('scrolled', y > 10);
        if (backToTop) backToTop.classList.toggle('show', y > 400);
    }
    window.addEventListener('scroll', onScroll, { passive: true });
    onScroll();

    // ---- Back to top ----
    if (backToTop) {
        backToTop.addEventListener('click', function () {
            window.scrollTo({ top: 0, behavior: 'smooth' });
        });
    }

    // ---- Light / dark theme toggle ----
    var root = document.documentElement;
    var toggle = document.getElementById('themeToggle');

    function updateIcon() {
        if (!toggle) return;
        var dark = root.getAttribute('data-bs-theme') === 'dark';
        toggle.innerHTML = dark
            ? '<i class="bi bi-sun-fill"></i>'
            : '<i class="bi bi-moon-stars-fill"></i>';
    }
    updateIcon();

    if (toggle) {
        toggle.addEventListener('click', function () {
            var next = root.getAttribute('data-bs-theme') === 'dark' ? 'light' : 'dark';
            root.setAttribute('data-bs-theme', next);
            try { localStorage.setItem('theme', next); } catch (err) {}
            updateIcon();
        });
    }

    // ---- Scroll reveal animation ----
    var items = document.querySelectorAll('.reveal');
    if ('IntersectionObserver' in window) {
        var observer = new IntersectionObserver(function (entries) {
            entries.forEach(function (entry) {
                if (entry.isIntersecting) {
                    entry.target.classList.add('visible');
                    observer.unobserve(entry.target);
                }
            });
        }, { threshold: 0.12 });
        items.forEach(function (el) { observer.observe(el); });
    } else {
        items.forEach(function (el) { el.classList.add('visible'); });
    }
});

/* ---- Typing effect for the hero heading ---- */
document.addEventListener('DOMContentLoaded', function () {
    var el = document.getElementById('typed');
    if (!el) return;

    var words;
    try { words = JSON.parse(el.getAttribute('data-words')); } catch (err) { return; }
    if (!words || !words.length) return;

    // Respect users who prefer less motion
    if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
        el.textContent = words[0];
        return;
    }

    var w = 0, i = 0, deleting = false;

    function tick() {
        var word = words[w];
        el.textContent = word.substring(0, i);

        var delay = deleting ? 45 : 90;

        if (!deleting && i === word.length) {
            deleting = true;
            delay = 1500;                       // pause on the full word
        } else if (deleting && i === 0) {
            deleting = false;
            w = (w + 1) % words.length;
            delay = 400;
        } else {
            i += deleting ? -1 : 1;
        }
        setTimeout(tick, delay);
    }
    tick();
});

/* ---- Animate skill bars when they scroll into view ---- */
document.addEventListener('DOMContentLoaded', function () {
    var bars = document.querySelectorAll('.progress-bar[data-width]');
    if (!bars.length) return;

    function fill(bar) { bar.style.width = bar.getAttribute('data-width') + '%'; }

    if ('IntersectionObserver' in window) {
        var obs = new IntersectionObserver(function (entries) {
            entries.forEach(function (entry) {
                if (entry.isIntersecting) {
                    fill(entry.target);
                    obs.unobserve(entry.target);
                }
            });
        }, { threshold: 0.4 });
        bars.forEach(function (bar) { obs.observe(bar); });
    } else {
        bars.forEach(fill);
    }
});

/* ---- Portfolio category filter ---- */
document.addEventListener('DOMContentLoaded', function () {
    var buttons = document.querySelectorAll('.filter-btn');
    var items = document.querySelectorAll('.project-item');
    var empty = document.getElementById('noResults');
    if (!buttons.length || !items.length) return;

    buttons.forEach(function (btn) {
        btn.addEventListener('click', function () {
            var filter = btn.getAttribute('data-filter');

            buttons.forEach(function (b) { b.classList.remove('active'); });
            btn.classList.add('active');

            var shown = 0;
            items.forEach(function (item) {
                var match = filter === 'all' || item.getAttribute('data-category') === filter;
                item.classList.toggle('d-none', !match);
                if (match) {
                    item.classList.add('visible'); // make sure it is not stuck invisible
                    shown++;
                }
            });

            if (empty) empty.classList.toggle('d-none', shown !== 0);
        });
    });
});

/* ---- Contact form: validate and send with fetch ---- */
document.addEventListener('DOMContentLoaded', function () {
    var form = document.getElementById('contactForm');
    if (!form) return;

    var alertBox = document.getElementById('formAlert');
    var btn = document.getElementById('sendBtn');
    var label = btn.querySelector('.btn-label');
    var loading = btn.querySelector('.btn-loading');

    function showAlert(type, text) {
        alertBox.innerHTML =
            '<div class="alert alert-' + type + ' alert-dismissible fade show" role="alert">' +
            text.replace(/</g, '&lt;') +
            '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>';
        alertBox.scrollIntoView({ behavior: 'smooth', block: 'center' });
    }

    function setLoading(on) {
        btn.disabled = on;
        label.classList.toggle('d-none', on);
        loading.classList.toggle('d-none', !on);
    }

    form.addEventListener('submit', function (event) {
        event.preventDefault();
        alertBox.innerHTML = '';

        // Browser-side validation first
        if (!form.checkValidity()) {
            form.classList.add('was-validated');
            return;
        }

        setLoading(true);

        fetch(form.action, {
            method: 'POST',
            body: new FormData(form),
            headers: { 'X-Requested-With': 'fetch' }
        })
        .then(function (response) { return response.json(); })
        .then(function (data) {
            showAlert(data.success ? 'success' : 'danger', data.message);
            if (data.success) {
                form.reset();
                form.classList.remove('was-validated');
            }
        })
        .catch(function () {
            showAlert('danger', 'Could not send your message. Please check your connection or message me on WhatsApp.');
        })
        .finally(function () { setLoading(false); });
    });
});