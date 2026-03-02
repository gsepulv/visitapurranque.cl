<?php
/**
 * Mapa interactivo — visitapurranque.cl
 * Variables: $fichas, $categorias, $eventos
 */
$fichasJson     = json_encode($fichas, JSON_UNESCAPED_UNICODE);
$categoriasJson = json_encode($categorias, JSON_UNESCAPED_UNICODE);
$eventosJson    = json_encode($eventos ?? [], JSON_UNESCAPED_UNICODE);
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
        <?php if (!empty($eventos)): ?>
            <button class="mapa-filter mapa-filter--eventos active" id="toggle-eventos" data-cat="eventos" style="--cat-color: #e11d48">
                &#x1f4c5; Eventos <span class="mapa-filter__count"><?= count($eventos) ?></span>
            </button>
        <?php endif; ?>
    </div>
</div>

<!-- Layout: sidebar + mapa -->
<section class="mapa-section">
    <div class="mapa-layout">
        <!-- Panel lateral -->
        <aside class="mapa-sidebar" id="mapa-sidebar">
            <div class="mapa-sidebar__header">
                <h2 class="mapa-sidebar__title">Atractivos <span id="sidebar-count">(<?= count($fichas) + count($eventos ?? []) ?>)</span></h2>
                <button class="mapa-sidebar__toggle" id="sidebar-toggle" aria-label="Cerrar panel">
                    <svg width="20" height="20" viewBox="0 0 20 20" fill="none"><path d="M7 4l6 6-6 6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                </button>
            </div>
            <div class="mapa-sidebar__search-wrap">
                <input type="search" id="mapa-search" class="mapa-sidebar__search" placeholder="Buscar atractivo o evento...">
            </div>
            <ul class="mapa-sidebar__list" id="sidebar-list">
                <?php foreach ($fichas as $f): ?>
                    <li class="mapa-sidebar__item" data-ficha-id="<?= (int)$f['id'] ?>" data-cat="<?= (int)$f['categoria_id'] ?>" data-type="ficha" data-name="<?= htmlspecialchars(mb_strtolower($f['nombre'])) ?>">
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
                <?php if (!empty($eventos)): ?>
                    <?php foreach ($eventos as $ev): ?>
                        <li class="mapa-sidebar__item mapa-sidebar__item--evento" data-evento-id="<?= (int)$ev['id'] ?>" data-type="evento" data-name="<?= htmlspecialchars(mb_strtolower($ev['titulo'])) ?>">
                            <div class="mapa-sidebar__dot" style="background: #e11d48"></div>
                            <div class="mapa-sidebar__info">
                                <strong class="mapa-sidebar__name">&#x1f4c5; <?= htmlspecialchars($ev['titulo']) ?></strong>
                                <span class="mapa-sidebar__cat mapa-sidebar__badge-evento">Evento<?php if (!empty($ev['lugar'])): ?> &middot; <?= htmlspecialchars($ev['lugar']) ?><?php endif; ?></span>
                                <?php if (!empty($ev['fecha_inicio'])): ?>
                                    <span class="mapa-sidebar__cat"><?= date('d/m/Y', strtotime($ev['fecha_inicio'])) ?></span>
                                <?php endif; ?>
                            </div>
                        </li>
                    <?php endforeach; ?>
                <?php endif; ?>
            </ul>
        </aside>

        <!-- Mapa -->
        <div class="mapa-main" id="mapa-principal">
            <button class="mapa-geoloc-btn" id="geoloc-btn" title="Mi ubicacion">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="4"/><path d="M12 2v4M12 18v4M2 12h4M18 12h4"/></svg>
            </button>
        </div>
    </div>
</section>

<!-- Boton mobile para mostrar sidebar -->
<button class="mapa-sidebar-fab" id="sidebar-fab" aria-label="Ver listado">
    <svg width="20" height="20" viewBox="0 0 20 20" fill="none"><path d="M3 5h14M3 10h14M3 15h14" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
    <span>Lista</span>
</button>

<!-- Leaflet + MarkerCluster -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" crossorigin="">
<link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster@1.5.3/dist/MarkerCluster.css" crossorigin="">
<link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster@1.5.3/dist/MarkerCluster.Default.css" crossorigin="">
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" crossorigin=""></script>
<script src="https://unpkg.com/leaflet.markercluster@1.5.3/dist/leaflet.markercluster.js" crossorigin=""></script>
<script>
(function(){
    var fichas = <?= $fichasJson ?>;
    var categorias = <?= $categoriasJson ?>;
    var eventos = <?= $eventosJson ?>;
    var fichaMarkers = {};
    var eventoMarkers = {};
    var activeFilter = 'all';
    var showEventos = true;
    var searchQuery = '';
    var userMarker = null;

    // Init map
    var map = L.map('mapa-principal', { zoomControl: false }).setView([-40.91, -73.13], 10);
    L.control.zoom({ position: 'topright' }).addTo(map);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>',
        maxZoom: 18
    }).addTo(map);

    // Cluster group
    var clusterGroup = L.markerClusterGroup({
        maxClusterRadius: 50,
        spiderfyOnMaxZoom: true,
        showCoverageOnHover: false
    });
    map.addLayer(clusterGroup);

    // Build color map
    var catColors = {};
    categorias.forEach(function(c){ catColors[c.id] = c.color || '#3b82f6'; });

    // ── Create ficha markers ──
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

        var marker = L.marker([lat, lng], { icon: icon }).bindPopup(popup);
        marker._fichaId = f.id;
        marker._catId = f.categoria_id;
        marker._nombre = (f.nombre || '').toLowerCase();
        marker._type = 'ficha';

        marker.on('click', function(){ scrollSidebarTo('ficha', f.id); });
        fichaMarkers[f.id] = marker;
    });

    // ── Create evento markers ──
    eventos.forEach(function(ev){
        var lat = parseFloat(ev.latitud), lng = parseFloat(ev.longitud);
        if (isNaN(lat) || isNaN(lng)) return;
        bounds.push([lat, lng]);

        var icon = L.divIcon({
            className: 'mapa-marker mapa-marker--evento',
            html: '<div class="mapa-marker__pin mapa-marker__pin--evento"></div>',
            iconSize: [24, 24],
            iconAnchor: [12, 12],
            popupAnchor: [0, -14]
        });

        var fechaStr = '';
        if (ev.fecha_inicio) {
            var d = new Date(ev.fecha_inicio);
            fechaStr = d.toLocaleDateString('es-CL', { day: 'numeric', month: 'short', year: 'numeric' });
        }

        var popup = '<div class="mapa-popup">'
            + '<strong class="mapa-popup__title">&#x1f4c5; ' + escHtml(ev.titulo) + '</strong>'
            + (fechaStr ? '<p class="mapa-popup__desc" style="margin:2px 0">' + fechaStr + '</p>' : '')
            + (ev.lugar ? '<p class="mapa-popup__desc" style="margin:2px 0">' + escHtml(ev.lugar) + '</p>' : '')
            + '<span class="mapa-popup__badge" style="background:#e11d4815;color:#e11d48">Evento</span> '
            + '<a href="<?= url('/evento/') ?>' + ev.slug + '" class="mapa-popup__link">Ver detalle &rarr;</a>'
            + '</div>';

        var marker = L.marker([lat, lng], { icon: icon }).bindPopup(popup);
        marker._eventoId = ev.id;
        marker._nombre = (ev.titulo || '').toLowerCase();
        marker._type = 'evento';

        marker.on('click', function(){ scrollSidebarTo('evento', ev.id); });
        eventoMarkers[ev.id] = marker;
    });

    // Initial render
    rebuildCluster();

    if (bounds.length > 1) {
        map.fitBounds(bounds, { padding: [40, 40] });
    }

    // ── Rebuild cluster group based on active filters ──
    function rebuildCluster() {
        clusterGroup.clearLayers();
        var visibleCount = 0;

        // Add ficha markers
        for (var id in fichaMarkers) {
            var m = fichaMarkers[id];
            var catMatch = (activeFilter === 'all' || String(m._catId) === String(activeFilter));
            var searchMatch = (!searchQuery || m._nombre.indexOf(searchQuery) !== -1);
            if (catMatch && searchMatch) {
                clusterGroup.addLayer(m);
                visibleCount++;
            }
        }

        // Add evento markers
        if (showEventos) {
            for (var eid in eventoMarkers) {
                var em = eventoMarkers[eid];
                var evSearchMatch = (!searchQuery || em._nombre.indexOf(searchQuery) !== -1);
                if (evSearchMatch) {
                    clusterGroup.addLayer(em);
                    visibleCount++;
                }
            }
        }

        // Filter sidebar items
        var items = document.querySelectorAll('.mapa-sidebar__item');
        items.forEach(function(item){
            var type = item.dataset.type;
            var name = item.dataset.name || '';
            var show = false;

            if (type === 'ficha') {
                var catMatch = (activeFilter === 'all' || item.dataset.cat === String(activeFilter));
                var searchMatch = (!searchQuery || name.indexOf(searchQuery) !== -1);
                show = catMatch && searchMatch;
            } else if (type === 'evento') {
                var evSearchMatch = (!searchQuery || name.indexOf(searchQuery) !== -1);
                show = showEventos && evSearchMatch;
            }

            item.style.display = show ? '' : 'none';
        });

        // Update count
        document.getElementById('sidebar-count').textContent = '(' + visibleCount + ')';
    }

    // ── Category filters ──
    var filterBtns = document.querySelectorAll('.mapa-filter:not(#toggle-eventos)');
    filterBtns.forEach(function(btn){
        btn.addEventListener('click', function(){
            var cat = this.dataset.cat;
            activeFilter = cat;

            filterBtns.forEach(function(b){ b.classList.remove('active'); });
            this.classList.add('active');

            rebuildCluster();
        });
    });

    // ── Eventos toggle ──
    var toggleEventosBtn = document.getElementById('toggle-eventos');
    if (toggleEventosBtn) {
        toggleEventosBtn.addEventListener('click', function(e){
            e.stopPropagation();
            showEventos = !showEventos;
            this.classList.toggle('active', showEventos);
            rebuildCluster();
        });
    }

    // ── Search ──
    var searchInput = document.getElementById('mapa-search');
    searchInput.addEventListener('input', function(){
        searchQuery = this.value.trim().toLowerCase();
        rebuildCluster();
    });

    // ── Sidebar click → fly to marker ──
    document.getElementById('sidebar-list').addEventListener('click', function(e){
        var item = e.target.closest('.mapa-sidebar__item');
        if (!item) return;

        var type = item.dataset.type;
        var marker = null;

        if (type === 'ficha') {
            marker = fichaMarkers[parseInt(item.dataset.fichaId)];
        } else if (type === 'evento') {
            marker = eventoMarkers[parseInt(item.dataset.eventoId)];
        }
        if (!marker) return;

        // Highlight
        document.querySelectorAll('.mapa-sidebar__item').forEach(function(i){ i.classList.remove('active'); });
        item.classList.add('active');

        // Zoom to marker — spiderfy cluster if needed
        clusterGroup.zoomToShowLayer(marker, function(){
            marker.openPopup();
        });

        // Mobile: close sidebar
        if (window.innerWidth < 768) {
            document.getElementById('mapa-sidebar').classList.remove('open');
        }
    });

    // ── Scroll sidebar to item ──
    function scrollSidebarTo(type, id) {
        var selector = type === 'ficha'
            ? '.mapa-sidebar__item[data-ficha-id="' + id + '"]'
            : '.mapa-sidebar__item[data-evento-id="' + id + '"]';
        var item = document.querySelector(selector);
        if (!item) return;
        document.querySelectorAll('.mapa-sidebar__item').forEach(function(i){ i.classList.remove('active'); });
        item.classList.add('active');
        item.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
    }

    // ── Sidebar toggle (mobile) ──
    var sidebar = document.getElementById('mapa-sidebar');
    document.getElementById('sidebar-toggle').addEventListener('click', function(){
        sidebar.classList.remove('open');
    });
    document.getElementById('sidebar-fab').addEventListener('click', function(){
        sidebar.classList.toggle('open');
    });

    // ── Geolocation ──
    document.getElementById('geoloc-btn').addEventListener('click', function(){
        if (!navigator.geolocation) {
            alert('Tu navegador no soporta geolocalizacion.');
            return;
        }
        var btn = this;
        btn.classList.add('loading');

        navigator.geolocation.getCurrentPosition(
            function(pos){
                btn.classList.remove('loading');
                var lat = pos.coords.latitude, lng = pos.coords.longitude;

                if (userMarker) {
                    userMarker.setLatLng([lat, lng]);
                } else {
                    userMarker = L.marker([lat, lng], {
                        icon: L.divIcon({
                            className: 'mapa-user-marker',
                            html: '<div class="mapa-user-marker__dot"></div>',
                            iconSize: [20, 20],
                            iconAnchor: [10, 10]
                        }),
                        zIndexOffset: 1000
                    }).addTo(map).bindPopup('<strong>Tu ubicacion</strong>');
                }

                map.flyTo([lat, lng], 14, { duration: 1 });
            },
            function(err){
                btn.classList.remove('loading');
                alert('No se pudo obtener tu ubicacion. Verifica los permisos de tu navegador.');
            },
            { enableHighAccuracy: true, timeout: 10000 }
        );
    });

    // ── Helpers ──
    function escHtml(s) {
        var d = document.createElement('div');
        d.textContent = s;
        return d.innerHTML;
    }
})();
</script>
