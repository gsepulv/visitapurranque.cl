<?php
/**
 * Admin — Listado de Suscripciones — visitapurranque.cl
 * Variables: $suscripciones, $planes, $fichas, $filtros, $pagina, $total, $totalPaginas, $stats, $csrf
 */

$estadoBadge = [
    'activa'    => 'badge--green',
    'expirada'  => 'badge--red',
    'cancelada' => 'badge--gray',
    'pendiente' => 'badge--yellow',
];
?>

<div class="admin-page-header admin-page-header--flex">
    <div>
        <h1>Suscripciones</h1>
        <p class="admin-page-subtitle">
            <?= $total ?> suscripci<?= $total !== 1 ? 'ones' : 'ón' ?> en total
            &nbsp;·&nbsp;
            <a href="<?= url('/admin/planes') ?>">&larr; Ver planes</a>
        </p>
    </div>
    <a href="<?= url('/admin/suscripciones/crear') ?>" class="btn btn--primary">+ Nueva Suscripción</a>
</div>

<!-- Stats -->
<div class="resena-stats">
    <span class="resena-stat resena-stat--green"><?= $stats['activa'] ?> activa<?= $stats['activa'] !== 1 ? 's' : '' ?></span>
    <span class="resena-stat resena-stat--yellow"><?= $stats['pendiente'] ?> pendiente<?= $stats['pendiente'] !== 1 ? 's' : '' ?></span>
    <span class="resena-stat resena-stat--red"><?= $stats['expirada'] ?> expirada<?= $stats['expirada'] !== 1 ? 's' : '' ?></span>
    <span class="resena-stat resena-stat--gray"><?= $stats['cancelada'] ?> cancelada<?= $stats['cancelada'] !== 1 ? 's' : '' ?></span>
</div>

<!-- Filtros -->
<form class="admin-filters" method="GET" action="<?= url('/admin/suscripciones') ?>">
    <div class="admin-filters__group">
        <select name="estado" class="form-select">
            <option value="">Todos los estados</option>
            <option value="activa" <?= ($filtros['estado'] === 'activa') ? 'selected' : '' ?>>Activa</option>
            <option value="pendiente" <?= ($filtros['estado'] === 'pendiente') ? 'selected' : '' ?>>Pendiente</option>
            <option value="expirada" <?= ($filtros['estado'] === 'expirada') ? 'selected' : '' ?>>Expirada</option>
            <option value="cancelada" <?= ($filtros['estado'] === 'cancelada') ? 'selected' : '' ?>>Cancelada</option>
        </select>

        <select name="plan_id" class="form-select">
            <option value="">Todos los planes</option>
            <?php foreach ($planes as $p): ?>
            <option value="<?= $p['id'] ?>" <?= ((int)$filtros['plan_id'] === (int)$p['id']) ? 'selected' : '' ?>><?= e($p['nombre']) ?></option>
            <?php endforeach; ?>
        </select>

        <div class="admin-filters__search">
            <input type="text" name="q" value="<?= e($filtros['q']) ?>"
                   placeholder="Buscar por ficha..." class="form-input">
            <button type="submit" class="btn btn--small">Buscar</button>
        </div>
    </div>

    <?php if (!empty($filtros['estado']) || !empty($filtros['plan_id']) || !empty($filtros['q'])): ?>
        <a href="<?= url('/admin/suscripciones') ?>" class="admin-filters__clear">Limpiar filtros</a>
    <?php endif; ?>
</form>

<!-- Tabla -->
<?php if (!empty($suscripciones)): ?>
<div class="admin-table-wrap">
    <table class="admin-table">
        <thead>
            <tr>
                <th>Ficha</th>
                <th>Plan</th>
                <th>Inicio</th>
                <th>Fin</th>
                <th>Monto</th>
                <th>Estado</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($suscripciones as $s):
                $vencida = $s['estado'] === 'activa' && $s['fecha_fin'] < date('Y-m-d');
                $porVencer = $s['estado'] === 'activa' && !$vencida && $s['fecha_fin'] <= date('Y-m-d', strtotime('+30 days'));
            ?>
            <tr class="<?= ($s['estado'] === 'cancelada' || $s['estado'] === 'expirada') ? 'row--muted' : '' ?>">
                <td><strong><?= e($s['ficha_nombre'] ?? 'Ficha #' . $s['ficha_id']) ?></strong></td>
                <td><span class="badge badge--outline"><?= e($s['plan_nombre'] ?? '—') ?></span></td>
                <td class="text-mono"><?= formatDate($s['fecha_inicio'], 'd/m/Y') ?></td>
                <td class="text-mono">
                    <?= formatDate($s['fecha_fin'], 'd/m/Y') ?>
                    <?php if ($vencida): ?>
                        <small class="table-sub" style="color:var(--admin-red)">Vencida</small>
                    <?php elseif ($porVencer): ?>
                        <small class="table-sub" style="color:var(--admin-yellow)">Por vencer</small>
                    <?php endif; ?>
                </td>
                <td class="text-mono">$<?= number_format($s['monto'], 0, ',', '.') ?></td>
                <td>
                    <span class="badge <?= $estadoBadge[$s['estado']] ?? 'badge--gray' ?>">
                        <?= ucfirst($s['estado']) ?>
                    </span>
                </td>
                <td>
                    <div class="table-actions">
                        <a href="<?= url("/admin/suscripciones/{$s['id']}/editar") ?>"
                           class="btn btn--small btn--outline">Editar</a>
                        <form method="POST" action="<?= url("/admin/suscripciones/{$s['id']}/eliminar") ?>" class="inline-form">
                            <?= csrf_field() ?>
                            <button type="submit" class="btn btn--small btn--danger"
                                    data-confirm="¿Eliminar esta suscripción?">Eliminar</button>
                        </form>
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
    $baseUrl = url('/admin/suscripciones') . ($qsStr ? '?' . $qsStr . '&' : '?');
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
    <?php if (!empty($filtros['estado']) || !empty($filtros['plan_id']) || !empty($filtros['q'])): ?>
        No se encontraron suscripciones con esos filtros.
    <?php else: ?>
        Aún no hay suscripciones. <a href="<?= url('/admin/suscripciones/crear') ?>">Crear la primera</a>
    <?php endif; ?>
</p>
<?php endif; ?>
