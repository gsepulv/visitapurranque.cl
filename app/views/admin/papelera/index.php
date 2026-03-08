<?php
/**
 * Admin — Papelera
 * Variables: $items, $tab, $tipos, $csrf
 */
$totalPapelera = 0;
foreach ($items as $arr) $totalPapelera += count($arr);
?>

<div class="admin-page-header admin-page-header--flex">
    <div>
        <h1>Papelera</h1>
        <p class="admin-page-subtitle"><?= $totalPapelera ?> elemento<?= $totalPapelera !== 1 ? 's' : '' ?> en la papelera</p>
    </div>
    <?php if ($totalPapelera > 0): ?>
    <form method="POST" action="<?= url('/admin/papelera/vaciar') ?>"
          onsubmit="return confirm('¿Vaciar toda la papelera? Esta acción no se puede deshacer.')">
        <?= csrf_field() ?>
        <button type="submit" class="btn btn--danger">Vaciar papelera</button>
    </form>
    <?php endif; ?>
</div>

<div class="admin-tabs">
    <?php foreach ($tipos as $key => $cfg): ?>
    <a href="<?= url('/admin/papelera?tab=' . $key) ?>"
       class="admin-tab<?= $tab === $key ? ' admin-tab--active' : '' ?>">
        <?= $cfg['label'] ?>
        <?php if (count($items[$key]) > 0): ?>
            <span class="sidebar-badge"><?= count($items[$key]) ?></span>
        <?php endif; ?>
    </a>
    <?php endforeach; ?>
</div>

<?php $current = $items[$tab] ?? []; ?>

<?php if (empty($current)): ?>
    <div class="admin-empty">No hay elementos eliminados en esta sección.</div>
<?php else: ?>
<div class="admin-table-wrapper">
<table class="admin-table">
    <thead>
        <tr>
            <th>Nombre</th>
            <th>Fecha eliminación</th>
            <th>Acciones</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($current as $item): ?>
        <tr>
            <td><?= e($item['nombre']) ?></td>
            <td class="text-muted"><?= $item['eliminado_at'] ? date('d/m/Y H:i', strtotime($item['eliminado_at'])) : '—' ?></td>
            <td class="admin-table__actions">
                <form method="POST" action="<?= url("/admin/papelera/restaurar/{$tab}/{$item['id']}") ?>" style="display:inline">
                    <?= csrf_field() ?>
                    <button type="submit" class="btn btn--small btn--outline">Restaurar</button>
                </form>
                <form method="POST" action="<?= url("/admin/papelera/eliminar/{$tab}/{$item['id']}") ?>" style="display:inline"
                      onsubmit="return confirm('¿Eliminar permanentemente? No se puede deshacer.')">
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
