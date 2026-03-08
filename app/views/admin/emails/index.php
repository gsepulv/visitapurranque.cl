<?php
/**
 * Admin — Plantillas de Email
 * Variables: $templates
 */
?>

<div class="admin-page-header admin-page-header--flex">
    <div>
        <h1>Plantillas de Email</h1>
        <p class="admin-page-subtitle"><?= count($templates) ?> plantilla<?= count($templates) !== 1 ? 's' : '' ?></p>
    </div>
    <a href="<?= url('/admin/emails/crear') ?>" class="btn btn--primary">+ Nueva Plantilla</a>
</div>

<?php if (empty($templates)): ?>
    <div class="admin-empty">No hay plantillas de email configuradas.</div>
<?php else: ?>
<div class="admin-table-wrapper">
<table class="admin-table">
    <thead>
        <tr>
            <th>Nombre</th>
            <th>Slug</th>
            <th>Asunto</th>
            <th>Variables</th>
            <th>Estado</th>
            <th>Acciones</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($templates as $t): ?>
        <tr>
            <td><strong><?= e($t['nombre']) ?></strong></td>
            <td><code><?= e($t['slug']) ?></code></td>
            <td><?= e($t['asunto']) ?></td>
            <td>
                <?php
                $vars = json_decode($t['variables'] ?? '[]', true) ?: [];
                foreach ($vars as $v):
                ?>
                    <span class="badge badge--small" style="background:#e0e7ff;color:#3730a3;margin:1px;"><?= e($v) ?></span>
                <?php endforeach; ?>
                <?php if (empty($vars)): ?>
                    <span class="text-muted">—</span>
                <?php endif; ?>
            </td>
            <td>
                <span class="status-dot status-dot--<?= $t['activo'] ? 'active' : 'inactive' ?>"></span>
                <?= $t['activo'] ? 'Activa' : 'Inactiva' ?>
            </td>
            <td class="admin-table__actions">
                <a href="<?= url("/admin/emails/{$t['id']}/preview") ?>" class="btn btn--small btn--outline" target="_blank">Preview</a>
                <a href="<?= url("/admin/emails/{$t['id']}/editar") ?>" class="btn btn--small btn--outline">Editar</a>
                <form method="POST" action="<?= url("/admin/emails/{$t['id']}/eliminar") ?>" style="display:inline"
                      onsubmit="return confirm('¿Eliminar esta plantilla?')">
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
