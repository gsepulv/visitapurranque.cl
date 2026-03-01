<?php
/**
 * Admin — Bandeja de Mensajes — visitapurranque.cl
 * Variables: $mensajes, $filtros, $pagina, $total, $totalPaginas, $stats, $csrf
 */
?>

<div class="admin-page-header admin-page-header--flex">
    <div>
        <h1>Mensajes</h1>
        <p class="admin-page-subtitle"><?= $total ?> mensaje<?= $total !== 1 ? 's' : '' ?> en total</p>
    </div>
</div>

<!-- Stats -->
<div class="resena-stats">
    <span class="resena-stat resena-stat--red"><?= $stats['no_leidos'] ?> sin leer</span>
    <span class="resena-stat resena-stat--yellow"><?= $stats['sin_respuesta'] ?> sin respuesta</span>
    <span class="resena-stat resena-stat--green"><?= $stats['respondidos'] ?> respondido<?= $stats['respondidos'] !== 1 ? 's' : '' ?></span>
</div>

<!-- Filtros -->
<form class="admin-filters" method="GET" action="<?= url('/admin/mensajes') ?>">
    <div class="admin-filters__group">
        <select name="leido" class="form-select">
            <option value="">Todos</option>
            <option value="0" <?= ($filtros['leido'] === '0') ? 'selected' : '' ?>>No leídos</option>
            <option value="1" <?= ($filtros['leido'] === '1') ? 'selected' : '' ?>>Leídos</option>
        </select>

        <select name="respondido" class="form-select">
            <option value="">Todos</option>
            <option value="0" <?= ($filtros['respondido'] === '0') ? 'selected' : '' ?>>Sin respuesta</option>
            <option value="1" <?= ($filtros['respondido'] === '1') ? 'selected' : '' ?>>Respondidos</option>
        </select>

        <div class="admin-filters__search">
            <input type="text" name="q" value="<?= e($filtros['q']) ?>"
                   placeholder="Buscar por nombre, email, asunto..." class="form-input">
            <button type="submit" class="btn btn--small">Buscar</button>
        </div>
    </div>

    <?php if ($filtros['leido'] !== '' || $filtros['respondido'] !== '' || !empty($filtros['q'])): ?>
        <a href="<?= url('/admin/mensajes') ?>" class="admin-filters__clear">Limpiar filtros</a>
    <?php endif; ?>
</form>

<!-- Tabla -->
<?php if (!empty($mensajes)): ?>
<div class="admin-table-wrap">
    <table class="admin-table">
        <thead>
            <tr>
                <th></th>
                <th>De</th>
                <th>Asunto</th>
                <th>Mensaje</th>
                <th>Fecha</th>
                <th>Estado</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($mensajes as $m): ?>
            <tr class="<?= !$m['leido'] ? 'msg-row--unread' : '' ?>">
                <td class="text-center">
                    <?php if (!$m['leido']): ?>
                        <span class="msg-dot" title="No leído"></span>
                    <?php endif; ?>
                </td>
                <td>
                    <strong><?= e($m['nombre']) ?></strong>
                    <small class="table-sub"><?= e($m['email']) ?></small>
                    <?php if (!empty($m['telefono'])): ?>
                        <small class="table-sub"><?= e($m['telefono']) ?></small>
                    <?php endif; ?>
                </td>
                <td><?= e($m['asunto'] ?: '(sin asunto)') ?></td>
                <td class="resena-comentario-cell">
                    <?= e(mb_strimwidth($m['mensaje'], 0, 80, '...')) ?>
                </td>
                <td class="text-mono" style="white-space:nowrap">
                    <?= formatDate($m['created_at'], 'd/m/Y') ?>
                    <small class="table-sub"><?= formatDate($m['created_at'], 'H:i') ?></small>
                </td>
                <td>
                    <?php if ($m['respondido']): ?>
                        <span class="badge badge--green">Respondido</span>
                    <?php elseif ($m['leido']): ?>
                        <span class="badge badge--gray">Leído</span>
                    <?php else: ?>
                        <span class="badge badge--yellow">Nuevo</span>
                    <?php endif; ?>
                </td>
                <td>
                    <div class="table-actions">
                        <a href="<?= url("/admin/mensajes/{$m['id']}") ?>"
                           class="btn btn--small btn--outline">Ver</a>

                        <form method="POST" action="<?= url("/admin/mensajes/{$m['id']}/toggle-leido") ?>" class="inline-form">
                            <?= csrf_field() ?>
                            <button type="submit" class="btn btn--small btn--outline" title="<?= $m['leido'] ? 'Marcar no leído' : 'Marcar leído' ?>">
                                <?= $m['leido'] ? '✉' : '📩' ?>
                            </button>
                        </form>

                        <form method="POST" action="<?= url("/admin/mensajes/{$m['id']}/eliminar") ?>" class="inline-form">
                            <?= csrf_field() ?>
                            <button type="submit" class="btn btn--small btn--danger"
                                    data-confirm="¿Eliminar mensaje de &laquo;<?= e($m['nombre']) ?>&raquo;?">Eliminar</button>
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
    $baseUrl = url('/admin/mensajes') . ($qsStr ? '?' . $qsStr . '&' : '?');
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
    <?php if ($filtros['leido'] !== '' || $filtros['respondido'] !== '' || !empty($filtros['q'])): ?>
        No se encontraron mensajes con esos filtros.
    <?php else: ?>
        No hay mensajes en la bandeja.
    <?php endif; ?>
</p>
<?php endif; ?>
