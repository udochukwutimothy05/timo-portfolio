    </main>
</div><!-- /.admin-main -->

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Mobile sidebar: open, close, close on backdrop / link / Escape
    (function () {
        var body = document.body;
        var open = document.getElementById('sidebarToggle');
        var close = document.getElementById('sidebarClose');
        var backdrop = document.getElementById('sidebarBackdrop');

        function setOpen(state) { body.classList.toggle('sidebar-open', state); }

        if (open) open.addEventListener('click', function () { setOpen(true); });
        if (close) close.addEventListener('click', function () { setOpen(false); });
        if (backdrop) backdrop.addEventListener('click', function () { setOpen(false); });

        document.querySelectorAll('.sidebar-link').forEach(function (link) {
            link.addEventListener('click', function () { setOpen(false); });
        });
        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape') setOpen(false);
        });
    })();
</script>
</body>
</html>