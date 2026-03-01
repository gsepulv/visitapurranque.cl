<?php
/**
 * Admin — Formulario Banner (crear/editar) — visitapurranque.cl
 * Variables: $banner (null=crear), $posiciones, $csrf
 */
$isEdit = !empty($banner);
$v = fn(string $key, string $default = '') => e($banner[$key] ?? $default);
?>

<div class="admin-page-header">
    <h1><?= $isEdit ? 'Editar Banner' : 'Nuevo Banner' ?></h1>
    <p class="admin-page-subtitle">
        <a href="<?= url('/admin/banners') ?>">&larr; Volver al listado</a>
    </p>
</div>

<form method="POST"
      action="<?= $isEdit ? url("/admin/banners/{$banner['id']}/editar") : url('/admin/banners/crear') ?>"
      enctype="multipart/form-data"
      class="admin-form">
    <?= csrf_field() ?>

    <!-- ── Datos principales ── -->
    <fieldset class="form-fieldset">
        <legend>Datos del banner</legend>

        <div class="form-group">
            <label class="form-label" for="titulo">Título *</label>
            <input type="text" id="titulo" name="titulo" value="<?= $v('titulo') ?>"
                   class="form-input" required maxlength="200"
                   placeholder="Ej: Promoción verano 2026">
        </div>

        <div class="form-row">
            <div class="form-group">
                <label class="form-label" for="url">URL de destino</label>
                <input type="url" id="url" name="url" value="<?= $v('url') ?>"
                       class="form-input" maxlength="255"
                       placeholder="https://...">
            </div>

            <div class="form-group">
                <label class="form-label" for="posicion">Posición *</label>
                <select id="posicion" name="posicion" class="form-select" required>
                    <?php foreach ($posiciones as $val => $label): ?>
                    <option value="<?= $val ?>" <?= (($banner['posicion'] ?? 'home_top') === $val) ? 'selected' : '' ?>>
                        <?= $label ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <div class="form-row">
            <div class="form-group form-group--small">
                <label class="form-label" for="orden">Orden</label>
                <input type="number" id="orden" name="orden" value="<?= $v('orden', '0') ?>"
                       class="form-input" min="0" max="255">
            </div>

            <div class="form-group form-group--small">
                <label class="form-label" for="variante">Variante</label>
                <select id="variante" name="variante" class="form-select">
                    <option value="A" <?= (($banner['variante'] ?? 'A') === 'A') ? 'selected' : '' ?>>A</option>
                    <option value="B" <?= (($banner['variante'] ?? 'A') === 'B') ? 'selected' : '' ?>>B</option>
                </select>
            </div>
        </div>
    </fieldset>

    <!-- ── Imágenes ── -->
    <fieldset class="form-fieldset">
        <legend>Imágenes</legend>

        <div class="form-group">
            <label class="form-label" for="imagen">Imagen desktop *<?= $isEdit ? ' (actual cargada)' : '' ?></label>
            <?php if ($isEdit && !empty($banner['imagen'])): ?>
                <div class="form-preview">
                    <img src="<?= url('uploads/banners/' . $banner['imagen']) ?>"
                         alt="Banner actual" class="form-preview__img" style="max-width:400px;max-height:150px;">
                    <small>Imagen actual. Sube otra para reemplazar.</small>
                </div>
            <?php endif; ?>
            <input type="file" id="imagen" name="imagen"
                   class="form-input" accept="image/jpeg,image/png,image/webp"
                   <?= !$isEdit ? 'required' : '' ?>>
            <small class="form-help">JPG, PNG o WebP. Máximo 5 MB.</small>
        </div>

        <div class="form-group">
            <label class="form-label" for="imagen_mobile">Imagen mobile (opcional)</label>
            <?php if ($isEdit && !empty($banner['imagen_mobile'])): ?>
                <div class="form-preview">
                    <img src="<?= url('uploads/banners/' . $banner['imagen_mobile']) ?>"
                         alt="Banner mobile" class="form-preview__img" style="max-width:200px;max-height:120px;">
                    <small>Imagen mobile actual.</small>
                </div>
            <?php endif; ?>
            <input type="file" id="imagen_mobile" name="imagen_mobile"
                   class="form-input" accept="image/jpeg,image/png,image/webp">
            <small class="form-help">Versión optimizada para móviles.</small>
        </div>
    </fieldset>

    <!-- ── Vigencia ── -->
    <fieldset class="form-fieldset">
        <legend>Vigencia</legend>

        <div class="form-row">
            <div class="form-group">
                <label class="form-label" for="fecha_inicio">Fecha inicio</label>
                <input type="date" id="fecha_inicio" name="fecha_inicio"
                       value="<?= $v('fecha_inicio') ?>"
                       class="form-input">
            </div>
            <div class="form-group">
                <label class="form-label" for="fecha_fin">Fecha fin</label>
                <input type="date" id="fecha_fin" name="fecha_fin"
                       value="<?= $v('fecha_fin') ?>"
                       class="form-input">
            </div>
        </div>
        <small class="form-help">Deja vacío para un banner permanente.</small>
    </fieldset>

    <!-- ── Opciones ── -->
    <fieldset class="form-fieldset">
        <legend>Opciones</legend>

        <div class="form-checkboxes">
            <label class="form-checkbox">
                <input type="checkbox" name="activo" value="1"
                       <?= (!$isEdit || !empty($banner['activo'])) ? 'checked' : '' ?>>
                <span>Activo</span>
            </label>
        </div>
    </fieldset>

    <!-- ── Estadísticas (solo editar) ── -->
    <?php if ($isEdit): ?>
    <fieldset class="form-fieldset">
        <legend>Estadísticas</legend>

        <?php
        $ctr = $banner['impresiones'] > 0
            ? round(($banner['clics'] / $banner['impresiones']) * 100, 2)
            : 0;
        ?>

        <div class="banner-stats-grid">
            <div class="banner-stat">
                <span class="banner-stat__value"><?= number_format($banner['impresiones']) ?></span>
                <span class="banner-stat__label">Impresiones</span>
            </div>
            <div class="banner-stat">
                <span class="banner-stat__value"><?= number_format($banner['clics']) ?></span>
                <span class="banner-stat__label">Clics</span>
            </div>
            <div class="banner-stat">
                <span class="banner-stat__value"><?= $ctr ?>%</span>
                <span class="banner-stat__label">CTR</span>
            </div>
        </div>

        <?php if ($banner['impresiones'] > 0 || $banner['clics'] > 0): ?>
        <form method="POST" action="<?= url("/admin/banners/{$banner['id']}/reset-stats") ?>" class="inline-form" style="margin-top:12px;">
            <?= csrf_field() ?>
            <button type="submit" class="btn btn--small btn--outline"
                    data-confirm="¿Resetear las estadísticas de este banner?">
                Resetear estadísticas
            </button>
        </form>
        <?php endif; ?>
    </fieldset>
    <?php endif; ?>

    <!-- ── Botones ── -->
    <div class="form-actions">
        <button type="submit" class="btn btn--primary btn--lg">
            <?= $isEdit ? 'Guardar Cambios' : 'Crear Banner' ?>
        </button>
        <a href="<?= url('/admin/banners') ?>" class="btn btn--outline btn--lg">Cancelar</a>
    </div>
</form>
