<?php
/**
 * Listado de blog — visitapurranque.cl
 * Variables: $posts, $pagina, $totalPaginas, $total
 */
?>

<section class="hero-section">
    <div class="container">
        <nav class="breadcrumb breadcrumb--light" aria-label="Migas de pan">
            <a href="<?= url('/') ?>">Inicio</a> <span class="breadcrumb-sep">/</span>
            <span>Blog</span>
        </nav>
        <h1>Blog</h1>
        <p class="hero-subtitle">Noticias, guias y articulos sobre turismo, cultura y gastronomia en Purranque.</p>
    </div>
</section>

<section class="page-section">
    <div class="container">

        <?php if (empty($posts)): ?>
            <p class="text-muted text-center" style="padding:40px 0">No hay articulos publicados por el momento.</p>
        <?php else: ?>

        <div class="blog-grid">
            <?php foreach ($posts as $p): ?>
            <a href="<?= url('/blog/' . e($p['slug'])) ?>" class="card">
                <?php if (!empty($p['imagen_portada'])): ?>
                    <div class="card-img" style="background-image:url('<?= asset('uploads/' . e($p['imagen_portada'])) ?>')"></div>
                <?php else: ?>
                    <div class="card-img-placeholder">
                        <span>&#128221;</span>
                    </div>
                <?php endif; ?>
                <div class="card-body">
                    <?php if (!empty($p['categoria_nombre'])): ?>
                        <span class="badge badge-green" style="font-size:.72rem;margin-bottom:6px"><?= e($p['categoria_nombre']) ?></span>
                    <?php endif; ?>
                    <h3 class="card-title"><?= e($p['titulo']) ?></h3>
                    <?php if (!empty($p['extracto'])): ?>
                        <p class="card-text"><?= e(mb_strimwidth($p['extracto'], 0, 120, '...')) ?></p>
                    <?php endif; ?>
                    <div style="display:flex;gap:12px;font-size:.78rem;color:var(--text-muted);margin-top:8px">
                        <?php if (!empty($p['publicado_at'])): ?>
                            <span><?= formatDate($p['publicado_at'], 'd M Y') ?></span>
                        <?php endif; ?>
                        <?php if (!empty($p['tiempo_lectura'])): ?>
                            <span><?= $p['tiempo_lectura'] ?> min lectura</span>
                        <?php endif; ?>
                    </div>
                </div>
            </a>
            <?php endforeach; ?>
        </div>

        <!-- Paginacion -->
        <?php if ($totalPaginas > 1): ?>
        <nav class="pagination" aria-label="Paginacion del blog">
            <?php if ($pagina > 1): ?>
                <a href="<?= url('/blog?p=' . ($pagina - 1)) ?>" class="pagination-link">&laquo; Anterior</a>
            <?php endif; ?>
            <?php for ($i = 1; $i <= $totalPaginas; $i++): ?>
                <?php if ($i === $pagina): ?>
                    <span class="pagination-link active"><?= $i ?></span>
                <?php else: ?>
                    <a href="<?= url('/blog?p=' . $i) ?>" class="pagination-link"><?= $i ?></a>
                <?php endif; ?>
            <?php endfor; ?>
            <?php if ($pagina < $totalPaginas): ?>
                <a href="<?= url('/blog?p=' . ($pagina + 1)) ?>" class="pagination-link">Siguiente &raquo;</a>
            <?php endif; ?>
        </nav>
        <?php endif; ?>

        <?php endif; ?>

    </div>
</section>
