<?php
/**
 * Admin — Estadísticas de Compartidos — visitapurranque.cl
 * Variables: $dias, $porRed, $porTipo, $porDia, $topCompartidos, $totalCompartidos
 */
?>

<div class="admin-page-header admin-page-header--flex">
    <div>
        <h1>Compartidos en Redes</h1>
        <p class="admin-page-subtitle"><?= $totalCompartidos ?> veces compartido en <?= $dias ?> días</p>
    </div>
    <div class="admin-page-actions">
        <a href="<?= url('/admin/seo') ?>" class="btn btn--outline">&larr; SEO</a>
        <form method="GET" action="<?= url('/admin/seo/compartidos') ?>">
            <select name="dias" class="form-select" onchange="this.form.submit()">
                <option value="7"   <?= $dias === 7   ? 'selected' : '' ?>>7 días</option>
                <option value="30"  <?= $dias === 30  ? 'selected' : '' ?>>30 días</option>
                <option value="90"  <?= $dias === 90  ? 'selected' : '' ?>>90 días</option>
                <option value="365" <?= $dias === 365 ? 'selected' : '' ?>>1 año</option>
            </select>
        </form>
    </div>
</div>

<?php if ($totalCompartidos > 0): ?>

<div class="report-charts-grid">
    <!-- Por red social -->
    <div class="report-chart-card">
        <h3 class="report-chart-title">Por red social</h3>
        <?php if (!empty($porRed)): ?>
            <canvas id="chartRed" height="120"></canvas>
        <?php else: ?>
            <p class="admin-empty">Sin datos</p>
        <?php endif; ?>
    </div>

    <!-- Por tipo de contenido -->
    <div class="report-chart-card">
        <h3 class="report-chart-title">Por tipo de contenido</h3>
        <?php if (!empty($porTipo)): ?>
            <canvas id="chartTipo" height="120"></canvas>
        <?php else: ?>
            <p class="admin-empty">Sin datos</p>
        <?php endif; ?>
    </div>

    <!-- Por día -->
    <div class="report-chart-card report-chart-card--full">
        <h3 class="report-chart-title">Compartidos por día</h3>
        <?php if (!empty($porDia)): ?>
            <canvas id="chartDia" height="80"></canvas>
        <?php else: ?>
            <p class="admin-empty">Sin datos</p>
        <?php endif; ?>
    </div>
</div>

<!-- Top contenido compartido -->
<?php if (!empty($topCompartidos)): ?>
<fieldset class="form-fieldset">
    <legend>Top 10 contenido más compartido</legend>
    <div class="admin-table-wrap">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Contenido</th>
                    <th>Tipo</th>
                    <th class="text-right">Compartidos</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($topCompartidos as $i => $item): ?>
                <tr>
                    <td class="text-mono"><?= $i + 1 ?></td>
                    <td><strong><?= e($item['nombre'] ?? "#{$item['registro_id']}") ?></strong></td>
                    <td><span class="badge badge--outline"><?= e($item['tipo']) ?></span></td>
                    <td class="text-right text-mono"><strong><?= $item['total'] ?></strong></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</fieldset>
<?php endif; ?>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4/dist/chart.umd.min.js"></script>
<script>
(function() {
    const colors = ['#2563eb','#16a34a','#eab308','#dc2626','#7c3aed','#ea580c','#0d9488','#db2777'];

    <?php if (!empty($porRed)): ?>
    new Chart(document.getElementById('chartRed'), {
        type: 'doughnut',
        data: {
            labels: <?= json_encode(array_column($porRed, 'red_social')) ?>,
            datasets: [{
                data: <?= json_encode(array_map('intval', array_column($porRed, 'total'))) ?>,
                backgroundColor: colors
            }]
        },
        options: { responsive: true, plugins: { legend: { position: 'right' } } }
    });
    <?php endif; ?>

    <?php if (!empty($porTipo)): ?>
    new Chart(document.getElementById('chartTipo'), {
        type: 'doughnut',
        data: {
            labels: <?= json_encode(array_column($porTipo, 'tipo')) ?>,
            datasets: [{
                data: <?= json_encode(array_map('intval', array_column($porTipo, 'total'))) ?>,
                backgroundColor: colors.slice(2)
            }]
        },
        options: { responsive: true, plugins: { legend: { position: 'right' } } }
    });
    <?php endif; ?>

    <?php if (!empty($porDia)): ?>
    new Chart(document.getElementById('chartDia'), {
        type: 'line',
        data: {
            labels: <?= json_encode(array_column($porDia, 'fecha')) ?>,
            datasets: [{
                label: 'Compartidos',
                data: <?= json_encode(array_map('intval', array_column($porDia, 'total'))) ?>,
                borderColor: '#7c3aed',
                backgroundColor: 'rgba(124, 58, 237, 0.1)',
                fill: true,
                tension: 0.3
            }]
        },
        options: {
            responsive: true,
            plugins: { legend: { display: true, position: 'top' } },
            scales: { y: { beginAtZero: true } }
        }
    });
    <?php endif; ?>
})();
</script>

<?php else: ?>
<p class="admin-empty" style="margin-top:24px">
    No se han registrado compartidos en los últimos <?= $dias ?> días.
    <br>Los compartidos se registran automáticamente cuando los visitantes usan los botones de compartir en fichas y posts.
</p>
<?php endif; ?>
