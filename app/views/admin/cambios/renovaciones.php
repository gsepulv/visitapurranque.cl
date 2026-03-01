<?php
/**
 * Admin — Renovaciones de Suscripciones — visitapurranque.cl
 * Variables: $dias, $proximasVencer, $expiradas, $stats, $ingresosMes, $ingresosTotales, $csrf
 */

$mesesEs = ['ene','feb','mar','abr','may','jun','jul','ago','sep','oct','nov','dic'];
?>

<div class="admin-page-header admin-page-header--flex">
    <div>
        <h1>Renovaciones</h1>
        <p class="admin-page-subtitle">Gestión de vencimientos y renovaciones de suscripciones</p>
    </div>
    <div class="admin-page-actions">
        <a href="<?= url('/admin/cambios') ?>" class="btn btn--outline">Ver cambios pendientes</a>
        <a href="<?= url('/admin/suscripciones') ?>" class="btn btn--outline">Todas las suscripciones</a>
    </div>
</div>

<!-- KPIs -->
<div class="report-kpi-grid">
    <div class="report-kpi">
        <span class="report-kpi__value"><?= $stats['activa'] ?></span>
        <span class="report-kpi__label">Activas</span>
    </div>
    <div class="report-kpi">
        <span class="report-kpi__value"><?= $stats['pendiente'] ?></span>
        <span class="report-kpi__label">Pendientes</span>
    </div>
    <div class="report-kpi">
        <span class="report-kpi__value renov-kpi--warning"><?= count($proximasVencer) ?></span>
        <span class="report-kpi__label">Por vencer (<?= $dias ?>d)</span>
    </div>
    <div class="report-kpi">
        <span class="report-kpi__value renov-kpi--danger"><?= count($expiradas) ?></span>
        <span class="report-kpi__label">Vencidas sin renovar</span>
    </div>
    <div class="report-kpi">
        <span class="report-kpi__value">$<?= number_format($ingresosMes, 0, ',', '.') ?></span>
        <span class="report-kpi__label">Ingresos este mes</span>
    </div>
    <div class="report-kpi">
        <span class="report-kpi__value">$<?= number_format($ingresosTotales, 0, ',', '.') ?></span>
        <span class="report-kpi__label">Total activo</span>
    </div>
</div>

<!-- Filtro periodo -->
<form class="admin-filters" method="GET" action="<?= url('/admin/renovaciones') ?>" style="margin-bottom:20px">
    <div class="admin-filters__group">
        <label class="form-label" style="margin:0;line-height:36px">Ventana de vencimiento:</label>
        <select name="dias" class="form-select" onchange="this.form.submit()">
            <option value="7"   <?= $dias === 7   ? 'selected' : '' ?>>7 días</option>
            <option value="15"  <?= $dias === 15  ? 'selected' : '' ?>>15 días</option>
            <option value="30"  <?= $dias === 30  ? 'selected' : '' ?>>30 días</option>
            <option value="60"  <?= $dias === 60  ? 'selected' : '' ?>>60 días</option>
            <option value="90"  <?= $dias === 90  ? 'selected' : '' ?>>90 días</option>
        </select>
    </div>
</form>

<!-- Expiradas (urgente) -->
<?php if (!empty($expiradas)): ?>
<fieldset class="form-fieldset renov-section--danger">
    <legend>Vencidas sin renovar (<?= count($expiradas) ?>)</legend>
    <div class="admin-table-wrap">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>Ficha</th>
                    <th>Plan</th>
                    <th>Venció</th>
                    <th>Días vencida</th>
                    <th>Monto</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($expiradas as $s): ?>
                <tr>
                    <td><strong><?= e($s['ficha_nombre']) ?></strong></td>
                    <td><span class="badge badge--outline"><?= e($s['plan_nombre']) ?></span></td>
                    <td class="text-mono"><?= formatDate($s['fecha_fin'], 'd/m/Y') ?></td>
                    <td>
                        <span class="badge badge--red"><?= $s['dias_expirada'] ?> día<?= $s['dias_expirada'] != 1 ? 's' : '' ?></span>
                    </td>
                    <td class="text-mono">$<?= number_format($s['monto'], 0, ',', '.') ?></td>
                    <td>
                        <div class="table-actions">
                            <form method="POST" action="<?= url("/admin/renovaciones/{$s['id']}/renovar") ?>" class="inline-form renov-form">
                                <?= csrf_field() ?>
                                <select name="meses" class="form-select form-select--mini">
                                    <option value="1">1 mes</option>
                                    <option value="3">3 meses</option>
                                    <option value="6">6 meses</option>
                                    <option value="12" selected>12 meses</option>
                                </select>
                                <button type="submit" class="btn btn--small btn--success"
                                        data-confirm="¿Renovar esta suscripción?">Renovar</button>
                            </form>
                            <form method="POST" action="<?= url("/admin/renovaciones/{$s['id']}/expirar") ?>" class="inline-form">
                                <?= csrf_field() ?>
                                <button type="submit" class="btn btn--small btn--danger"
                                        data-confirm="¿Marcar como expirada? Se quitará el plan de la ficha.">Expirar</button>
                            </form>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</fieldset>
<?php endif; ?>

<!-- Próximas a vencer -->
<?php if (!empty($proximasVencer)): ?>
<fieldset class="form-fieldset renov-section--warning">
    <legend>Próximas a vencer en <?= $dias ?> días (<?= count($proximasVencer) ?>)</legend>
    <div class="admin-table-wrap">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>Ficha</th>
                    <th>Plan</th>
                    <th>Vence</th>
                    <th>Días restantes</th>
                    <th>Monto</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($proximasVencer as $s): ?>
                <?php $diasRestantes = (int)((strtotime($s['fecha_fin']) - time()) / 86400); ?>
                <tr>
                    <td><strong><?= e($s['ficha_nombre']) ?></strong></td>
                    <td><span class="badge badge--outline"><?= e($s['plan_nombre']) ?></span></td>
                    <td class="text-mono"><?= formatDate($s['fecha_fin'], 'd/m/Y') ?></td>
                    <td>
                        <?php if ($diasRestantes <= 7): ?>
                            <span class="badge badge--red"><?= $diasRestantes ?> día<?= $diasRestantes != 1 ? 's' : '' ?></span>
                        <?php elseif ($diasRestantes <= 15): ?>
                            <span class="badge badge--yellow"><?= $diasRestantes ?> días</span>
                        <?php else: ?>
                            <span class="badge badge--gray"><?= $diasRestantes ?> días</span>
                        <?php endif; ?>
                    </td>
                    <td class="text-mono">$<?= number_format($s['monto'], 0, ',', '.') ?></td>
                    <td>
                        <div class="table-actions">
                            <form method="POST" action="<?= url("/admin/renovaciones/{$s['id']}/renovar") ?>" class="inline-form renov-form">
                                <?= csrf_field() ?>
                                <select name="meses" class="form-select form-select--mini">
                                    <option value="1">1 mes</option>
                                    <option value="3">3 meses</option>
                                    <option value="6">6 meses</option>
                                    <option value="12" selected>12 meses</option>
                                </select>
                                <button type="submit" class="btn btn--small btn--success"
                                        data-confirm="¿Renovar esta suscripción?">Renovar</button>
                            </form>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</fieldset>
<?php else: ?>
<fieldset class="form-fieldset">
    <legend>Próximas a vencer</legend>
    <p class="admin-empty">No hay suscripciones por vencer en los próximos <?= $dias ?> días.</p>
</fieldset>
<?php endif; ?>

<?php if (empty($expiradas) && empty($proximasVencer)): ?>
<p class="admin-empty" style="margin-top:20px">Todo al día. No hay renovaciones pendientes ni suscripciones vencidas.</p>
<?php endif; ?>
