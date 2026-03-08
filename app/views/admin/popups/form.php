<?php
/**
 * Admin — Formulario Popup
 * Variables: $popup (null = crear)
 */
$isEdit = $popup !== null;
$action = $isEdit ? url("/admin/popups/{$popup['id']}/editar") : url('/admin/popups/crear');
?>

<div class="admin-page-header">
    <h1><?= $isEdit ? 'Editar Popup' : 'Nuevo Popup' ?></h1>
</div>

<div class="admin-card">
    <form method="POST" action="<?= $action ?>" class="admin-form">
        <?= csrf_field() ?>

        <div class="form-group">
            <label class="form-label">Título</label>
            <input type="text" name="titulo" class="form-input" value="<?= e($popup['titulo'] ?? '') ?>" required>
        </div>

        <div class="form-group">
            <label class="form-label">Contenido (HTML)</label>
            <textarea name="contenido" class="form-input" rows="10" required style="font-family:monospace;font-size:.9rem;"><?= e($popup['contenido'] ?? '') ?></textarea>
        </div>

        <div style="display:grid; grid-template-columns:1fr 1fr; gap:16px;">
            <div class="form-group">
                <label class="form-label">Tipo de popup</label>
                <select name="tipo" class="form-select">
                    <?php
                    $tipos = ['modal' => 'Modal centrado', 'banner_top' => 'Banner superior', 'banner_bottom' => 'Banner inferior', 'slide_in' => 'Slide-in (esquina)'];
                    foreach ($tipos as $val => $label):
                    ?>
                        <option value="<?= $val ?>"<?= ($popup['tipo'] ?? 'modal') === $val ? ' selected' : '' ?>><?= $label ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label class="form-label">Trigger (cuándo mostrar)</label>
                <select name="trigger_type" class="form-select">
                    <?php
                    $triggers = ['tiempo' => 'Después de X segundos', 'scroll' => 'Al hacer scroll (% de página)', 'exit_intent' => 'Al intentar salir', 'click' => 'Al hacer clic en elemento'];
                    foreach ($triggers as $val => $label):
                    ?>
                        <option value="<?= $val ?>"<?= ($popup['trigger_type'] ?? 'tiempo') === $val ? ' selected' : '' ?>><?= $label ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <div style="display:grid; grid-template-columns:1fr 1fr 1fr; gap:16px;">
            <div class="form-group">
                <label class="form-label">Valor del trigger</label>
                <input type="text" name="trigger_valor" class="form-input" value="<?= e($popup['trigger_valor'] ?? '5') ?>"
                       placeholder="5 (segundos o %)">
                <small class="text-muted">Segundos para tiempo, % para scroll</small>
            </div>
            <div class="form-group">
                <label class="form-label">Fecha inicio</label>
                <input type="date" name="fecha_inicio" class="form-input" value="<?= e($popup['fecha_inicio'] ?? '') ?>">
            </div>
            <div class="form-group">
                <label class="form-label">Fecha fin</label>
                <input type="date" name="fecha_fin" class="form-input" value="<?= e($popup['fecha_fin'] ?? '') ?>">
            </div>
        </div>

        <div class="form-group">
            <label class="form-label">Páginas donde mostrar (JSON array, vacío = todas)</label>
            <input type="text" name="paginas" class="form-input"
                   value="<?= e($popup['paginas'] ?? '') ?>"
                   placeholder='["/", "/categorias"]'>
            <small class="text-muted">Dejar vacío para mostrar en todas las páginas.</small>
        </div>

        <div class="form-group">
            <label class="form-checkbox">
                <input type="checkbox" name="activo" value="1" <?= ($popup['activo'] ?? 0) ? 'checked' : '' ?>>
                Popup activo
            </label>
        </div>

        <div style="display:flex; gap:10px;">
            <button type="submit" class="btn btn--primary"><?= $isEdit ? 'Guardar Cambios' : 'Crear Popup' ?></button>
            <a href="<?= url('/admin/popups') ?>" class="btn btn--outline">Cancelar</a>
        </div>
    </form>
</div>
