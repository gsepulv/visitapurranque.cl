<?php
/**
 * Admin — Ver Versión de Página — visitapurranque.cl
 * Variables: $pagina, $version
 */
?>

<div class="admin-page-header admin-page-header--flex">
    <div>
        <h1>Versión #<?= $version['id'] ?></h1>
        <p class="admin-page-subtitle">
            Página: <?= e($pagina['titulo']) ?> —
            <?= date('d/m/Y H:i', strtotime($version['created_at'])) ?>
            por <?= e($version['usuario_nombre'] ?? 'Sistema') ?>
        </p>
    </div>
    <div class="admin-page-actions">
        <a href="<?= url("/admin/paginas/{$pagina['id']}/editar") ?>" class="btn btn--outline">&larr; Volver a editar</a>
        <form method="POST" action="<?= url("/admin/paginas/{$pagina['id']}/restaurar/{$version['id']}") ?>"
              class="inline-form" onsubmit="return confirm('¿Restaurar esta versión?')">
            <?= csrf_field() ?>
            <button type="submit" class="btn btn--primary">Restaurar esta versión</button>
        </form>
    </div>
</div>

<?php if ($version['nota']): ?>
<div class="admin-flash admin-flash--info">Nota: <?= e($version['nota']) ?></div>
<?php endif; ?>

<fieldset class="form-fieldset">
    <legend>Contenido de esta versión</legend>
    <div class="version-content-preview">
        <pre class="version-code"><?= e($version['contenido']) ?></pre>
    </div>
</fieldset>

<fieldset class="form-fieldset">
    <legend>Contenido actual (para comparar)</legend>
    <div class="version-content-preview">
        <pre class="version-code"><?= e($pagina['contenido']) ?></pre>
    </div>
</fieldset>
