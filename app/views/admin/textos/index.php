<?php
/**
 * Admin — Textos Editables — visitapurranque.cl
 * Variables: $grupos (sección => rows[])
 */
$total = 0;
foreach ($grupos as $items) $total += count($items);
?>

<div class="admin-page-header admin-page-header--flex">
    <div>
        <h1>Textos Editables</h1>
        <p class="admin-page-subtitle"><?= $total ?> textos en <?= count($grupos) ?> secciones</p>
    </div>
    <div class="admin-page-actions">
        <a href="<?= url('/admin/textos/crear') ?>" class="btn btn--primary">+ Nuevo texto</a>
    </div>
</div>

<?php if (empty($grupos)): ?>
    <p class="admin-empty">No hay textos editables registrados.</p>
<?php else: ?>

<form method="POST" action="<?= url('/admin/textos/guardar') ?>" class="admin-form">
    <?= csrf_field() ?>

    <?php foreach ($grupos as $seccion => $items): ?>
    <fieldset class="form-fieldset">
        <legend><?= e(ucfirst($seccion)) ?> <span class="badge badge--outline"><?= count($items) ?></span></legend>

        <?php foreach ($items as $t): ?>
        <div class="form-group textos-grupo">
            <div class="textos-header">
                <label class="form-label" for="txt_<?= $t['id'] ?>">
                    <code class="textos-clave"><?= e($t['clave']) ?></code>
                    <?php if ($t['descripcion']): ?>
                        <span class="textos-desc">— <?= e($t['descripcion']) ?></span>
                    <?php endif; ?>
                </label>
                <div class="textos-actions">
                    <?php if ($t['valor'] !== $t['valor_default']): ?>
                        <form method="POST" action="<?= url("/admin/textos/{$t['id']}/restaurar") ?>" class="inline-form"
                              onsubmit="return confirm('¿Restaurar a valor original?')">
                            <?= csrf_field() ?>
                            <button type="submit" class="btn btn--xs btn--outline" title="Restaurar default">&#8634;</button>
                        </form>
                    <?php endif; ?>
                    <a href="<?= url("/admin/textos/{$t['id']}/editar") ?>" class="btn btn--xs btn--outline" title="Editar registro">&#9998;</a>
                    <form method="POST" action="<?= url("/admin/textos/{$t['id']}/eliminar") ?>" class="inline-form"
                          onsubmit="return confirm('¿Eliminar texto?')">
                        <?= csrf_field() ?>
                        <button type="submit" class="btn btn--xs btn--red" title="Eliminar">&#10005;</button>
                    </form>
                </div>
            </div>
            <?php if ($t['tipo'] === 'textarea' || $t['tipo'] === 'html'): ?>
                <textarea id="txt_<?= $t['id'] ?>" name="texto[<?= $t['id'] ?>]"
                          class="form-textarea<?= $t['tipo'] === 'html' ? ' form-textarea--code' : '' ?>"
                          rows="3"><?= e($t['valor']) ?></textarea>
            <?php else: ?>
                <input type="text" id="txt_<?= $t['id'] ?>" name="texto[<?= $t['id'] ?>]"
                       value="<?= e($t['valor']) ?>" class="form-input">
            <?php endif; ?>
            <?php if ($t['valor'] !== $t['valor_default']): ?>
                <small class="form-help textos-modified">Modificado (original: <?= e(mb_strimwidth($t['valor_default'], 0, 60, '...')) ?>)</small>
            <?php endif; ?>
        </div>
        <?php endforeach; ?>
    </fieldset>
    <?php endforeach; ?>

    <div class="form-actions">
        <button type="submit" class="btn btn--primary">Guardar todos los textos</button>
    </div>
</form>

<?php endif; ?>
