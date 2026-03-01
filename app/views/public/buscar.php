<?php
/**
 * Buscador — visitapurranque.cl
 * Variables: $q, $resultados, $totalResultados
 */
?>

<section class="hero-section">
    <div class="container">
        <nav class="breadcrumb breadcrumb--light" aria-label="Migas de pan">
            <a href="<?= url('/') ?>">Inicio</a> <span class="breadcrumb-sep">/</span>
            <span>Buscar</span>
        </nav>
        <h1>Buscar</h1>
        <p class="hero-subtitle">Encuentra atractivos turisticos, eventos y articulos en Purranque.</p>
    </div>
</section>

<section class="page-section">
    <div class="container">

        <!-- Formulario de busqueda -->
        <div class="buscar-form">
            <form method="get" action="<?= url('/buscar') ?>">
                <div class="buscar-input-group">
                    <input type="text" name="q" value="<?= e($q) ?>" placeholder="Buscar atractivos, eventos, articulos..." autofocus>
                    <button type="submit" class="btn btn-primary">Buscar</button>
                </div>
            </form>
        </div>

        <?php if ($q !== ''): ?>
            <?php if ($totalResultados === 0): ?>
                <p class="text-muted text-center" style="padding:40px 0">No se encontraron resultados para "<?= e($q) ?>". Intenta con otros terminos.</p>
            <?php else: ?>
                <p class="text-muted mb-3"><?= $totalResultados ?> resultado<?= $totalResultados !== 1 ? 's' : '' ?> para "<?= e($q) ?>"</p>

                <div class="buscar-resultados">
                    <?php if (!empty($resultados['fichas'])): ?>
                    <h3>&#128205; Atractivos turisticos (<?= count($resultados['fichas']) ?>)</h3>
                    <?php foreach ($resultados['fichas'] as $r): ?>
                    <a href="<?= url('/atractivo/' . e($r['slug'])) ?>" class="buscar-item">
                        <h4><?= e($r['nombre']) ?></h4>
                        <?php if (!empty($r['descripcion_corta'])): ?>
                            <p><?= e(mb_strimwidth($r['descripcion_corta'], 0, 150, '...')) ?></p>
                        <?php endif; ?>
                    </a>
                    <?php endforeach; ?>
                    <?php endif; ?>

                    <?php if (!empty($resultados['eventos'])): ?>
                    <h3>&#127881; Eventos (<?= count($resultados['eventos']) ?>)</h3>
                    <?php foreach ($resultados['eventos'] as $r): ?>
                    <a href="<?= url('/evento/' . e($r['slug'])) ?>" class="buscar-item">
                        <h4><?= e($r['nombre']) ?></h4>
                        <?php if (!empty($r['descripcion_corta'])): ?>
                            <p><?= e(mb_strimwidth($r['descripcion_corta'], 0, 150, '...')) ?></p>
                        <?php endif; ?>
                    </a>
                    <?php endforeach; ?>
                    <?php endif; ?>

                    <?php if (!empty($resultados['blog'])): ?>
                    <h3>&#128221; Blog (<?= count($resultados['blog']) ?>)</h3>
                    <?php foreach ($resultados['blog'] as $r): ?>
                    <a href="<?= url('/blog/' . e($r['slug'])) ?>" class="buscar-item">
                        <h4><?= e($r['nombre']) ?></h4>
                        <?php if (!empty($r['descripcion_corta'])): ?>
                            <p><?= e(mb_strimwidth($r['descripcion_corta'], 0, 150, '...')) ?></p>
                        <?php endif; ?>
                    </a>
                    <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        <?php endif; ?>

    </div>
</section>
