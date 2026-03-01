<?php
/**
 * Admin — Listado de Reseñas (moderación) — visitapurranque.cl
 * Variables: $resenas, $fichas, $filtros, $pagina, $total, $totalPaginas, $stats, $csrf
 */

$estadoBadge = [
    'pendiente'  => 'badge--yellow',
    'aprobada'   => 'badge--green',
    'rechazada'  => 'badge--gray',
    'spam'       => 'badge--red',
];

$tipoExpLabel = [
    'trekking'        => 'Trekking',
    'visita_cultural' => 'Visita Cultural',
    'gastronomia'     => 'Gastronomía',
    'playa'           => 'Playa',
    'camping'         => 'Camping',
    'tour_guiado'     => 'Tour Guiado',
    'alojamiento'     => 'Alojamiento',
    'otro'            => 'Otro',
];
?>

<div class="admin-page-header admin-page-header--flex">
    <div>
        <h1>Reseñas</h1>
        <p class="admin-page-subtitle"><?= $total ?> reseña<?= $total !== 1 ? 's' : '' ?> en total</p>
    </div>
</div>

<!-- Stats rápidas -->
<div class="resena-stats">
    <span class="resena-stat resena-stat--yellow"><?= $stats['pendiente'] ?> pendiente<?= $stats['pendiente'] !== 1 ? 's' : '' ?></span>
    <span class="resena-stat resena-stat--green"><?= $stats['aprobada'] ?> aprobada<?= $stats['aprobada'] !== 1 ? 's' : '' ?></span>
    <span class="resena-stat resena-stat--gray"><?= $stats['rechazada'] ?> rechazada<?= $stats['rechazada'] !== 1 ? 's' : '' ?></span>
    <span class="resena-stat resena-stat--red"><?= $stats['spam'] ?> spam</span>
</div>

<!-- Filtros -->
<form class="admin-filters" method="GET" action="<?= url('/admin/resenas') ?>">
    <div class="admin-filters__group">
        <select name="estado" class="form-select">
            <option value="">Todos los estados</option>
            <option value="pendiente" <?= ($filtros['estado'] === 'pendiente') ? 'selected' : '' ?>>Pendiente</option>
            <option value="aprobada" <?= ($filtros['estado'] === 'aprobada') ? 'selected' : '' ?>>Aprobada</option>
            <option value="rechazada" <?= ($filtros['estado'] === 'rechazada') ? 'selected' : '' ?>>Rechazada</option>
            <option value="spam" <?= ($filtros['estado'] === 'spam') ? 'selected' : '' ?>>Spam</option>
        </select>

        <select name="rating" class="form-select">
            <option value="">Todas las notas</option>
            <?php for ($r = 5; $r >= 1; $r--): ?>
            <option value="<?= $r ?>" <?= ((int)$filtros['rating'] === $r) ? 'selected' : '' ?>><?= str_repeat('★', $r) . str_repeat('☆', 5 - $r) ?></option>
            <?php endfor; ?>
        </select>

        <?php if (!empty($fichas)): ?>
        <select name="ficha_id" class="form-select">
            <option value="">Todas las fichas</option>
            <?php foreach ($fichas as $f): ?>
            <option value="<?= $f['id'] ?>" <?= ((int)$filtros['ficha_id'] === (int)$f['id']) ? 'selected' : '' ?>><?= e($f['nombre']) ?></option>
            <?php endforeach; ?>
        </select>
        <?php endif; ?>

        <div class="admin-filters__search">
            <input type="text" name="q" value="<?= e($filtros['q']) ?>"
                   placeholder="Buscar por nombre, email o comentario..." class="form-input">
            <button type="submit" class="btn btn--small">Buscar</button>
        </div>
    </div>

    <?php if (!empty($filtros['estado']) || !empty($filtros['ficha_id']) || !empty($filtros['rating']) || !empty($filtros['tipo_experiencia']) || !empty($filtros['q'])): ?>
        <a href="<?= url('/admin/resenas') ?>" class="admin-filters__clear">Limpiar filtros</a>
    <?php endif; ?>
</form>

<!-- Tabla -->
<?php if (!empty($resenas)): ?>
<div class="admin-table-wrap">
    <table class="admin-table">
        <thead>
            <tr>
                <th>Autor</th>
                <th>Ficha</th>
                <th>Rating</th>
                <th>Comentario</th>
                <th>Tipo</th>
                <th>Estado</th>
                <th>Fecha</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($resenas as $r): ?>
            <tr class="<?= $r['estado'] === 'spam' ? 'row--muted' : '' ?>">
                <td>
                    <strong><?= e($r['nombre']) ?></strong>
                    <?php if (!empty($r['email'])): ?>
                        <small class="table-sub"><?= e($r['email']) ?></small>
                    <?php endif; ?>
                    <?php if (!empty($r['ciudad_origen'])): ?>
                        <small class="table-sub"><?= e($r['ciudad_origen']) ?></small>
                    <?php endif; ?>
                </td>
                <td><?= e($r['ficha_nombre'] ?? '—') ?></td>
                <td>
                    <span class="rating-stars" title="<?= $r['rating'] ?>/5">
                        <?= str_repeat('★', $r['rating']) . str_repeat('☆', 5 - $r['rating']) ?>
                    </span>
                </td>
                <td class="resena-comentario-cell">
                    <?= e(mb_strimwidth($r['comentario'], 0, 100, '...')) ?>
                    <?php if (!empty($r['respuesta_admin'])): ?>
                        <small class="table-sub">↳ Respondida</small>
                    <?php endif; ?>
                </td>
                <td><span class="badge badge--outline"><?= $tipoExpLabel[$r['tipo_experiencia']] ?? $r['tipo_experiencia'] ?></span></td>
                <td>
                    <span class="badge <?= $estadoBadge[$r['estado']] ?? 'badge--gray' ?>">
                        <?= ucfirst($r['estado']) ?>
                    </span>
                </td>
                <td class="text-mono" style="white-space:nowrap">
                    <?= formatDate($r['created_at'], 'd/m/Y') ?>
                    <?php if (!empty($r['fecha_visita'])): ?>
                        <small class="table-sub">Visita: <?= formatDate($r['fecha_visita'], 'd/m/Y') ?></small>
                    <?php endif; ?>
                </td>
                <td>
                    <div class="table-actions">
                        <a href="<?= url("/admin/resenas/{$r['id']}") ?>"
                           class="btn btn--small btn--outline">Ver</a>

                        <?php if ($r['estado'] === 'pendiente'): ?>
                        <form method="POST" action="<?= url("/admin/resenas/{$r['id']}/estado") ?>" class="inline-form">
                            <?= csrf_field() ?>
                            <input type="hidden" name="estado" value="aprobada">
                            <button type="submit" class="btn btn--small btn--success" title="Aprobar">✓</button>
                        </form>
                        <form method="POST" action="<?= url("/admin/resenas/{$r['id']}/estado") ?>" class="inline-form">
                            <?= csrf_field() ?>
                            <input type="hidden" name="estado" value="rechazada">
                            <button type="submit" class="btn btn--small btn--danger" title="Rechazar">✗</button>
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
    $baseUrl = url('/admin/resenas') . ($qsStr ? '?' . $qsStr . '&' : '?');
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
    <?php if (!empty($filtros['q']) || !empty($filtros['estado']) || !empty($filtros['ficha_id']) || !empty($filtros['rating'])): ?>
        No se encontraron reseñas con esos filtros.
    <?php else: ?>
        Aún no hay reseñas registradas.
    <?php endif; ?>
</p>
<?php endif; ?>
