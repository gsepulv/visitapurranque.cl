<?php
/**
 * Admin — Formulario Post (crear/editar) — visitapurranque.cl
 * Variables: $post (null=crear), $categorias, $autores, $csrf
 */
$isEdit = !empty($post);
$v = fn(string $key, string $default = '') => e($post[$key] ?? $default);
?>

<div class="admin-page-header">
    <h1><?= $isEdit ? 'Editar Post' : 'Nuevo Post' ?></h1>
    <p class="admin-page-subtitle">
        <a href="<?= url('/admin/blog') ?>">&larr; Volver al listado</a>
    </p>
</div>

<form method="POST"
      action="<?= $isEdit ? url("/admin/blog/{$post['id']}/editar") : url('/admin/blog/crear') ?>"
      enctype="multipart/form-data"
      class="admin-form">
    <?= csrf_field() ?>

    <!-- ── Contenido ── -->
    <fieldset class="form-fieldset">
        <legend>Contenido</legend>

        <div class="form-group">
            <label class="form-label" for="titulo">Título *</label>
            <input type="text" id="titulo" name="titulo" value="<?= $v('titulo') ?>"
                   class="form-input" required maxlength="300"
                   placeholder="Ej: Fiesta Costumbrista de Hueyusca reunió a más de 3.000 personas">
        </div>

        <div class="form-group">
            <label class="form-label" for="extracto">Extracto</label>
            <textarea id="extracto" name="extracto" class="form-textarea" rows="2"
                      maxlength="500"
                      placeholder="Resumen breve para tarjetas y redes sociales..."><?= $v('extracto') ?></textarea>
        </div>

        <div class="form-group">
            <label class="form-label" for="contenido">Contenido *</label>
            <textarea id="contenido" name="contenido" class="form-textarea form-textarea--tall" rows="15"
                      required
                      placeholder="Escribe el contenido del post..."><?= $v('contenido') ?></textarea>
            <small class="form-help">Puedes usar HTML básico para formato.</small>
        </div>
    </fieldset>

    <!-- ── Clasificación ── -->
    <fieldset class="form-fieldset">
        <legend>Clasificación</legend>

        <div class="form-row">
            <div class="form-group">
                <label class="form-label" for="tipo">Tipo *</label>
                <select id="tipo" name="tipo" class="form-select" required>
                    <?php
                    $tipos = [
                        'noticia'    => 'Noticia',
                        'articulo'   => 'Artículo',
                        'guia'       => 'Guía',
                        'opinion'    => 'Opinión',
                        'entrevista' => 'Entrevista',
                        'galeria'    => 'Galería',
                    ];
                    foreach ($tipos as $val => $label):
                    ?>
                    <option value="<?= $val ?>" <?= (($post['tipo'] ?? 'articulo') === $val) ? 'selected' : '' ?>>
                        <?= $label ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label class="form-label" for="categoria_id">Categoría</label>
                <select id="categoria_id" name="categoria_id" class="form-select">
                    <option value="">Sin categoría</option>
                    <?php foreach ($categorias as $cat): ?>
                    <option value="<?= $cat['id'] ?>" <?= (($post['categoria_id'] ?? '') == $cat['id']) ? 'selected' : '' ?>>
                        <?= e($cat['nombre']) ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label class="form-label" for="autor_id">Autor</label>
                <select id="autor_id" name="autor_id" class="form-select">
                    <option value="">Sin autor</option>
                    <?php foreach ($autores as $autor): ?>
                    <option value="<?= $autor['id'] ?>" <?= (($post['autor_id'] ?? '') == $autor['id']) ? 'selected' : '' ?>>
                        <?= e($autor['nombre']) ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
    </fieldset>

    <!-- ── Fuente ── -->
    <fieldset class="form-fieldset">
        <legend>Fuente (opcional)</legend>

        <div class="form-row">
            <div class="form-group">
                <label class="form-label" for="fuente_nombre">Nombre de la fuente</label>
                <input type="text" id="fuente_nombre" name="fuente_nombre" value="<?= $v('fuente_nombre') ?>"
                       class="form-input" maxlength="200"
                       placeholder="Ej: Diario Austral de Osorno">
            </div>
            <div class="form-group">
                <label class="form-label" for="fuente_url">URL de la fuente</label>
                <input type="url" id="fuente_url" name="fuente_url" value="<?= $v('fuente_url') ?>"
                       class="form-input" maxlength="255"
                       placeholder="https://...">
            </div>
        </div>
    </fieldset>

    <!-- ── Publicación ── -->
    <fieldset class="form-fieldset">
        <legend>Publicación</legend>

        <div class="form-row">
            <div class="form-group">
                <label class="form-label" for="estado">Estado *</label>
                <select id="estado" name="estado" class="form-select" required>
                    <?php
                    $estados = [
                        'borrador'   => 'Borrador',
                        'revision'   => 'En revisión',
                        'programado' => 'Programado',
                        'publicado'  => 'Publicado',
                        'archivado'  => 'Archivado',
                    ];
                    foreach ($estados as $val => $label):
                    ?>
                    <option value="<?= $val ?>" <?= (($post['estado'] ?? 'borrador') === $val) ? 'selected' : '' ?>>
                        <?= $label ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group" id="programadoGroup" style="display:none">
                <label class="form-label" for="programado_at">Fecha de publicación programada</label>
                <input type="datetime-local" id="programado_at" name="programado_at"
                       value="<?= ($isEdit && !empty($post['programado_at'])) ? date('Y-m-d\TH:i', strtotime($post['programado_at'])) : '' ?>"
                       class="form-input">
            </div>
        </div>

        <?php if ($isEdit && !empty($post['publicado_at'])): ?>
        <p class="form-help">Publicado el <?= formatDate($post['publicado_at'], 'd/m/Y H:i') ?>
            — <?= $post['vistas'] ?> vista<?= $post['vistas'] !== 1 ? 's' : '' ?>,
            <?= $post['compartidos'] ?> compartido<?= $post['compartidos'] !== 1 ? 's' : '' ?>,
            ~<?= $post['tiempo_lectura'] ?> min lectura</p>
        <?php endif; ?>
    </fieldset>

    <!-- ── Imagen y SEO ── -->
    <fieldset class="form-fieldset">
        <legend>Imagen y SEO</legend>

        <div class="form-group">
            <label class="form-label" for="imagen_portada">Imagen de portada</label>
            <?php if ($isEdit && !empty($post['imagen_portada'])): ?>
                <div class="form-preview">
                    <img src="<?= url('uploads/blog/' . $post['imagen_portada']) ?>"
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
                <input type="checkbox" name="destacado" value="1"
                       <?= (!empty($post['destacado'])) ? 'checked' : '' ?>>
                <span>Destacado</span>
            </label>
            <label class="form-checkbox">
                <input type="checkbox" name="permite_comentarios" value="1"
                       <?= (!$isEdit || !empty($post['permite_comentarios'])) ? 'checked' : '' ?>>
                <span>Permitir comentarios</span>
            </label>
        </div>
    </fieldset>

    <!-- ── Botones ── -->
    <div class="form-actions">
        <button type="submit" class="btn btn--primary btn--lg">
            <?= $isEdit ? 'Guardar Cambios' : 'Crear Post' ?>
        </button>
        <a href="<?= url('/admin/blog') ?>" class="btn btn--outline btn--lg">Cancelar</a>
    </div>
</form>

<script>
// Mostrar/ocultar campo de fecha programada
var estadoSelect = document.getElementById('estado');
var programadoGroup = document.getElementById('programadoGroup');
function toggleProgramado() {
    programadoGroup.style.display = estadoSelect.value === 'programado' ? '' : 'none';
}
estadoSelect.addEventListener('change', toggleProgramado);
toggleProgramado();
</script>
