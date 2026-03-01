<?php
/**
 * Categoria detalle — visitapurranque.cl
 * Variables: $categoria, $fichas, $page, $totalPages, $total
 */
?>

<section class="hero-section" style="background: linear-gradient(135deg, <?= e($categoria['color'] ?? '#1a5632') ?> 0%, var(--green-dark) 100%);">
    <div class="container">
        <nav class="breadcrumb breadcrumb--light" aria-label="Migas de pan">
            <a href="<?= url('/') ?>">Inicio</a> <span class="breadcrumb-sep">/</span>
            <a href="<?= url('/categorias') ?>">Categorias</a> <span class="breadcrumb-sep">/</span>
            <span><?= e($categoria['nombre']) ?></span>
        </nav>
        <h1><?= e($categoria['emoji'] ?? '') ?> <?= e($categoria['nombre']) ?></h1>
        <?php if (!empty($categoria['descripcion'])): ?>
            <p class="hero-subtitle"><?= e($categoria['descripcion']) ?></p>
        <?php endif; ?>
        <p class="hero-subtitle" style="margin-top: 8px; font-size: 0.9rem; opacity: .8;">
            <?= $total ?> <?= $total == 1 ? 'lugar encontrado' : 'lugares encontrados' ?>
        </p>
    </div>
</section>

<section class="page-section">
    <div class="container">
        <?php if (!empty($fichas)): ?>
        <div class="fichas-grid">
            <?php foreach ($fichas as $ficha): ?>
            <a href="<?= url('/atractivo/' . e($ficha['slug'])) ?>" class="ficha-card card">
                <div class="card-img-placeholder">
                    <span><?= e($ficha['categoria_emoji'] ?? $categoria['emoji'] ?? '📍') ?></span>
                </div>
                <div class="card-body">
                    <?php if ($ficha['verificado']): ?>
                        <span class="badge badge-blue">Verificado</span>
                    <?php endif; ?>
                    <?php if ($ficha['destacado']): ?>
                        <span class="badge badge-orange">Destacado</span>
                    <?php endif; ?>
                    <h3 class="card-title"><?= e($ficha['nombre']) ?></h3>
                    <p class="card-text"><?= e($ficha['descripcion_corta'] ?? '') ?></p>
                    <?php if (!empty($ficha['direccion'])): ?>
                        <p class="ficha-direccion">&#128205; <?= e($ficha['direccion']) ?></p>
                    <?php endif; ?>
                    <?php if ($ficha['promedio_rating'] > 0): ?>
                    <div class="rating-stars">
                        <?php for ($i = 1; $i <= 5; $i++): ?>
                            <span class="star <?= $i <= round($ficha['promedio_rating']) ? 'star--full' : 'star--empty' ?>">&#9733;</span>
                        <?php endfor; ?>
                        <span class="rating-value"><?= number_format($ficha['promedio_rating'], 1) ?> (<?= (int)$ficha['total_resenas'] ?>)</span>
                    </div>
                    <?php endif; ?>
                </div>
            </a>
            <?php endforeach; ?>
        </div>

        <!-- Paginacion -->
        <?php if ($totalPages > 1): ?>
        <nav class="pagination" aria-label="Paginacion">
            <?php if ($page > 1): ?>
                <a href="?page=<?= $page - 1 ?>" class="pagination-link">&laquo; Anterior</a>
            <?php endif; ?>

            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                <?php if ($i == $page): ?>
                    <span class="pagination-link pagination-link--active"><?= $i ?></span>
                <?php else: ?>
                    <a href="?page=<?= $i ?>" class="pagination-link"><?= $i ?></a>
                <?php endif; ?>
            <?php endfor; ?>

            <?php if ($page < $totalPages): ?>
                <a href="?page=<?= $page + 1 ?>" class="pagination-link">Siguiente &raquo;</a>
            <?php endif; ?>
        </nav>
        <?php endif; ?>

        <?php else: ?>
        <div class="empty-state">
            <span class="empty-state-icon"><?= e($categoria['emoji'] ?? '📍') ?></span>
            <p>Aun no hay atractivos en esta categoria.</p>
            <a href="<?= url('/categorias') ?>" class="btn btn-secondary mt-2">Ver otras categorias</a>
        </div>
        <?php endif; ?>
    </div>
</section>
