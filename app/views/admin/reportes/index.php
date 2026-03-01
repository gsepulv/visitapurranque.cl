<?php
/**
 * Admin — Estadísticas y Reportes — visitapurranque.cl
 * Variables: $dias, $kpis, $visitasPorDia, $topFichas, $resenasPorRating,
 *            $resenasPorMes, $bannersRendimiento, $mensajesPorMes,
 *            $actividadPorModulo, $fichasPorCategoria
 */
?>

<div class="admin-page-header admin-page-header--flex">
    <div>
        <h1>Estadísticas y Reportes</h1>
        <p class="admin-page-subtitle">Vista general del rendimiento del sitio</p>
    </div>
    <div class="admin-page-actions">
        <form class="admin-filters" method="GET" action="<?= url('/admin/estadisticas') ?>">
            <select name="dias" class="form-select" onchange="this.form.submit()">
                <option value="7"   <?= $dias === 7   ? 'selected' : '' ?>>Últimos 7 días</option>
                <option value="30"  <?= $dias === 30  ? 'selected' : '' ?>>Últimos 30 días</option>
                <option value="90"  <?= $dias === 90  ? 'selected' : '' ?>>Últimos 90 días</option>
                <option value="365" <?= $dias === 365 ? 'selected' : '' ?>>Último año</option>
            </select>
        </form>
    </div>
</div>

<!-- KPI Cards -->
<div class="report-kpi-grid">
    <div class="report-kpi">
        <span class="report-kpi__value"><?= $kpis['fichas_activas'] ?></span>
        <span class="report-kpi__label">Fichas activas</span>
        <small class="report-kpi__sub"><?= $kpis['fichas_total'] ?> total</small>
    </div>
    <div class="report-kpi">
        <span class="report-kpi__value"><?= $kpis['categorias'] ?></span>
        <span class="report-kpi__label">Categorías</span>
    </div>
    <div class="report-kpi">
        <span class="report-kpi__value"><?= $kpis['eventos_proximos'] ?></span>
        <span class="report-kpi__label">Eventos próximos</span>
    </div>
    <div class="report-kpi">
        <span class="report-kpi__value"><?= $kpis['resenas_total'] ?></span>
        <span class="report-kpi__label">Reseñas</span>
        <small class="report-kpi__sub"><?= $kpis['resenas_pendientes'] ?> pendientes</small>
    </div>
    <div class="report-kpi">
        <span class="report-kpi__value"><?= number_format($kpis['rating_promedio'], 1) ?></span>
        <span class="report-kpi__label">Rating promedio</span>
    </div>
    <div class="report-kpi">
        <span class="report-kpi__value"><?= $kpis['mensajes_total'] ?></span>
        <span class="report-kpi__label">Mensajes</span>
        <small class="report-kpi__sub"><?= $kpis['mensajes_no_leidos'] ?> sin leer</small>
    </div>
    <div class="report-kpi">
        <span class="report-kpi__value"><?= $kpis['posts_publicados'] ?></span>
        <span class="report-kpi__label">Posts blog</span>
    </div>
    <div class="report-kpi">
        <span class="report-kpi__value"><?= $kpis['suscripciones_activas'] ?></span>
        <span class="report-kpi__label">Suscripciones activas</span>
    </div>
</div>

<!-- Exportar CSV -->
<fieldset class="form-fieldset">
    <legend>Exportar datos (CSV)</legend>
    <div class="csv-export-bar">
        <a href="<?= url("/admin/estadisticas/csv?tipo=fichas&dias={$dias}") ?>" class="btn btn--outline">Fichas + vistas</a>
        <a href="<?= url("/admin/estadisticas/csv?tipo=visitas&dias={$dias}") ?>" class="btn btn--outline">Estadísticas visitas</a>
        <a href="<?= url("/admin/estadisticas/csv?tipo=resenas") ?>" class="btn btn--outline">Reseñas</a>
        <a href="<?= url("/admin/estadisticas/csv?tipo=mensajes") ?>" class="btn btn--outline">Mensajes</a>
    </div>
</fieldset>

<!-- Charts Grid -->
<div class="report-charts-grid">

    <!-- Visitas por día -->
    <div class="report-chart-card report-chart-card--full">
        <h3 class="report-chart-title">Visitas por día (últimos <?= $dias ?> días)</h3>
        <?php if (!empty($visitasPorDia)): ?>
            <canvas id="chartVisitas" height="80"></canvas>
        <?php else: ?>
            <p class="admin-empty">Sin datos de visitas en este periodo</p>
        <?php endif; ?>
    </div>

    <!-- Top fichas -->
    <div class="report-chart-card">
        <h3 class="report-chart-title">Top 10 fichas más vistas</h3>
        <?php if (!empty($topFichas)): ?>
            <canvas id="chartTopFichas" height="120"></canvas>
        <?php else: ?>
            <p class="admin-empty">Sin datos de fichas en este periodo</p>
        <?php endif; ?>
    </div>

    <!-- Fichas por categoría -->
    <div class="report-chart-card">
        <h3 class="report-chart-title">Fichas por categoría</h3>
        <?php if (!empty($fichasPorCategoria)): ?>
            <canvas id="chartCategorias" height="120"></canvas>
        <?php else: ?>
            <p class="admin-empty">Sin datos de categorías</p>
        <?php endif; ?>
    </div>

    <!-- Reseñas por rating -->
    <div class="report-chart-card">
        <h3 class="report-chart-title">Distribución de ratings</h3>
        <?php if (array_sum($resenasPorRating) > 0): ?>
            <canvas id="chartRatings" height="120"></canvas>
        <?php else: ?>
            <p class="admin-empty">Sin reseñas aprobadas</p>
        <?php endif; ?>
    </div>

    <!-- Reseñas por mes -->
    <div class="report-chart-card">
        <h3 class="report-chart-title">Reseñas por mes</h3>
        <?php if (!empty($resenasPorMes)): ?>
            <canvas id="chartResenasMes" height="120"></canvas>
        <?php else: ?>
            <p class="admin-empty">Sin reseñas en los últimos 12 meses</p>
        <?php endif; ?>
    </div>

    <!-- Mensajes por mes -->
    <div class="report-chart-card">
        <h3 class="report-chart-title">Mensajes por mes</h3>
        <?php if (!empty($mensajesPorMes)): ?>
            <canvas id="chartMensajesMes" height="120"></canvas>
        <?php else: ?>
            <p class="admin-empty">Sin mensajes en los últimos 12 meses</p>
        <?php endif; ?>
    </div>

    <!-- Actividad por módulo -->
    <div class="report-chart-card">
        <h3 class="report-chart-title">Actividad por módulo (<?= $dias ?> días)</h3>
        <?php if (!empty($actividadPorModulo)): ?>
            <canvas id="chartModulos" height="120"></canvas>
        <?php else: ?>
            <p class="admin-empty">Sin actividad registrada</p>
        <?php endif; ?>
    </div>
</div>

<!-- Banners rendimiento -->
<?php if (!empty($bannersRendimiento)): ?>
<fieldset class="form-fieldset">
    <legend>Rendimiento de banners activos</legend>
    <div class="admin-table-wrap">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>Banner</th>
                    <th>Posición</th>
                    <th>Variante</th>
                    <th class="text-right">Impresiones</th>
                    <th class="text-right">Clics</th>
                    <th class="text-right">CTR</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($bannersRendimiento as $b): ?>
                <tr>
                    <td><?= e($b['titulo']) ?></td>
                    <td><span class="badge badge--outline"><?= e($b['posicion']) ?></span></td>
                    <td><span class="badge badge--gray"><?= e($b['variante']) ?></span></td>
                    <td class="text-right text-mono"><?= number_format($b['impresiones']) ?></td>
                    <td class="text-right text-mono"><?= number_format($b['clics']) ?></td>
                    <td class="text-right text-mono">
                        <strong><?= $b['ctr'] ?>%</strong>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</fieldset>
<?php endif; ?>

<!-- Chart.js CDN -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4/dist/chart.umd.min.js"></script>
<script>
(function() {
    const colors = {
        primary: '#2563eb',
        green:   '#16a34a',
        yellow:  '#eab308',
        red:     '#dc2626',
        purple:  '#7c3aed',
        orange:  '#ea580c',
        teal:    '#0d9488',
        pink:    '#db2777',
        gray:    '#6b7280',
        blue:    '#3b82f6'
    };
    const colorArr = Object.values(colors);

    const defaultOpts = {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: { display: false }
        }
    };

    // ── Visitas por día ──
    <?php if (!empty($visitasPorDia)): ?>
    new Chart(document.getElementById('chartVisitas'), {
        type: 'line',
        data: {
            labels: <?= json_encode(array_column($visitasPorDia, 'fecha')) ?>,
            datasets: [{
                label: 'Vistas',
                data: <?= json_encode(array_map('intval', array_column($visitasPorDia, 'vistas'))) ?>,
                borderColor: colors.primary,
                backgroundColor: 'rgba(37, 99, 235, 0.1)',
                fill: true,
                tension: 0.3
            }]
        },
        options: {
            ...defaultOpts,
            plugins: { legend: { display: true, position: 'top' } },
            scales: { y: { beginAtZero: true } }
        }
    });
    <?php endif; ?>

    // ── Top Fichas ──
    <?php if (!empty($topFichas)): ?>
    new Chart(document.getElementById('chartTopFichas'), {
        type: 'bar',
        data: {
            labels: <?= json_encode(array_map(fn($f) => mb_strimwidth($f['nombre'], 0, 25, '...'), $topFichas)) ?>,
            datasets: [{
                label: 'Vistas',
                data: <?= json_encode(array_map('intval', array_column($topFichas, 'vistas'))) ?>,
                backgroundColor: colors.primary
            }, {
                label: 'Clics',
                data: <?= json_encode(array_map('intval', array_column($topFichas, 'clics'))) ?>,
                backgroundColor: colors.green
            }]
        },
        options: {
            ...defaultOpts,
            indexAxis: 'y',
            plugins: { legend: { display: true, position: 'top' } },
            scales: { x: { beginAtZero: true } }
        }
    });
    <?php endif; ?>

    // ── Fichas por categoría ──
    <?php if (!empty($fichasPorCategoria)): ?>
    new Chart(document.getElementById('chartCategorias'), {
        type: 'doughnut',
        data: {
            labels: <?= json_encode(array_column($fichasPorCategoria, 'nombre')) ?>,
            datasets: [{
                data: <?= json_encode(array_map('intval', array_column($fichasPorCategoria, 'total'))) ?>,
                backgroundColor: colorArr.slice(0, <?= count($fichasPorCategoria) ?>)
            }]
        },
        options: {
            ...defaultOpts,
            plugins: { legend: { display: true, position: 'right' } }
        }
    });
    <?php endif; ?>

    // ── Ratings ──
    <?php if (array_sum($resenasPorRating) > 0): ?>
    new Chart(document.getElementById('chartRatings'), {
        type: 'bar',
        data: {
            labels: ['1 estrella', '2 estrellas', '3 estrellas', '4 estrellas', '5 estrellas'],
            datasets: [{
                data: <?= json_encode(array_values($resenasPorRating)) ?>,
                backgroundColor: [colors.red, colors.orange, colors.yellow, colors.blue, colors.green]
            }]
        },
        options: {
            ...defaultOpts,
            scales: { y: { beginAtZero: true, ticks: { stepSize: 1 } } }
        }
    });
    <?php endif; ?>

    // ── Reseñas por mes ──
    <?php if (!empty($resenasPorMes)): ?>
    new Chart(document.getElementById('chartResenasMes'), {
        type: 'bar',
        data: {
            labels: <?= json_encode(array_column($resenasPorMes, 'mes')) ?>,
            datasets: [{
                label: 'Reseñas',
                data: <?= json_encode(array_map('intval', array_column($resenasPorMes, 'total'))) ?>,
                backgroundColor: colors.purple
            }]
        },
        options: {
            ...defaultOpts,
            plugins: { legend: { display: true, position: 'top' } },
            scales: { y: { beginAtZero: true, ticks: { stepSize: 1 } } }
        }
    });
    <?php endif; ?>

    // ── Mensajes por mes ──
    <?php if (!empty($mensajesPorMes)): ?>
    new Chart(document.getElementById('chartMensajesMes'), {
        type: 'bar',
        data: {
            labels: <?= json_encode(array_column($mensajesPorMes, 'mes')) ?>,
            datasets: [{
                label: 'Total',
                data: <?= json_encode(array_map('intval', array_column($mensajesPorMes, 'total'))) ?>,
                backgroundColor: colors.blue
            }, {
                label: 'Respondidos',
                data: <?= json_encode(array_map('intval', array_column($mensajesPorMes, 'respondidos'))) ?>,
                backgroundColor: colors.green
            }]
        },
        options: {
            ...defaultOpts,
            plugins: { legend: { display: true, position: 'top' } },
            scales: { y: { beginAtZero: true, ticks: { stepSize: 1 } } }
        }
    });
    <?php endif; ?>

    // ── Actividad por módulo ──
    <?php if (!empty($actividadPorModulo)): ?>
    new Chart(document.getElementById('chartModulos'), {
        type: 'doughnut',
        data: {
            labels: <?= json_encode(array_column($actividadPorModulo, 'modulo')) ?>,
            datasets: [{
                data: <?= json_encode(array_map('intval', array_column($actividadPorModulo, 'total'))) ?>,
                backgroundColor: colorArr.slice(0, <?= count($actividadPorModulo) ?>)
            }]
        },
        options: {
            ...defaultOpts,
            plugins: { legend: { display: true, position: 'right' } }
        }
    });
    <?php endif; ?>
})();
</script>
