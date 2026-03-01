<?php
/**
 * Admin — Formulario Categoría (crear/editar) — visitapurranque.cl
 * Variables: $cat (null=crear), $subcategorias, $csrf
 */
$isEdit = !empty($cat);
$v = fn(string $key, string $default = '') => e($cat[$key] ?? $default);
?>

<div class="admin-page-header">
    <h1><?= $isEdit ? 'Editar Categoría' : 'Nueva Categoría' ?></h1>
    <p class="admin-page-subtitle">
        <a href="<?= url('/admin/categorias') ?>">&larr; Volver al listado</a>
    </p>
</div>

<form method="POST"
      action="<?= $isEdit ? url("/admin/categorias/{$cat['id']}/editar") : url('/admin/categorias/crear') ?>"
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
                       class="form-input" required maxlength="100"
                       placeholder="Ej: Playas y Costa">
            </div>

            <div class="form-group form-group--small">
                <label class="form-label" for="orden">Orden</label>
                <input type="number" id="orden" name="orden" value="<?= $v('orden', '0') ?>"
                       class="form-input" min="0" max="255">
            </div>
        </div>

        <div class="form-group">
            <label class="form-label" for="descripcion">Descripción</label>
            <textarea id="descripcion" name="descripcion" class="form-textarea" rows="3"
                      placeholder="Descripción de la categoría para el sitio público..."><?= $v('descripcion') ?></textarea>
        </div>

        <div class="form-row">
            <div class="form-group form-group--small">
                <label class="form-label" for="emoji">Emoji</label>
                <input type="text" id="emoji" name="emoji" value="<?= $v('emoji') ?>"
                       class="form-input" maxlength="10"
                       placeholder="&#127958;">
            </div>

            <div class="form-group">
                <label class="form-label" for="icono">Icono (clase CSS)</label>
                <input type="text" id="icono" name="icono" value="<?= $v('icono') ?>"
                       class="form-input" maxlength="50"
                       placeholder="Ej: icon-beach">
            </div>

            <div class="form-group form-group--small">
                <label class="form-label" for="color">Color</label>
                <input type="color" id="color" name="color" value="<?= $v('color', '#3b82f6') ?>"
                       class="form-input form-input--color">
            </div>
        </div>
    </fieldset>

    <!-- ── Imagen y SEO ── -->
    <fieldset class="form-fieldset">
        <legend>Imagen y SEO</legend>

        <div class="form-group">
            <label class="form-label" for="imagen">Imagen de portada</label>
            <?php if ($isEdit && !empty($cat['imagen'])): ?>
                <div class="form-preview">
                    <img src="<?= url('uploads/categorias/' . $cat['imagen']) ?>"
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
                       <?= (!$isEdit || !empty($cat['activo'])) ? 'checked' : '' ?>>
                <span>Activa</span>
            </label>
        </div>
    </fieldset>

    <!-- ── Botones ── -->
    <div class="form-actions">
        <button type="submit" class="btn btn--primary btn--lg">
            <?= $isEdit ? 'Guardar Cambios' : 'Crear Categoría' ?>
        </button>
        <a href="<?= url('/admin/categorias') ?>" class="btn btn--outline btn--lg">Cancelar</a>
    </div>
</form>

<?php if ($isEdit): ?>
<!-- ── Subcategorías ── -->
<div class="admin-section" style="margin-top: 2rem;">
    <div class="admin-page-header admin-page-header--flex">
        <div>
            <h2>Subcategorías</h2>
            <p class="admin-page-subtitle"><?= count($subcategorias) ?> subcategoría<?= count($subcategorias) !== 1 ? 's' : '' ?></p>
        </div>
    </div>

    <?php if (!empty($subcategorias)): ?>
    <div class="admin-table-wrap">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>Orden</th>
                    <th>Nombre</th>
                    <th>Slug</th>
                    <th>Fichas</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($subcategorias as $sub): ?>
                <tr>
                    <td class="text-mono text-center"><?= (int)$sub['orden'] ?></td>
                    <td><strong><?= e($sub['nombre']) ?></strong></td>
                    <td class="text-mono text-muted"><?= e($sub['slug']) ?></td>
                    <td class="text-center"><?= (int)$sub['total_fichas'] ?></td>
                    <td>
                        <?php if ($sub['activo']): ?>
                            <span class="badge badge--green">Activa</span>
                        <?php else: ?>
                            <span class="badge badge--gray">Inactiva</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php if ((int)$sub['total_fichas'] === 0): ?>
                        <form method="POST"
                              action="<?= url("/admin/categorias/{$cat['id']}/subcategorias/{$sub['id']}/eliminar") ?>"
                              class="inline-form">
                            <?= csrf_field() ?>
                            <button type="submit" class="btn btn--small btn--danger"
                                    data-confirm="¿Eliminar &laquo;<?= e($sub['nombre']) ?>&raquo;?">
                                Eliminar
                            </button>
                        </form>
                        <?php else: ?>
                            <span class="text-muted">En uso</span>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php endif; ?>

    <!-- Formulario agregar subcategoría -->
    <form method="POST" action="<?= url("/admin/categorias/{$cat['id']}/subcategorias/crear") ?>"
          class="admin-form admin-form--inline" style="margin-top: 1rem;">
        <?= csrf_field() ?>

        <fieldset class="form-fieldset">
            <legend>Agregar subcategoría</legend>

            <div class="form-row">
                <div class="form-group form-group--wide">
                    <label class="form-label" for="sub_nombre">Nombre *</label>
                    <input type="text" id="sub_nombre" name="sub_nombre"
                           class="form-input" required maxlength="100"
                           placeholder="Ej: Playas aptas para baño">
                </div>

                <div class="form-group form-group--small">
                    <label class="form-label" for="sub_orden">Orden</label>
                    <input type="number" id="sub_orden" name="sub_orden" value="0"
                           class="form-input" min="0" max="255">
                </div>
            </div>

            <div class="form-group">
                <label class="form-label" for="sub_descripcion">Descripción</label>
                <input type="text" id="sub_descripcion" name="sub_descripcion"
                       class="form-input" maxlength="255"
                       placeholder="Descripción breve (opcional)">
            </div>

            <div class="form-checkboxes">
                <label class="form-checkbox">
                    <input type="checkbox" name="sub_activo" value="1" checked>
                    <span>Activa</span>
                </label>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn--primary">Agregar Subcategoría</button>
            </div>
        </fieldset>
    </form>
</div>
<?php endif; ?>
