<?php
/**
 * Admin — Salud del Sistema
 * Variables: $dbSize, $tablas, $ultimasAcciones, $stats, $phpVersion
 */
?>

<div class="admin-page-header admin-page-header--flex">
    <div>
        <h1>Salud del Sistema</h1>
        <p class="admin-page-subtitle">Estado general de la plataforma</p>
    </div>
    <a href="<?= url('/admin/logs') ?>" class="btn btn--outline">&larr; Registro de Actividad</a>
</div>

<!-- Info cards -->
<div class="admin-stats-grid" style="margin-bottom:24px;">
    <div class="admin-stat-card">
        <div class="admin-stat-value"><?= $phpVersion ?></div>
        <div class="admin-stat-label">PHP</div>
    </div>
    <div class="admin-stat-card">
        <div class="admin-stat-value"><?= $dbSize ?> MB</div>
        <div class="admin-stat-label">Base de datos</div>
    </div>
    <div class="admin-stat-card">
        <div class="admin-stat-value"><?= $stats['fichas'] ?></div>
        <div class="admin-stat-label">Fichas</div>
    </div>
    <div class="admin-stat-card">
        <div class="admin-stat-value"><?= $stats['eventos'] ?></div>
        <div class="admin-stat-label">Eventos</div>
    </div>
    <div class="admin-stat-card">
        <div class="admin-stat-value"><?= $stats['blog_posts'] ?></div>
        <div class="admin-stat-label">Blog Posts</div>
    </div>
    <div class="admin-stat-card">
        <div class="admin-stat-value"><?= $stats['usuarios'] ?></div>
        <div class="admin-stat-label">Usuarios</div>
    </div>
    <div class="admin-stat-card">
        <div class="admin-stat-value"><?= $stats['resenas'] ?></div>
        <div class="admin-stat-label">Reseñas</div>
    </div>
    <div class="admin-stat-card">
        <div class="admin-stat-value"><?= $stats['audit_logs'] ?></div>
        <div class="admin-stat-label">Logs</div>
    </div>
</div>

<!-- Tablas de la BD -->
<div class="admin-card" style="margin-bottom:24px;">
    <h3 style="margin-bottom:12px;">Tablas de la Base de Datos</h3>
    <div class="admin-table-wrapper">
    <table class="admin-table">
        <thead>
            <tr>
                <th>Tabla</th>
                <th>Registros</th>
                <th>Tamaño</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($tablas as $t): ?>
            <tr>
                <td><code><?= e($t['table_name'] ?? $t['TABLE_NAME'] ?? '') ?></code></td>
                <td><?= number_format((int)($t['table_rows'] ?? $t['TABLE_ROWS'] ?? 0)) ?></td>
                <td><?= ($t['size_mb'] ?? $t['SIZE_MB'] ?? 0) ?> MB</td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    </div>
</div>

<!-- Últimas acciones -->
<div class="admin-card">
    <h3 style="margin-bottom:12px;">Últimas Acciones</h3>
    <?php if (empty($ultimasAcciones)): ?>
        <div class="admin-empty">No hay registros aún.</div>
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
            </tr>
        </thead>
        <tbody>
            <?php foreach ($ultimasAcciones as $log): ?>
            <tr>
                <td class="text-muted" style="white-space:nowrap;font-size:.85rem;"><?= date('d/m/Y H:i', strtotime($log['created_at'])) ?></td>
                <td><?= e($log['usuario_nombre'] ?? 'Sistema') ?></td>
                <td><?= e($log['accion']) ?></td>
                <td><?= e($log['modulo']) ?></td>
                <td><?php $d = json_decode($log['datos_despues'] ?? '{}', true); echo e($d['detalle'] ?? '—'); ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    </div>
    <?php endif; ?>
</div>
