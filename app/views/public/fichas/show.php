<?php
/**
 * Ficha detalle — visitapurranque.cl
 * Variables: $ficha, $rating, $resenas, $relacionadas
 */
$promedio   = round($rating['promedio'] ?? 0, 1);
$totalRes   = (int)($rating['total'] ?? 0);
$currentUrl = url('/atractivo/' . e($ficha['slug']));
?>

<!-- Schema.org JSON-LD -->
<script type="application/ld+json">
{
    "@context": "https://schema.org",
    "@type": "TouristAttraction",
    "name": <?= json_encode($ficha['nombre'], JSON_UNESCAPED_UNICODE) ?>,
    "description": <?= json_encode($ficha['descripcion_corta'] ?? $ficha['nombre'], JSON_UNESCAPED_UNICODE) ?>,
    "address": {
        "@type": "PostalAddress",
        "addressLocality": "Purranque",
        "addressRegion": "Los Lagos",
        "addressCountry": "CL"
        <?php if (!empty($ficha['direccion'])): ?>
        ,"streetAddress": <?= json_encode($ficha['direccion'], JSON_UNESCAPED_UNICODE) ?>
        <?php endif; ?>
    }
    <?php if ($ficha['latitud'] && $ficha['longitud']): ?>
    ,"geo": {
        "@type": "GeoCoordinates",
        "latitude": <?= $ficha['latitud'] ?>,
        "longitude": <?= $ficha['longitud'] ?>
    }
    <?php endif; ?>
    <?php if ($promedio > 0): ?>
    ,"aggregateRating": {
        "@type": "AggregateRating",
        "ratingValue": "<?= $promedio ?>",
        "reviewCount": "<?= $totalRes ?>"
    }
    <?php endif; ?>
}
</script>

<section class="hero-section">
    <div class="container">
        <nav class="breadcrumb breadcrumb--light" aria-label="Migas de pan">
            <a href="<?= url('/') ?>">Inicio</a> <span class="breadcrumb-sep">/</span>
            <?php if (!empty($ficha['categoria_slug'])): ?>
                <a href="<?= url('/categoria/' . e($ficha['categoria_slug'])) ?>"><?= e($ficha['categoria_nombre'] ?? 'Categoria') ?></a> <span class="breadcrumb-sep">/</span>
            <?php endif; ?>
            <span><?= e($ficha['nombre']) ?></span>
        </nav>
        <h1><?= e($ficha['nombre']) ?></h1>
        <?php if (!empty($ficha['descripcion_corta'])): ?>
            <p class="hero-subtitle"><?= e($ficha['descripcion_corta']) ?></p>
        <?php endif; ?>
    </div>
</section>

<section class="page-section">
    <div class="container">

        <!-- Badges -->
        <div class="ficha-badges">
            <?php if (!empty($ficha['categoria_nombre'])): ?>
                <span class="badge badge-green"><?= e(($ficha['categoria_emoji'] ?? '') . ' ' . $ficha['categoria_nombre']) ?></span>
            <?php endif; ?>
            <?php if ($ficha['verificado']): ?>
                <span class="badge badge-blue">&#10003; Verificado</span>
            <?php endif; ?>
            <?php if ($ficha['destacado']): ?>
                <span class="badge badge-orange">&#9733; Destacado</span>
            <?php endif; ?>
            <?php if ($promedio > 0): ?>
                <span class="badge badge-gray">&#9733; <?= $promedio ?> (<?= $totalRes ?> <?= $totalRes == 1 ? 'resena' : 'resenas' ?>)</span>
            <?php endif; ?>
        </div>

        <!-- Descripcion -->
        <?php if (!empty($ficha['descripcion'])): ?>
            <div class="ficha-descripcion"><?= nl2br(e($ficha['descripcion'])) ?></div>
        <?php endif; ?>

        <!-- Info grid: Mapa + Contacto -->
        <div class="ficha-info-grid">
            <!-- Mapa -->
            <?php if ($ficha['latitud'] && $ficha['longitud']): ?>
            <div>
                <h2>Ubicacion</h2>
                <?php if (!empty($ficha['direccion'])): ?>
                    <p class="text-sm text-muted mb-2">&#128205; <?= e($ficha['direccion']) ?></p>
                <?php endif; ?>
                <div class="ficha-mapa" id="ficha-map"></div>
                <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" crossorigin="">
                <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" crossorigin=""></script>
                <script>
                (function(){
                    var lat = <?= $ficha['latitud'] ?>, lng = <?= $ficha['longitud'] ?>;
                    var map = L.map('ficha-map').setView([lat, lng], 14);
                    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                        attribution: '&copy; OpenStreetMap',
                        maxZoom: 18
                    }).addTo(map);
                    L.marker([lat, lng]).addTo(map).bindPopup(<?= json_encode(e($ficha['nombre']), JSON_UNESCAPED_UNICODE) ?>).openPopup();
                })();
                </script>
            </div>
            <?php endif; ?>

            <!-- Contacto -->
            <div>
                <h2>Contacto</h2>
                <div class="ficha-contacto">
                    <?php if (!empty($ficha['telefono'])): ?>
                        <a href="tel:<?= e($ficha['telefono']) ?>" class="ficha-contacto-link ficha-contacto-link--tel">
                            &#128222; <?= e($ficha['telefono']) ?>
                        </a>
                    <?php endif; ?>

                    <?php if (!empty($ficha['whatsapp'])): ?>
                        <a href="https://wa.me/<?= e($ficha['whatsapp']) ?>" target="_blank" rel="noopener" class="ficha-contacto-link ficha-contacto-link--wa">
                            &#128172; WhatsApp
                        </a>
                    <?php endif; ?>

                    <?php if (!empty($ficha['email'])): ?>
                        <a href="mailto:<?= e($ficha['email']) ?>" class="ficha-contacto-link ficha-contacto-link--tel">
                            &#9993; <?= e($ficha['email']) ?>
                        </a>
                    <?php endif; ?>

                    <?php if (!empty($ficha['sitio_web'])): ?>
                        <a href="<?= e($ficha['sitio_web']) ?>" target="_blank" rel="noopener" class="ficha-contacto-link ficha-contacto-link--tel">
                            &#127760; Sitio web
                        </a>
                    <?php endif; ?>

                    <?php if ($ficha['latitud'] && $ficha['longitud']): ?>
                        <a href="https://www.google.com/maps/dir/?api=1&destination=<?= $ficha['latitud'] ?>,<?= $ficha['longitud'] ?>" target="_blank" rel="noopener" class="ficha-contacto-link ficha-contacto-link--map">
                            &#128663; Como llegar
                        </a>
                    <?php endif; ?>
                </div>

                <?php if (!empty($ficha['info_practica'])): ?>
                    <div class="mt-3">
                        <h3>Info practica</h3>
                        <p class="text-sm"><?= nl2br(e($ficha['info_practica'])) ?></p>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Compartir -->
        <div class="share-buttons">
            <a href="https://wa.me/?text=<?= urlencode($ficha['nombre'] . ' — ' . $currentUrl) ?>" target="_blank" rel="noopener" class="share-btn share-btn--wa">
                &#128172; WhatsApp
            </a>
            <a href="https://www.facebook.com/sharer/sharer.php?u=<?= urlencode($currentUrl) ?>" target="_blank" rel="noopener" class="share-btn share-btn--fb">
                f Facebook
            </a>
            <button type="button" class="share-btn share-btn--copy" onclick="navigator.clipboard.writeText('<?= $currentUrl ?>').then(function(){this.textContent='Copiado!'}.bind(this))">
                &#128203; Copiar enlace
            </button>
        </div>

        <!-- Resenas -->
        <div class="resenas-section" id="resenas">
            <h2>Resenas</h2>

            <?php if ($promedio > 0): ?>
            <div class="resena-summary">
                <span class="resena-avg"><?= $promedio ?></span>
                <div>
                    <div class="rating-stars">
                        <?php for ($i = 1; $i <= 5; $i++): ?>
                            <span class="star <?= $i <= round($promedio) ? 'star--full' : 'star--empty' ?>">&#9733;</span>
                        <?php endfor; ?>
                    </div>
                    <span class="text-sm text-muted"><?= $totalRes ?> <?= $totalRes == 1 ? 'resena' : 'resenas' ?></span>
                </div>
            </div>
            <?php endif; ?>

            <?php if (!empty($resenas)): ?>
            <div class="resena-list">
                <?php foreach ($resenas as $r): ?>
                <div class="resena-item">
                    <div class="rating-stars">
                        <?php for ($i = 1; $i <= 5; $i++): ?>
                            <span class="star <?= $i <= $r['rating'] ? 'star--full' : 'star--empty' ?>">&#9733;</span>
                        <?php endfor; ?>
                    </div>
                    <p class="resena-autor"><?= e($r['nombre']) ?></p>
                    <p class="resena-fecha"><?= formatDate($r['created_at'], 'd M Y') ?></p>
                    <p class="resena-comentario"><?= e($r['comentario']) ?></p>
                    <?php if (!empty($r['respuesta_admin'])): ?>
                        <div class="resena-respuesta mt-2" style="padding: 10px 14px; background: var(--green-50); border-radius: var(--radius); font-size: .88rem;">
                            <strong>Respuesta:</strong> <?= e($r['respuesta_admin']) ?>
                        </div>
                    <?php endif; ?>
                </div>
                <?php endforeach; ?>
            </div>
            <?php elseif ($totalRes === 0): ?>
            <p class="text-muted mb-3">Aun no hay resenas. Se el primero en opinar.</p>
            <?php endif; ?>

            <!-- Formulario nueva resena -->
            <div class="resena-form">
                <h3>Deja tu resena</h3>
                <form method="post" action="<?= url('/atractivo/' . e($ficha['slug']) . '/resena') ?>">
                    <?= csrf_field() ?>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="r-nombre">Nombre *</label>
                            <input type="text" id="r-nombre" name="nombre" required maxlength="100">
                        </div>
                        <div class="form-group">
                            <label for="r-email">Email (opcional)</label>
                            <input type="email" id="r-email" name="email" maxlength="200">
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label>Calificacion *</label>
                            <input type="hidden" name="rating" id="r-rating" value="5" required>
                            <div class="star-rating-input" id="starRating">
                                <?php for ($i = 1; $i <= 5; $i++): ?>
                                    <button type="button" class="star-btn <?= $i <= 5 ? 'active' : '' ?>" data-value="<?= $i ?>">&#9733;</button>
                                <?php endfor; ?>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="r-tipo">Tipo de experiencia</label>
                            <select id="r-tipo" name="tipo_experiencia">
                                <option value="otro">Otro</option>
                                <option value="trekking">Trekking</option>
                                <option value="visita_cultural">Visita cultural</option>
                                <option value="gastronomia">Gastronomia</option>
                                <option value="playa">Playa</option>
                                <option value="camping">Camping</option>
                                <option value="tour_guiado">Tour guiado</option>
                                <option value="alojamiento">Alojamiento</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group mb-2">
                        <label for="r-comentario">Comentario *</label>
                        <textarea id="r-comentario" name="comentario" required maxlength="1000" rows="4" placeholder="Cuenta tu experiencia..."></textarea>
                    </div>

                    <button type="submit" class="btn btn-primary">Enviar resena</button>
                </form>
            </div>
        </div>

        <!-- Star rating script -->
        <script>
        (function(){
            var container = document.getElementById('starRating');
            var input = document.getElementById('r-rating');
            if(!container || !input) return;
            var stars = container.querySelectorAll('.star-btn');
            function setRating(val){
                input.value = val;
                stars.forEach(function(s,i){
                    s.classList.toggle('active', i < val);
                });
            }
            stars.forEach(function(s){
                s.addEventListener('click', function(){ setRating(parseInt(this.dataset.value)); });
            });
        })();
        </script>

        <!-- Relacionadas -->
        <?php if (!empty($relacionadas)): ?>
        <div class="mt-4">
            <h2>Lugares similares</h2>
            <div class="relacionadas-grid mt-2">
                <?php foreach ($relacionadas as $rel): ?>
                <a href="<?= url('/atractivo/' . e($rel['slug'])) ?>" class="card">
                    <div class="card-img-placeholder">
                        <span><?= e($ficha['categoria_emoji'] ?? '📍') ?></span>
                    </div>
                    <div class="card-body">
                        <h3 class="card-title" style="font-size:.95rem"><?= e($rel['nombre']) ?></h3>
                        <p class="card-text" style="font-size:.82rem"><?= e(mb_strimwidth($rel['descripcion_corta'] ?? '', 0, 60, '...')) ?></p>
                    </div>
                </a>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>

    </div>
</section>
