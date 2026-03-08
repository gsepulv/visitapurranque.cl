<?php
/**
 * Admin — Listado de Usuarios
 * Variables: $usuarios, $usuario (logueado), $csrf
 */
$rolColors = [
    'Administrador' => '#ef4444',
    'Editor'        => '#3b82f6',
    'Colaborador'   => '#f59e0b',
    'Visitante'     => '#6b7280',
];
?>

<div class="admin-page-header admin-page-header--flex">
    <div>
        <h1>Usuarios</h1>
        <p class="admin-page-subtitle"><?= count($usuarios) ?> usuario<?= count($usuarios) !== 1 ? 's' : '' ?> registrados</p>
    </div>
    <a href="<?= url('/admin/usuarios/crear') ?>" class="btn btn--primary">+ Nuevo Usuario</a>
</div>

<div class="admin-table-wrapper">
<table class="admin-table">
    <thead>
        <tr>
            <th>Nombre</th>
            <th>Email</th>
            <th>Rol</th>
            <th>Estado</th>
            <th>Último login</th>
            <th>Acciones</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($usuarios as $u): ?>
        <tr>
            <td>
                <strong><?= e($u['nombre']) ?></strong>
                <?php if ((int)$u['id'] === (int)$usuario['id']): ?>
                    <span class="badge badge--small" style="background:#22c55e;color:#fff;margin-left:4px;">Tú</span>
                <?php endif; ?>
            </td>
            <td><?= e($u['email']) ?></td>
            <td>
                <span class="badge badge--small" style="background:<?= $rolColors[$u['rol_nombre']] ?? '#6b7280' ?>;color:#fff;">
                    <?= e($u['rol_nombre']) ?>
                </span>
            </td>
            <td>
                <?php if ($u['activo']): ?>
                    <span class="status-dot status-dot--active"></span> Activo
                <?php else: ?>
                    <span class="status-dot status-dot--inactive"></span> Inactivo
                <?php endif; ?>
            </td>
            <td>
                <?= $u['ultimo_login'] ? date('d/m/Y H:i', strtotime($u['ultimo_login'])) : '<span class="text-muted">Nunca</span>' ?>
            </td>
            <td class="admin-table__actions">
                <a href="<?= url("/admin/usuarios/{$u['id']}/editar") ?>" class="btn btn--small">Editar</a>

                <?php if ((int)$u['id'] !== (int)$usuario['id']): ?>
                <form method="POST" action="<?= url("/admin/usuarios/{$u['id']}/toggle") ?>" style="display:inline">
                    <?= csrf_field() ?>
                    <button type="submit" class="btn btn--small btn--outline">
                        <?= $u['activo'] ? 'Desactivar' : 'Activar' ?>
                    </button>
                </form>
                <form method="POST" action="<?= url("/admin/usuarios/{$u['id']}/eliminar") ?>" style="display:inline"
                      onsubmit="return confirm('¿Eliminar al usuario <?= e($u['nombre']) ?>? Esta acción no se puede deshacer.')">
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
