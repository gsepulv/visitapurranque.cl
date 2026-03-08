<?php
/**
 * Admin — Formulario Medio (subir/editar)
 * Variables: $medio (null=crear), $csrf
 */
$isEdit = !empty($medio);
?>

<div class="admin-page-header">
    <h1><?= $isEdit ? 'Editar Archivo' : 'Subir Archivo' ?></h1>
    <p class="admin-page-subtitle">
        <a href="<?= url('/admin/medios') ?>">&larr; Volver a la galería</a>
    </p>
</div>

<form method="POST"
      action="<?= $isEdit ? url("/admin/medios/{$medio['id']}/editar") : url('/admin/medios/crear') ?>"
      enctype="multipart/form-data"
      class="admin-form" style="max-width: 600px;">
    <?= csrf_field() ?>

    <?php if (!$isEdit): ?>
    <fieldset class="form-fieldset">
        <legend>Archivo</legend>
        <div class="form-group">
            <label class="form-label" for="archivo">Seleccionar archivo *</label>
            <div class="upload-dropzone" id="dropzone">
                <input type="file" id="archivo" name="archivo" required
                       accept="image/jpeg,image/png,image/gif,image/webp,application/pdf,.doc,.docx"
                       class="upload-dropzone__input">
                <div class="upload-dropzone__text">
                    <p>Arrastra un archivo aquí o haz clic para seleccionar</p>
                    <small>JPG, PNG, GIF, WebP, PDF, DOC — máx. 5 MB</small>
                </div>
                <div class="upload-dropzone__preview" id="preview" style="display:none">
                    <img id="previewImg" alt="Vista previa">
                    <span id="previewName"></span>
                </div>
            </div>
        </div>
    </fieldset>
    <?php else: ?>
    <fieldset class="form-fieldset">
        <legend>Archivo actual</legend>
        <div class="form-group">
            <?php if (str_starts_with($medio['tipo'], 'image/')): ?>
                <img src="<?= asset('uploads/' . $medio['archivo']) ?>" alt="<?= e($medio['alt'] ?? '') ?>"
                     style="max-width: 100%; max-height: 300px; border-radius: var(--radius); margin-bottom: 12px;">
            <?php endif; ?>
            <p class="text-muted" style="font-size: 13px;">
                <?= e($medio['archivo']) ?> — <?= e($medio['tipo']) ?>
                <?= $medio['ancho'] ? " — {$medio['ancho']}x{$medio['alto']}px" : '' ?>
            </p>
        </div>
    </fieldset>
    <?php endif; ?>

    <fieldset class="form-fieldset">
        <legend>Información</legend>

        <div class="form-group">
            <label class="form-label" for="titulo">Título</label>
            <input type="text" id="titulo" name="titulo"
                   value="<?= e($isEdit ? $medio['nombre'] : '') ?>"
                   class="form-input" maxlength="200"
                   placeholder="Nombre descriptivo del archivo">
        </div>

        <div class="form-group">
            <label class="form-label" for="alt">Texto alternativo (alt)</label>
            <input type="text" id="alt" name="alt"
                   value="<?= e($isEdit ? ($medio['alt'] ?? '') : '') ?>"
                   class="form-input" maxlength="200"
                   placeholder="Descripción para accesibilidad y SEO">
        </div>

        <div class="form-group">
            <label class="form-label" for="carpeta">Carpeta</label>
            <select id="carpeta" name="carpeta" class="form-select">
                <?php
                $carpetas = ['general' => 'General', 'fichas' => 'Fichas', 'eventos' => 'Eventos', 'blog' => 'Blog', 'banners' => 'Banners'];
                foreach ($carpetas as $val => $label):
                ?>
                <option value="<?= $val ?>" <?= ($isEdit && $medio['carpeta'] === $val) ? 'selected' : '' ?>><?= $label ?></option>
                <?php endforeach; ?>
            </select>
        </div>
    </fieldset>

    <button type="submit" class="btn btn--primary"><?= $isEdit ? 'Guardar cambios' : 'Subir archivo' ?></button>
</form>

<script>
document.addEventListener('DOMContentLoaded', function() {
    var input = document.getElementById('archivo');
    var zone = document.getElementById('dropzone');
    if (!input || !zone) return;

    var preview = document.getElementById('preview');
    var previewImg = document.getElementById('previewImg');
    var previewName = document.getElementById('previewName');
    var textDiv = zone.querySelector('.upload-dropzone__text');

    function showPreview(file) {
        previewName.textContent = file.name + ' (' + (file.size / 1024).toFixed(0) + ' KB)';
        if (file.type.startsWith('image/')) {
            var reader = new FileReader();
            reader.onload = function(e) { previewImg.src = e.target.result; previewImg.style.display = 'block'; };
            reader.readAsDataURL(file);
        } else {
            previewImg.style.display = 'none';
        }
        textDiv.style.display = 'none';
        preview.style.display = 'flex';
    }

    input.addEventListener('change', function() {
        if (this.files[0]) showPreview(this.files[0]);
    });

    zone.addEventListener('dragover', function(e) { e.preventDefault(); zone.classList.add('dragover'); });
    zone.addEventListener('dragleave', function() { zone.classList.remove('dragover'); });
    zone.addEventListener('drop', function(e) {
        e.preventDefault();
        zone.classList.remove('dragover');
        if (e.dataTransfer.files[0]) {
            input.files = e.dataTransfer.files;
            showPreview(e.dataTransfer.files[0]);
        }
    });
});
</script>
