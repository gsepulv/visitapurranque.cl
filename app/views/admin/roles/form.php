<?php
/**
 * Admin — Formulario Rol (crear/editar)
 * Variables: $rol (null=crear), $permisosGrouped, $asignados, $csrf
 */
$isEdit = !empty($rol);
$isAdmin = $isEdit && ($rol['slug'] ?? '') === 'admin';
$moduloLabels = [
    'fichas' => 'Fichas / Atractivos',
    'categorias' => 'Categorías',
    'eventos' => 'Eventos',
    'blog' => 'Blog',
    'resenas' => 'Reseñas',
    'medios' => 'Medios',
    'usuarios' => 'Usuarios',
    'configuracion' => 'Configuración',
];
?>

<div class="admin-page-header">
    <h1><?= $isEdit ? 'Editar Rol: ' . e($rol['nombre']) : 'Nuevo Rol' ?></h1>
    <p class="admin-page-subtitle">
        <a href="<?= url('/admin/roles') ?>">&larr; Volver al listado</a>
    </p>
</div>

<form method="POST"
      action="<?= $isEdit ? url("/admin/roles/{$rol['id']}/editar") : url('/admin/roles/crear') ?>"
      class="admin-form" style="max-width: 700px;">
    <?= csrf_field() ?>

    <fieldset class="form-fieldset">
        <legend>Datos del rol</legend>
        <div class="form-group">
            <label class="form-label" for="nombre">Nombre *</label>
            <input type="text" id="nombre" name="nombre"
                   value="<?= e($isEdit ? $rol['nombre'] : '') ?>"
                   class="form-input" required maxlength="50"
                   <?= $isAdmin ? 'readonly' : '' ?>>
        </div>
        <div class="form-group">
            <label class="form-label" for="descripcion">Descripción</label>
            <input type="text" id="descripcion" name="descripcion"
                   value="<?= e($isEdit ? ($rol['descripcion'] ?? '') : '') ?>"
                   class="form-input" maxlength="200">
        </div>
    </fieldset>

    <fieldset class="form-fieldset">
        <legend>Permisos</legend>
        <?php if ($isAdmin): ?>
            <p class="admin-flash admin-flash--info">El rol Administrador siempre tiene todos los permisos.</p>
        <?php endif; ?>

        <?php foreach ($permisosGrouped as $modulo => $permisos): ?>
        <div class="permisos-modulo">
            <div class="permisos-modulo__header">
                <label class="form-checkbox">
                    <input type="checkbox" class="select-all-modulo"
                           data-modulo="<?= e($modulo) ?>"
                           <?= $isAdmin ? 'checked disabled' : '' ?>>
                    <strong><?= e($moduloLabels[$modulo] ?? ucfirst($modulo)) ?></strong>
                </label>
            </div>
            <div class="permisos-modulo__body">
                <?php foreach ($permisos as $p): ?>
                <label class="form-checkbox">
                    <input type="checkbox" name="permisos[]" value="<?= $p['id'] ?>"
                           data-modulo="<?= e($modulo) ?>"
                           <?= in_array($p['id'], $asignados) ? 'checked' : '' ?>
                           <?= $isAdmin ? 'checked disabled' : '' ?>>
                    <span><?= e($p['nombre']) ?> <small class="text-muted">— <?= e($p['descripcion'] ?? '') ?></small></span>
                </label>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endforeach; ?>

        <?php if ($isAdmin): ?>
            <?php foreach ($permisosGrouped as $permisos): ?>
                <?php foreach ($permisos as $p): ?>
                    <input type="hidden" name="permisos[]" value="<?= $p['id'] ?>">
                <?php endforeach; ?>
            <?php endforeach; ?>
        <?php endif; ?>
    </fieldset>

    <button type="submit" class="btn btn--primary"><?= $isEdit ? 'Guardar cambios' : 'Crear rol' ?></button>
</form>

<style>
.permisos-modulo { margin-bottom: 16px; border: 1px solid var(--border); border-radius: var(--radius); }
.permisos-modulo__header { padding: 10px 14px; background: var(--content-bg); border-bottom: 1px solid var(--border); }
.permisos-modulo__body { padding: 10px 14px; display: flex; flex-direction: column; gap: 6px; }
</style>

<script>
document.querySelectorAll('.select-all-modulo').forEach(function(cb) {
    cb.addEventListener('change', function() {
        var mod = this.getAttribute('data-modulo');
        document.querySelectorAll('input[data-modulo="' + mod + '"][name="permisos[]"]').forEach(function(inp) {
            inp.checked = cb.checked;
        });
    });
});
</script>
