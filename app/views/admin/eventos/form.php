<?php
/**
 * Admin — Formulario Evento (crear/editar) — visitapurranque.cl
 * Variables: $evento (null=crear), $categorias, $csrf
 */
$isEdit = !empty($evento);
$v = fn(string $key, string $default = '') => e($evento[$key] ?? $default);
?>

<div class="admin-page-header">
    <h1><?= $isEdit ? 'Editar Evento' : 'Nuevo Evento' ?></h1>
    <p class="admin-page-subtitle">
        <a href="<?= url('/admin/eventos') ?>">&larr; Volver al listado</a>
    </p>
</div>

<form method="POST"
      action="<?= $isEdit ? url("/admin/eventos/{$evento['id']}/editar") : url('/admin/eventos/crear') ?>"
      enctype="multipart/form-data"
      class="admin-form">
    <?= csrf_field() ?>

    <!-- ── Información básica ── -->
    <fieldset class="form-fieldset">
        <legend>Información básica</legend>

        <div class="form-group">
            <label class="form-label" for="titulo">Título *</label>
            <input type="text" id="titulo" name="titulo" value="<?= $v('titulo') ?>"
                   class="form-input" required maxlength="200"
                   placeholder="Ej: Fiesta Costumbrista de Hueyusca">
        </div>

        <div class="form-group">
            <label class="form-label" for="descripcion_corta">Descripción corta</label>
            <input type="text" id="descripcion_corta" name="descripcion_corta"
                   value="<?= $v('descripcion_corta') ?>"
                   class="form-input" maxlength="300"
                   placeholder="Resumen breve para tarjetas y listados">
        </div>

        <div class="form-group">
            <label class="form-label" for="descripcion">Descripción completa</label>
            <textarea id="descripcion" name="descripcion" class="form-textarea" rows="5"
                      placeholder="Descripción detallada del evento..."><?= $v('descripcion') ?></textarea>
        </div>

        <div class="form-group">
            <label class="form-label" for="categoria_id">Categoría</label>
            <select id="categoria_id" name="categoria_id" class="form-select">
                <option value="">Sin categoría</option>
                <?php foreach ($categorias as $cat): ?>
                <option value="<?= $cat['id'] ?>" <?= (($evento['categoria_id'] ?? '') == $cat['id']) ? 'selected' : '' ?>>
                    <?= e($cat['nombre']) ?>
                </option>
                <?php endforeach; ?>
            </select>
        </div>
    </fieldset>

    <!-- ── Fecha y hora ── -->
    <fieldset class="form-fieldset">
        <legend>Fecha y hora</legend>

        <div class="form-row">
            <div class="form-group">
                <label class="form-label" for="fecha_inicio">Fecha y hora de inicio *</label>
                <input type="datetime-local" id="fecha_inicio" name="fecha_inicio"
                       value="<?= $isEdit ? date('Y-m-d\TH:i', strtotime($evento['fecha_inicio'])) : '' ?>"
                       class="form-input" required>
            </div>
            <div class="form-group">
                <label class="form-label" for="fecha_fin">Fecha y hora de término</label>
                <input type="datetime-local" id="fecha_fin" name="fecha_fin"
                       value="<?= ($isEdit && !empty($evento['fecha_fin'])) ? date('Y-m-d\TH:i', strtotime($evento['fecha_fin'])) : '' ?>"
                       class="form-input">
            </div>
        </div>
    </fieldset>

    <!-- ── Ubicación ── -->
    <fieldset class="form-fieldset">
        <legend>Ubicación</legend>

        <div class="form-row">
            <div class="form-group form-group--wide">
                <label class="form-label" for="lugar">Lugar / Recinto</label>
                <input type="text" id="lugar" name="lugar" value="<?= $v('lugar') ?>"
                       class="form-input" maxlength="200"
                       placeholder="Ej: Medialuna Municipal de Purranque">
            </div>
        </div>

        <div class="form-group">
            <label class="form-label" for="direccion">Dirección</label>
            <input type="text" id="direccion" name="direccion" value="<?= $v('direccion') ?>"
                   class="form-input" maxlength="255"
                   placeholder="Dirección completa">
        </div>

        <div class="form-row">
            <div class="form-group">
                <label class="form-label" for="latitud">Latitud</label>
                <input type="text" id="latitud" name="latitud" value="<?= $v('latitud') ?>"
                       class="form-input" placeholder="-40.91305">
            </div>
            <div class="form-group">
                <label class="form-label" for="longitud">Longitud</label>
                <input type="text" id="longitud" name="longitud" value="<?= $v('longitud') ?>"
                       class="form-input" placeholder="-73.15913">
            </div>
        </div>
    </fieldset>

    <!-- ── Organizador y contacto ── -->
    <fieldset class="form-fieldset">
        <legend>Organizador y contacto</legend>

        <div class="form-row">
            <div class="form-group">
                <label class="form-label" for="organizador">Organizador</label>
                <input type="text" id="organizador" name="organizador" value="<?= $v('organizador') ?>"
                       class="form-input" maxlength="200"
                       placeholder="Ej: Municipalidad de Purranque">
            </div>
            <div class="form-group">
                <label class="form-label" for="contacto">Contacto</label>
                <input type="text" id="contacto" name="contacto" value="<?= $v('contacto') ?>"
                       class="form-input" maxlength="200"
                       placeholder="Teléfono o email de contacto">
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label class="form-label" for="precio">Precio / Entrada</label>
                <input type="text" id="precio" name="precio" value="<?= $v('precio') ?>"
                       class="form-input" maxlength="100"
                       placeholder="Ej: Gratis, $5.000, Colaboración voluntaria">
            </div>
            <div class="form-group">
                <label class="form-label" for="url_externa">URL externa</label>
                <input type="url" id="url_externa" name="url_externa" value="<?= $v('url_externa') ?>"
                       class="form-input" maxlength="255"
                       placeholder="https://...">
            </div>
        </div>
    </fieldset>

    <!-- ── Imagen y SEO ── -->
    <fieldset class="form-fieldset">
        <legend>Imagen y SEO</legend>

        <div class="form-group">
            <label class="form-label" for="imagen">Imagen del evento</label>
            <?php if ($isEdit && !empty($evento['imagen'])): ?>
                <div class="form-preview">
                    <img src="<?= url('uploads/eventos/' . $evento['imagen']) ?>"
                         alt="Imagen actual" class="form-preview__img">
                    <small>Imagen actual. Sube otra para reemplazar.</small>
                </div>
            <?php endif; ?>
            <input type="file" id="imagen" name="imagen"
                   class="form-input" accept="image/jpeg,image/png,image/webp">
            <small class="form-help">JPG, PNG o WebP. Máximo 5 MB.</small>
        </div>

        <div class="form-group">
            <label class="form-label" for="meta_title">Meta title</label>
            <input type="text" id="meta_title" name="meta_title" value="<?= $v('meta_title') ?>"
                   class="form-input" maxlength="160"
                   placeholder="Título para buscadores">
        </div>

        <div class="form-group">
            <label class="form-label" for="meta_description">Meta description</label>
            <textarea id="meta_description" name="meta_description" class="form-textarea" rows="2"
                      maxlength="300"
                      placeholder="Descripción para buscadores..."><?= $v('meta_description') ?></textarea>
        </div>
    </fieldset>

    <!-- ── Opciones ── -->
    <fieldset class="form-fieldset">
        <legend>Opciones</legend>

        <div class="form-checkboxes">
            <label class="form-checkbox">
                <input type="checkbox" name="activo" value="1"
                       <?= (!$isEdit || !empty($evento['activo'])) ? 'checked' : '' ?>>
                <span>Activo</span>
            </label>
            <label class="form-checkbox">
                <input type="checkbox" name="destacado" value="1"
                       <?= (!empty($evento['destacado'])) ? 'checked' : '' ?>>
                <span>Destacado</span>
            </label>
            <label class="form-checkbox">
                <input type="checkbox" name="recurrente" value="1"
                       <?= (!empty($evento['recurrente'])) ? 'checked' : '' ?>>
                <span>Evento recurrente</span>
            </label>
        </div>
    </fieldset>

    <!-- ── Botones ── -->
    <div class="form-actions">
        <button type="submit" class="btn btn--primary btn--lg">
            <?= $isEdit ? 'Guardar Cambios' : 'Crear Evento' ?>
        </button>
        <a href="<?= url('/admin/eventos') ?>" class="btn btn--outline btn--lg">Cancelar</a>
    </div>
</form>
