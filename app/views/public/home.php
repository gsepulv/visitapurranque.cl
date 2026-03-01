<?php
/**
 * Home — visitapurranque.cl
 * Variables: $categorias, $destacados, $proximoEvento, $eventos, $posts
 */
?>

<!-- Hero -->
<section class="hero-home">
    <div class="container hero-home-inner">
        <span class="hero-home-badge">Region de Los Lagos, Chile</span>
        <h1>Descubre Purranque</h1>
        <p class="hero-home-desc">Naturaleza, cultura y tradicion en el corazon de Los Lagos. Explora volcanes, bosques milenarios, caletas costeras y la mejor gastronomia del sur.</p>

        <form class="hero-search" action="<?= url('/buscar') ?>" method="get">
            <span class="hero-search-icon">&#128269;</span>
            <input type="text" name="q" class="hero-search-input" placeholder="Buscar atractivos, eventos, lugares..." autocomplete="off">
            <button type="submit" class="hero-search-btn">Buscar</button>
        </form>

        <a href="<?= url('/categorias') ?>" class="btn hero-cta-btn">Explorar categorias</a>

        <?php if ($proximoEvento): ?>
        <div class="hero-countdown">
            <p class="hero-countdown-label">Proximo evento: <strong><?= e($proximoEvento['titulo']) ?></strong></p>
            <div class="countdown-timer" data-target="<?= e($proximoEvento['fecha_inicio']) ?>">
                <div class="countdown-unit"><span class="countdown-number" id="cd-days">--</span><span class="countdown-text">dias</span></div>
                <div class="countdown-unit"><span class="countdown-number" id="cd-hours">--</span><span class="countdown-text">hrs</span></div>
                <div class="countdown-unit"><span class="countdown-number" id="cd-mins">--</span><span class="countdown-text">min</span></div>
                <div class="countdown-unit"><span class="countdown-number" id="cd-secs">--</span><span class="countdown-text">seg</span></div>
            </div>
        </div>
        <?php endif; ?>
    </div>
</section>

<!-- Categorias -->
<?php if (!empty($categorias)): ?>
<section class="home-section">
    <div class="container">
        <h2 class="section-title">Explora por categoria</h2>
        <div class="categorias-grid">
            <?php foreach ($categorias as $cat): ?>
            <a href="<?= url('/categoria/' . e($cat['slug'])) ?>"
               class="categoria-card <?= $cat['total_fichas'] == 0 ? 'categoria-card--empty' : '' ?>"
               style="--cat-color: <?= e($cat['color'] ?? '#3b82f6') ?>">
                <span class="categoria-emoji"><?= e($cat['emoji'] ?? '📍') ?></span>
                <span class="categoria-nombre"><?= e($cat['nombre']) ?></span>
                <?php if ($cat['total_fichas'] > 0): ?>
                    <span class="categoria-count" style="color: <?= e($cat['color'] ?? '#3b82f6') ?>">
                        <?= (int)$cat['total_fichas'] ?> <?= $cat['total_fichas'] == 1 ? 'lugar' : 'lugares' ?>
                    </span>
                <?php else: ?>
                    <span class="categoria-count categoria-count--soon">Proximamente</span>
                <?php endif; ?>
            </a>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- Destacados -->
<?php if (!empty($destacados)): ?>
<section class="home-section bg-alt">
    <div class="container">
        <h2 class="section-title">Lugares destacados</h2>
        <div class="destacados-grid">
            <?php foreach ($destacados as $ficha): ?>
            <a href="<?= url('/atractivo/' . e($ficha['slug'])) ?>" class="destacado-card card">
                <div class="card-img-placeholder">
                    <span><?= e($ficha['categoria_emoji'] ?? '📍') ?></span>
                </div>
                <div class="card-body">
                    <span class="badge badge-green"><?= e($ficha['categoria_nombre'] ?? 'Sin categoria') ?></span>
                    <?php if ($ficha['verificado']): ?>
                        <span class="badge badge-blue">Verificado</span>
                    <?php endif; ?>
                    <h3 class="card-title"><?= e($ficha['nombre']) ?></h3>
                    <p class="card-text"><?= e($ficha['descripcion_corta'] ?? '') ?></p>
                    <?php if ($ficha['promedio_rating'] > 0): ?>
                    <div class="rating-stars">
                        <?php for ($i = 1; $i <= 5; $i++): ?>
                            <span class="star <?= $i <= round($ficha['promedio_rating']) ? 'star--full' : 'star--empty' ?>">&#9733;</span>
                        <?php endfor; ?>
                        <span class="rating-value"><?= number_format($ficha['promedio_rating'], 1) ?></span>
                    </div>
                    <?php endif; ?>
                </div>
            </a>
            <?php endforeach; ?>
        </div>
        <div class="section-footer">
            <a href="<?= url('/categorias') ?>" class="btn btn-secondary">Ver todos los atractivos</a>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- Eventos -->
<?php if (!empty($eventos)): ?>
<section class="home-section">
    <div class="container">
        <h2 class="section-title">Proximos eventos</h2>
        <div class="eventos-grid">
            <?php foreach ($eventos as $ev):
                $ts = strtotime($ev['fecha_inicio']);
            ?>
            <div class="evento-card card">
                <div class="card-img-placeholder card-img-placeholder--evento">
                    <span>&#127879;</span>
                </div>
                <div class="card-body">
                    <div class="evento-fecha-badge">
                        <span class="evento-dia"><?= date('d', $ts) ?></span>
                        <span class="evento-mes"><?= strftime_es($ts) ?></span>
                    </div>
                    <h3 class="card-title"><?= e($ev['titulo']) ?></h3>
                    <?php if (!empty($ev['lugar'])): ?>
                        <p class="evento-lugar">&#128205; <?= e($ev['lugar']) ?></p>
                    <?php endif; ?>
                    <p class="card-text"><?= e(mb_strimwidth($ev['descripcion'] ?? '', 0, 120, '...')) ?></p>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- Blog -->
<?php if (!empty($posts)): ?>
<section class="home-section bg-alt">
    <div class="container">
        <h2 class="section-title">Desde el blog</h2>
        <div class="blog-grid">
            <?php foreach ($posts as $post): ?>
            <a href="<?= url('/blog/' . e($post['slug'])) ?>" class="blog-card card">
                <div class="card-img-placeholder card-img-placeholder--blog">
                    <span>&#128221;</span>
                </div>
                <div class="card-body">
                    <span class="badge badge-green"><?= e(ucfirst($post['tipo'] ?? 'articulo')) ?></span>
                    <h3 class="card-title"><?= e($post['titulo']) ?></h3>
                    <p class="card-text"><?= e($post['extracto'] ?? '') ?></p>
                    <p class="blog-meta">
                        <span><?= formatDate($post['publicado_at'] ?? $post['created_at']) ?></span>
                        <?php if (!empty($post['tiempo_lectura'])): ?>
                            <span> &middot; <?= (int)$post['tiempo_lectura'] ?> min de lectura</span>
                        <?php endif; ?>
                    </p>
                </div>
            </a>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- CTA -->
<section class="cta-section">
    <div class="container cta-inner">
        <h2>Tienes un emprendimiento turistico?</h2>
        <p>Publica tu ficha gratis en la guia turistica mas completa de Purranque.</p>
        <a href="<?= url('/contacto') ?>" class="btn btn-lg btn-accent">Contactanos</a>
    </div>
</section>

<!-- Countdown script -->
<?php if ($proximoEvento): ?>
<script>
(function(){
    var target = document.querySelector('.countdown-timer');
    if(!target) return;
    var end = new Date(target.dataset.target).getTime();
    function tick(){
        var now = Date.now(), d = end - now;
        if(d < 0){ document.getElementById('cd-days').textContent='0'; document.getElementById('cd-hours').textContent='0'; document.getElementById('cd-mins').textContent='0'; document.getElementById('cd-secs').textContent='0'; return; }
        document.getElementById('cd-days').textContent = Math.floor(d/86400000);
        document.getElementById('cd-hours').textContent = Math.floor((d%86400000)/3600000);
        document.getElementById('cd-mins').textContent = Math.floor((d%3600000)/60000);
        document.getElementById('cd-secs').textContent = Math.floor((d%60000)/1000);
    }
    tick(); setInterval(tick, 1000);
})();
</script>
<?php endif; ?>
