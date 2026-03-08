<?php
/**
 * Admin Header — visitapurranque.cl
 * Variables: $pageTitle, $usuario, $csrf, $flash
 */
?>
<!DOCTYPE html>
<html lang="es-CL">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($pageTitle ?? 'Admin') ?> — <?= e(SITE_NAME) ?></title>
    <meta name="robots" content="noindex, nofollow">
    <link rel="icon" href="<?= asset('img/favicon.ico') ?>" type="image/x-icon">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Serif+Display&family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= asset('css/admin.css?v=' . APP_VERSION) ?>">
</head>
<body class="admin-body">

    <!-- Top bar mobile -->
    <header class="admin-topbar">
        <button class="admin-hamburger" id="adminHamburger" aria-label="Abrir menú">
            <span></span><span></span><span></span>
        </button>
        <span class="admin-topbar-title">VP Admin</span>
        <div class="admin-topbar-actions">
            <!-- Notificaciones -->
            <div class="notif-bell" id="notifBell">
                <button type="button" class="notif-bell__btn" id="notifBellBtn" aria-label="Notificaciones">
                    &#128276;
                    <span class="notif-bell__badge" id="notifBadge" style="display:none;">0</span>
                </button>
                <div class="notif-bell__dropdown" id="notifDropdown" style="display:none;">
                    <div class="notif-bell__header">
                        <strong>Notificaciones</strong>
                        <button type="button" class="notif-bell__mark-all" id="notifMarkAll">Marcar leídas</button>
                    </div>
                    <div class="notif-bell__list" id="notifList">
                        <div class="notif-bell__empty">Cargando...</div>
                    </div>
                    <a href="<?= url('/admin/notificaciones') ?>" class="notif-bell__footer">Ver todas</a>
                </div>
            </div>
            <a href="<?= url('/') ?>" target="_blank" class="admin-topbar-site" title="Ver sitio">&#8599;</a>
        </div>
    </header>

    <!-- Overlay mobile -->
    <div class="admin-overlay" id="adminOverlay"></div>

    <script>
    (function() {
        var bell = document.getElementById('notifBellBtn');
        var dropdown = document.getElementById('notifDropdown');
        var badge = document.getElementById('notifBadge');
        var list = document.getElementById('notifList');

        function loadNotifs() {
            fetch('<?= url('/admin/notificaciones/api') ?>')
                .then(function(r) { return r.json(); })
                .then(function(data) {
                    if (data.no_leidas > 0) {
                        badge.textContent = data.no_leidas > 99 ? '99+' : data.no_leidas;
                        badge.style.display = '';
                    } else {
                        badge.style.display = 'none';
                    }
                    if (data.items.length === 0) {
                        list.innerHTML = '<div class="notif-bell__empty">Sin notificaciones</div>';
                        return;
                    }
                    var icons = { resena: '&#11088;', contacto: '&#128231;', sistema: '&#9881;', ficha: '&#128205;', evento: '&#128197;', blog: '&#128221;' };
                    var html = '';
                    data.items.forEach(function(n) {
                        var cls = n.leida ? 'notif-bell__item' : 'notif-bell__item notif-bell__item--unread';
                        var icon = icons[n.tipo] || '&#128276;';
                        var link = n.url ? '<?= url('') ?>' + n.url : '#';
                        html += '<a href="' + link + '" class="' + cls + '">';
                        html += '<span class="notif-bell__icon">' + icon + '</span>';
                        html += '<span class="notif-bell__text">' + n.titulo + '</span>';
                        html += '</a>';
                    });
                    list.innerHTML = html;
                })
                .catch(function() {});
        }

        bell.addEventListener('click', function(e) {
            e.stopPropagation();
            var visible = dropdown.style.display !== 'none';
            dropdown.style.display = visible ? 'none' : 'block';
            if (!visible) loadNotifs();
        });

        document.addEventListener('click', function(e) {
            if (!e.target.closest('#notifBell')) dropdown.style.display = 'none';
        });

        document.getElementById('notifMarkAll').addEventListener('click', function() {
            fetch('<?= url('/admin/notificaciones/leer-todas') ?>', {
                method: 'POST',
                headers: { 'X-Requested-With': 'XMLHttpRequest', 'Content-Type': 'application/x-www-form-urlencoded' },
                body: '_csrf=<?= csrf_token() ?>'
            }).then(function() { loadNotifs(); });
        });

        // Poll every 60 seconds
        loadNotifs();
        setInterval(loadNotifs, 60000);
    })();
    </script>
