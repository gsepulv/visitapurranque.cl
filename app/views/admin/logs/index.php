<?php
/**
 * Admin — Registro de Actividad
 * Variables: $logs, $modulos, $acciones, $filtroModulo, $filtroAccion, $filtroDesde, $filtroHasta, $page, $totalPages, $total
 */
?>

<div class="admin-page-header admin-page-header--flex">
    <div>
        <h1>Registro de Actividad</h1>
        <p class="admin-page-subtitle"><?= $total ?> registro<?= $total !== 1 ? 's' : '' ?></p>
    </div>
    <a href="<?= url('/admin/logs/salud') ?>" class="btn btn--outline">Salud del Sistema</a>
</div>

<!-- Filtros -->
<div class="admin-card" style="margin-bottom:20px;">
    <form method="GET" action="<?= url('/admin/logs') ?>" style="display:flex;gap:10px;align-items:flex-end;flex-wrap:wrap;">
        <div class="form-group" style="min-width:140px;">
            <label class="form-label">Módulo</label>
            <select name="modulo" class="form-select">
                <option value="">Todos</option>
                <?php foreach ($modulos as $m): ?>
                    <option value="<?= e($m) ?>"<?= $filtroModulo === $m ? ' selected' : '' ?>><?= e($m) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-group" style="min-width:140px;">
            <label class="form-label">Acción</label>
            <select name="accion" class="form-select">
                <option value="">Todas</option>
                <?php foreach ($acciones as $a): ?>
                    <option value="<?= e($a) ?>"<?= $filtroAccion === $a ? ' selected' : '' ?>><?= e($a) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-group" style="min-width:140px;">
            <label class="form-label">Desde</label>
            <input type="date" name="desde" class="form-input" value="<?= e($filtroDesde) ?>">
        </div>
        <div class="form-group" style="min-width:140px;">
            <label class="form-label">Hasta</label>
            <input type="date" name="hasta" class="form-input" value="<?= e($filtroHasta) ?>">
        </div>
        <button type="submit" class="btn btn--primary" style="margin-bottom:16px;">Filtrar</button>
        <?php if ($filtroModulo || $filtroAccion || $filtroDesde || $filtroHasta): ?>
            <a href="<?= url('/admin/logs') ?>" class="btn btn--outline" style="margin-bottom:16px;">Limpiar</a>
        <?php endif; ?>
    </form>
</div>

<?php if (empty($logs)): ?>
    <div class="admin-empty">No hay registros que coincidan con los filtros.</div>
<?php else: ?>
<div class="admin-table-wrapper">
<table class="admin-table">
    <thead>
        <tr>
            <th>Fecha</th>
            <th>Usuario</th>
            <th>Acción</th>
            <th>Módulo</th>
            <th>Detalle</th>
            <th>IP</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($logs as $log): ?>
        <tr>
            <td class="text-muted" style="white-space:nowrap;font-size:.85rem;">
                <?= date('d/m/Y H:i', strtotime($log['created_at'])) ?>
            </td>
            <td><?= e($log['usuario_nombre'] ?? 'Sistema') ?></td>
            <td>
                <?php
                $badgeColor = match($log['accion']) {
                    'crear'    => '#22c55e',
                    'editar'   => '#3b82f6',
                    'eliminar' => '#ef4444',
                    'login'    => '#8b5cf6',
                    'logout'   => '#6b7280',
                    default    => '#f59e0b',
                };
                ?>
                <span class="badge badge--small" style="background:<?= $badgeColor ?>;color:#fff;"><?= e($log['accion']) ?></span>
            </td>
            <td><?= e($log['modulo']) ?></td>
            <td style="max-width:300px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">
                <?php
                $datos = json_decode($log['datos_despues'] ?? '{}', true);
                echo e($datos['detalle'] ?? '—');
                ?>
            </td>
            <td class="text-muted" style="font-size:.85rem;"><?= e($log['ip'] ?? '') ?></td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>
</div>

<?php if ($totalPages > 1): ?>
<div class="admin-pagination">
    <?php if ($page > 1): ?>
        <a href="<?= url('/admin/logs?' . http_build_query(array_merge($_GET, ['page' => $page - 1]))) ?>" class="btn btn--small btn--outline">&laquo; Anterior</a>
    <?php endif; ?>
    <span class="text-muted" style="padding:0 12px;">Página <?= $page ?> de <?= $totalPages ?></span>
    <?php if ($page < $totalPages): ?>
        <a href="<?= url('/admin/logs?' . http_build_query(array_merge($_GET, ['page' => $page + 1]))) ?>" class="btn btn--small btn--outline">Siguiente &raquo;</a>
    <?php endif; ?>
</div>
<?php endif; ?>

<?php endif; ?>
