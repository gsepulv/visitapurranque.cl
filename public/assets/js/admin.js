/**
 * admin.js — visitapurranque.cl Panel de Administración
 */
document.addEventListener('DOMContentLoaded', function () {

    // ── Sidebar toggle (mobile) ─────────────────────────
    var hamburger = document.getElementById('adminHamburger');
    var sidebar   = document.getElementById('adminSidebar');
    var overlay   = document.getElementById('adminOverlay');

    function toggleSidebar() {
        var open = sidebar.classList.toggle('active');
        overlay.classList.toggle('active', open);
        hamburger.classList.toggle('active', open);
    }

    function closeSidebar() {
        sidebar.classList.remove('active');
        overlay.classList.remove('active');
        hamburger.classList.remove('active');
    }

    if (hamburger) hamburger.addEventListener('click', toggleSidebar);
    if (overlay) overlay.addEventListener('click', closeSidebar);

    // Cerrar sidebar al navegar en mobile
    if (sidebar) {
        sidebar.querySelectorAll('.sidebar-link').forEach(function (link) {
            link.addEventListener('click', closeSidebar);
        });
    }

    // ── Auto-hide flash messages ────────────────────────
    document.querySelectorAll('[data-autohide]').forEach(function (el) {
        setTimeout(function () {
            el.style.opacity = '0';
            setTimeout(function () { el.remove(); }, 400);
        }, 4000);
    });

    // ── Confirmación de eliminación ─────────────────────
    document.querySelectorAll('[data-confirm]').forEach(function (el) {
        el.addEventListener('click', function (e) {
            var msg = this.getAttribute('data-confirm') || '¿Estás seguro?';
            if (!confirm(msg)) {
                e.preventDefault();
            }
        });
    });
});
