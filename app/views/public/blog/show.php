<?php
/**
 * Detalle de articulo — visitapurranque.cl
 * Variables: $post, $relacionados
 */
$currentUrl = url('/blog/' . e($post['slug']));
?>

<!-- Schema.org JSON-LD -->
<script type="application/ld+json">
{
    "@context": "https://schema.org",
    "@type": "Article",
    "headline": <?= json_encode($post['titulo'], JSON_UNESCAPED_UNICODE) ?>,
    "datePublished": "<?= $post['publicado_at'] ?? $post['created_at'] ?>"
    <?php if (!empty($post['extracto'])): ?>
    ,"description": <?= json_encode($post['extracto'], JSON_UNESCAPED_UNICODE) ?>
    <?php endif; ?>
    <?php if (!empty($post['autor_nombre'])): ?>
    ,"author": {
        "@type": "Person",
        "name": <?= json_encode($post['autor_nombre'], JSON_UNESCAPED_UNICODE) ?>
    }
    <?php endif; ?>
    <?php if (!empty($post['imagen_portada'])): ?>
    ,"image": "<?= asset('uploads/' . e($post['imagen_portada'])) ?>"
    <?php endif; ?>
    ,"publisher": {
        "@type": "Organization",
        "name": "<?= SITE_NAME ?>"
    }
}
</script>

<section class="hero-section">
    <div class="container">
        <nav class="breadcrumb breadcrumb--light" aria-label="Migas de pan">
            <a href="<?= url('/') ?>">Inicio</a> <span class="breadcrumb-sep">/</span>
            <a href="<?= url('/blog') ?>">Blog</a> <span class="breadcrumb-sep">/</span>
            <span><?= e($post['titulo']) ?></span>
        </nav>
        <h1><?= e($post['titulo']) ?></h1>
        <?php if (!empty($post['extracto'])): ?>
            <p class="hero-subtitle"><?= e($post['extracto']) ?></p>
        <?php endif; ?>
    </div>
</section>

<section class="page-section">
    <div class="container">
        <div class="blog-article">

            <!-- Meta -->
            <div style="display:flex;flex-wrap:wrap;gap:16px;margin-bottom:24px;font-size:.88rem;color:var(--text-muted)">
                <?php if (!empty($post['autor_nombre'])): ?>
                    <span>&#128100; <?= e($post['autor_nombre']) ?></span>
                <?php endif; ?>
                <?php if (!empty($post['publicado_at'])): ?>
                    <span>&#128197; <?= formatDate($post['publicado_at'], 'd M Y') ?></span>
                <?php endif; ?>
                <?php if (!empty($post['tiempo_lectura'])): ?>
                    <span>&#9201; <?= $post['tiempo_lectura'] ?> min lectura</span>
                <?php endif; ?>
                <?php if (!empty($post['categoria_nombre'])): ?>
                    <span class="badge badge-green" style="font-size:.75rem"><?= e($post['categoria_nombre']) ?></span>
                <?php endif; ?>
            </div>

            <!-- Imagen portada -->
            <?php if (!empty($post['imagen_portada'])): ?>
                <div style="margin-bottom:32px;border-radius:var(--radius-lg);overflow:hidden">
                    <img src="<?= asset('uploads/' . e($post['imagen_portada'])) ?>" alt="<?= e($post['titulo']) ?>" style="width:100%;height:auto;display:block">
                </div>
            <?php endif; ?>

            <!-- Contenido -->
            <div class="blog-article-content">
                <?= nl2br(e($post['contenido'])) ?>
            </div>

            <!-- Fuente -->
            <?php if (!empty($post['fuente_nombre'])): ?>
            <div style="margin-top:24px;padding:12px 16px;background:var(--bg-alt);border-radius:var(--radius);font-size:.85rem;color:var(--text-light)">
                Fuente: <?php if (!empty($post['fuente_url'])): ?><a href="<?= e($post['fuente_url']) ?>" target="_blank" rel="noopener"><?= e($post['fuente_nombre']) ?></a><?php else: ?><?= e($post['fuente_nombre']) ?><?php endif; ?>
            </div>
            <?php endif; ?>

            <!-- Footer -->
            <div class="blog-article-footer">
                <!-- Compartir -->
                <div class="share-buttons">
                    <a href="https://wa.me/?text=<?= urlencode($post['titulo'] . ' — ' . $currentUrl) ?>" target="_blank" rel="noopener" class="share-btn share-btn--wa">
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
        </div>

        <!-- Relacionados -->
        <?php if (!empty($relacionados)): ?>
        <div class="mt-4">
            <h2>Articulos relacionados</h2>
            <div class="blog-grid mt-2">
                <?php foreach ($relacionados as $rel): ?>
                <a href="<?= url('/blog/' . e($rel['slug'])) ?>" class="card">
                    <?php if (!empty($rel['imagen_portada'])): ?>
                        <div class="card-img" style="background-image:url('<?= asset('uploads/' . e($rel['imagen_portada'])) ?>')"></div>
                    <?php else: ?>
                        <div class="card-img-placeholder"><span>&#128221;</span></div>
                    <?php endif; ?>
                    <div class="card-body">
                        <h3 class="card-title" style="font-size:.95rem"><?= e($rel['titulo']) ?></h3>
                        <?php if (!empty($rel['extracto'])): ?>
                            <p class="card-text" style="font-size:.82rem"><?= e(mb_strimwidth($rel['extracto'], 0, 80, '...')) ?></p>
                        <?php endif; ?>
                    </div>
                </a>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>

    </div>
</section>
