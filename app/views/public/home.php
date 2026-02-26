<?php
/**
 * Home — visitapurranque.cl
 * Variables: $categorias, $destacados, $proximoEvento, $eventos, $posts
 */
?>

<!-- ═══════════════ SCHEMA.ORG ═══════════════ -->
<script type="application/ld+json">
{
    "@context": "https://schema.org",
    "@graph": [
        {
            "@type": "WebSite",
            "name": <?= json_encode(SITE_NAME, JSON_UNESCAPED_UNICODE) ?>,
            "url": <?= json_encode(SITE_URL, JSON_UNESCAPED_UNICODE) ?>,
            "description": <?= json_encode(SITE_DESCRIPTION, JSON_UNESCAPED_UNICODE) ?>,
            "inLanguage": "es-CL",
            "potentialAction": {
                "@type": "SearchAction",
                "target": {
                    "@type": "EntryPoint",
                    "urlTemplate": <?= json_encode(url('/buscar') . '?q={search_term_string}', JSON_UNESCAPED_UNICODE) ?>
                },
                "query-input": "required name=search_term_string"
            }
        },
        {
            "@type": "TouristDestination",
            "name": "Purranque",
            "description": <?= json_encode(texto('hero_descripcion', SITE_DESCRIPTION), JSON_UNESCAPED_UNICODE) ?>,
            "geo": {
                "@type": "GeoCoordinates",
                "latitude": <?= CITY_LAT ?>,
                "longitude": <?= CITY_LNG ?>
            },
            "containedInPlace": {
                "@type": "AdministrativeArea",
                "name": "Región de Los Lagos, Chile"
            },
            "touristType": ["naturaleza", "cultura", "gastronomía", "playas"]
        }
    ]
}
</script>

<!-- ═══════════════ 1. HERO SECTION ═══════════════ -->
<section class="hero-home">
    <div class="container hero-home-inner">
        <h1><?= e(texto('hero_titulo', SITE_NAME)) ?></h1>
        <p class="hero-home-badge"><?= e(texto('hero_subtitulo', 'Guía del Visitante')) ?></p>
        <p class="hero-home-desc"><?= e(texto('hero_descripcion', SITE_DESCRIPTION)) ?></p>

        <!-- Buscador -->
        <form class="hero-search" action="<?= url('/buscar') ?>" method="GET" role="search">
            <label for="hero-q" class="sr-only">Buscar</label>
            <span class="hero-search-icon" aria-hidden="true">&#128269;</span>
            <input type="search"
                   id="hero-q"
                   name="q"
                   class="hero-search-input"
                   placeholder="<?= e(texto('buscar_placeholder', 'Busca playas, senderos, restaurantes...')) ?>"
                   autocomplete="off">
            <button type="submit" class="hero-search-btn">Buscar</button>
        </form>

        <a href="<?= url('/categorias') ?>" class="btn btn-lg hero-cta-btn">
            <?= e(texto('hero_cta', 'Explorar atractivos')) ?> &rarr;
        </a>

        <?php if ($proximoEvento): ?>
        <!-- Countdown al próximo evento -->
        <div class="hero-countdown" data-fecha="<?= e($proximoEvento['fecha_inicio']) ?>">
            <p class="hero-countdown-label">
                Próximo evento: <strong><?= e($proximoEvento['titulo']) ?></strong>
                <?php if ($proximoEvento['lugar']): ?>
                    &mdash; <?= e($proximoEvento['lugar']) ?>
                <?php endif; ?>
            </p>
            <div class="countdown-timer">
                <div class="countdown-unit">
                    <span class="countdown-number" id="cd-days">--</span>
                    <span class="countdown-text">días</span>
                </div>
                <div class="countdown-unit">
                    <span class="countdown-number" id="cd-hours">--</span>
                    <span class="countdown-text">hrs</span>
                </div>
                <div class="countdown-unit">
                    <span class="countdown-number" id="cd-mins">--</span>
                    <span class="countdown-text">min</span>
                </div>
                <div class="countdown-unit">
                    <span class="countdown-number" id="cd-secs">--</span>
                    <span class="countdown-text">seg</span>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>
</section>

<!-- ═══════════════ 2. GRID DE CATEGORÍAS ═══════════════ -->
<section class="home-section">
    <div class="container">
        <h2 class="section-title">Explora Purranque</h2>

        <div class="categorias-grid">
            <?php foreach ($categorias as $cat): ?>
            <?php $tieneFichas = (int)$cat['total_fichas'] > 0; ?>
            <a href="<?= $tieneFichas ? url('/categoria/' . e($cat['slug'])) : '#' ?>"
               class="categoria-card<?= $tieneFichas ? '' : ' categoria-card--empty' ?>"
               style="--cat-color: <?= e($cat['color']) ?>"
               <?= $tieneFichas ? '' : 'aria-disabled="true"' ?>>
                <span class="categoria-emoji"><?= $cat['emoji'] ?></span>
                <h3 class="categoria-nombre"><?= e($cat['nombre']) ?></h3>
                <?php
                    $desc = $cat['descripcion'] ?? '';
                    if (mb_strlen($desc) > 80) {
                        $desc = mb_substr($desc, 0, 80) . '...';
                    }
                ?>
                <?php if ($desc): ?>
                    <p class="categoria-desc"><?= e($desc) ?></p>
                <?php endif; ?>
                <?php if ($tieneFichas): ?>
                    <span class="categoria-count"><?= (int)$cat['total_fichas'] ?> lugar<?= (int)$cat['total_fichas'] !== 1 ? 'es' : '' ?></span>
                <?php else: ?>
                    <span class="categoria-count categoria-count--soon">Próximamente</span>
                <?php endif; ?>
            </a>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- ═══════════════ 3. ATRACTIVOS DESTACADOS ═══════════════ -->
<section class="home-section bg-alt">
    <div class="container">
        <h2 class="section-title">Imperdibles de Purranque</h2>

        <?php if (!empty($destacados)): ?>
        <div class="destacados-grid">
            <?php foreach ($destacados as $ficha): ?>
            <a href="<?= url('/atractivo/' . e($ficha['slug'])) ?>" class="card destacado-card">
                <?php if ($ficha['imagen_portada']): ?>
                    <img src="<?= e($ficha['imagen_portada']) ?>"
                         alt="<?= e($ficha['nombre']) ?>"
                         class="card-img"
                         loading="lazy">
                <?php else: ?>
                    <div class="card-img card-img-placeholder">
                        <span><?= $ficha['categoria_emoji'] ?? '📍' ?></span>
                    </div>
                <?php endif; ?>
                <div class="card-body">
                    <span class="badge badge-green" style="background: <?= e($ficha['categoria_color'] ?? '#f0fdf4') ?>20; color: <?= e($ficha['categoria_color'] ?? 'var(--green)') ?>">
                        <?= $ficha['categoria_emoji'] ?? '' ?> <?= e($ficha['categoria_nombre'] ?? '') ?>
                    </span>
                    <h3 class="card-title"><?= e($ficha['nombre']) ?></h3>
                    <?php if ((float)$ficha['promedio_rating'] > 0): ?>
                    <div class="rating-stars">
                        <?php
                        $rating = (float)$ficha['promedio_rating'];
                        for ($i = 1; $i <= 5; $i++):
                            if ($i <= floor($rating)):
                                echo '<span class="star star--full">&#9733;</span>';
                            elseif ($i - $rating < 1):
                                echo '<span class="star star--half">&#9733;</span>';
                            else:
                                echo '<span class="star star--empty">&#9734;</span>';
                            endif;
                        endfor;
                        ?>
                        <span class="rating-value"><?= number_format($rating, 1) ?></span>
                    </div>
                    <?php endif; ?>
                    <?php if ($ficha['descripcion_corta']): ?>
                        <p class="card-text"><?= e($ficha['descripcion_corta']) ?></p>
                    <?php endif; ?>
                </div>
            </a>
            <?php endforeach; ?>
        </div>
        <?php else: ?>
        <div class="empty-state">
            <span class="empty-state-icon">&#127967;</span>
            <p>Pronto agregaremos los mejores atractivos de Purranque</p>
        </div>
        <?php endif; ?>
    </div>
</section>

<!-- ═══════════════ 4. PRÓXIMOS EVENTOS ═══════════════ -->
<section class="home-section">
    <div class="container">
        <h2 class="section-title">Próximos Eventos</h2>

        <?php if (!empty($eventos)): ?>
        <div class="eventos-grid">
            <?php foreach ($eventos as $ev): ?>
            <div class="card evento-card">
                <?php if ($ev['imagen']): ?>
                    <img src="<?= e($ev['imagen']) ?>"
                         alt="<?= e($ev['titulo']) ?>"
                         class="card-img"
                         loading="lazy">
                <?php else: ?>
                    <div class="card-img card-img-placeholder card-img-placeholder--evento">
                        <span>&#127881;</span>
                    </div>
                <?php endif; ?>
                <div class="card-body">
                    <div class="evento-fecha-badge">
                        <span class="evento-dia"><?= date('d', strtotime($ev['fecha_inicio'])) ?></span>
                        <span class="evento-mes"><?= strftime_es(strtotime($ev['fecha_inicio'])) ?></span>
                    </div>
                    <h3 class="card-title"><?= e($ev['titulo']) ?></h3>
                    <?php if ($ev['lugar']): ?>
                        <p class="evento-lugar">&#128205; <?= e($ev['lugar']) ?></p>
                    <?php endif; ?>
                    <?php if ($ev['descripcion_corta']): ?>
                        <p class="card-text"><?= e($ev['descripcion_corta']) ?></p>
                    <?php endif; ?>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <div class="section-footer">
            <a href="<?= url('/eventos') ?>" class="btn btn-secondary">Ver todos los eventos &rarr;</a>
        </div>
        <?php else: ?>
        <div class="empty-state">
            <span class="empty-state-icon">&#127879;</span>
            <p>Pronto publicaremos eventos en Purranque</p>
        </div>
        <?php endif; ?>
    </div>
</section>

<!-- ═══════════════ 5. BLOG RECIENTE ═══════════════ -->
<section class="home-section bg-alt">
    <div class="container">
        <h2 class="section-title">Últimas Noticias</h2>

        <?php if (!empty($posts)): ?>
        <div class="blog-grid">
            <?php foreach ($posts as $post): ?>
            <a href="<?= url('/blog/' . e($post['slug'])) ?>" class="card blog-card">
                <?php if ($post['imagen_portada']): ?>
                    <img src="<?= e($post['imagen_portada']) ?>"
                         alt="<?= e($post['titulo']) ?>"
                         class="card-img"
                         loading="lazy">
                <?php else: ?>
                    <div class="card-img card-img-placeholder card-img-placeholder--blog">
                        <span>&#128240;</span>
                    </div>
                <?php endif; ?>
                <div class="card-body">
                    <?php if (!empty($post['categoria_nombre'])): ?>
                        <span class="badge badge-blue">
                            <?= $post['categoria_emoji'] ?? '' ?> <?= e($post['categoria_nombre']) ?>
                        </span>
                    <?php endif; ?>
                    <h3 class="card-title"><?= e($post['titulo']) ?></h3>
                    <?php if ($post['extracto']): ?>
                        <p class="card-text"><?= e($post['extracto']) ?></p>
                    <?php endif; ?>
                    <div class="blog-meta">
                        <span><?= formatDate($post['publicado_at']) ?></span>
                        <?php if ($post['tiempo_lectura']): ?>
                            <span>&middot; <?= (int)$post['tiempo_lectura'] ?> min lectura</span>
                        <?php endif; ?>
                    </div>
                </div>
            </a>
            <?php endforeach; ?>
        </div>
        <div class="section-footer">
            <a href="<?= url('/blog') ?>" class="btn btn-secondary">Ver todas las noticias &rarr;</a>
        </div>
        <?php else: ?>
        <div class="empty-state">
            <span class="empty-state-icon">&#128221;</span>
            <p>Pronto publicaremos noticias sobre Purranque</p>
        </div>
        <?php endif; ?>
    </div>
</section>

<!-- ═══════════════ 6. CTA FINAL ═══════════════ -->
<section class="cta-section">
    <div class="container cta-inner">
        <h2>¿Tienes un servicio turístico en Purranque?</h2>
        <p>Registra tu negocio en nuestra guía y aumenta tu visibilidad</p>
        <a href="<?= url('/contacto') ?>" class="btn btn-lg btn-accent">Contáctanos</a>
    </div>
</section>

<!-- ═══════════════ COUNTDOWN JS ═══════════════ -->
<?php if ($proximoEvento): ?>
<script>
(function() {
    var el = document.querySelector('.hero-countdown');
    if (!el) return;

    var target = new Date(el.dataset.fecha).getTime();
    var days = document.getElementById('cd-days');
    var hours = document.getElementById('cd-hours');
    var mins = document.getElementById('cd-mins');
    var secs = document.getElementById('cd-secs');

    function pad(n) { return n < 10 ? '0' + n : n; }

    function tick() {
        var diff = target - Date.now();
        if (diff <= 0) {
            el.style.display = 'none';
            return;
        }
        var d = Math.floor(diff / 86400000);
        var h = Math.floor((diff % 86400000) / 3600000);
        var m = Math.floor((diff % 3600000) / 60000);
        var s = Math.floor((diff % 60000) / 1000);
        days.textContent = d;
        hours.textContent = pad(h);
        mins.textContent = pad(m);
        secs.textContent = pad(s);
    }

    tick();
    setInterval(tick, 1000);
})();
</script>
<?php endif; ?>
