<?php
/**
 * Admin — Popups
 * Variables: $popups
 */
?>

<div class="admin-page-header admin-page-header--flex">
    <div>
        <h1>Popups</h1>
        <p class="admin-page-subtitle"><?= count($popups) ?> popup<?= count($popups) !== 1 ? 's' : '' ?></p>
    </div>
    <a href="<?= url('/admin/popups/crear') ?>" class="btn btn--primary">+ Nuevo Popup</a>
</div>

<?php if (empty($popups)): ?>
    <div class="admin-empty">No hay popups configurados.</div>
<?php else: ?>
<div class="admin-table-wrapper">
<table class="admin-table">
    <thead>
        <tr>
            <th>Título</th>
            <th>Tipo</th>
            <th>Trigger</th>
            <th>Fechas</th>
            <th>Estado</th>
            <th>Acciones</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($popups as $p): ?>
        <tr>
            <td><strong><?= e($p['titulo']) ?></strong></td>
            <td>
                <?php
                $tipoLabels = ['modal' => 'Modal', 'banner_top' => 'Banner Superior', 'banner_bottom' => 'Banner Inferior', 'slide_in' => 'Slide-in'];
                ?>
                <span class="badge badge--small" style="background:#e0e7ff;color:#3730a3;"><?= $tipoLabels[$p['tipo']] ?? $p['tipo'] ?></span>
            </td>
            <td>
                <?php
                $triggerLabels = ['tiempo' => 'Tiempo', 'scroll' => 'Scroll', 'exit_intent' => 'Exit intent', 'click' => 'Click'];
                echo ($triggerLabels[$p['trigger_type']] ?? $p['trigger_type']);
                if ($p['trigger_valor']) echo ' (' . e($p['trigger_valor']) . ')';
                ?>
            </td>
            <td class="text-muted" style="font-size:.85rem;">
                <?php if ($p['fecha_inicio'] || $p['fecha_fin']): ?>
                    <?= $p['fecha_inicio'] ? date('d/m/Y', strtotime($p['fecha_inicio'])) : '...' ?>
                    &mdash;
                    <?= $p['fecha_fin'] ? date('d/m/Y', strtotime($p['fecha_fin'])) : '...' ?>
                <?php else: ?>
                    Siempre
                <?php endif; ?>
            </td>
            <td>
                <span class="status-dot status-dot--<?= $p['activo'] ? 'active' : 'inactive' ?>"></span>
                <?= $p['activo'] ? 'Activo' : 'Inactivo' ?>
            </td>
            <td class="admin-table__actions">
                <a href="<?= url("/admin/popups/{$p['id']}/editar") ?>" class="btn btn--small btn--outline">Editar</a>
                <form method="POST" action="<?= url("/admin/popups/{$p['id']}/eliminar") ?>" style="display:inline"
                      onsubmit="return confirm('¿Eliminar este popup?')">
                    <?= csrf_field() ?>
                    <button type="submit" class="btn btn--small btn--danger">Eliminar</button>
                </form>
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>
</div>
<?php endif; ?>
