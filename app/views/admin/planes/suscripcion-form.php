<?php
/**
 * Admin — Formulario Suscripción (crear/editar) — visitapurranque.cl
 * Variables: $suscripcion (null=crear), $planes, $fichas, $csrf
 */
$isEdit = !empty($suscripcion);
$v = fn(string $key, string $default = '') => e($suscripcion[$key] ?? $default);
?>

<div class="admin-page-header">
    <h1><?= $isEdit ? 'Editar Suscripción #' . $suscripcion['id'] : 'Nueva Suscripción' ?></h1>
    <p class="admin-page-subtitle">
        <a href="<?= url('/admin/suscripciones') ?>">&larr; Volver a suscripciones</a>
    </p>
</div>

<form method="POST"
      action="<?= $isEdit ? url("/admin/suscripciones/{$suscripcion['id']}/editar") : url('/admin/suscripciones/crear') ?>"
      class="admin-form">
    <?= csrf_field() ?>

    <fieldset class="form-fieldset">
        <legend>Datos de la suscripción</legend>

        <div class="form-row">
            <div class="form-group">
                <label class="form-label" for="ficha_id">Ficha / Comercio *</label>
                <select id="ficha_id" name="ficha_id" class="form-select" required>
                    <option value="">Seleccionar ficha...</option>
                    <?php foreach ($fichas as $f): ?>
                    <option value="<?= $f['id'] ?>" <?= (($suscripcion['ficha_id'] ?? '') == $f['id']) ? 'selected' : '' ?>>
                        <?= e($f['nombre']) ?>
                    </option>
                    <?php endforeach; ?>
                </select>
                <?php if (empty($fichas)): ?>
                    <small class="form-help" style="color:var(--admin-red)">No hay fichas disponibles. Crea una ficha primero.</small>
                <?php endif; ?>
            </div>

            <div class="form-group">
                <label class="form-label" for="plan_id">Plan *</label>
                <select id="plan_id" name="plan_id" class="form-select" required>
                    <option value="">Seleccionar plan...</option>
                    <?php foreach ($planes as $p): ?>
                    <option value="<?= $p['id'] ?>" <?= (($suscripcion['plan_id'] ?? '') == $p['id']) ? 'selected' : '' ?>
                            data-precio="<?= $p['precio_mensual'] ?>">
                        <?= e($p['nombre']) ?> — $<?= number_format($p['precio_mensual'], 0, ',', '.') ?>/mes
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label class="form-label" for="fecha_inicio">Fecha inicio *</label>
                <input type="date" id="fecha_inicio" name="fecha_inicio"
                       value="<?= $v('fecha_inicio', date('Y-m-d')) ?>"
                       class="form-input" required>
            </div>
            <div class="form-group">
                <label class="form-label" for="fecha_fin">Fecha fin *</label>
                <input type="date" id="fecha_fin" name="fecha_fin"
                       value="<?= $v('fecha_fin', date('Y-m-d', strtotime('+1 month'))) ?>"
                       class="form-input" required>
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label class="form-label" for="monto">Monto cobrado (CLP) *</label>
                <input type="number" id="monto" name="monto"
                       value="<?= $v('monto', '0') ?>"
                       class="form-input" min="0" step="1000">
            </div>
            <div class="form-group">
                <label class="form-label" for="estado">Estado *</label>
                <select id="estado" name="estado" class="form-select" required>
                    <?php
                    $estados = [
                        'pendiente' => 'Pendiente',
                        'activa'    => 'Activa',
                        'expirada'  => 'Expirada',
                        'cancelada' => 'Cancelada',
                    ];
                    foreach ($estados as $val => $label):
                    ?>
                    <option value="<?= $val ?>" <?= (($suscripcion['estado'] ?? 'pendiente') === $val) ? 'selected' : '' ?>>
                        <?= $label ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <div class="form-group">
            <label class="form-label" for="notas">Notas internas</label>
            <textarea id="notas" name="notas" class="form-textarea" rows="3"
                      placeholder="Notas sobre el pago, acuerdos, etc..."><?= $v('notas') ?></textarea>
        </div>
    </fieldset>

    <div class="form-actions">
        <button type="submit" class="btn btn--primary btn--lg">
            <?= $isEdit ? 'Guardar Cambios' : 'Crear Suscripción' ?>
        </button>
        <a href="<?= url('/admin/suscripciones') ?>" class="btn btn--outline btn--lg">Cancelar</a>
    </div>
</form>
