<?php
/**
 * Admin — Galería de Medios
 * Variables: $medios, $filtros, $pagina, $total, $totalPaginas, $totalSize, $csrf
 */
$formatSize = function(int $bytes): string {
    if ($bytes >= 1048576) return round($bytes / 1048576, 1) . ' MB';
    if ($bytes >= 1024) return round($bytes / 1024, 1) . ' KB';
    return $bytes . ' B';
};
?>

<div class="admin-page-header admin-page-header--flex">
    <div>
        <h1>Galería de Medios</h1>
        <p class="admin-page-subtitle"><?= $total ?> archivo<?= $total !== 1 ? 's' : '' ?> — <?= $formatSize($totalSize) ?> usados</p>
    </div>
    <a href="<?= url('/admin/medios/crear') ?>" class="btn btn--primary">+ Subir Archivo</a>
</div>

<form class="admin-filters" method="GET" action="<?= url('/admin/medios') ?>">
    <div class="admin-filters__group">
        <select name="tipo" class="form-select">
            <option value="">Todos los tipos</option>
            <option value="image" <?= $filtros['tipo'] === 'image' ? 'selected' : '' ?>>Imágenes</option>
            <option value="application/pdf" <?= $filtros['tipo'] === 'application/pdf' ? 'selected' : '' ?>>PDF</option>
            <option value="application/msword" <?= str_starts_with($filtros['tipo'], 'application/msword') ? 'selected' : '' ?>>Documentos</option>
        </select>
        <div class="admin-filters__search">
            <input type="text" name="q" value="<?= e($filtros['q']) ?>"
                   placeholder="Buscar por nombre..." class="form-input">
            <button type="submit" class="btn btn--small">Buscar</button>
        </div>
    </div>
    <?php if (!empty($filtros['tipo']) || !empty($filtros['q'])): ?>
        <a href="<?= url('/admin/medios') ?>" class="admin-filters__clear">Limpiar filtros</a>
    <?php endif; ?>
</form>

<?php if (empty($medios)): ?>
    <div class="admin-empty">No hay archivos<?= !empty($filtros['q']) ? ' que coincidan con la búsqueda' : '' ?>.</div>
<?php else: ?>

<div class="media-grid">
    <?php foreach ($medios as $m): ?>
    <div class="media-card">
        <div class="media-card__preview">
            <?php if (str_starts_with($m['tipo'], 'image/')): ?>
                <img src="<?= asset('uploads/' . $m['archivo']) ?>" alt="<?= e($m['alt'] ?? $m['nombre']) ?>" loading="lazy">
            <?php elseif ($m['tipo'] === 'application/pdf'): ?>
                <div class="media-card__icon">PDF</div>
            <?php else: ?>
                <div class="media-card__icon">DOC</div>
            <?php endif; ?>
            <div class="media-card__overlay">
                <a href="<?= url("/admin/medios/{$m['id']}/editar") ?>" class="btn btn--small">Editar</a>
                <form method="POST" action="<?= url("/admin/medios/{$m['id']}/eliminar") ?>"
                      onsubmit="return confirm('¿Eliminar este archivo?')">
                    <?= csrf_field() ?>
                    <button type="submit" class="btn btn--small btn--danger">Eliminar</button>
                </form>
            </div>
        </div>
        <div class="media-card__info">
            <span class="media-card__name" title="<?= e($m['nombre']) ?>"><?= e(mb_strimwidth($m['nombre'], 0, 30, '...')) ?></span>
            <span class="media-card__meta"><?= $formatSize($m['tamano']) ?><?= $m['ancho'] ? " — {$m['ancho']}x{$m['alto']}" : '' ?></span>
        </div>
    </div>
    <?php endforeach; ?>
</div>

<?php if ($totalPaginas > 1): ?>
<div class="admin-pagination">
    <?php if ($pagina > 1): ?>
        <a href="<?= url('/admin/medios?p=' . ($pagina - 1) . ($filtros['tipo'] ? '&tipo=' . urlencode($filtros['tipo']) : '') . ($filtros['q'] ? '&q=' . urlencode($filtros['q']) : '')) ?>" class="btn btn--small">&laquo; Anterior</a>
    <?php endif; ?>
    <span class="admin-pagination__info">Página <?= $pagina ?> de <?= $totalPaginas ?></span>
    <?php if ($pagina < $totalPaginas): ?>
        <a href="<?= url('/admin/medios?p=' . ($pagina + 1) . ($filtros['tipo'] ? '&tipo=' . urlencode($filtros['tipo']) : '') . ($filtros['q'] ? '&q=' . urlencode($filtros['q']) : '')) ?>" class="btn btn--small">Siguiente &raquo;</a>
    <?php endif; ?>
</div>
<?php endif; ?>

<?php endif; ?>
