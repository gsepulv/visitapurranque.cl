<?php
/**
 * Mapa interactivo — visitapurranque.cl
 * Variables: $fichas, $categorias
 */
$fichasJson     = json_encode($fichas, JSON_UNESCAPED_UNICODE);
$categoriasJson = json_encode($categorias, JSON_UNESCAPED_UNICODE);
?>

<section class="hero-section">
    <div class="container">
        <nav class="breadcrumb breadcrumb--light" aria-label="Migas de pan">
            <a href="<?= url('/') ?>">Inicio</a> <span class="breadcrumb-sep">/</span>
            <span>Mapa</span>
        </nav>
        <h1>Mapa interactivo</h1>
        <p class="hero-subtitle">Explora los atractivos turisticos de Purranque. Filtra por categoria o haz clic en un marcador para ver mas detalles.</p>
    </div>
</section>

<!-- Toolbar filtros -->
<div class="mapa-toolbar">
    <div class="mapa-toolbar__inner">
        <button class="mapa-filter active" data-cat="all">Todos <span class="mapa-filter__count"><?= count($fichas) ?></span></button>
        <?php foreach ($categorias as $cat): ?>
            <?php
            $countCat = 0;
            foreach ($fichas as $f) {
                if ((int)$f['categoria_id'] === (int)$cat['id']) $countCat++;
            }
            if ($countCat === 0) continue;
            ?>
            <button class="mapa-filter" data-cat="<?= (int)$cat['id'] ?>" style="--cat-color: <?= htmlspecialchars($cat['color'] ?? '#3b82f6') ?>">
                <?= htmlspecialchars($cat['emoji'] ?? '') ?> <?= htmlspecialchars($cat['nombre']) ?>
                <span class="mapa-filter__count"><?= $countCat ?></span>
            </button>
        <?php endforeach; ?>
    </div>
</div>

<!-- Layout: sidebar + mapa -->
<section class="mapa-section">
    <div class="mapa-layout">
        <!-- Panel lateral -->
        <aside class="mapa-sidebar" id="mapa-sidebar">
            <div class="mapa-sidebar__header">
                <h2 class="mapa-sidebar__title">Atractivos <span id="sidebar-count">(<?= count($fichas) ?>)</span></h2>
                <button class="mapa-sidebar__toggle" id="sidebar-toggle" aria-label="Cerrar panel">
                    <svg width="20" height="20" viewBox="0 0 20 20" fill="none"><path d="M7 4l6 6-6 6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                </button>
            </div>
            <ul class="mapa-sidebar__list" id="sidebar-list">
                <?php foreach ($fichas as $f): ?>
                    <li class="mapa-sidebar__item" data-ficha-id="<?= (int)$f['id'] ?>" data-cat="<?= (int)$f['categoria_id'] ?>">
                        <div class="mapa-sidebar__dot" style="background: <?= htmlspecialchars($f['categoria_color'] ?? '#3b82f6') ?>"></div>
                        <div class="mapa-sidebar__info">
                            <strong class="mapa-sidebar__name"><?= htmlspecialchars($f['categoria_emoji'] ?? '') ?> <?= htmlspecialchars($f['nombre']) ?></strong>
                            <span class="mapa-sidebar__cat"><?= htmlspecialchars($f['categoria_nombre'] ?? '') ?></span>
                            <?php if (!empty($f['descripcion_corta'])): ?>
                                <p class="mapa-sidebar__desc"><?= htmlspecialchars(mb_substr($f['descripcion_corta'], 0, 80)) ?><?= mb_strlen($f['descripcion_corta']) > 80 ? '...' : '' ?></p>
                            <?php endif; ?>
                        </div>
                    </li>
                <?php endforeach; ?>
            </ul>
        </aside>

        <!-- Mapa -->
        <div class="mapa-main" id="mapa-principal"></div>
    </div>
</section>

<!-- Boton mobile para mostrar sidebar -->
<button class="mapa-sidebar-fab" id="sidebar-fab" aria-label="Ver listado">
    <svg width="20" height="20" viewBox="0 0 20 20" fill="none"><path d="M3 5h14M3 10h14M3 15h14" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
    <span>Lista</span>
</button>

<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" crossorigin="">
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" crossorigin=""></script>
<script>
(function(){
    var fichas = <?= $fichasJson ?>;
    var categorias = <?= $categoriasJson ?>;
    var markers = {};
    var activeFilter = 'all';

    // Init map
    var map = L.map('mapa-principal', { zoomControl: false }).setView([-40.91, -73.13], 10);

    L.control.zoom({ position: 'topright' }).addTo(map);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>',
        maxZoom: 18
    }).addTo(map);

    if (fichas.length === 0) return;

    // Build color map
    var catColors = {};
    categorias.forEach(function(c){ catColors[c.id] = c.color || '#3b82f6'; });

    // Create markers
    var bounds = [];
    fichas.forEach(function(f){
        var lat = parseFloat(f.latitud), lng = parseFloat(f.longitud);
        if (isNaN(lat) || isNaN(lng)) return;
        bounds.push([lat, lng]);

        var color = catColors[f.categoria_id] || '#3b82f6';
        var icon = L.divIcon({
            className: 'mapa-marker',
            html: '<div class="mapa-marker__pin" style="background:' + color + ';box-shadow:0 2px 6px ' + color + '66"></div>',
            iconSize: [24, 24],
            iconAnchor: [12, 12],
            popupAnchor: [0, -14]
        });

        var emoji = f.categoria_emoji || '';
        var popup = '<div class="mapa-popup">'
            + '<strong class="mapa-popup__title">' + emoji + ' ' + escHtml(f.nombre) + '</strong>'
            + (f.descripcion_corta ? '<p class="mapa-popup__desc">' + escHtml(f.descripcion_corta) + '</p>' : '')
            + (f.categoria_nombre ? '<span class="mapa-popup__badge" style="background:' + color + '15;color:' + color + '">' + escHtml(f.categoria_nombre) + '</span> ' : '')
            + '<a href="<?= url('/atractivo/') ?>' + f.slug + '" class="mapa-popup__link">Ver detalle &rarr;</a>'
            + '</div>';

        var marker = L.marker([lat, lng], { icon: icon }).addTo(map).bindPopup(popup);
        marker._fichaId = f.id;
        marker._catId = f.categoria_id;

        marker.on('click', function(){
            scrollSidebarTo(f.id);
        });

        markers[f.id] = marker;
    });

    if (bounds.length > 1) {
        map.fitBounds(bounds, { padding: [40, 40] });
    }

    // ── Filters ──
    var filterBtns = document.querySelectorAll('.mapa-filter');
    filterBtns.forEach(function(btn){
        btn.addEventListener('click', function(){
            var cat = this.dataset.cat;
            activeFilter = cat;

            filterBtns.forEach(function(b){ b.classList.remove('active'); });
            this.classList.add('active');

            applyFilter(cat);
        });
    });

    function applyFilter(cat) {
        var visibleCount = 0;
        var visibleBounds = [];

        // Filter markers
        for (var id in markers) {
            var m = markers[id];
            if (cat === 'all' || String(m._catId) === String(cat)) {
                m.addTo(map);
                visibleCount++;
                visibleBounds.push(m.getLatLng());
            } else {
                map.removeLayer(m);
            }
        }

        // Filter sidebar items
        var items = document.querySelectorAll('.mapa-sidebar__item');
        items.forEach(function(item){
            if (cat === 'all' || item.dataset.cat === String(cat)) {
                item.style.display = '';
            } else {
                item.style.display = 'none';
            }
        });

        // Update count
        document.getElementById('sidebar-count').textContent = '(' + visibleCount + ')';

        // Fit bounds to visible
        if (visibleBounds.length > 1) {
            map.fitBounds(visibleBounds, { padding: [40, 40] });
        } else if (visibleBounds.length === 1) {
            map.setView(visibleBounds[0], 14);
        }
    }

    // ── Sidebar click → fly to marker ──
    document.querySelectorAll('.mapa-sidebar__item').forEach(function(item){
        item.addEventListener('click', function(){
            var fid = parseInt(this.dataset.fichaId);
            var marker = markers[fid];
            if (!marker) return;

            // Highlight
            document.querySelectorAll('.mapa-sidebar__item').forEach(function(i){ i.classList.remove('active'); });
            this.classList.add('active');

            map.flyTo(marker.getLatLng(), 15, { duration: 0.8 });
            setTimeout(function(){ marker.openPopup(); }, 400);

            // Mobile: close sidebar
            if (window.innerWidth < 768) {
                document.getElementById('mapa-sidebar').classList.remove('open');
            }
        });
    });

    // ── Scroll sidebar to item ──
    function scrollSidebarTo(fichaId) {
        var item = document.querySelector('.mapa-sidebar__item[data-ficha-id="' + fichaId + '"]');
        if (!item) return;
        document.querySelectorAll('.mapa-sidebar__item').forEach(function(i){ i.classList.remove('active'); });
        item.classList.add('active');
        item.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
    }

    // ── Sidebar toggle (mobile) ──
    var sidebar = document.getElementById('mapa-sidebar');
    var sidebarToggle = document.getElementById('sidebar-toggle');
    var sidebarFab = document.getElementById('sidebar-fab');

    sidebarToggle.addEventListener('click', function(){
        sidebar.classList.remove('open');
    });
    sidebarFab.addEventListener('click', function(){
        sidebar.classList.toggle('open');
    });

    // Escape html
    function escHtml(s) {
        var d = document.createElement('div');
        d.textContent = s;
        return d.innerHTML;
    }
})();
</script>
