<?php
/**
 * Admin — SEO y Redes Sociales — visitapurranque.cl
 * Variables: $cobertura, $configSeo, $configRedes, $sinMeta, $csrf
 */

// Helper para indexar config por clave
$cfgMap = [];
foreach (array_merge($configSeo, $configRedes) as $c) {
    $cfgMap[$c['clave']] = $c;
}
?>

<div class="admin-page-header admin-page-header--flex">
    <div>
        <h1>SEO y Redes Sociales</h1>
        <p class="admin-page-subtitle">Optimización para buscadores y presencia social</p>
    </div>
    <div class="admin-page-actions">
        <a href="<?= url('/admin/seo/compartidos') ?>" class="btn btn--outline">Estadísticas compartidos</a>
    </div>
</div>

<!-- Cobertura SEO -->
<fieldset class="form-fieldset">
    <legend>Cobertura de Meta Tags</legend>
    <div class="admin-table-wrap">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>Sección</th>
                    <th class="text-center">Total</th>
                    <th class="text-center">Con meta_title</th>
                    <th class="text-center">Con meta_description</th>
                    <th>Cobertura título</th>
                    <th>Cobertura descripción</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($cobertura as $key => $c): ?>
                <tr>
                    <td><strong><?= e($c['label']) ?></strong></td>
                    <td class="text-center text-mono"><?= $c['total'] ?></td>
                    <td class="text-center text-mono"><?= $c['con_title'] ?></td>
                    <td class="text-center text-mono"><?= $c['con_desc'] ?></td>
                    <td>
                        <div class="seo-bar">
                            <div class="seo-bar__fill seo-bar__fill--<?= $c['pct_title'] >= 80 ? 'green' : ($c['pct_title'] >= 50 ? 'yellow' : 'red') ?>"
                                 style="width:<?= $c['pct_title'] ?>%"></div>
                            <span class="seo-bar__label"><?= $c['pct_title'] ?>%</span>
                        </div>
                    </td>
                    <td>
                        <div class="seo-bar">
                            <div class="seo-bar__fill seo-bar__fill--<?= $c['pct_desc'] >= 80 ? 'green' : ($c['pct_desc'] >= 50 ? 'yellow' : 'red') ?>"
                                 style="width:<?= $c['pct_desc'] ?>%"></div>
                            <span class="seo-bar__label"><?= $c['pct_desc'] ?>%</span>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</fieldset>

<!-- Registros sin Meta Tags -->
<?php if (!empty($sinMeta)): ?>
<fieldset class="form-fieldset">
    <legend>Registros sin meta tags (acción rápida)</legend>
    <?php foreach ($sinMeta as $tabla => $items): ?>
    <div class="seo-sinmeta-grupo">
        <h4><?= e(ucfirst($tabla)) ?></h4>
        <div class="seo-sinmeta-list">
            <?php foreach ($items as $item): ?>
            <a href="<?= url($item['edit_url']) ?>" class="seo-sinmeta-item">
                <?= e($item['nombre']) ?>
                <span class="badge badge--red">Sin SEO</span>
            </a>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endforeach; ?>
</fieldset>
<?php endif; ?>

<!-- Configuración SEO -->
<form method="POST" action="<?= url('/admin/seo/guardar') ?>" class="admin-form">
    <?= csrf_field() ?>

    <fieldset class="form-fieldset">
        <legend>Configuración SEO global</legend>

        <?php foreach ($configSeo as $c): ?>
        <div class="form-group">
            <label class="form-label" for="cfg_<?= e($c['clave']) ?>"><?= e($c['descripcion']) ?></label>
            <?php if ($c['tipo'] === 'textarea'): ?>
                <textarea id="cfg_<?= e($c['clave']) ?>" name="config[<?= e($c['clave']) ?>]"
                          class="form-textarea" rows="3"><?= e($c['valor']) ?></textarea>
            <?php else: ?>
                <input type="text" id="cfg_<?= e($c['clave']) ?>" name="config[<?= e($c['clave']) ?>]"
                       value="<?= e($c['valor']) ?>" class="form-input"
                       <?= $c['clave'] === 'seo_site_title' ? 'maxlength="70"' : '' ?>
                       <?= $c['clave'] === 'seo_site_description' ? 'maxlength="160"' : '' ?>>
            <?php endif; ?>
            <?php if (in_array($c['clave'], ['seo_site_title'])): ?>
                <small class="form-help">Máximo 70 caracteres para buscadores</small>
            <?php elseif ($c['clave'] === 'seo_site_description'): ?>
                <small class="form-help">Máximo 160 caracteres para snippet de Google</small>
            <?php endif; ?>
        </div>
        <?php endforeach; ?>
    </fieldset>

    <fieldset class="form-fieldset">
        <legend>Redes Sociales</legend>

        <?php foreach ($configRedes as $c): ?>
        <div class="form-group">
            <label class="form-label" for="cfg_<?= e($c['clave']) ?>"><?= e($c['descripcion']) ?></label>
            <input type="text" id="cfg_<?= e($c['clave']) ?>" name="config[<?= e($c['clave']) ?>]"
                   value="<?= e($c['valor']) ?>" class="form-input"
                   placeholder="https://...">
        </div>
        <?php endforeach; ?>
    </fieldset>

    <div class="form-actions">
        <button type="submit" class="btn btn--primary">Guardar configuración</button>
    </div>
</form>
