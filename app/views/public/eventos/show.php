<?php
/**
 * Detalle de evento — visitapurranque.cl
 * Variables: $evento
 */
$inicio    = strtotime($evento['fecha_inicio']);
$fin       = $evento['fecha_fin'] ? strtotime($evento['fecha_fin']) : null;
$isPasado  = ($fin ?? $inicio) < time();
$currentUrl = url('/evento/' . e($evento['slug']));
?>

<!-- Schema.org JSON-LD -->
<script type="application/ld+json">
{
    "@context": "https://schema.org",
    "@type": "Event",
    "name": <?= json_encode($evento['titulo'], JSON_UNESCAPED_UNICODE) ?>,
    "startDate": "<?= date('c', $inicio) ?>"
    <?php if ($fin): ?>
    ,"endDate": "<?= date('c', $fin) ?>"
    <?php endif; ?>
    <?php if (!empty($evento['descripcion_corta'])): ?>
    ,"description": <?= json_encode($evento['descripcion_corta'], JSON_UNESCAPED_UNICODE) ?>
    <?php endif; ?>
    <?php if (!empty($evento['lugar'])): ?>
    ,"location": {
        "@type": "Place",
        "name": <?= json_encode($evento['lugar'], JSON_UNESCAPED_UNICODE) ?>
        <?php if ($evento['latitud'] && $evento['longitud']): ?>
        ,"geo": {
            "@type": "GeoCoordinates",
            "latitude": <?= $evento['latitud'] ?>,
            "longitude": <?= $evento['longitud'] ?>
        }
        <?php endif; ?>
        <?php if (!empty($evento['direccion'])): ?>
        ,"address": <?= json_encode($evento['direccion'], JSON_UNESCAPED_UNICODE) ?>
        <?php endif; ?>
    }
    <?php endif; ?>
    <?php if (!empty($evento['organizador'])): ?>
    ,"organizer": {
        "@type": "Organization",
        "name": <?= json_encode($evento['organizador'], JSON_UNESCAPED_UNICODE) ?>
    }
    <?php endif; ?>
    ,"eventStatus": "<?= $isPasado ? 'https://schema.org/EventScheduled' : 'https://schema.org/EventScheduled' ?>"
}
</script>

<section class="hero-section">
    <div class="container">
        <nav class="breadcrumb breadcrumb--light" aria-label="Migas de pan">
            <a href="<?= url('/') ?>">Inicio</a> <span class="breadcrumb-sep">/</span>
            <a href="<?= url('/eventos') ?>">Eventos</a> <span class="breadcrumb-sep">/</span>
            <span><?= e($evento['titulo']) ?></span>
        </nav>
        <h1><?= e($evento['titulo']) ?></h1>
        <?php if (!empty($evento['descripcion_corta'])): ?>
            <p class="hero-subtitle"><?= e($evento['descripcion_corta']) ?></p>
        <?php endif; ?>
    </div>
</section>

<section class="page-section">
    <div class="container">

        <!-- Badges -->
        <div class="ficha-badges">
            <?php if ($isPasado): ?>
                <span class="badge badge-gray">Finalizado</span>
            <?php else: ?>
                <span class="badge badge-green">Proximo evento</span>
            <?php endif; ?>
            <?php if ($evento['destacado']): ?>
                <span class="badge badge-orange">&#9733; Destacado</span>
            <?php endif; ?>
            <?php if ($evento['recurrente']): ?>
                <span class="badge badge-blue">&#8634; Recurrente</span>
            <?php endif; ?>
        </div>

        <!-- Meta info -->
        <div class="evento-detalle-meta">
            <div class="evento-meta-item">
                <span>&#128197;</span>
                <span>
                    <?= formatDate($evento['fecha_inicio'], 'd M Y, H:i') ?> hrs
                    <?php if ($fin && date('Y-m-d', $inicio) !== date('Y-m-d', $fin)): ?>
                        — <?= formatDate($evento['fecha_fin'], 'd M Y, H:i') ?> hrs
                    <?php elseif ($fin): ?>
                        — <?= formatDate($evento['fecha_fin'], 'H:i') ?> hrs
                    <?php endif; ?>
                </span>
            </div>
            <?php if (!empty($evento['lugar'])): ?>
            <div class="evento-meta-item">
                <span>&#128205;</span>
                <span><?= e($evento['lugar']) ?></span>
            </div>
            <?php endif; ?>
            <?php if (!empty($evento['organizador'])): ?>
            <div class="evento-meta-item">
                <span>&#128100;</span>
                <span><?= e($evento['organizador']) ?></span>
            </div>
            <?php endif; ?>
            <?php if (!empty($evento['precio'])): ?>
            <div class="evento-meta-item">
                <span>&#128176;</span>
                <span><?= e($evento['precio']) ?></span>
            </div>
            <?php endif; ?>
        </div>

        <!-- Descripcion -->
        <?php if (!empty($evento['descripcion'])): ?>
            <div class="evento-descripcion"><?= nl2br(e($evento['descripcion'])) ?></div>
        <?php endif; ?>

        <!-- Mapa + Contacto -->
        <div class="ficha-info-grid">
            <?php if ($evento['latitud'] && $evento['longitud']): ?>
            <div>
                <h2>Ubicacion</h2>
                <?php if (!empty($evento['direccion'])): ?>
                    <p class="text-sm text-muted mb-2">&#128205; <?= e($evento['direccion']) ?></p>
                <?php endif; ?>
                <div class="ficha-mapa" id="evento-map"></div>
                <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" crossorigin="">
                <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" crossorigin=""></script>
                <script>
                (function(){
                    var lat = <?= $evento['latitud'] ?>, lng = <?= $evento['longitud'] ?>;
                    var map = L.map('evento-map').setView([lat, lng], 14);
                    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                        attribution: '&copy; OpenStreetMap',
                        maxZoom: 18
                    }).addTo(map);
                    L.marker([lat, lng]).addTo(map).bindPopup(<?= json_encode(e($evento['titulo']), JSON_UNESCAPED_UNICODE) ?>).openPopup();
                })();
                </script>
            </div>
            <?php endif; ?>

            <div>
                <h2>Informacion</h2>
                <div class="ficha-contacto">
                    <?php if (!empty($evento['contacto'])): ?>
                        <p class="ficha-contacto-link">&#128222; <?= e($evento['contacto']) ?></p>
                    <?php endif; ?>

                    <?php if (!empty($evento['url_externa'])): ?>
                        <a href="<?= e($evento['url_externa']) ?>" target="_blank" rel="noopener" class="ficha-contacto-link ficha-contacto-link--tel">
                            &#127760; Mas informacion
                        </a>
                    <?php endif; ?>

                    <?php if ($evento['latitud'] && $evento['longitud']): ?>
                        <a href="https://www.google.com/maps/dir/?api=1&destination=<?= $evento['latitud'] ?>,<?= $evento['longitud'] ?>" target="_blank" rel="noopener" class="ficha-contacto-link ficha-contacto-link--map">
                            &#128663; Como llegar
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Compartir -->
        <div class="share-buttons">
            <a href="https://wa.me/?text=<?= urlencode($evento['titulo'] . ' — ' . $currentUrl) ?>" target="_blank" rel="noopener" class="share-btn share-btn--wa">
                &#128172; WhatsApp
            </a>
            <a href="https://www.facebook.com/sharer/sharer.php?u=<?= urlencode($currentUrl) ?>" target="_blank" rel="noopener" class="share-btn share-btn--fb">
                f Facebook
            </a>
            <button type="button" class="share-btn share-btn--copy" onclick="navigator.clipboard.writeText('<?= $currentUrl ?>').then(function(){this.textContent='Copiado!'}.bind(this))">
                &#128203; Copiar enlace
            </button>
        </div>

    </div>
</section>
