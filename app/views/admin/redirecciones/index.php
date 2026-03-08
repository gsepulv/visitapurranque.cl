<?php
/**
 * Admin — Redirecciones
 * Variables: $redirecciones, $csrf
 */
?>

<div class="admin-page-header">
    <h1>Redirecciones</h1>
    <p class="admin-page-subtitle"><?= count($redirecciones) ?> redirección<?= count($redirecciones) !== 1 ? 'es' : '' ?> configuradas</p>
</div>

<!-- Formulario inline para agregar -->
<div class="admin-card" style="margin-bottom: 24px;">
    <form method="POST" action="<?= url('/admin/redirecciones/crear') ?>" class="admin-form">
        <?= csrf_field() ?>
        <div style="display:flex; gap:10px; align-items:flex-end; flex-wrap:wrap;">
            <div class="form-group" style="flex:1; min-width:200px;">
                <label class="form-label">URL origen</label>
                <input type="text" name="url_origen" class="form-input" placeholder="/ruta-antigua" required>
            </div>
            <div class="form-group" style="flex:1; min-width:200px;">
                <label class="form-label">URL destino</label>
                <input type="text" name="url_destino" class="form-input" placeholder="/ruta-nueva o https://..." required>
            </div>
            <div class="form-group" style="width:100px;">
                <label class="form-label">Tipo</label>
                <select name="tipo" class="form-select">
                    <option value="301">301</option>
                    <option value="302">302</option>
                </select>
            </div>
            <button type="submit" class="btn btn--primary" style="margin-bottom:16px;">Agregar</button>
        </div>
    </form>
</div>

<?php if (empty($redirecciones)): ?>
    <div class="admin-empty">No hay redirecciones configuradas.</div>
<?php else: ?>
<div class="admin-table-wrapper">
<table class="admin-table">
    <thead>
        <tr>
            <th>Origen</th>
            <th>Destino</th>
            <th>Tipo</th>
            <th>Hits</th>
            <th>Estado</th>
            <th>Acciones</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($redirecciones as $r): ?>
        <tr>
            <td><code><?= e($r['url_origen']) ?></code></td>
            <td><code><?= e($r['url_destino']) ?></code></td>
            <td><span class="badge badge--small" style="background:<?= $r['tipo'] == 301 ? '#3b82f6' : '#f59e0b' ?>;color:#fff;"><?= $r['tipo'] ?></span></td>
            <td><?= (int)$r['hits'] ?></td>
            <td>
                <span class="status-dot status-dot--<?= $r['activo'] ? 'active' : 'inactive' ?>"></span>
                <?= $r['activo'] ? 'Activa' : 'Inactiva' ?>
            </td>
            <td class="admin-table__actions">
                <form method="POST" action="<?= url("/admin/redirecciones/{$r['id']}/toggle") ?>" style="display:inline">
                    <?= csrf_field() ?>
                    <button type="submit" class="btn btn--small btn--outline"><?= $r['activo'] ? 'Desactivar' : 'Activar' ?></button>
                </form>
                <form method="POST" action="<?= url("/admin/redirecciones/{$r['id']}/eliminar") ?>" style="display:inline"
                      onsubmit="return confirm('¿Eliminar esta redirección?')">
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
