<?php
/**
 * Admin — Formulario Ficha (crear/editar) — visitapurranque.cl
 * Variables: $ficha (null=crear), $categorias, $subcategorias, $planes, $csrf
 */
$isEdit = !empty($ficha);
$v = fn(string $key, string $default = '') => e($ficha[$key] ?? $default);
?>

<div class="admin-page-header">
    <h1><?= $isEdit ? 'Editar Ficha' : 'Nueva Ficha' ?></h1>
    <p class="admin-page-subtitle">
        <a href="<?= url('/admin/fichas') ?>">&larr; Volver al listado</a>
    </p>
</div>

<form method="POST"
      action="<?= $isEdit ? url("/admin/fichas/{$ficha['id']}/editar") : url('/admin/fichas/crear') ?>"
      enctype="multipart/form-data"
      class="admin-form">
    <?= csrf_field() ?>

    <!-- ── Información básica ── -->
    <fieldset class="form-fieldset">
        <legend>Información básica</legend>

        <div class="form-row">
            <div class="form-group form-group--wide">
                <label class="form-label" for="nombre">Nombre *</label>
                <input type="text" id="nombre" name="nombre" value="<?= $v('nombre') ?>"
                       class="form-input" required maxlength="200"
                       placeholder="Ej: Salto del Pilmaiquén">
            </div>

            <div class="form-group">
                <label class="form-label" for="categoria_id">Categoría *</label>
                <select id="categoria_id" name="categoria_id" class="form-select" required>
                    <option value="">Seleccionar...</option>
                    <?php foreach ($categorias as $cat): ?>
                    <option value="<?= $cat['id'] ?>" <?= (($ficha['categoria_id'] ?? '') == $cat['id']) ? 'selected' : '' ?>>
                        <?= e($cat['nombre']) ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label class="form-label" for="subcategoria_id">Subcategoría</label>
                <select id="subcategoria_id" name="subcategoria_id" class="form-select">
                    <option value="">Ninguna</option>
                    <?php foreach ($subcategorias as $sub): ?>
                    <option value="<?= $sub['id'] ?>"
                            data-categoria="<?= $sub['categoria_id'] ?>"
                            <?= (($ficha['subcategoria_id'] ?? '') == $sub['id']) ? 'selected' : '' ?>>
                        <?= e($sub['nombre']) ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <div class="form-group">
            <label class="form-label" for="descripcion_corta">Descripción corta</label>
            <input type="text" id="descripcion_corta" name="descripcion_corta"
                   value="<?= $v('descripcion_corta') ?>"
                   class="form-input" maxlength="300"
                   placeholder="Resumen de 1-2 líneas para tarjetas y listados">
        </div>

        <div class="form-group">
            <label class="form-label" for="descripcion">Descripción completa</label>
            <textarea id="descripcion" name="descripcion" class="form-textarea" rows="6"
                      placeholder="Descripción detallada del atractivo..."><?= $v('descripcion') ?></textarea>
        </div>
    </fieldset>

    <!-- ── Ubicación ── -->
    <fieldset class="form-fieldset">
        <legend>Ubicación</legend>

        <div class="form-group">
            <label class="form-label" for="direccion">Dirección</label>
            <input type="text" id="direccion" name="direccion" value="<?= $v('direccion') ?>"
                   class="form-input" maxlength="255"
                   placeholder="Ej: Camino a Pucatrihue km 12, Purranque">
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

        <div class="form-group">
            <label class="form-label" for="como_llegar">Cómo llegar</label>
            <textarea id="como_llegar" name="como_llegar" class="form-textarea" rows="3"
                      placeholder="Indicaciones de acceso..."><?= $v('como_llegar') ?></textarea>
        </div>
    </fieldset>

    <!-- ── Contacto y redes ── -->
    <fieldset class="form-fieldset">
        <legend>Contacto y redes sociales</legend>

        <div class="form-row">
            <div class="form-group">
                <label class="form-label" for="telefono">Teléfono</label>
                <input type="tel" id="telefono" name="telefono" value="<?= $v('telefono') ?>"
                       class="form-input" maxlength="20" placeholder="+56 9 1234 5678">
            </div>
            <div class="form-group">
                <label class="form-label" for="whatsapp">WhatsApp</label>
                <input type="tel" id="whatsapp" name="whatsapp" value="<?= $v('whatsapp') ?>"
                       class="form-input" maxlength="20" placeholder="+56912345678">
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label class="form-label" for="email">Email</label>
                <input type="email" id="email" name="email" value="<?= $v('email') ?>"
                       class="form-input" maxlength="200">
            </div>
            <div class="form-group">
                <label class="form-label" for="sitio_web">Sitio web</label>
                <input type="url" id="sitio_web" name="sitio_web" value="<?= $v('sitio_web') ?>"
                       class="form-input" maxlength="255" placeholder="https://...">
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label class="form-label" for="instagram">Instagram</label>
                <input type="text" id="instagram" name="instagram" value="<?= $v('instagram') ?>"
                       class="form-input" maxlength="255" placeholder="@usuario o URL">
            </div>
            <div class="form-group">
                <label class="form-label" for="facebook">Facebook</label>
                <input type="text" id="facebook" name="facebook" value="<?= $v('facebook') ?>"
                       class="form-input" maxlength="255" placeholder="URL de la página">
            </div>
        </div>
    </fieldset>

    <!-- ── Información práctica ── -->
    <fieldset class="form-fieldset">
        <legend>Información práctica</legend>

        <div class="form-group">
            <label class="form-label" for="horarios">Horarios</label>
            <input type="text" id="horarios" name="horarios" value="<?= $v('horarios') ?>"
                   class="form-input" placeholder="Ej: Lunes a Viernes 9:00 - 18:00">
        </div>

        <div class="form-row">
            <div class="form-group">
                <label class="form-label" for="temporada">Temporada</label>
                <input type="text" id="temporada" name="temporada" value="<?= $v('temporada') ?>"
                       class="form-input" maxlength="100" placeholder="Ej: Todo el año, Verano">
            </div>
            <div class="form-group">
                <label class="form-label" for="dificultad">Dificultad</label>
                <select id="dificultad" name="dificultad" class="form-select">
                    <option value="">No aplica</option>
                    <option value="facil" <?= (($ficha['dificultad'] ?? '') === 'facil') ? 'selected' : '' ?>>Fácil</option>
                    <option value="moderada" <?= (($ficha['dificultad'] ?? '') === 'moderada') ? 'selected' : '' ?>>Moderada</option>
                    <option value="dificil" <?= (($ficha['dificultad'] ?? '') === 'dificil') ? 'selected' : '' ?>>Difícil</option>
                    <option value="experto" <?= (($ficha['dificultad'] ?? '') === 'experto') ? 'selected' : '' ?>>Experto</option>
                </select>
            </div>
            <div class="form-group">
                <label class="form-label" for="duracion_estimada">Duración estimada</label>
                <input type="text" id="duracion_estimada" name="duracion_estimada"
                       value="<?= $v('duracion_estimada') ?>"
                       class="form-input" maxlength="50" placeholder="Ej: 2 horas">
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label class="form-label" for="precio_desde">Precio desde ($)</label>
                <input type="number" id="precio_desde" name="precio_desde"
                       value="<?= $v('precio_desde') ?>"
                       class="form-input" min="0">
            </div>
            <div class="form-group">
                <label class="form-label" for="precio_hasta">Precio hasta ($)</label>
                <input type="number" id="precio_hasta" name="precio_hasta"
                       value="<?= $v('precio_hasta') ?>"
                       class="form-input" min="0">
            </div>
            <div class="form-group">
                <label class="form-label" for="precio_texto">Precio (texto)</label>
                <input type="text" id="precio_texto" name="precio_texto"
                       value="<?= $v('precio_texto') ?>"
                       class="form-input" maxlength="100" placeholder="Ej: Gratis, Desde $5.000">
            </div>
        </div>

        <div class="form-group">
            <label class="form-label" for="que_llevar">Qué llevar</label>
            <textarea id="que_llevar" name="que_llevar" class="form-textarea" rows="3"
                      placeholder="Recomendaciones de equipamiento..."><?= $v('que_llevar') ?></textarea>
        </div>

        <div class="form-group">
            <label class="form-label" for="info_practica">Información práctica adicional</label>
            <textarea id="info_practica" name="info_practica" class="form-textarea" rows="3"
                      placeholder="Otros datos útiles para el visitante..."><?= $v('info_practica') ?></textarea>
        </div>
    </fieldset>

    <!-- ── Imagen y SEO ── -->
    <fieldset class="form-fieldset">
        <legend>Imagen y SEO</legend>

        <div class="form-group">
            <label class="form-label" for="imagen_portada">Imagen de portada</label>
            <?php if ($isEdit && !empty($ficha['imagen_portada'])): ?>
                <div class="form-preview">
                    <img src="<?= url('uploads/fichas/' . $ficha['imagen_portada']) ?>"
                         alt="Portada actual" class="form-preview__img">
                    <small>Imagen actual. Sube otra para reemplazar.</small>
                </div>
            <?php endif; ?>
            <input type="file" id="imagen_portada" name="imagen_portada"
                   class="form-input" accept="image/jpeg,image/png,image/webp">
            <small class="form-help">JPG, PNG o WebP. Máximo 5 MB.</small>
        </div>

        <div class="form-group">
            <label class="form-label" for="meta_title">Meta title</label>
            <input type="text" id="meta_title" name="meta_title" value="<?= $v('meta_title') ?>"
                   class="form-input" maxlength="160"
                   placeholder="Título para buscadores (se genera automáticamente si se deja vacío)">
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

        <div class="form-group">
            <label class="form-label" for="plan_id">Plan</label>
            <select id="plan_id" name="plan_id" class="form-select">
                <option value="">Sin plan</option>
                <?php foreach ($planes as $plan): ?>
                <option value="<?= $plan['id'] ?>" <?= (($ficha['plan_id'] ?? '') == $plan['id']) ? 'selected' : '' ?>>
                    <?= e($plan['nombre']) ?>
                </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="form-checkboxes">
            <label class="form-checkbox">
                <input type="checkbox" name="activo" value="1"
                       <?= (!$isEdit || !empty($ficha['activo'])) ? 'checked' : '' ?>>
                <span>Activo</span>
            </label>
            <label class="form-checkbox">
                <input type="checkbox" name="verificado" value="1"
                       <?= (!empty($ficha['verificado'])) ? 'checked' : '' ?>>
                <span>Verificado</span>
            </label>
            <label class="form-checkbox">
                <input type="checkbox" name="destacado" value="1"
                       <?= (!empty($ficha['destacado'])) ? 'checked' : '' ?>>
                <span>Destacado</span>
            </label>
            <label class="form-checkbox">
                <input type="checkbox" name="imperdible" value="1"
                       <?= (!empty($ficha['imperdible'])) ? 'checked' : '' ?>>
                <span>Imperdible</span>
            </label>
        </div>
    </fieldset>

    <!-- ── Botones ── -->
    <div class="form-actions">
        <button type="submit" class="btn btn--primary btn--lg">
            <?= $isEdit ? 'Guardar Cambios' : 'Crear Ficha' ?>
        </button>
        <a href="<?= url('/admin/fichas') ?>" class="btn btn--outline btn--lg">Cancelar</a>
    </div>
</form>

<script>
// Filtrar subcategorías según categoría seleccionada
document.getElementById('categoria_id').addEventListener('change', function() {
    var catId = this.value;
    var subSelect = document.getElementById('subcategoria_id');
    var options = subSelect.querySelectorAll('option[data-categoria]');
    options.forEach(function(opt) {
        opt.style.display = (!catId || opt.getAttribute('data-categoria') === catId) ? '' : 'none';
        if (opt.style.display === 'none' && opt.selected) {
            subSelect.value = '';
        }
    });
});
// Disparar al cargar para filtrar si hay categoría seleccionada
document.getElementById('categoria_id').dispatchEvent(new Event('change'));
</script>
