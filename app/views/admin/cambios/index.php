<?php
/**
 * Admin — Cambios Pendientes — visitapurranque.cl
 * Variables: $cambios, $filtros, $pagina, $total, $totalPaginas, $stats, $csrf
 */
?>

<div class="admin-page-header admin-page-header--flex">
    <div>
        <h1>Cambios Pendientes</h1>
        <p class="admin-page-subtitle"><?= $total ?> cambio<?= $total !== 1 ? 's' : '' ?> en total</p>
    </div>
    <div class="admin-page-actions">
        <a href="<?= url('/admin/renovaciones') ?>" class="btn btn--outline">Ver renovaciones</a>
    </div>
</div>

<!-- Stats -->
<div class="resena-stats">
    <span class="resena-stat resena-stat--yellow"><?= $stats['pendiente'] ?> pendiente<?= $stats['pendiente'] !== 1 ? 's' : '' ?></span>
    <span class="resena-stat resena-stat--green"><?= $stats['aprobado'] ?> aprobado<?= $stats['aprobado'] !== 1 ? 's' : '' ?></span>
    <span class="resena-stat resena-stat--red"><?= $stats['rechazado'] ?> rechazado<?= $stats['rechazado'] !== 1 ? 's' : '' ?></span>
</div>

<!-- Filtros -->
<form class="admin-filters" method="GET" action="<?= url('/admin/cambios') ?>">
    <div class="admin-filters__group">
        <select name="estado" class="form-select">
            <option value="">Todos los estados</option>
            <option value="pendiente" <?= ($filtros['estado'] === 'pendiente') ? 'selected' : '' ?>>Pendientes</option>
            <option value="aprobado" <?= ($filtros['estado'] === 'aprobado') ? 'selected' : '' ?>>Aprobados</option>
            <option value="rechazado" <?= ($filtros['estado'] === 'rechazado') ? 'selected' : '' ?>>Rechazados</option>
        </select>

        <select name="tipo" class="form-select">
            <option value="">Todos los tipos</option>
            <option value="edicion" <?= ($filtros['tipo'] === 'edicion') ? 'selected' : '' ?>>Edición</option>
            <option value="nueva" <?= ($filtros['tipo'] === 'nueva') ? 'selected' : '' ?>>Nueva ficha</option>
            <option value="eliminacion" <?= ($filtros['tipo'] === 'eliminacion') ? 'selected' : '' ?>>Eliminación</option>
        </select>

        <div class="admin-filters__search">
            <input type="text" name="q" value="<?= e($filtros['q']) ?>"
                   placeholder="Buscar por ficha, usuario, motivo..." class="form-input">
            <button type="submit" class="btn btn--small">Buscar</button>
        </div>
    </div>

    <?php if ($filtros['estado'] !== '' || $filtros['tipo'] !== '' || !empty($filtros['q'])): ?>
        <a href="<?= url('/admin/cambios') ?>" class="admin-filters__clear">Limpiar filtros</a>
    <?php endif; ?>
</form>

<!-- Tabla -->
<?php if (!empty($cambios)): ?>
<div class="admin-table-wrap">
    <table class="admin-table">
        <thead>
            <tr>
                <th>#</th>
                <th>Ficha</th>
                <th>Tipo</th>
                <th>Solicitante</th>
                <th>Motivo</th>
                <th>Fecha</th>
                <th>Estado</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($cambios as $c): ?>
            <tr>
                <td class="text-mono"><?= $c['id'] ?></td>
                <td>
                    <strong><?= e($c['ficha_nombre'] ?? '(ficha eliminada)') ?></strong>
                </td>
                <td>
                    <?php
                    $tipoBadge = match($c['tipo']) {
                        'edicion'     => 'badge--blue',
                        'nueva'       => 'badge--green',
                        'eliminacion' => 'badge--red',
                        default       => 'badge--gray',
                    };
                    ?>
                    <span class="badge <?= $tipoBadge ?>"><?= e(ucfirst($c['tipo'])) ?></span>
                </td>
                <td><?= e($c['usuario_nombre'] ?? 'Sistema') ?></td>
                <td class="resena-comentario-cell"><?= e(mb_strimwidth($c['motivo'] ?? '—', 0, 60, '...')) ?></td>
                <td class="text-mono" style="white-space:nowrap">
                    <?= formatDate($c['created_at'], 'd/m/Y') ?>
                    <small class="table-sub"><?= formatDate($c['created_at'], 'H:i') ?></small>
                </td>
                <td>
                    <?php
                    $estadoBadge = match($c['estado']) {
                        'pendiente' => 'badge--yellow',
                        'aprobado'  => 'badge--green',
                        'rechazado' => 'badge--red',
                        default     => 'badge--gray',
                    };
                    ?>
                    <span class="badge <?= $estadoBadge ?>"><?= e(ucfirst($c['estado'])) ?></span>
                </td>
                <td>
                    <div class="table-actions">
                        <a href="<?= url("/admin/cambios/{$c['id']}") ?>"
                           class="btn btn--small btn--outline">Revisar</a>

                        <?php if ($c['estado'] !== 'pendiente'): ?>
                        <form method="POST" action="<?= url("/admin/cambios/{$c['id']}/eliminar") ?>" class="inline-form">
                            <?= csrf_field() ?>
                            <button type="submit" class="btn btn--small btn--danger"
                                    data-confirm="¿Eliminar este registro?">Eliminar</button>
                        </form>
                        <?php endif; ?>
                    </div>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<!-- Paginación -->
<?php if ($totalPaginas > 1): ?>
<nav class="admin-pagination">
    <?php
    $qs = $_GET;
    unset($qs['pagina']);
    $qsStr = http_build_query($qs);
    $baseUrl = url('/admin/cambios') . ($qsStr ? '?' . $qsStr . '&' : '?');
    ?>

    <?php if ($pagina > 1): ?>
        <a href="<?= $baseUrl ?>pagina=<?= $pagina - 1 ?>" class="pagination-link">&laquo; Anterior</a>
    <?php endif; ?>

    <?php for ($i = 1; $i <= $totalPaginas; $i++): ?>
        <?php if ($i === $pagina): ?>
            <span class="pagination-link pagination-link--active"><?= $i ?></span>
        <?php elseif (abs($i - $pagina) <= 2 || $i === 1 || $i === $totalPaginas): ?>
            <a href="<?= $baseUrl ?>pagina=<?= $i ?>" class="pagination-link"><?= $i ?></a>
        <?php elseif (abs($i - $pagina) === 3): ?>
            <span class="pagination-ellipsis">&hellip;</span>
        <?php endif; ?>
    <?php endfor; ?>

    <?php if ($pagina < $totalPaginas): ?>
        <a href="<?= $baseUrl ?>pagina=<?= $pagina + 1 ?>" class="pagination-link">Siguiente &raquo;</a>
    <?php endif; ?>
</nav>
<?php endif; ?>

<?php else: ?>
<p class="admin-empty">
    <?php if ($filtros['estado'] !== '' || $filtros['tipo'] !== '' || !empty($filtros['q'])): ?>
        No se encontraron cambios con esos filtros.
    <?php else: ?>
        No hay cambios pendientes. Todo al día.
    <?php endif; ?>
</p>
<?php endif; ?>
