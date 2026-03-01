<?php
/**
 * Categorias index — visitapurranque.cl
 * Variables: $categorias
 */
?>

<section class="hero-section">
    <div class="container">
        <nav class="breadcrumb" aria-label="Migas de pan">
            <a href="<?= url('/') ?>">Inicio</a> <span class="breadcrumb-sep">/</span>
            <span>Categorias</span>
        </nav>
        <h1>Categorias</h1>
        <p class="hero-subtitle">Explora los atractivos turisticos de Purranque organizados por categoria.</p>
    </div>
</section>

<section class="page-section">
    <div class="container">
        <?php if (!empty($categorias)): ?>
        <div class="categorias-grid categorias-grid--full">
            <?php foreach ($categorias as $cat): ?>
            <a href="<?= url('/categoria/' . e($cat['slug'])) ?>"
               class="categoria-card <?= $cat['total_fichas'] == 0 ? 'categoria-card--empty' : '' ?>"
               style="--cat-color: <?= e($cat['color'] ?? '#3b82f6') ?>">
                <span class="categoria-emoji"><?= e($cat['emoji'] ?? '📍') ?></span>
                <span class="categoria-nombre"><?= e($cat['nombre']) ?></span>
                <?php if (!empty($cat['descripcion'])): ?>
                    <span class="categoria-desc"><?= e(mb_strimwidth($cat['descripcion'], 0, 80, '...')) ?></span>
                <?php endif; ?>
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
        <?php else: ?>
        <div class="empty-state">
            <span class="empty-state-icon">&#128204;</span>
            <p>Aun no hay categorias disponibles.</p>
        </div>
        <?php endif; ?>
    </div>
</section>
