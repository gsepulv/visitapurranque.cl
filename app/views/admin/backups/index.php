<?php
/**
 * Admin — Backups
 * Variables: $backups, $ultimoCron, $driveConfigurado
 */

function formatBytes(int $bytes): string {
    if ($bytes < 1024) return $bytes . ' B';
    if ($bytes < 1048576) return round($bytes / 1024, 1) . ' KB';
    return round($bytes / 1048576, 1) . ' MB';
}
?>

<div class="admin-page-header admin-page-header--flex">
    <div>
        <h1>Backups</h1>
        <p class="admin-page-subtitle"><?= count($backups) ?> backup<?= count($backups) !== 1 ? 's' : '' ?> locales</p>
    </div>
    <form method="POST" action="<?= url('/admin/backups/crear') ?>">
        <?= csrf_field() ?>
        <button type="submit" class="btn btn--primary">Crear backup ahora</button>
    </form>
</div>

<!-- Estado del sistema -->
<div class="admin-stats-grid" style="margin-bottom:24px;">
    <div class="admin-stat-card">
        <div class="admin-stat-value"><?= count($backups) ?></div>
        <div class="admin-stat-label">Backups locales</div>
    </div>
    <div class="admin-stat-card">
        <div class="admin-stat-value" style="color:<?= $driveConfigurado ? 'var(--admin-green)' : 'var(--admin-yellow)' ?>;">
            <?= $driveConfigurado ? 'Activo' : 'No configurado' ?>
        </div>
        <div class="admin-stat-label">Google Drive</div>
    </div>
    <div class="admin-stat-card">
        <div class="admin-stat-value" style="font-size:1rem;">
            <?= $ultimoCron ? date('d/m/Y H:i', strtotime($ultimoCron['created_at'])) : '—' ?>
        </div>
        <div class="admin-stat-label">Último backup</div>
    </div>
    <div class="admin-stat-card">
        <div class="admin-stat-value" style="color:<?= ($ultimoCron['resultado'] ?? '') === 'ok' ? 'var(--admin-green)' : 'var(--admin-red)' ?>;">
            <?= $ultimoCron ? ($ultimoCron['resultado'] === 'ok' ? 'OK' : 'Error') : '—' ?>
        </div>
        <div class="admin-stat-label">Estado</div>
    </div>
</div>

<?php if (!$driveConfigurado): ?>
<div class="admin-card" style="margin-bottom:24px;border-left:3px solid var(--admin-yellow);padding:16px;">
    <strong>Google Drive no configurado.</strong>
    <p class="text-muted" style="margin-top:4px;font-size:.9rem;">
        Para habilitar backups automáticos a Google Drive, configura las variables de entorno:
        <code>GOOGLE_CLIENT_ID</code>, <code>GOOGLE_CLIENT_SECRET</code>, <code>GOOGLE_REFRESH_TOKEN</code>, <code>GOOGLE_DRIVE_FOLDER_ID</code>.
    </p>
</div>
<?php endif; ?>

<?php if (empty($backups)): ?>
    <div class="admin-empty">No hay backups locales. Crea uno con el botón de arriba.</div>
<?php else: ?>
<div class="admin-table-wrapper">
<table class="admin-table">
    <thead>
        <tr>
            <th>Archivo</th>
            <th>Tamaño</th>
            <th>Fecha</th>
            <th>Acciones</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($backups as $b): ?>
        <tr>
            <td><code><?= e($b['nombre']) ?></code></td>
            <td><?= formatBytes((int)$b['tamano']) ?></td>
            <td class="text-muted"><?= date('d/m/Y H:i', strtotime($b['fecha'])) ?></td>
            <td class="admin-table__actions">
                <a href="<?= url('/admin/backups/descargar?archivo=' . urlencode($b['nombre'])) ?>"
                   class="btn btn--small btn--outline">Descargar</a>
                <?php if ($driveConfigurado): ?>
                <form method="POST" action="<?= url('/admin/backups/subir-drive') ?>" style="display:inline">
                    <?= csrf_field() ?>
                    <input type="hidden" name="archivo" value="<?= e($b['nombre']) ?>">
                    <button type="submit" class="btn btn--small btn--outline">Subir a Drive</button>
                </form>
                <?php endif; ?>
                <form method="POST" action="<?= url('/admin/backups/eliminar') ?>" style="display:inline"
                      onsubmit="return confirm('¿Eliminar este backup?')">
                    <?= csrf_field() ?>
                    <input type="hidden" name="archivo" value="<?= e($b['nombre']) ?>">
                    <button type="submit" class="btn btn--small btn--danger">Eliminar</button>
                </form>
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>
</div>
<?php endif; ?>
