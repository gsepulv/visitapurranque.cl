<?php
/**
 * Mapa interactivo — visitapurranque.cl
 * Variables: $fichas (con latitud, longitud, nombre, slug, categoria_emoji, etc.)
 */
$fichasJson = json_encode($fichas, JSON_UNESCAPED_UNICODE);
?>

<section class="hero-section">
    <div class="container">
        <nav class="breadcrumb breadcrumb--light" aria-label="Migas de pan">
            <a href="<?= url('/') ?>">Inicio</a> <span class="breadcrumb-sep">/</span>
            <span>Mapa</span>
        </nav>
        <h1>Mapa interactivo</h1>
        <p class="hero-subtitle">Explora los atractivos turísticos de Purranque. Haz clic en un marcador para ver más detalles.</p>
    </div>
</section>

<section class="mapa-section">
    <div class="map-wrap">
        <div class="mapa-fullwidth" id="mapa-principal"></div>
        <div class="map-interaction-overlay" id="mapa-overlay">
            <span>Haz clic en el mapa para interactuar</span>
        </div>
    </div>
</section>

<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" crossorigin="">
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" crossorigin=""></script>
<script>
(function(){
    var fichas = <?= $fichasJson ?>;
    var wrapper = document.getElementById('mapa-principal').parentElement;
    var overlay = document.getElementById('mapa-overlay');
    var map = L.map('mapa-principal', { scrollWheelZoom: false }).setView([-40.91, -73.13], 10);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>',
        maxZoom: 18
    }).addTo(map);

    // Clic en overlay activa interacción
    overlay.addEventListener('click', function() {
        map.scrollWheelZoom.enable();
        overlay.classList.add('map-interaction-overlay--hidden');
    });
    // Salir del wrapper desactiva
    wrapper.addEventListener('mouseleave', function() {
        map.scrollWheelZoom.disable();
        overlay.classList.remove('map-interaction-overlay--hidden');
    });
    if (L.Browser.mobile) {
        map.dragging.disable();
        overlay.addEventListener('touchstart', function() {
            map.dragging.enable();
            map.scrollWheelZoom.enable();
            overlay.classList.add('map-interaction-overlay--hidden');
        });
    }

    if (fichas.length === 0) return;

    var bounds = [];
    fichas.forEach(function(f){
        var lat = parseFloat(f.latitud), lng = parseFloat(f.longitud);
        if (isNaN(lat) || isNaN(lng)) return;
        bounds.push([lat, lng]);
        var emoji = f.categoria_emoji || '📍';
        var popup = '<div style="min-width:180px">'
            + '<strong style="font-size:1rem">' + emoji + ' ' + f.nombre + '</strong>'
            + (f.descripcion_corta ? '<p style="font-size:.85rem;margin:6px 0;color:#6b7280">' + f.descripcion_corta + '</p>' : '')
            + (f.categoria_nombre ? '<span style="font-size:.75rem;background:#f0fdf4;color:#1a5632;padding:2px 8px;border-radius:10px">' + f.categoria_nombre + '</span> ' : '')
            + '<br><a href="<?= url('/atractivo/') ?>' + f.slug + '" style="font-size:.85rem;margin-top:8px;display:inline-block">Ver detalle &rarr;</a>'
            + '</div>';
        L.marker([lat, lng]).addTo(map).bindPopup(popup);
    });

    if (bounds.length > 1) {
        map.fitBounds(bounds, { padding: [40, 40] });
    }
})();
</script>
