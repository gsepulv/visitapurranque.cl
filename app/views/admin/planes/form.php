<?php
/**
 * Admin — Formulario Plan (crear/editar) — visitapurranque.cl
 * Variables: $plan (null=crear), $suscripciones (solo editar), $csrf
 */
$isEdit = !empty($plan);
$v = fn(string $key, string $default = '') => e($plan[$key] ?? $default);

// Decodificar características JSON a texto (una por línea)
$caractTexto = '';
if ($isEdit && !empty($plan['caracteristicas'])) {
    $arr = json_decode($plan['caracteristicas'], true);
    if (is_array($arr)) {
        $caractTexto = implode("\n", $arr);
    }
}
?>

<div class="admin-page-header">
    <h1><?= $isEdit ? 'Editar Plan' : 'Nuevo Plan' ?></h1>
    <p class="admin-page-subtitle">
        <a href="<?= url('/admin/planes') ?>">&larr; Volver a planes</a>
    </p>
</div>

<form method="POST"
      action="<?= $isEdit ? url("/admin/planes/{$plan['id']}/editar") : url('/admin/planes/crear') ?>"
      class="admin-form">
    <?= csrf_field() ?>

    <fieldset class="form-fieldset">
        <legend>Datos del plan</legend>

        <div class="form-row">
            <div class="form-group">
                <label class="form-label" for="nombre">Nombre *</label>
                <input type="text" id="nombre" name="nombre" value="<?= $v('nombre') ?>"
                       class="form-input" required maxlength="100"
                       placeholder="Ej: Premium">
            </div>
            <div class="form-group form-group--small">
                <label class="form-label" for="orden">Orden</label>
                <input type="number" id="orden" name="orden" value="<?= $v('orden', '0') ?>"
                       class="form-input" min="0" max="255">
            </div>
        </div>

        <div class="form-group">
            <label class="form-label" for="descripcion">Descripción</label>
            <textarea id="descripcion" name="descripcion" class="form-textarea" rows="2"
                      placeholder="Descripción breve del plan..."><?= $v('descripcion') ?></textarea>
        </div>
    </fieldset>

    <fieldset class="form-fieldset">
        <legend>Precios</legend>

        <div class="form-row">
            <div class="form-group">
                <label class="form-label" for="precio_mensual">Precio mensual (CLP) *</label>
                <input type="number" id="precio_mensual" name="precio_mensual"
                       value="<?= $v('precio_mensual', '0') ?>"
                       class="form-input" min="0" step="1000"
                       placeholder="0 = Gratis">
            </div>
            <div class="form-group">
                <label class="form-label" for="precio_anual">Precio anual (CLP)</label>
                <input type="number" id="precio_anual" name="precio_anual"
                       value="<?= $v('precio_anual') ?>"
                       class="form-input" min="0" step="1000"
                       placeholder="Opcional">
            </div>
        </div>
    </fieldset>

    <fieldset class="form-fieldset">
        <legend>Características</legend>

        <div class="form-group">
            <label class="form-label" for="caracteristicas">Lista de características</label>
            <textarea id="caracteristicas" name="caracteristicas" class="form-textarea" rows="5"
                      placeholder="Una característica por línea..."><?= e($caractTexto) ?></textarea>
            <small class="form-help">Escribe una característica por línea. Se mostrará como lista en el sitio.</small>
        </div>

        <div class="form-group">
            <label class="form-label" for="max_imagenes">Máximo de imágenes por ficha</label>
            <input type="number" id="max_imagenes" name="max_imagenes"
                   value="<?= $v('max_imagenes', '5') ?>"
                   class="form-input" min="1" max="50" style="max-width:120px;">
        </div>
    </fieldset>

    <fieldset class="form-fieldset">
        <legend>Opciones</legend>

        <div class="form-checkboxes">
            <label class="form-checkbox">
                <input type="checkbox" name="activo" value="1"
                       <?= (!$isEdit || !empty($plan['activo'])) ? 'checked' : '' ?>>
                <span>Activo</span>
            </label>
            <label class="form-checkbox">
                <input type="checkbox" name="tiene_badge" value="1"
                       <?= (!empty($plan['tiene_badge'])) ? 'checked' : '' ?>>
                <span>Incluye badge premium</span>
            </label>
            <label class="form-checkbox">
                <input type="checkbox" name="destacado_home" value="1"
                       <?= (!empty($plan['destacado_home'])) ? 'checked' : '' ?>>
                <span>Destacar ficha en home</span>
            </label>
        </div>
    </fieldset>

    <?php if ($isEdit): ?>
    <fieldset class="form-fieldset">
        <legend>Info</legend>
        <p class="form-help">
            Suscripciones activas con este plan: <strong><?= $suscripciones ?? 0 ?></strong>
            &nbsp;·&nbsp; Slug: <code><?= e($plan['slug']) ?></code>
        </p>
    </fieldset>
    <?php endif; ?>

    <div class="form-actions">
        <button type="submit" class="btn btn--primary btn--lg">
            <?= $isEdit ? 'Guardar Cambios' : 'Crear Plan' ?>
        </button>
        <a href="<?= url('/admin/planes') ?>" class="btn btn--outline btn--lg">Cancelar</a>
    </div>
</form>
