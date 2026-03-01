<?php
/**
 * Admin — Apariencia — visitapurranque.cl
 * Variables: $config (mapa clave => row)
 */

// Helper para obtener valor de config
$val = function(string $key) use ($config) {
    return $config[$key]['valor'] ?? '';
};
?>

<div class="admin-page-header admin-page-header--flex">
    <div>
        <h1>Apariencia</h1>
        <p class="admin-page-subtitle">Personalización visual: colores, fuentes, logo y código custom</p>
    </div>
</div>

<form method="POST" action="<?= url('/admin/apariencia/guardar') ?>" enctype="multipart/form-data" class="admin-form">
    <?= csrf_field() ?>

    <!-- Colores -->
    <fieldset class="form-fieldset">
        <legend>Colores</legend>
        <div class="apariencia-colors-grid">
            <?php
            $colores = [
                'apariencia_color_primario'   => 'Color primario',
                'apariencia_color_secundario' => 'Color secundario',
                'apariencia_color_header'     => 'Header',
                'apariencia_color_footer'     => 'Footer',
            ];
            foreach ($colores as $key => $label): ?>
            <div class="form-group apariencia-color-group">
                <label class="form-label" for="cfg_<?= $key ?>"><?= e($label) ?></label>
                <div class="apariencia-color-row">
                    <input type="color" id="color_<?= $key ?>"
                           value="<?= e($val($key)) ?>"
                           class="apariencia-color-picker"
                           onchange="document.getElementById('cfg_<?= $key ?>').value = this.value">
                    <input type="text" id="cfg_<?= $key ?>" name="config[<?= $key ?>]"
                           value="<?= e($val($key)) ?>" class="form-input apariencia-color-hex"
                           pattern="^#[0-9a-fA-F]{6}$" maxlength="7"
                           onchange="document.getElementById('color_<?= $key ?>').value = this.value">
                </div>
                <small class="form-help"><?= e($config[$key]['descripcion'] ?? '') ?></small>
            </div>
            <?php endforeach; ?>
        </div>
    </fieldset>

    <!-- Tipografía -->
    <fieldset class="form-fieldset">
        <legend>Tipografía</legend>
        <div class="form-row-2">
            <div class="form-group">
                <label class="form-label" for="cfg_apariencia_fuente_titulo">Fuente para títulos</label>
                <input type="text" id="cfg_apariencia_fuente_titulo" name="config[apariencia_fuente_titulo]"
                       value="<?= e($val('apariencia_fuente_titulo')) ?>" class="form-input"
                       placeholder="Montserrat">
                <small class="form-help">Nombre exacto de Google Fonts</small>
            </div>
            <div class="form-group">
                <label class="form-label" for="cfg_apariencia_fuente_cuerpo">Fuente para texto</label>
                <input type="text" id="cfg_apariencia_fuente_cuerpo" name="config[apariencia_fuente_cuerpo]"
                       value="<?= e($val('apariencia_fuente_cuerpo')) ?>" class="form-input"
                       placeholder="Open Sans">
                <small class="form-help">Nombre exacto de Google Fonts</small>
            </div>
        </div>
    </fieldset>

    <!-- Logo y Favicon -->
    <fieldset class="form-fieldset">
        <legend>Logo y Favicon</legend>
        <div class="form-row-2">
            <div class="form-group">
                <label class="form-label">Logo del sitio</label>
                <?php if ($val('apariencia_logo')): ?>
                    <div class="apariencia-preview">
                        <img src="<?= e($val('apariencia_logo')) ?>" alt="Logo actual" class="apariencia-preview-img">
                        <span class="apariencia-preview-path"><?= e($val('apariencia_logo')) ?></span>
                    </div>
                <?php endif; ?>
                <input type="file" name="apariencia_logo" accept="image/png,image/jpeg,image/webp,image/svg+xml"
                       class="form-input">
                <small class="form-help">PNG, JPG, WebP o SVG. Máx 2 MB. Fondo transparente recomendado.</small>
            </div>
            <div class="form-group">
                <label class="form-label">Favicon</label>
                <?php if ($val('apariencia_favicon')): ?>
                    <div class="apariencia-preview">
                        <img src="<?= e($val('apariencia_favicon')) ?>" alt="Favicon actual" class="apariencia-preview-img apariencia-preview-img--small">
                        <span class="apariencia-preview-path"><?= e($val('apariencia_favicon')) ?></span>
                    </div>
                <?php endif; ?>
                <input type="file" name="apariencia_favicon" accept="image/png,image/x-icon,image/svg+xml"
                       class="form-input">
                <small class="form-help">PNG 32×32 o ICO. Máx 2 MB.</small>
            </div>
        </div>
    </fieldset>

    <!-- CSS y JS personalizado -->
    <fieldset class="form-fieldset">
        <legend>Código personalizado</legend>
        <div class="form-group">
            <label class="form-label" for="cfg_apariencia_css_custom">CSS personalizado</label>
            <textarea id="cfg_apariencia_css_custom" name="config[apariencia_css_custom]"
                      class="form-textarea form-textarea--code" rows="8"
                      placeholder="/* Se inyecta en <head> de todas las páginas */"><?= e($val('apariencia_css_custom')) ?></textarea>
        </div>
        <div class="form-group">
            <label class="form-label" for="cfg_apariencia_js_custom">JavaScript personalizado</label>
            <textarea id="cfg_apariencia_js_custom" name="config[apariencia_js_custom]"
                      class="form-textarea form-textarea--code" rows="8"
                      placeholder="// Se inyecta antes de </body>"><?= e($val('apariencia_js_custom')) ?></textarea>
        </div>
    </fieldset>

    <!-- Mantenimiento -->
    <fieldset class="form-fieldset">
        <legend>Modo mantenimiento</legend>
        <div class="form-group">
            <label class="form-toggle-label">
                <input type="hidden" name="config[apariencia_banner_mantenimiento]" value="0">
                <input type="checkbox" name="config[apariencia_banner_mantenimiento]" value="1"
                       class="form-toggle" <?= $val('apariencia_banner_mantenimiento') === '1' ? 'checked' : '' ?>>
                <span class="form-toggle-text">Mostrar banner de mantenimiento en el sitio público</span>
            </label>
            <small class="form-help">Muestra un aviso visible para los visitantes indicando que el sitio está en construcción.</small>
        </div>
    </fieldset>

    <!-- Preview rápido -->
    <fieldset class="form-fieldset">
        <legend>Vista previa de colores</legend>
        <div class="apariencia-preview-bar" id="previewBar">
            <div class="apariencia-preview-swatch" id="prev_header" style="background:<?= e($val('apariencia_color_header')) ?>">
                <span>Header</span>
            </div>
            <div class="apariencia-preview-swatch" id="prev_primario" style="background:<?= e($val('apariencia_color_primario')) ?>">
                <span>Primario</span>
            </div>
            <div class="apariencia-preview-swatch" id="prev_secundario" style="background:<?= e($val('apariencia_color_secundario')) ?>">
                <span>Secundario</span>
            </div>
            <div class="apariencia-preview-swatch" id="prev_footer" style="background:<?= e($val('apariencia_color_footer')) ?>">
                <span>Footer</span>
            </div>
        </div>
    </fieldset>

    <div class="form-actions">
        <button type="submit" class="btn btn--primary">Guardar apariencia</button>
    </div>
</form>

<script>
(function() {
    // Live color preview
    const map = {
        'apariencia_color_header':     'prev_header',
        'apariencia_color_primario':   'prev_primario',
        'apariencia_color_secundario': 'prev_secundario',
        'apariencia_color_footer':     'prev_footer',
    };
    Object.entries(map).forEach(([key, previewId]) => {
        const picker = document.getElementById('color_' + key);
        const input  = document.getElementById('cfg_' + key);
        const swatch = document.getElementById(previewId);
        if (!picker || !input || !swatch) return;

        function update(val) {
            swatch.style.background = val;
            input.value = val;
            picker.value = val;
        }
        picker.addEventListener('input', e => update(e.target.value));
        input.addEventListener('change', e => {
            if (/^#[0-9a-fA-F]{6}$/.test(e.target.value)) update(e.target.value);
        });
    });
})();
</script>
