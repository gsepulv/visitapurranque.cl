<?php
/**
 * Admin — Crear/Editar Página — visitapurranque.cl
 * Variables: $pagina (null para crear), $versiones
 */
$isEdit = !empty($pagina);
?>

<div class="admin-page-header admin-page-header--flex">
    <div>
        <h1><?= $isEdit ? 'Editar Página' : 'Nueva Página' ?></h1>
        <p class="admin-page-subtitle">
            <?= $isEdit ? e($pagina['titulo']) . ' — /' . e($pagina['slug']) : 'Crear nueva página estática' ?>
        </p>
    </div>
    <div class="admin-page-actions">
        <a href="<?= url('/admin/paginas') ?>" class="btn btn--outline">&larr; Páginas</a>
        <?php if ($isEdit): ?>
            <a href="<?= url('/' . $pagina['slug']) ?>" target="_blank" class="btn btn--outline">Ver en sitio &#8599;</a>
        <?php endif; ?>
    </div>
</div>

<form method="POST"
      action="<?= url($isEdit ? "/admin/paginas/{$pagina['id']}/editar" : '/admin/paginas/crear') ?>"
      class="admin-form">
    <?= csrf_field() ?>

    <fieldset class="form-fieldset">
        <legend>Información</legend>
        <div class="form-row-2">
            <div class="form-group">
                <label class="form-label" for="titulo">Título *</label>
                <input type="text" id="titulo" name="titulo"
                       value="<?= e($pagina['titulo'] ?? '') ?>" class="form-input" required>
            </div>
            <div class="form-group">
                <label class="form-label" for="slug">Slug</label>
                <input type="text" id="slug" name="slug"
                       value="<?= e($pagina['slug'] ?? '') ?>" class="form-input"
                       pattern="^[a-z0-9\-]+$" placeholder="se-genera-del-titulo">
                <small class="form-help">Se genera automáticamente del título si se deja vacío</small>
            </div>
        </div>
        <div class="form-row-2">
            <div class="form-group">
                <label class="form-label" for="template">Template</label>
                <select id="template" name="template" class="form-select">
                    <option value="default" <?= ($pagina['template'] ?? 'default') === 'default' ? 'selected' : '' ?>>Default</option>
                    <option value="ancho_completo" <?= ($pagina['template'] ?? '') === 'ancho_completo' ? 'selected' : '' ?>>Ancho completo</option>
                    <option value="sin_sidebar" <?= ($pagina['template'] ?? '') === 'sin_sidebar' ? 'selected' : '' ?>>Sin sidebar</option>
                </select>
            </div>
            <div class="form-group">
                <label class="form-label" for="orden">Orden</label>
                <input type="number" id="orden" name="orden"
                       value="<?= e($pagina['orden'] ?? 0) ?>" class="form-input" min="0" max="255">
            </div>
        </div>
        <div class="form-group">
            <label class="form-toggle-label">
                <input type="checkbox" name="activo" value="1" class="form-toggle"
                       <?= ($pagina['activo'] ?? 1) ? 'checked' : '' ?>>
                <span class="form-toggle-text">Página activa (visible en el sitio)</span>
            </label>
        </div>
    </fieldset>

    <fieldset class="form-fieldset">
        <legend>Contenido</legend>
        <?php if ($isEdit): ?>
        <div class="form-group">
            <label class="form-label" for="version_nota">Nota de versión (opcional)</label>
            <input type="text" id="version_nota" name="version_nota" class="form-input"
                   placeholder="Ej: Actualizado datos de contacto">
            <small class="form-help">Se guarda con la versión anterior para referencia</small>
        </div>
        <?php endif; ?>
        <div class="form-group">
            <label class="form-label" for="contenido">Contenido HTML</label>
            <textarea id="contenido" name="contenido"
                      class="form-textarea form-textarea--code" rows="20"><?= e($pagina['contenido'] ?? '') ?></textarea>
        </div>
    </fieldset>

    <fieldset class="form-fieldset">
        <legend>SEO</legend>
        <div class="form-group">
            <label class="form-label" for="meta_title">Meta título</label>
            <input type="text" id="meta_title" name="meta_title"
                   value="<?= e($pagina['meta_title'] ?? '') ?>" class="form-input" maxlength="160">
            <small class="form-help">Máximo 70 caracteres recomendados</small>
        </div>
        <div class="form-group">
            <label class="form-label" for="meta_description">Meta descripción</label>
            <textarea id="meta_description" name="meta_description"
                      class="form-textarea" rows="2" maxlength="300"><?= e($pagina['meta_description'] ?? '') ?></textarea>
            <small class="form-help">Máximo 160 caracteres recomendados para snippet de Google</small>
        </div>
    </fieldset>

    <div class="form-actions">
        <button type="submit" class="btn btn--primary"><?= $isEdit ? 'Guardar cambios' : 'Crear página' ?></button>
        <a href="<?= url('/admin/paginas') ?>" class="btn btn--outline">Cancelar</a>
    </div>
</form>

<?php if ($isEdit && !empty($versiones)): ?>
<fieldset class="form-fieldset" style="margin-top:24px">
    <legend>Historial de versiones <span class="badge badge--outline"><?= count($versiones) ?></span></legend>
    <div class="admin-table-wrap">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Fecha</th>
                    <th>Usuario</th>
                    <th>Nota</th>
                    <th class="text-right">Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($versiones as $v): ?>
                <tr>
                    <td class="text-mono"><?= $v['id'] ?></td>
                    <td><?= date('d/m/Y H:i', strtotime($v['created_at'])) ?></td>
                    <td><?= e($v['usuario_nombre'] ?? 'Sistema') ?></td>
                    <td><?= $v['nota'] ? e($v['nota']) : '<span class="text-muted">—</span>' ?></td>
                    <td class="text-right">
                        <a href="<?= url("/admin/paginas/{$pagina['id']}/version/{$v['id']}") ?>"
                           class="btn btn--xs btn--outline">Ver</a>
                        <form method="POST" action="<?= url("/admin/paginas/{$pagina['id']}/restaurar/{$v['id']}") ?>"
                              class="inline-form" onsubmit="return confirm('¿Restaurar esta versión? El contenido actual se guardará como nueva versión.')">
                            <?= csrf_field() ?>
                            <button type="submit" class="btn btn--xs btn--outline">Restaurar</button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</fieldset>
<?php endif; ?>
