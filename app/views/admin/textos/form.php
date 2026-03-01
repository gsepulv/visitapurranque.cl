<?php
/**
 * Admin — Crear/Editar Texto Editable — visitapurranque.cl
 * Variables: $texto (null para crear), $secciones
 */
$isEdit = !empty($texto);
?>

<div class="admin-page-header admin-page-header--flex">
    <div>
        <h1><?= $isEdit ? 'Editar Texto' : 'Nuevo Texto Editable' ?></h1>
        <p class="admin-page-subtitle"><?= $isEdit ? e($texto['clave']) : 'Agregar un nuevo texto editable al sitio' ?></p>
    </div>
    <div class="admin-page-actions">
        <a href="<?= url('/admin/textos') ?>" class="btn btn--outline">&larr; Textos</a>
    </div>
</div>

<form method="POST"
      action="<?= url($isEdit ? "/admin/textos/{$texto['id']}/editar" : '/admin/textos/crear') ?>"
      class="admin-form">
    <?= csrf_field() ?>

    <fieldset class="form-fieldset">
        <legend>Datos del texto</legend>

        <div class="form-row-2">
            <div class="form-group">
                <label class="form-label" for="clave">Clave *</label>
                <input type="text" id="clave" name="clave"
                       value="<?= e($texto['clave'] ?? '') ?>" class="form-input"
                       pattern="^[a-z0-9_]+$" required
                       placeholder="hero_titulo">
                <small class="form-help">Solo letras minúsculas, números y guion bajo</small>
            </div>
            <div class="form-group">
                <label class="form-label" for="tipo">Tipo *</label>
                <select id="tipo" name="tipo" class="form-select">
                    <option value="text" <?= ($texto['tipo'] ?? 'text') === 'text' ? 'selected' : '' ?>>Texto corto</option>
                    <option value="textarea" <?= ($texto['tipo'] ?? '') === 'textarea' ? 'selected' : '' ?>>Texto largo</option>
                    <option value="html" <?= ($texto['tipo'] ?? '') === 'html' ? 'selected' : '' ?>>HTML</option>
                </select>
            </div>
        </div>

        <div class="form-row-2">
            <div class="form-group">
                <label class="form-label" for="seccion">Sección *</label>
                <select id="seccion" name="seccion" class="form-select" onchange="toggleSeccionNueva(this)">
                    <?php foreach ($secciones as $s): ?>
                        <option value="<?= e($s) ?>" <?= ($texto['seccion'] ?? '') === $s ? 'selected' : '' ?>><?= e(ucfirst($s)) ?></option>
                    <?php endforeach; ?>
                    <option value="__nueva__">+ Nueva sección...</option>
                </select>
            </div>
            <div class="form-group" id="seccionNuevaGroup" style="display:none">
                <label class="form-label" for="seccion_nueva">Nombre nueva sección</label>
                <input type="text" id="seccion_nueva" name="seccion_nueva" class="form-input"
                       pattern="^[a-z0-9_]+$" placeholder="contacto">
                <small class="form-help">Solo letras minúsculas, números y guion bajo</small>
            </div>
        </div>

        <div class="form-group">
            <label class="form-label" for="descripcion">Descripción</label>
            <input type="text" id="descripcion" name="descripcion"
                   value="<?= e($texto['descripcion'] ?? '') ?>" class="form-input"
                   placeholder="Breve descripción del uso de este texto">
        </div>

        <div class="form-group">
            <label class="form-label" for="valor">Valor</label>
            <textarea id="valor" name="valor" class="form-textarea" rows="4"><?= e($texto['valor'] ?? '') ?></textarea>
        </div>

        <?php if ($isEdit): ?>
        <div class="form-group">
            <label class="form-label" for="valor_default">Valor por defecto</label>
            <textarea id="valor_default" name="valor_default" class="form-textarea" rows="3"><?= e($texto['valor_default'] ?? '') ?></textarea>
            <small class="form-help">Valor al que se restaura al presionar "restaurar"</small>
        </div>
        <?php endif; ?>
    </fieldset>

    <div class="form-actions">
        <button type="submit" class="btn btn--primary"><?= $isEdit ? 'Guardar cambios' : 'Crear texto' ?></button>
        <a href="<?= url('/admin/textos') ?>" class="btn btn--outline">Cancelar</a>
    </div>
</form>

<script>
function toggleSeccionNueva(select) {
    var group = document.getElementById('seccionNuevaGroup');
    group.style.display = select.value === '__nueva__' ? '' : 'none';
    if (select.value === '__nueva__') {
        document.getElementById('seccion_nueva').focus();
    }
}
</script>
