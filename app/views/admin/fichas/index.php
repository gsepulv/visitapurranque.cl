<?php
/**
 * Admin — Listado de Fichas — visitapurranque.cl
 * Variables: $fichas, $categorias, $filtros, $pagina, $total, $totalPaginas, $csrf
 */
?>

<div class="admin-page-header admin-page-header--flex">
    <div>
        <h1>Fichas de Atractivos</h1>
        <p class="admin-page-subtitle"><?= $total ?> ficha<?= $total !== 1 ? 's' : '' ?> en total</p>
    </div>
    <a href="<?= url('/admin/fichas/crear') ?>" class="btn btn--primary">+ Nueva Ficha</a>
</div>

<!-- Filtros -->
<form class="admin-filters" method="GET" action="<?= url('/admin/fichas') ?>">
    <div class="admin-filters__group">
        <select name="categoria_id" class="form-select">
            <option value="">Todas las categorías</option>
            <?php foreach ($categorias as $cat): ?>
            <option value="<?= $cat['id'] ?>" <?= ($filtros['categoria_id'] == $cat['id']) ? 'selected' : '' ?>>
                <?= e($cat['nombre']) ?>
            </option>
            <?php endforeach; ?>
        </select>

        <select name="activo" class="form-select">
            <option value="">Todos los estados</option>
            <option value="1" <?= ($filtros['activo'] === '1') ? 'selected' : '' ?>>Activo</option>
            <option value="0" <?= ($filtros['activo'] === '0') ? 'selected' : '' ?>>Inactivo</option>
        </select>

        <div class="admin-filters__search">
            <input type="text" name="q" value="<?= e($filtros['q']) ?>"
                   placeholder="Buscar por nombre..." class="form-input">
            <button type="submit" class="btn btn--small">Buscar</button>
        </div>
    </div>

    <?php if (!empty($filtros['categoria_id']) || $filtros['activo'] !== '' || !empty($filtros['q'])): ?>
        <a href="<?= url('/admin/fichas') ?>" class="admin-filters__clear">Limpiar filtros</a>
    <?php endif; ?>
</form>

<!-- Tabla -->
<?php if (!empty($fichas)): ?>
<div class="admin-table-wrap">
    <table class="admin-table">
        <thead>
            <tr>
                <th></th>
                <th>Nombre</th>
                <th>Categoría</th>
                <th>Estado</th>
                <th>Verif.</th>
                <th>Dest.</th>
                <th>Creada</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($fichas as $f): ?>
            <tr>
                <td>
                    <?php if (!empty($f['imagen_portada'])): ?>
                        <img src="<?= url('uploads/fichas/' . $f['imagen_portada']) ?>"
                             alt="" class="table-thumb">
                    <?php else: ?>
                        <span class="table-thumb table-thumb--empty">&#128205;</span>
                    <?php endif; ?>
                </td>
                <td>
                    <strong><?= e($f['nombre']) ?></strong>
                    <?php if (!empty($f['direccion'])): ?>
                        <small class="table-sub"><?= e($f['direccion']) ?></small>
                    <?php endif; ?>
                </td>
                <td><?= e($f['categoria_nombre'] ?? '—') ?></td>
                <td>
                    <?php if ($f['activo']): ?>
                        <span class="badge badge--green">Activo</span>
                    <?php else: ?>
                        <span class="badge badge--gray">Inactivo</span>
                    <?php endif; ?>
                </td>
                <td><?= $f['verificado'] ? '<span class="badge badge--blue">Si</span>' : '—' ?></td>
                <td><?= $f['destacado'] ? '<span class="badge badge--yellow">Si</span>' : '—' ?></td>
                <td class="text-mono"><?= formatDate($f['created_at'], 'd/m/Y') ?></td>
                <td>
                    <div class="table-actions">
                        <a href="<?= url("/admin/fichas/{$f['id']}/editar") ?>"
                           class="btn btn--small btn--outline" title="Editar">Editar</a>

                        <form method="POST" action="<?= url("/admin/fichas/{$f['id']}/toggle") ?>" class="inline-form">
                            <?= csrf_field() ?>
                            <button type="submit" class="btn btn--small btn--outline"
                                    title="<?= $f['activo'] ? 'Desactivar' : 'Activar' ?>">
                                <?= $f['activo'] ? 'Desactivar' : 'Activar' ?>
                            </button>
                        </form>

                        <form method="POST" action="<?= url("/admin/fichas/{$f['id']}/eliminar") ?>" class="inline-form">
                            <?= csrf_field() ?>
                            <button type="submit" class="btn btn--small btn--danger"
                                    data-confirm="¿Eliminar &laquo;<?= e($f['nombre']) ?>&raquo;? Esta acción no se puede deshacer.">
                                Eliminar
                            </button>
                        </form>
                    </div>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<!-- Paginacion -->
<?php if ($totalPaginas > 1): ?>
<nav class="admin-pagination">
    <?php
    $qs = $_GET;
    unset($qs['pagina']);
    $qsStr = http_build_query($qs);
    $baseUrl = url('/admin/fichas') . ($qsStr ? '?' . $qsStr . '&' : '?');
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
    <?php if (!empty($filtros['q']) || !empty($filtros['categoria_id']) || $filtros['activo'] !== ''): ?>
        No se encontraron fichas con esos filtros.
    <?php else: ?>
        Aún no hay fichas registradas. <a href="<?= url('/admin/fichas/crear') ?>">Crear la primera</a>
    <?php endif; ?>
</p>
<?php endif; ?>
