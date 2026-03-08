<?php
/**
 * Admin — Formulario Plantilla Email
 * Variables: $template (null = crear)
 */
$isEdit = $template !== null;
$action = $isEdit ? url("/admin/emails/{$template['id']}/editar") : url('/admin/emails/crear');
?>

<div class="admin-page-header">
    <h1><?= $isEdit ? 'Editar Plantilla' : 'Nueva Plantilla' ?></h1>
</div>

<div class="admin-card">
    <form method="POST" action="<?= $action ?>" class="admin-form">
        <?= csrf_field() ?>

        <div style="display:grid; grid-template-columns:1fr 1fr; gap:16px;">
            <div class="form-group">
                <label class="form-label">Nombre</label>
                <input type="text" name="nombre" class="form-input" value="<?= e($template['nombre'] ?? '') ?>" required>
            </div>
            <div class="form-group">
                <label class="form-label">Slug (identificador único)</label>
                <input type="text" name="slug" class="form-input" value="<?= e($template['slug'] ?? '') ?>" required
                       pattern="[a-z0-9_-]+" title="Solo letras minúsculas, números, guiones y guiones bajos">
            </div>
        </div>

        <div class="form-group">
            <label class="form-label">Asunto del email</label>
            <input type="text" name="asunto" class="form-input" value="<?= e($template['asunto'] ?? '') ?>" required>
        </div>

        <div class="form-group">
            <label class="form-label">Cuerpo HTML</label>
            <textarea name="cuerpo_html" class="form-input" rows="15" required style="font-family:monospace;font-size:.9rem;"><?= e($template['cuerpo_html'] ?? '') ?></textarea>
            <small class="text-muted">Usa <code>{{variable}}</code> para insertar variables dinámicas.</small>
        </div>

        <div class="form-group">
            <label class="form-label">Variables (JSON array)</label>
            <input type="text" name="variables" class="form-input"
                   value="<?= e($template['variables'] ?? '[]') ?>"
                   placeholder='["nombre", "email", "mensaje"]'>
            <small class="text-muted">Ejemplo: <code>["nombre", "email", "link"]</code></small>
        </div>

        <div class="form-group">
            <label class="form-checkbox">
                <input type="checkbox" name="activo" value="1" <?= ($template['activo'] ?? 1) ? 'checked' : '' ?>>
                Plantilla activa
            </label>
        </div>

        <div style="display:flex; gap:10px;">
            <button type="submit" class="btn btn--primary"><?= $isEdit ? 'Guardar Cambios' : 'Crear Plantilla' ?></button>
            <a href="<?= url('/admin/emails') ?>" class="btn btn--outline">Cancelar</a>
        </div>
    </form>
</div>
