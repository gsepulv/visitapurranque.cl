<?php
/**
 * Admin — Listado de Eventos — visitapurranque.cl
 * Variables: $eventos, $categorias, $filtros, $pagina, $total, $totalPaginas, $csrf
 */
?>

<div class="admin-page-header admin-page-header--flex">
    <div>
        <h1>Eventos</h1>
        <p class="admin-page-subtitle"><?= $total ?> evento<?= $total !== 1 ? 's' : '' ?> en total</p>
    </div>
    <a href="<?= url('/admin/eventos/crear') ?>" class="btn btn--primary">+ Nuevo Evento</a>
</div>

<!-- Filtros -->
<form class="admin-filters" method="GET" action="<?= url('/admin/eventos') ?>">
    <div class="admin-filters__group">
        <select name="tiempo" class="form-select">
            <option value="">Todos los eventos</option>
            <option value="proximos" <?= ($filtros['tiempo'] === 'proximos') ? 'selected' : '' ?>>Próximos</option>
            <option value="pasados" <?= ($filtros['tiempo'] === 'pasados') ? 'selected' : '' ?>>Pasados</option>
        </select>

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
                   placeholder="Buscar por título, lugar..." class="form-input">
            <button type="submit" class="btn btn--small">Buscar</button>
        </div>
    </div>

    <?php if (!empty($filtros['categoria_id']) || $filtros['activo'] !== '' || !empty($filtros['q']) || !empty($filtros['tiempo'])): ?>
        <a href="<?= url('/admin/eventos') ?>" class="admin-filters__clear">Limpiar filtros</a>
    <?php endif; ?>
</form>

<!-- Tabla -->
<?php if (!empty($eventos)): ?>
<div class="admin-table-wrap">
    <table class="admin-table">
        <thead>
            <tr>
                <th>Fecha</th>
                <th>Título</th>
                <th>Lugar</th>
                <th>Categoría</th>
                <th>Estado</th>
                <th>Dest.</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($eventos as $ev): ?>
            <?php
                $fechaInicio = strtotime($ev['fecha_inicio']);
                $esPasado = $fechaInicio < time();
            ?>
            <tr<?= $esPasado ? ' class="row--muted"' : '' ?>>
                <td class="text-mono" style="white-space:nowrap">
                    <?= formatDate($ev['fecha_inicio'], 'd/m/Y') ?>
                    <small class="table-sub"><?= formatDate($ev['fecha_inicio'], 'H:i') ?></small>
                </td>
                <td>
                    <strong><?= e($ev['titulo']) ?></strong>
                    <?php if ($ev['recurrente']): ?>
                        <span class="badge badge--purple" title="Recurrente">&#x21bb;</span>
                    <?php endif; ?>
                </td>
                <td><?= e($ev['lugar'] ?? '—') ?></td>
                <td><?= e($ev['categoria_nombre'] ?? '—') ?></td>
                <td>
                    <?php if ($ev['activo']): ?>
                        <span class="badge badge--green">Activo</span>
                    <?php else: ?>
                        <span class="badge badge--gray">Inactivo</span>
                    <?php endif; ?>
                </td>
                <td><?= $ev['destacado'] ? '<span class="badge badge--yellow">Sí</span>' : '—' ?></td>
                <td>
                    <div class="table-actions">
                        <a href="<?= url("/admin/eventos/{$ev['id']}/editar") ?>"
                           class="btn btn--small btn--outline">Editar</a>

                        <form method="POST" action="<?= url("/admin/eventos/{$ev['id']}/toggle") ?>" class="inline-form">
                            <?= csrf_field() ?>
                            <button type="submit" class="btn btn--small btn--outline">
                                <?= $ev['activo'] ? 'Desactivar' : 'Activar' ?>
                            </button>
                        </form>

                        <form method="POST" action="<?= url("/admin/eventos/{$ev['id']}/eliminar") ?>" class="inline-form">
                            <?= csrf_field() ?>
                            <button type="submit" class="btn btn--small btn--danger"
                                    data-confirm="¿Eliminar &laquo;<?= e($ev['titulo']) ?>&raquo;?">
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

<!-- Paginación -->
<?php if ($totalPaginas > 1): ?>
<nav class="admin-pagination">
    <?php
    $qs = $_GET;
    unset($qs['pagina']);
    $qsStr = http_build_query($qs);
    $baseUrl = url('/admin/eventos') . ($qsStr ? '?' . $qsStr . '&' : '?');
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
    <?php if (!empty($filtros['q']) || !empty($filtros['categoria_id']) || $filtros['activo'] !== '' || !empty($filtros['tiempo'])): ?>
        No se encontraron eventos con esos filtros.
    <?php else: ?>
        Aún no hay eventos registrados. <a href="<?= url('/admin/eventos/crear') ?>">Crear el primero</a>
    <?php endif; ?>
</p>
<?php endif; ?>
