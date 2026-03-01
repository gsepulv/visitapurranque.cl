<?php
/**
 * Listado de eventos — visitapurranque.cl
 * Variables: $eventos, $tiempo, $pagina, $totalPaginas, $total
 */
?>

<section class="hero-section">
    <div class="container">
        <nav class="breadcrumb breadcrumb--light" aria-label="Migas de pan">
            <a href="<?= url('/') ?>">Inicio</a> <span class="breadcrumb-sep">/</span>
            <span>Eventos</span>
        </nav>
        <h1>Eventos y Actividades</h1>
        <p class="hero-subtitle">Fiestas, ferias, trekking y mas actividades en Purranque y sus alrededores.</p>
    </div>
</section>

<section class="page-section">
    <div class="container">

        <!-- Filtros -->
        <div class="filter-tabs mb-3">
            <a href="<?= url('/eventos?t=proximos') ?>" class="filter-tab <?= $tiempo === 'proximos' ? 'active' : '' ?>">Proximos</a>
            <a href="<?= url('/eventos?t=pasados') ?>" class="filter-tab <?= $tiempo === 'pasados' ? 'active' : '' ?>">Pasados</a>
            <a href="<?= url('/eventos?t=todos') ?>" class="filter-tab <?= $tiempo === 'todos' ? 'active' : '' ?>">Todos</a>
        </div>

        <?php if (empty($eventos)): ?>
            <p class="text-muted text-center" style="padding:40px 0">No hay eventos <?= $tiempo === 'proximos' ? 'proximos' : ($tiempo === 'pasados' ? 'pasados' : '') ?> por el momento.</p>
        <?php else: ?>

        <div class="eventos-list">
            <?php foreach ($eventos as $ev): ?>
            <?php
                $inicio = strtotime($ev['fecha_inicio']);
                $dia = date('d', $inicio);
                $mes = strftime_es($inicio);
                $anio = date('Y', $inicio);
                $isPasado = strtotime($ev['fecha_fin'] ?? $ev['fecha_inicio']) < time();
            ?>
            <a href="<?= url('/evento/' . e($ev['slug'])) ?>" class="evento-list-card<?= $isPasado ? ' opacity-60' : '' ?>">
                <div class="evento-list-fecha">
                    <div style="font-size:1.4rem;font-weight:700;line-height:1;color:var(--green)"><?= $dia ?></div>
                    <div style="font-size:.75rem;text-transform:uppercase;color:var(--text-muted)"><?= $mes ?></div>
                    <div style="font-size:.7rem;color:var(--text-muted)"><?= $anio ?></div>
                </div>
                <div class="evento-list-body">
                    <h3><?= e($ev['titulo']) ?></h3>
                    <?php if (!empty($ev['lugar'])): ?>
                        <p style="font-size:.85rem;color:var(--text-muted);margin-bottom:4px">&#128205; <?= e($ev['lugar']) ?></p>
                    <?php endif; ?>
                    <?php if (!empty($ev['descripcion_corta'])): ?>
                        <p style="font-size:.88rem;color:var(--text-light);margin-bottom:0"><?= e(mb_strimwidth($ev['descripcion_corta'], 0, 120, '...')) ?></p>
                    <?php endif; ?>
                    <?php if ($isPasado): ?>
                        <span class="badge badge-gray" style="margin-top:6px">Finalizado</span>
                    <?php elseif (!empty($ev['precio'])): ?>
                        <span class="badge badge-green" style="margin-top:6px"><?= e($ev['precio']) ?></span>
                    <?php endif; ?>
                </div>
            </a>
            <?php endforeach; ?>
        </div>

        <!-- Paginacion -->
        <?php if ($totalPaginas > 1): ?>
        <nav class="pagination" aria-label="Paginacion de eventos">
            <?php if ($pagina > 1): ?>
                <a href="<?= url('/eventos?t=' . e($tiempo) . '&p=' . ($pagina - 1)) ?>" class="pagination-link">&laquo; Anterior</a>
            <?php endif; ?>
            <?php for ($i = 1; $i <= $totalPaginas; $i++): ?>
                <?php if ($i === $pagina): ?>
                    <span class="pagination-link active"><?= $i ?></span>
                <?php else: ?>
                    <a href="<?= url('/eventos?t=' . e($tiempo) . '&p=' . $i) ?>" class="pagination-link"><?= $i ?></a>
                <?php endif; ?>
            <?php endfor; ?>
            <?php if ($pagina < $totalPaginas): ?>
                <a href="<?= url('/eventos?t=' . e($tiempo) . '&p=' . ($pagina + 1)) ?>" class="pagination-link">Siguiente &raquo;</a>
            <?php endif; ?>
        </nav>
        <?php endif; ?>

        <?php endif; ?>

    </div>
</section>
