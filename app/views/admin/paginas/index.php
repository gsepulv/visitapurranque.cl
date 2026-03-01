<?php
/**
 * Admin — Páginas Estáticas — visitapurranque.cl
 * Variables: $paginas, $q
 */
?>

<div class="admin-page-header admin-page-header--flex">
    <div>
        <h1>Páginas Estáticas</h1>
        <p class="admin-page-subtitle"><?= count($paginas) ?> páginas</p>
    </div>
    <div class="admin-page-actions">
        <a href="<?= url('/admin/paginas/crear') ?>" class="btn btn--primary">+ Nueva página</a>
    </div>
</div>

<!-- Buscador -->
<form method="GET" action="<?= url('/admin/paginas') ?>" class="admin-filters">
    <input type="text" name="q" value="<?= e($q) ?>" class="form-input" placeholder="Buscar por título o slug...">
    <button type="submit" class="btn btn--outline">Buscar</button>
    <?php if ($q): ?>
        <a href="<?= url('/admin/paginas') ?>" class="btn btn--outline">Limpiar</a>
    <?php endif; ?>
</form>

<?php if (empty($paginas)): ?>
    <p class="admin-empty">No hay páginas registradas.</p>
<?php else: ?>
<div class="admin-table-wrap">
    <table class="admin-table">
        <thead>
            <tr>
                <th>Título</th>
                <th>Slug</th>
                <th class="text-center">Template</th>
                <th class="text-center">Orden</th>
                <th class="text-center">Estado</th>
                <th>Última edición</th>
                <th class="text-right">Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($paginas as $p): ?>
            <tr>
                <td><strong><?= e($p['titulo']) ?></strong></td>
                <td>
                    <a href="<?= url('/' . $p['slug']) ?>" target="_blank" class="text-mono" style="font-size:.85rem">
                        /<?= e($p['slug']) ?>
                    </a>
                </td>
                <td class="text-center">
                    <span class="badge badge--outline"><?= e($p['template']) ?></span>
                </td>
                <td class="text-center text-mono"><?= $p['orden'] ?></td>
                <td class="text-center">
                    <form method="POST" action="<?= url("/admin/paginas/{$p['id']}/toggle") ?>" class="inline-form">
                        <?= csrf_field() ?>
                        <button type="submit" class="badge <?= $p['activo'] ? 'badge--green' : 'badge--red' ?>"
                                style="cursor:pointer;border:0">
                            <?= $p['activo'] ? 'Activa' : 'Inactiva' ?>
                        </button>
                    </form>
                </td>
                <td style="font-size:.85rem"><?= $p['updated_at'] ? date('d/m/Y H:i', strtotime($p['updated_at'])) : '—' ?></td>
                <td class="text-right">
                    <a href="<?= url("/admin/paginas/{$p['id']}/editar") ?>" class="btn btn--xs btn--outline">Editar</a>
                    <form method="POST" action="<?= url("/admin/paginas/{$p['id']}/eliminar") ?>" class="inline-form"
                          onsubmit="return confirm('¿Eliminar página «<?= e($p['titulo']) ?>»?')">
                        <?= csrf_field() ?>
                        <button type="submit" class="btn btn--xs btn--red">Eliminar</button>
                    </form>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php endif; ?>
