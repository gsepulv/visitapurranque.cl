<?php
/**
 * Admin — Revisar Cambio Pendiente — visitapurranque.cl
 * Variables: $cambio, $datosNuevos, $datosAnteriores, $fichaActual, $csrf
 */

$labels = [
    'nombre'            => 'Nombre',
    'descripcion'       => 'Descripción',
    'descripcion_corta' => 'Descripción corta',
    'direccion'         => 'Dirección',
    'telefono'          => 'Teléfono',
    'whatsapp'          => 'WhatsApp',
    'email'             => 'Email',
    'sitio_web'         => 'Sitio web',
    'facebook'          => 'Facebook',
    'instagram'         => 'Instagram',
    'horarios'          => 'Horarios',
    'precio_desde'      => 'Precio desde',
    'precio_hasta'      => 'Precio hasta',
    'precio_texto'      => 'Precio (texto)',
    'temporada'         => 'Temporada',
    'como_llegar'       => 'Cómo llegar',
    'info_practica'     => 'Info práctica',
    'que_llevar'        => 'Qué llevar',
    'latitud'           => 'Latitud',
    'longitud'          => 'Longitud',
];
?>

<div class="admin-page-header">
    <h1>Revisar cambio #<?= $cambio['id'] ?></h1>
    <p class="admin-page-subtitle">
        <a href="<?= url('/admin/cambios') ?>">&larr; Volver a cambios pendientes</a>
    </p>
</div>

<!-- Info general -->
<fieldset class="form-fieldset">
    <legend>Información del cambio</legend>
    <div class="resena-meta">
        <div class="resena-meta__item">
            <span class="resena-meta__label">Ficha</span>
            <span class="resena-meta__value">
                <strong><?= e($cambio['ficha_nombre'] ?? '(eliminada)') ?></strong>
                <?php if ($cambio['ficha_id']): ?>
                    <a href="<?= url("/admin/fichas/{$cambio['ficha_id']}/editar") ?>" class="btn btn--small btn--outline" style="margin-left:8px">Ver ficha</a>
                <?php endif; ?>
            </span>
        </div>
        <div class="resena-meta__item">
            <span class="resena-meta__label">Tipo</span>
            <span class="resena-meta__value">
                <?php
                $tipoBadge = match($cambio['tipo']) {
                    'edicion'     => 'badge--blue',
                    'nueva'       => 'badge--green',
                    'eliminacion' => 'badge--red',
                    default       => 'badge--gray',
                };
                ?>
                <span class="badge <?= $tipoBadge ?>"><?= e(ucfirst($cambio['tipo'])) ?></span>
            </span>
        </div>
        <div class="resena-meta__item">
            <span class="resena-meta__label">Solicitante</span>
            <span class="resena-meta__value"><?= e($cambio['usuario_nombre'] ?? 'Sistema') ?>
                <?php if (!empty($cambio['usuario_email'])): ?>
                    <small>(<?= e($cambio['usuario_email']) ?>)</small>
                <?php endif; ?>
            </span>
        </div>
        <div class="resena-meta__item">
            <span class="resena-meta__label">Fecha</span>
            <span class="resena-meta__value"><?= formatDate($cambio['created_at'], 'd/m/Y H:i') ?></span>
        </div>
        <div class="resena-meta__item">
            <span class="resena-meta__label">Estado</span>
            <span class="resena-meta__value">
                <?php
                $estadoBadge = match($cambio['estado']) {
                    'pendiente' => 'badge--yellow',
                    'aprobado'  => 'badge--green',
                    'rechazado' => 'badge--red',
                    default     => 'badge--gray',
                };
                ?>
                <span class="badge <?= $estadoBadge ?>"><?= e(ucfirst($cambio['estado'])) ?></span>
                <?php if ($cambio['revisado_at']): ?>
                    <small>por <?= e($cambio['revisor_nombre'] ?? '?') ?> el <?= formatDate($cambio['revisado_at'], 'd/m/Y H:i') ?></small>
                <?php endif; ?>
            </span>
        </div>
        <?php if (!empty($cambio['motivo'])): ?>
        <div class="resena-meta__item">
            <span class="resena-meta__label">Motivo</span>
            <span class="resena-meta__value"><?= e($cambio['motivo']) ?></span>
        </div>
        <?php endif; ?>
        <?php if (!empty($cambio['nota_revision'])): ?>
        <div class="resena-meta__item">
            <span class="resena-meta__label">Nota revisión</span>
            <span class="resena-meta__value"><?= e($cambio['nota_revision']) ?></span>
        </div>
        <?php endif; ?>
    </div>
</fieldset>

<!-- Diff de cambios -->
<fieldset class="form-fieldset">
    <legend>Cambios propuestos</legend>

    <?php if (!empty($datosNuevos)): ?>
    <div class="admin-table-wrap">
        <table class="admin-table cambio-diff-table">
            <thead>
                <tr>
                    <th>Campo</th>
                    <th>Valor actual</th>
                    <th>Valor propuesto</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($datosNuevos as $campo => $valorNuevo): ?>
                <?php
                    $valorActual = $fichaActual[$campo] ?? $datosAnteriores[$campo] ?? '—';
                    $cambio_real = ($valorActual != $valorNuevo);
                ?>
                <tr class="<?= $cambio_real ? 'cambio-diff--changed' : '' ?>">
                    <td><strong><?= e($labels[$campo] ?? $campo) ?></strong></td>
                    <td class="cambio-diff__old"><?= e(is_array($valorActual) ? json_encode($valorActual) : (string)$valorActual) ?></td>
                    <td class="cambio-diff__new"><?= e(is_array($valorNuevo) ? json_encode($valorNuevo) : (string)$valorNuevo) ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php else: ?>
    <p class="admin-empty">Sin datos de cambio disponibles</p>
    <?php endif; ?>
</fieldset>

<!-- Acciones de revisión -->
<?php if ($cambio['estado'] === 'pendiente'): ?>
<fieldset class="form-fieldset">
    <legend>Revisión</legend>

    <div class="cambio-review-actions">
        <!-- Aprobar -->
        <form method="POST" action="<?= url("/admin/cambios/{$cambio['id']}/aprobar") ?>" class="admin-form cambio-review-form">
            <?= csrf_field() ?>
            <div class="form-group">
                <label class="form-label" for="nota_aprobacion">Nota (opcional)</label>
                <textarea id="nota_aprobacion" name="nota_revision" class="form-textarea" rows="2"
                          placeholder="Nota opcional al aprobar..."></textarea>
            </div>
            <button type="submit" class="btn btn--success" data-confirm="¿Aprobar este cambio y aplicarlo a la ficha?">Aprobar y aplicar</button>
        </form>

        <!-- Rechazar -->
        <form method="POST" action="<?= url("/admin/cambios/{$cambio['id']}/rechazar") ?>" class="admin-form cambio-review-form">
            <?= csrf_field() ?>
            <div class="form-group">
                <label class="form-label" for="nota_rechazo">Motivo del rechazo (obligatorio)</label>
                <textarea id="nota_rechazo" name="nota_revision" class="form-textarea" rows="2"
                          placeholder="Explica por qué se rechaza..." required></textarea>
            </div>
            <button type="submit" class="btn btn--danger" data-confirm="¿Rechazar este cambio?">Rechazar</button>
        </form>
    </div>
</fieldset>
<?php endif; ?>
