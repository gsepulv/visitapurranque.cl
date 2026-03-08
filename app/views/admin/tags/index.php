<?php
/**
 * Admin — Tags
 * Variables: $tags
 */
?>

<div class="admin-page-header">
    <h1>Tags</h1>
    <p class="admin-page-subtitle"><?= count($tags) ?> tag<?= count($tags) !== 1 ? 's' : '' ?></p>
</div>

<!-- Crear tag inline -->
<div class="admin-card" style="margin-bottom:24px;">
    <form method="POST" action="<?= url('/admin/tags/crear') ?>" class="admin-form" id="tagCreateForm">
        <?= csrf_field() ?>
        <div style="display:flex;gap:10px;align-items:flex-end;">
            <div class="form-group" style="flex:1;">
                <label class="form-label">Nuevo tag</label>
                <input type="text" name="nombre" class="form-input" id="tagNombre"
                       placeholder="Escribe un tag y presiona Enter" required>
            </div>
            <button type="submit" class="btn btn--primary" style="margin-bottom:16px;">Agregar</button>
        </div>
    </form>
</div>

<!-- Filtro -->
<div style="margin-bottom:16px;">
    <input type="text" id="tagFilter" class="form-input" placeholder="Filtrar tags..." style="max-width:300px;">
</div>

<?php if (empty($tags)): ?>
    <div class="admin-empty">No hay tags creados.</div>
<?php else: ?>
<div class="tag-cloud" id="tagCloud">
    <?php foreach ($tags as $tag): ?>
    <div class="tag-chip" data-name="<?= e(mb_strtolower($tag['nombre'])) ?>">
        <span class="tag-chip__name"><?= e($tag['nombre']) ?></span>
        <span class="tag-chip__count"><?= (int)$tag['uso'] ?></span>
        <button type="button" class="tag-chip__edit" title="Editar"
                onclick="editTag(<?= $tag['id'] ?>, '<?= e(addslashes($tag['nombre'])) ?>')">&#9998;</button>
        <form method="POST" action="<?= url("/admin/tags/eliminar/{$tag['id']}") ?>" style="display:inline"
              onsubmit="return confirm('¿Eliminar este tag?')">
            <?= csrf_field() ?>
            <button type="submit" class="tag-chip__delete" title="Eliminar">&times;</button>
        </form>
    </div>
    <?php endforeach; ?>
</div>
<?php endif; ?>

<!-- Modal editar -->
<div id="editModal" class="admin-modal" style="display:none;">
    <div class="admin-modal__overlay" onclick="closeEditModal()"></div>
    <div class="admin-modal__content" style="max-width:400px;">
        <h3 style="margin-bottom:12px;">Editar Tag</h3>
        <form method="POST" id="editTagForm">
            <?= csrf_field() ?>
            <div class="form-group">
                <input type="text" name="nombre" id="editTagNombre" class="form-input" required>
            </div>
            <div style="display:flex;gap:8px;">
                <button type="submit" class="btn btn--primary">Guardar</button>
                <button type="button" class="btn btn--outline" onclick="closeEditModal()">Cancelar</button>
            </div>
        </form>
    </div>
</div>

<script>
// Filtro en tiempo real
document.getElementById('tagFilter').addEventListener('input', function() {
    var q = this.value.toLowerCase();
    document.querySelectorAll('.tag-chip').forEach(function(chip) {
        chip.style.display = chip.dataset.name.includes(q) ? '' : 'none';
    });
});

// Editar tag
function editTag(id, nombre) {
    document.getElementById('editTagNombre').value = nombre;
    document.getElementById('editTagForm').action = '<?= url('/admin/tags/editar/') ?>' + '/' + id;
    document.getElementById('editModal').style.display = 'flex';
    document.getElementById('editTagNombre').focus();
}
function closeEditModal() {
    document.getElementById('editModal').style.display = 'none';
}
</script>
