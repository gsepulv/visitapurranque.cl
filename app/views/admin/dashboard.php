<?php
/**
 * Admin Dashboard — visitapurranque.cl
 * Variables: $usuario, $kpis, $actividad, $sidebarCounts
 */

$mesesEs = ['enero','febrero','marzo','abril','mayo','junio','julio','agosto','septiembre','octubre','noviembre','diciembre'];
$hoy = (int)date('j') . ' de ' . $mesesEs[(int)date('n') - 1] . ' de ' . date('Y');
?>

<div class="admin-page-header">
    <div>
        <h1>Hola, <?= e($usuario['nombre']) ?></h1>
        <p class="admin-page-date"><?= $hoy ?></p>
    </div>
</div>

<!-- KPI Cards -->
<div class="kpi-grid">
    <div class="kpi-card">
        <span class="kpi-icon kpi-icon--green">&#128205;</span>
        <div class="kpi-data">
            <span class="kpi-value"><?= $kpis['fichas'] ?></span>
            <span class="kpi-label">Fichas activas</span>
        </div>
    </div>
    <div class="kpi-card">
        <span class="kpi-icon kpi-icon--blue">&#128197;</span>
        <div class="kpi-data">
            <span class="kpi-value"><?= $kpis['eventos'] ?></span>
            <span class="kpi-label">Eventos próximos</span>
        </div>
    </div>
    <div class="kpi-card">
        <span class="kpi-icon kpi-icon--yellow">&#11088;</span>
        <div class="kpi-data">
            <span class="kpi-value"><?= $kpis['resenas'] ?></span>
            <span class="kpi-label">Reseñas pendientes</span>
        </div>
    </div>
    <div class="kpi-card">
        <span class="kpi-icon kpi-icon--red">&#128231;</span>
        <div class="kpi-data">
            <span class="kpi-value"><?= $kpis['mensajes'] ?></span>
            <span class="kpi-label">Mensajes sin leer</span>
        </div>
    </div>
    <div class="kpi-card">
        <span class="kpi-icon kpi-icon--purple">&#128065;</span>
        <div class="kpi-data">
            <span class="kpi-value"><?= $kpis['visitas'] ?></span>
            <span class="kpi-label">Visitas hoy</span>
        </div>
    </div>
    <div class="kpi-card">
        <span class="kpi-icon kpi-icon--teal">&#128221;</span>
        <div class="kpi-data">
            <span class="kpi-value"><?= $kpis['posts'] ?></span>
            <span class="kpi-label">Posts publicados</span>
        </div>
    </div>
</div>

<!-- Quick Actions -->
<div class="admin-section">
    <h2>Accesos rápidos</h2>
    <div class="quick-actions">
        <a href="<?= url('/admin/fichas/crear') ?>" class="quick-action-btn">
            <span>&#128205;</span> Nueva ficha
        </a>
        <a href="<?= url('/admin/eventos/crear') ?>" class="quick-action-btn">
            <span>&#128197;</span> Nuevo evento
        </a>
        <a href="<?= url('/admin/blog/crear') ?>" class="quick-action-btn">
            <span>&#128221;</span> Nuevo post
        </a>
    </div>
</div>

<!-- Actividad Reciente -->
<div class="admin-section">
    <h2>Actividad reciente</h2>
    <?php if (!empty($actividad)): ?>
    <div class="admin-table-wrap">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>Usuario</th>
                    <th>Acción</th>
                    <th>Módulo</th>
                    <th>IP</th>
                    <th>Fecha</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($actividad as $log): ?>
                <tr>
                    <td><?= e($log['usuario_nombre'] ?? 'Sistema') ?></td>
                    <td><span class="badge badge--action"><?= e($log['accion']) ?></span></td>
                    <td><?= e($log['modulo']) ?></td>
                    <td class="text-mono"><?= e($log['ip'] ?? '') ?></td>
                    <td><?= formatDate($log['created_at'], 'd/m/Y H:i') ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php else: ?>
    <p class="admin-empty">Sin actividad registrada aún</p>
    <?php endif; ?>
</div>
