<?php
/**
 * Admin — Crear/Editar ítem de menú — visitapurranque.cl
 * Variables: $item (null para crear), $menu, $categorias, $paginas, $padres, $csrf
 */
$isEdit = !empty($item);
?>

<div class="admin-page-header admin-page-header--flex">
    <div>
        <h1><?= $isEdit ? 'Editar ítem' : 'Nuevo ítem de menú' ?></h1>
        <p class="admin-page-subtitle">
            <?= $isEdit ? e($item['titulo']) : 'Agregar al menú: ' . e($menu) ?>
        </p>
    </div>
    <a href="<?= url('/admin/menu?tab=' . urlencode($menu)) ?>" class="btn btn--outline">&larr; Volver al menú</a>
</div>

<form method="POST"
      action="<?= url($isEdit ? "/admin/menu/{$item['id']}/editar" : '/admin/menu/crear') ?>"
      class="admin-form">
    <?= csrf_field() ?>

    <fieldset class="form-fieldset">
        <legend>Información del ítem</legend>

        <div class="form-row-2">
            <div class="form-group">
                <label class="form-label" for="titulo">Título *</label>
                <input type="text" id="titulo" name="titulo"
                       value="<?= e($item['titulo'] ?? '') ?>" class="form-input" required maxlength="100">
            </div>
            <div class="form-group">
                <label class="form-label" for="tipo">Tipo de enlace</label>
                <select id="tipo" name="tipo" class="form-select">
                    <option value="enlace" <?= ($item['tipo'] ?? 'enlace') === 'enlace' ? 'selected' : '' ?>>Enlace interno</option>
                    <option value="categoria" <?= ($item['tipo'] ?? '') === 'categoria' ? 'selected' : '' ?>>Categoría</option>
                    <option value="pagina" <?= ($item['tipo'] ?? '') === 'pagina' ? 'selected' : '' ?>>Página</option>
                    <option value="externo" <?= ($item['tipo'] ?? '') === 'externo' ? 'selected' : '' ?>>Enlace externo</option>
                </select>
            </div>
        </div>

        <!-- Campo URL (enlace interno / externo) -->
        <div class="form-group" id="groupUrl">
            <label class="form-label" for="url">URL</label>
            <input type="text" id="url" name="url"
                   value="<?= e($item['url'] ?? '') ?>" class="form-input"
                   placeholder="/ruta-interna o https://...">
            <small class="form-help" id="helpUrl">Ruta relativa (ej: /contacto) o URL completa para externos</small>
        </div>

        <!-- Selector categoría -->
        <div class="form-group" id="groupCategoria" style="display:none">
            <label class="form-label" for="ref_categoria">Categoría</label>
            <select id="ref_categoria" name="referencia_id_cat" class="form-select">
                <option value="">— Seleccionar categoría —</option>
                <?php foreach ($categorias as $cat): ?>
                <option value="<?= $cat['id'] ?>" <?= ($item['tipo'] ?? '') === 'categoria' && ($item['referencia_id'] ?? 0) == $cat['id'] ? 'selected' : '' ?>>
                    <?= e($cat['nombre']) ?>
                </option>
                <?php endforeach; ?>
            </select>
        </div>

        <!-- Selector página -->
        <div class="form-group" id="groupPagina" style="display:none">
            <label class="form-label" for="ref_pagina">Página</label>
            <select id="ref_pagina" name="referencia_id_pag" class="form-select">
                <option value="">— Seleccionar página —</option>
                <?php foreach ($paginas as $pag): ?>
                <option value="<?= $pag['id'] ?>" <?= ($item['tipo'] ?? '') === 'pagina' && ($item['referencia_id'] ?? 0) == $pag['id'] ? 'selected' : '' ?>>
                    <?= e($pag['titulo']) ?> (<?= e($pag['slug']) ?>)
                </option>
                <?php endforeach; ?>
            </select>
        </div>
    </fieldset>

    <fieldset class="form-fieldset">
        <legend>Configuración</legend>

        <div class="form-row-2">
            <div class="form-group">
                <label class="form-label" for="menu">Menú</label>
                <select id="menu" name="menu" class="form-select">
                    <option value="principal" <?= ($item['menu'] ?? $menu) === 'principal' ? 'selected' : '' ?>>Principal</option>
                    <option value="footer_legal" <?= ($item['menu'] ?? $menu) === 'footer_legal' ? 'selected' : '' ?>>Footer / Legal</option>
                </select>
            </div>
            <div class="form-group">
                <label class="form-label" for="parent_id">Ítem padre (opcional)</label>
                <select id="parent_id" name="parent_id" class="form-select">
                    <option value="">— Nivel raíz —</option>
                    <?php foreach ($padres as $padre): ?>
                    <option value="<?= $padre['id'] ?>" <?= ($item['parent_id'] ?? '') == $padre['id'] ? 'selected' : '' ?>>
                        <?= e($padre['titulo']) ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <div class="form-row-3">
            <div class="form-group">
                <label class="form-label" for="icono">Icono</label>
                <input type="text" id="icono" name="icono"
                       value="<?= e($item['icono'] ?? '') ?>" class="form-input"
                       placeholder="emoji o HTML entity">
                <small class="form-help">Ej: &#127968; &#128205; &#128197;</small>
            </div>
            <div class="form-group">
                <label class="form-label" for="target">Abrir en</label>
                <select id="target" name="target" class="form-select">
                    <option value="_self" <?= ($item['target'] ?? '_self') === '_self' ? 'selected' : '' ?>>Misma ventana</option>
                    <option value="_blank" <?= ($item['target'] ?? '') === '_blank' ? 'selected' : '' ?>>Nueva pestaña</option>
                </select>
            </div>
            <div class="form-group">
                <label class="form-label" for="orden">Orden</label>
                <input type="number" id="orden" name="orden"
                       value="<?= (int)($item['orden'] ?? 0) ?>" class="form-input" min="0" max="255">
            </div>
        </div>

        <div class="form-group">
            <label class="form-check">
                <input type="checkbox" name="activo" value="1"
                       <?= ($isEdit ? $item['activo'] : 1) ? 'checked' : '' ?>>
                <span>Activo</span>
            </label>
        </div>
    </fieldset>

    <!-- Hidden referencia_id que se llena por JS -->
    <input type="hidden" id="referencia_id" name="referencia_id" value="<?= (int)($item['referencia_id'] ?? 0) ?>">

    <div class="form-actions">
        <button type="submit" class="btn btn--primary"><?= $isEdit ? 'Guardar cambios' : 'Crear ítem' ?></button>
        <a href="<?= url('/admin/menu?tab=' . urlencode($menu)) ?>" class="btn btn--outline">Cancelar</a>
    </div>
</form>

<style>
.form-row-3 { display: grid; grid-template-columns: repeat(3, 1fr); gap: 1rem; }
@media (max-width: 640px) { .form-row-3 { grid-template-columns: 1fr; } }
</style>

<script>
(function() {
    const tipo = document.getElementById('tipo');
    const groupUrl = document.getElementById('groupUrl');
    const groupCat = document.getElementById('groupCategoria');
    const groupPag = document.getElementById('groupPagina');
    const refId = document.getElementById('referencia_id');
    const refCat = document.getElementById('ref_categoria');
    const refPag = document.getElementById('ref_pagina');

    function toggleFields() {
        const val = tipo.value;
        groupUrl.style.display = (val === 'enlace' || val === 'externo') ? '' : 'none';
        groupCat.style.display = (val === 'categoria') ? '' : 'none';
        groupPag.style.display = (val === 'pagina') ? '' : 'none';
    }

    tipo.addEventListener('change', toggleFields);
    toggleFields();

    // Sincronizar referencia_id antes de enviar
    document.querySelector('.admin-form').addEventListener('submit', function() {
        var val = tipo.value;
        if (val === 'categoria') {
            refId.value = refCat.value || '0';
        } else if (val === 'pagina') {
            refId.value = refPag.value || '0';
        } else {
            refId.value = '0';
        }
    });
})();
</script>
