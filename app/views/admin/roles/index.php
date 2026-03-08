<?php
/**
 * Admin — Roles y Permisos
 * Variables: $roles, $csrf
 */
?>

<div class="admin-page-header admin-page-header--flex">
    <div>
        <h1>Roles y Permisos</h1>
        <p class="admin-page-subtitle"><?= count($roles) ?> roles definidos</p>
    </div>
    <a href="<?= url('/admin/roles/crear') ?>" class="btn btn--primary">+ Nuevo Rol</a>
</div>

<div class="admin-table-wrapper">
<table class="admin-table">
    <thead>
        <tr>
            <th>Rol</th>
            <th>Descripción</th>
            <th>Permisos</th>
            <th>Usuarios</th>
            <th>Acciones</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($roles as $r): ?>
        <tr>
            <td><strong><?= e($r['nombre']) ?></strong></td>
            <td class="text-muted"><?= e($r['descripcion'] ?? '') ?></td>
            <td><span class="badge badge--small" style="background:#3b82f6;color:#fff;"><?= (int)$r['total_permisos'] ?></span></td>
            <td><?= (int)$r['total_usuarios'] ?></td>
            <td class="admin-table__actions">
                <a href="<?= url("/admin/roles/{$r['id']}/editar") ?>" class="btn btn--small">Editar</a>
                <?php if ($r['slug'] !== 'admin' && (int)$r['total_usuarios'] === 0): ?>
                <form method="POST" action="<?= url("/admin/roles/{$r['id']}/eliminar") ?>" style="display:inline"
                      onsubmit="return confirm('¿Eliminar el rol <?= e($r['nombre']) ?>?')">
                    <?= csrf_field() ?>
                    <button type="submit" class="btn btn--small btn--danger">Eliminar</button>
                </form>
                <?php endif; ?>
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>
</div>
