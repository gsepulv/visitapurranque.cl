/**
 * Proyecto Dashboard — visitapurranque.cl
 */
(function () {
    'use strict';

    // ── Toggle tarea estado ──────────────────────────────
    document.querySelectorAll('.tarea-check input[type="checkbox"]').forEach(function (cb) {
        cb.addEventListener('change', function () {
            var card = this.closest('.tarea-card');
            var id = card.dataset.id;
            var nuevoEstado = this.checked ? 'completada' : 'pendiente';

            // Update UI immediately
            var badge = card.querySelector('.tarea-badge-estado');
            if (badge) {
                badge.className = 'tarea-badge tarea-badge-estado ' + nuevoEstado;
                badge.textContent = nuevoEstado.replace('_', ' ');
            }
            card.classList.toggle('completada', this.checked);

            fetch(SITE_URL + '/proyecto/api/tarea-toggle', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ id: parseInt(id), estado: nuevoEstado })
            })
            .then(function (r) { return r.json(); })
            .then(function (data) {
                if (!data.ok) {
                    // Revert on error
                    cb.checked = !cb.checked;
                    card.classList.toggle('completada', cb.checked);
                }
            })
            .catch(function () {
                cb.checked = !cb.checked;
                card.classList.toggle('completada', cb.checked);
            });
        });
    });

    // ── Update horas reales ──────────────────────────────
    document.querySelectorAll('.horas-reales-input').forEach(function (input) {
        var timeout;
        input.addEventListener('input', function () {
            var el = this;
            clearTimeout(timeout);
            timeout = setTimeout(function () {
                var card = el.closest('.tarea-card');
                var id = card.dataset.id;
                var horas = parseFloat(el.value) || 0;

                fetch(SITE_URL + '/proyecto/api/tarea-toggle', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({
                        id: parseInt(id),
                        estado: card.querySelector('.tarea-check input').checked ? 'completada' : 'pendiente',
                        horas_reales: horas
                    })
                });
            }, 800);
        });
    });

    // ── Collapsible sections on mobile ───────────────────
    document.querySelectorAll('.proy-section-title[data-collapsible]').forEach(function (title) {
        title.style.cursor = 'pointer';
        title.addEventListener('click', function () {
            var next = this.nextElementSibling;
            if (next) {
                var isHidden = next.style.display === 'none';
                next.style.display = isHidden ? '' : 'none';
                this.classList.toggle('collapsed', !isHidden);
            }
        });
    });
})();
