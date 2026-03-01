<?php
/**
 * Admin — Listado de Banners — visitapurranque.cl
 * Variables: $banners, $posiciones, $filtros, $pagina, $total, $totalPaginas, $csrf
 */
?>

<div class="admin-page-header admin-page-header--flex">
    <div>
        <h1>Banners</h1>
        <p class="admin-page-subtitle"><?= $total ?> banner<?= $total !== 1 ? 's' : '' ?> en total</p>
    </div>
    <a href="<?= url('/admin/banners/crear') ?>" class="btn btn--primary">+ Nuevo Banner</a>
</div>

<!-- Filtros -->
<form class="admin-filters" method="GET" action="<?= url('/admin/banners') ?>">
    <div class="admin-filters__group">
        <select name="posicion" class="form-select">
            <option value="">Todas las posiciones</option>
            <?php foreach ($posiciones as $val => $label): ?>
            <option value="<?= $val ?>" <?= ($filtros['posicion'] === $val) ? 'selected' : '' ?>><?= $label ?></option>
            <?php endforeach; ?>
        </select>

        <select name="activo" class="form-select">
            <option value="">Todos</option>
            <option value="1" <?= ($filtros['activo'] === '1') ? 'selected' : '' ?>>Activos</option>
            <option value="0" <?= ($filtros['activo'] === '0') ? 'selected' : '' ?>>Inactivos</option>
        </select>

        <select name="variante" class="form-select">
            <option value="">Todas las variantes</option>
            <option value="A" <?= ($filtros['variante'] === 'A') ? 'selected' : '' ?>>Variante A</option>
            <option value="B" <?= ($filtros['variante'] === 'B') ? 'selected' : '' ?>>Variante B</option>
        </select>

        <div class="admin-filters__search">
            <input type="text" name="q" value="<?= e($filtros['q']) ?>"
                   placeholder="Buscar por título..." class="form-input">
            <button type="submit" class="btn btn--small">Buscar</button>
        </div>
    </div>

    <?php if (!empty($filtros['posicion']) || $filtros['activo'] !== '' || !empty($filtros['variante']) || !empty($filtros['q'])): ?>
        <a href="<?= url('/admin/banners') ?>" class="admin-filters__clear">Limpiar filtros</a>
    <?php endif; ?>
</form>

<!-- Tabla -->
<?php if (!empty($banners)): ?>
<div class="admin-table-wrap">
    <table class="admin-table">
        <thead>
            <tr>
                <th></th>
                <th>Título</th>
                <th>Posición</th>
                <th>Variante</th>
                <th>Vigencia</th>
                <th>Impresiones</th>
                <th>Clics</th>
                <th>CTR</th>
                <th>Estado</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($banners as $b):
                $ctr = $b['impresiones'] > 0
                    ? round(($b['clics'] / $b['impresiones']) * 100, 2)
                    : 0;

                $vigente = true;
                $hoy = date('Y-m-d');
                if (!empty($b['fecha_inicio']) && $hoy < $b['fecha_inicio']) $vigente = false;
                if (!empty($b['fecha_fin']) && $hoy > $b['fecha_fin']) $vigente = false;
            ?>
            <tr class="<?= (!$b['activo'] || !$vigente) ? 'row--muted' : '' ?>">
                <td>
                    <img src="<?= url('uploads/banners/' . $b['imagen']) ?>"
                         alt="" class="table-thumb" style="width:80px;height:40px;">
                </td>
                <td>
                    <strong><?= e($b['titulo']) ?></strong>
                    <?php if (!empty($b['url'])): ?>
                        <small class="table-sub"><?= e(mb_strimwidth($b['url'], 0, 40, '...')) ?></small>
                    <?php endif; ?>
                </td>
                <td><span class="badge badge--outline"><?= $posiciones[$b['posicion']] ?? $b['posicion'] ?></span></td>
                <td class="text-center">
                    <span class="badge <?= $b['variante'] === 'A' ? 'badge--blue' : 'badge--purple' ?>"><?= $b['variante'] ?></span>
                </td>
                <td class="text-mono" style="white-space:nowrap">
                    <?php if (!empty($b['fecha_inicio']) || !empty($b['fecha_fin'])): ?>
                        <?= !empty($b['fecha_inicio']) ? formatDate($b['fecha_inicio'], 'd/m') : '—' ?>
                        &rarr;
                        <?= !empty($b['fecha_fin']) ? formatDate($b['fecha_fin'], 'd/m') : '—' ?>
                        <?php if (!$vigente): ?>
                            <small class="table-sub" style="color:var(--admin-red)">Fuera de vigencia</small>
                        <?php endif; ?>
                    <?php else: ?>
                        <span class="text-muted">Permanente</span>
                    <?php endif; ?>
                </td>
                <td class="text-mono text-center"><?= number_format($b['impresiones']) ?></td>
                <td class="text-mono text-center"><?= number_format($b['clics']) ?></td>
                <td class="text-mono text-center">
                    <span class="badge <?= $ctr >= 2 ? 'badge--green' : ($ctr >= 0.5 ? 'badge--yellow' : 'badge--gray') ?>">
                        <?= $ctr ?>%
                    </span>
                </td>
                <td>
                    <span class="badge <?= $b['activo'] ? 'badge--green' : 'badge--gray' ?>">
                        <?= $b['activo'] ? 'Activo' : 'Inactivo' ?>
                    </span>
                </td>
                <td>
                    <div class="table-actions">
                        <a href="<?= url("/admin/banners/{$b['id']}/editar") ?>"
                           class="btn btn--small btn--outline">Editar</a>

                        <form method="POST" action="<?= url("/admin/banners/{$b['id']}/toggle") ?>" class="inline-form">
                            <?= csrf_field() ?>
                            <button type="submit" class="btn btn--small btn--outline">
                                <?= $b['activo'] ? 'Desactivar' : 'Activar' ?>
                            </button>
                        </form>

                        <form method="POST" action="<?= url("/admin/banners/{$b['id']}/eliminar") ?>" class="inline-form">
                            <?= csrf_field() ?>
                            <button type="submit" class="btn btn--small btn--danger"
                                    data-confirm="¿Eliminar &laquo;<?= e($b['titulo']) ?>&raquo;?">
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
    $baseUrl = url('/admin/banners') . ($qsStr ? '?' . $qsStr . '&' : '?');
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
    <?php if (!empty($filtros['q']) || !empty($filtros['posicion']) || $filtros['activo'] !== '' || !empty($filtros['variante'])): ?>
        No se encontraron banners con esos filtros.
    <?php else: ?>
        Aún no hay banners. <a href="<?= url('/admin/banners/crear') ?>">Crear el primero</a>
    <?php endif; ?>
</p>
<?php endif; ?>
