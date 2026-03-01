<?php
/**
 * Admin — Listado de Categorías — visitapurranque.cl
 * Variables: $categorias, $filtros, $pagina, $total, $totalPaginas, $csrf
 */
?>

<div class="admin-page-header admin-page-header--flex">
    <div>
        <h1>Categorías</h1>
        <p class="admin-page-subtitle"><?= $total ?> categoría<?= $total !== 1 ? 's' : '' ?> en total</p>
    </div>
    <a href="<?= url('/admin/categorias/crear') ?>" class="btn btn--primary">+ Nueva Categoría</a>
</div>

<!-- Filtros -->
<form class="admin-filters" method="GET" action="<?= url('/admin/categorias') ?>">
    <div class="admin-filters__group">
        <select name="activo" class="form-select">
            <option value="">Todos los estados</option>
            <option value="1" <?= ($filtros['activo'] === '1') ? 'selected' : '' ?>>Activa</option>
            <option value="0" <?= ($filtros['activo'] === '0') ? 'selected' : '' ?>>Inactiva</option>
        </select>

        <div class="admin-filters__search">
            <input type="text" name="q" value="<?= e($filtros['q']) ?>"
                   placeholder="Buscar por nombre..." class="form-input">
            <button type="submit" class="btn btn--small">Buscar</button>
        </div>
    </div>

    <?php if ($filtros['activo'] !== '' || !empty($filtros['q'])): ?>
        <a href="<?= url('/admin/categorias') ?>" class="admin-filters__clear">Limpiar filtros</a>
    <?php endif; ?>
</form>

<!-- Tabla -->
<?php if (!empty($categorias)): ?>
<div class="admin-table-wrap">
    <table class="admin-table">
        <thead>
            <tr>
                <th>Orden</th>
                <th></th>
                <th>Nombre</th>
                <th>Slug</th>
                <th>Subcategorías</th>
                <th>Fichas</th>
                <th>Estado</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($categorias as $cat): ?>
            <tr>
                <td class="text-mono text-center"><?= (int)$cat['orden'] ?></td>
                <td>
                    <?php if (!empty($cat['emoji'])): ?>
                        <span class="table-emoji"><?= $cat['emoji'] ?></span>
                    <?php else: ?>
                        <span class="table-color-dot" style="background:<?= e($cat['color']) ?>"></span>
                    <?php endif; ?>
                </td>
                <td>
                    <strong><?= e($cat['nombre']) ?></strong>
                    <?php if (!empty($cat['descripcion'])): ?>
                        <small class="table-sub"><?= e(mb_strimwidth($cat['descripcion'], 0, 80, '...')) ?></small>
                    <?php endif; ?>
                </td>
                <td class="text-mono text-muted"><?= e($cat['slug']) ?></td>
                <td class="text-center"><?= (int)$cat['total_subcategorias'] ?></td>
                <td class="text-center"><?= (int)$cat['total_fichas'] ?></td>
                <td>
                    <?php if ($cat['activo']): ?>
                        <span class="badge badge--green">Activa</span>
                    <?php else: ?>
                        <span class="badge badge--gray">Inactiva</span>
                    <?php endif; ?>
                </td>
                <td>
                    <div class="table-actions">
                        <a href="<?= url("/admin/categorias/{$cat['id']}/editar") ?>"
                           class="btn btn--small btn--outline" title="Editar">Editar</a>

                        <form method="POST" action="<?= url("/admin/categorias/{$cat['id']}/toggle") ?>" class="inline-form">
                            <?= csrf_field() ?>
                            <button type="submit" class="btn btn--small btn--outline"
                                    title="<?= $cat['activo'] ? 'Desactivar' : 'Activar' ?>">
                                <?= $cat['activo'] ? 'Desactivar' : 'Activar' ?>
                            </button>
                        </form>

                        <?php if ((int)$cat['total_fichas'] === 0): ?>
                        <form method="POST" action="<?= url("/admin/categorias/{$cat['id']}/eliminar") ?>" class="inline-form">
                            <?= csrf_field() ?>
                            <button type="submit" class="btn btn--small btn--danger"
                                    data-confirm="¿Eliminar &laquo;<?= e($cat['nombre']) ?>&raquo;?">
                                Eliminar
                            </button>
                        </form>
                        <?php endif; ?>
                    </div>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<!-- Paginación -->
<?php if ($totalPaginas > 1): ?>
<nav class="admin-pagination">
    <?php
    $qs = $_GET;
    unset($qs['pagina']);
    $qsStr = http_build_query($qs);
    $baseUrl = url('/admin/categorias') . ($qsStr ? '?' . $qsStr . '&' : '?');
    ?>

    <?php if ($pagina > 1): ?>
        <a href="<?= $baseUrl ?>pagina=<?= $pagina - 1 ?>" class="pagination-link">&laquo; Anterior</a>
    <?php endif; ?>

    <?php for ($i = 1; $i <= $totalPaginas; $i++): ?>
        <?php if ($i === $pagina): ?>
            <span class="pagination-link pagination-link--active"><?= $i ?></span>
        <?php elseif (abs($i - $pagina) <= 2 || $i === 1 || $i === $totalPaginas): ?>
            <a href="<?= $baseUrl ?>pagina=<?= $i ?>" class="pagination-link"><?= $i ?></a>
        <?php elseif (abs($i - $pagina) === 3): ?>
            <span class="pagination-ellipsis">&hellip;</span>
        <?php endif; ?>
    <?php endfor; ?>

    <?php if ($pagina < $totalPaginas): ?>
        <a href="<?= $baseUrl ?>pagina=<?= $pagina + 1 ?>" class="pagination-link">Siguiente &raquo;</a>
    <?php endif; ?>
</nav>
<?php endif; ?>

<?php else: ?>
<p class="admin-empty">
    <?php if ($filtros['activo'] !== '' || !empty($filtros['q'])): ?>
        No se encontraron categorías con esos filtros.
    <?php else: ?>
        Aún no hay categorías registradas. <a href="<?= url('/admin/categorias/crear') ?>">Crear la primera</a>
    <?php endif; ?>
</p>
<?php endif; ?>
