/**
 * app.js — visitapurranque.cl
 * Hamburger menu, scroll-to-top, close menu on link click
 */
document.addEventListener('DOMContentLoaded', function () {

    // ── Hamburger toggle ─────────────────────────────────
    var hamburger = document.getElementById('hamburger');
    var mainNav   = document.getElementById('mainNav');

    if (hamburger && mainNav) {
        hamburger.addEventListener('click', function () {
            var expanded = this.getAttribute('aria-expanded') === 'true';
            this.setAttribute('aria-expanded', !expanded);
            this.classList.toggle('active');
            mainNav.classList.toggle('active');
        });

        // Cerrar menu mobile al hacer clic en un enlace
        mainNav.querySelectorAll('a').forEach(function (link) {
            link.addEventListener('click', function () {
                hamburger.classList.remove('active');
                mainNav.classList.remove('active');
                hamburger.setAttribute('aria-expanded', 'false');
            });
        });
    }

    // ── Scroll to top ────────────────────────────────────
    var backToTop = document.getElementById('backToTop');

    if (backToTop) {
        window.addEventListener('scroll', function () {
            if (window.scrollY > 300) {
                backToTop.classList.add('visible');
            } else {
                backToTop.classList.remove('visible');
            }
        });

        backToTop.addEventListener('click', function () {
            window.scrollTo({ top: 0, behavior: 'smooth' });
        });
    }
});
