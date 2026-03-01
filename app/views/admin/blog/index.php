<?php
/**
 * Admin — Listado de Posts — visitapurranque.cl
 * Variables: $posts, $categorias, $autores, $filtros, $pagina, $total, $totalPaginas, $csrf
 */

$estadoBadge = [
    'borrador'   => 'badge--gray',
    'revision'   => 'badge--yellow',
    'programado' => 'badge--purple',
    'publicado'  => 'badge--green',
    'archivado'  => 'badge--red',
];

$tipoLabel = [
    'noticia'    => 'Noticia',
    'articulo'   => 'Artículo',
    'guia'       => 'Guía',
    'opinion'    => 'Opinión',
    'entrevista' => 'Entrevista',
    'galeria'    => 'Galería',
];
?>

<div class="admin-page-header admin-page-header--flex">
    <div>
        <h1>Blog</h1>
        <p class="admin-page-subtitle"><?= $total ?> post<?= $total !== 1 ? 's' : '' ?> en total</p>
    </div>
    <a href="<?= url('/admin/blog/crear') ?>" class="btn btn--primary">+ Nuevo Post</a>
</div>

<!-- Filtros -->
<form class="admin-filters" method="GET" action="<?= url('/admin/blog') ?>">
    <div class="admin-filters__group">
        <select name="estado" class="form-select">
            <option value="">Todos los estados</option>
            <option value="borrador" <?= ($filtros['estado'] === 'borrador') ? 'selected' : '' ?>>Borrador</option>
            <option value="revision" <?= ($filtros['estado'] === 'revision') ? 'selected' : '' ?>>En revisión</option>
            <option value="programado" <?= ($filtros['estado'] === 'programado') ? 'selected' : '' ?>>Programado</option>
            <option value="publicado" <?= ($filtros['estado'] === 'publicado') ? 'selected' : '' ?>>Publicado</option>
            <option value="archivado" <?= ($filtros['estado'] === 'archivado') ? 'selected' : '' ?>>Archivado</option>
        </select>

        <select name="tipo" class="form-select">
            <option value="">Todos los tipos</option>
            <?php foreach ($tipoLabel as $val => $label): ?>
            <option value="<?= $val ?>" <?= ($filtros['tipo'] === $val) ? 'selected' : '' ?>><?= $label ?></option>
            <?php endforeach; ?>
        </select>

        <select name="categoria_id" class="form-select">
            <option value="">Todas las categorías</option>
            <?php foreach ($categorias as $cat): ?>
            <option value="<?= $cat['id'] ?>" <?= ($filtros['categoria_id'] == $cat['id']) ? 'selected' : '' ?>>
                <?= e($cat['nombre']) ?>
            </option>
            <?php endforeach; ?>
        </select>

        <div class="admin-filters__search">
            <input type="text" name="q" value="<?= e($filtros['q']) ?>"
                   placeholder="Buscar por título..." class="form-input">
            <button type="submit" class="btn btn--small">Buscar</button>
        </div>
    </div>

    <?php if (!empty($filtros['categoria_id']) || !empty($filtros['estado']) || !empty($filtros['tipo']) || !empty($filtros['autor_id']) || !empty($filtros['q'])): ?>
        <a href="<?= url('/admin/blog') ?>" class="admin-filters__clear">Limpiar filtros</a>
    <?php endif; ?>
</form>

<!-- Tabla -->
<?php if (!empty($posts)): ?>
<div class="admin-table-wrap">
    <table class="admin-table">
        <thead>
            <tr>
                <th></th>
                <th>Título</th>
                <th>Tipo</th>
                <th>Categoría</th>
                <th>Autor</th>
                <th>Estado</th>
                <th>Fecha</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($posts as $p): ?>
            <tr>
                <td>
                    <?php if (!empty($p['imagen_portada'])): ?>
                        <img src="<?= url('uploads/blog/' . $p['imagen_portada']) ?>"
                             alt="" class="table-thumb">
                    <?php else: ?>
                        <span class="table-thumb table-thumb--empty">&#128221;</span>
                    <?php endif; ?>
                </td>
                <td>
                    <strong><?= e($p['titulo']) ?></strong>
                    <?php if ($p['destacado']): ?>
                        <span class="badge badge--yellow" title="Destacado">&#9733;</span>
                    <?php endif; ?>
                    <?php if (!empty($p['extracto'])): ?>
                        <small class="table-sub"><?= e(mb_strimwidth($p['extracto'], 0, 80, '...')) ?></small>
                    <?php endif; ?>
                </td>
                <td><span class="badge badge--outline"><?= $tipoLabel[$p['tipo']] ?? $p['tipo'] ?></span></td>
                <td><?= e($p['categoria_nombre'] ?? '—') ?></td>
                <td><?= e($p['autor_nombre'] ?? '—') ?></td>
                <td>
                    <span class="badge <?= $estadoBadge[$p['estado']] ?? 'badge--gray' ?>">
                        <?= ucfirst($p['estado']) ?>
                    </span>
                </td>
                <td class="text-mono" style="white-space:nowrap">
                    <?php if ($p['estado'] === 'publicado' && !empty($p['publicado_at'])): ?>
                        <?= formatDate($p['publicado_at'], 'd/m/Y') ?>
                    <?php elseif ($p['estado'] === 'programado' && !empty($p['programado_at'])): ?>
                        <?= formatDate($p['programado_at'], 'd/m/Y H:i') ?>
                    <?php else: ?>
                        <?= formatDate($p['created_at'], 'd/m/Y') ?>
                    <?php endif; ?>
                    <?php if ($p['tiempo_lectura']): ?>
                        <small class="table-sub"><?= $p['tiempo_lectura'] ?> min</small>
                    <?php endif; ?>
                </td>
                <td>
                    <div class="table-actions">
                        <a href="<?= url("/admin/blog/{$p['id']}/editar") ?>"
                           class="btn btn--small btn--outline">Editar</a>

                        <form method="POST" action="<?= url("/admin/blog/{$p['id']}/eliminar") ?>" class="inline-form">
                            <?= csrf_field() ?>
                            <button type="submit" class="btn btn--small btn--danger"
                                    data-confirm="¿Eliminar &laquo;<?= e($p['titulo']) ?>&raquo;?">
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
    $baseUrl = url('/admin/blog') . ($qsStr ? '?' . $qsStr . '&' : '?');
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
    <?php if (!empty($filtros['q']) || !empty($filtros['categoria_id']) || !empty($filtros['estado']) || !empty($filtros['tipo'])): ?>
        No se encontraron posts con esos filtros.
    <?php else: ?>
        Aún no hay posts publicados. <a href="<?= url('/admin/blog/crear') ?>">Escribir el primero</a>
    <?php endif; ?>
</p>
<?php endif; ?>
