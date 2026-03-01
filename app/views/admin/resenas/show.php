<?php
/**
 * Admin — Detalle de Reseña (moderación) — visitapurranque.cl
 * Variables: $resena, $csrf
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

<div class="admin-page-header">
    <h1>Reseña #<?= $resena['id'] ?></h1>
    <p class="admin-page-subtitle">
        <a href="<?= url('/admin/resenas') ?>">&larr; Volver al listado</a>
    </p>
</div>

<!-- Detalle de la reseña -->
<div class="resena-detail">
    <fieldset class="form-fieldset">
        <legend>Información del visitante</legend>

        <div class="resena-meta">
            <div class="resena-meta__item">
                <span class="resena-meta__label">Nombre</span>
                <span class="resena-meta__value"><?= e($resena['nombre']) ?></span>
            </div>
            <?php if (!empty($resena['email'])): ?>
            <div class="resena-meta__item">
                <span class="resena-meta__label">Email</span>
                <span class="resena-meta__value"><?= e($resena['email']) ?></span>
            </div>
            <?php endif; ?>
            <?php if (!empty($resena['ciudad_origen'])): ?>
            <div class="resena-meta__item">
                <span class="resena-meta__label">Ciudad de origen</span>
                <span class="resena-meta__value"><?= e($resena['ciudad_origen']) ?></span>
            </div>
            <?php endif; ?>
            <div class="resena-meta__item">
                <span class="resena-meta__label">IP</span>
                <span class="resena-meta__value text-mono"><?= e($resena['ip'] ?? '—') ?></span>
            </div>
            <div class="resena-meta__item">
                <span class="resena-meta__label">Fecha envío</span>
                <span class="resena-meta__value"><?= formatDate($resena['created_at'], 'd/m/Y H:i') ?></span>
            </div>
        </div>
    </fieldset>

    <fieldset class="form-fieldset">
        <legend>Reseña</legend>

        <div class="resena-meta">
            <div class="resena-meta__item">
                <span class="resena-meta__label">Ficha / Atractivo</span>
                <span class="resena-meta__value"><?= e($resena['ficha_nombre'] ?? 'Ficha #' . $resena['ficha_id']) ?></span>
            </div>
            <div class="resena-meta__item">
                <span class="resena-meta__label">Rating</span>
                <span class="resena-meta__value rating-stars rating-stars--lg"><?= str_repeat('★', $resena['rating']) . str_repeat('☆', 5 - $resena['rating']) ?> (<?= $resena['rating'] ?>/5)</span>
            </div>
            <div class="resena-meta__item">
                <span class="resena-meta__label">Tipo de experiencia</span>
                <span class="resena-meta__value"><?= $tipoExpLabel[$resena['tipo_experiencia']] ?? $resena['tipo_experiencia'] ?></span>
            </div>
            <?php if (!empty($resena['fecha_visita'])): ?>
            <div class="resena-meta__item">
                <span class="resena-meta__label">Fecha de visita</span>
                <span class="resena-meta__value"><?= formatDate($resena['fecha_visita'], 'd/m/Y') ?></span>
            </div>
            <?php endif; ?>
            <div class="resena-meta__item">
                <span class="resena-meta__label">Estado</span>
                <span class="resena-meta__value">
                    <span class="badge <?= $estadoBadge[$resena['estado']] ?? 'badge--gray' ?>"><?= ucfirst($resena['estado']) ?></span>
                </span>
            </div>
        </div>

        <div class="resena-comentario">
            <span class="resena-meta__label">Comentario</span>
            <div class="resena-comentario__text"><?= nl2br(e($resena['comentario'])) ?></div>
        </div>
    </fieldset>

    <!-- Acciones de moderación -->
    <fieldset class="form-fieldset">
        <legend>Moderación</legend>

        <div class="resena-actions-bar">
            <?php if ($resena['estado'] !== 'aprobada'): ?>
            <form method="POST" action="<?= url("/admin/resenas/{$resena['id']}/estado") ?>" class="inline-form">
                <?= csrf_field() ?>
                <input type="hidden" name="estado" value="aprobada">
                <button type="submit" class="btn btn--primary">Aprobar</button>
            </form>
            <?php endif; ?>

            <?php if ($resena['estado'] !== 'rechazada'): ?>
            <form method="POST" action="<?= url("/admin/resenas/{$resena['id']}/estado") ?>" class="inline-form">
                <?= csrf_field() ?>
                <input type="hidden" name="estado" value="rechazada">
                <button type="submit" class="btn btn--outline">Rechazar</button>
            </form>
            <?php endif; ?>

            <?php if ($resena['estado'] !== 'spam'): ?>
            <form method="POST" action="<?= url("/admin/resenas/{$resena['id']}/estado") ?>" class="inline-form">
                <?= csrf_field() ?>
                <input type="hidden" name="estado" value="spam">
                <button type="submit" class="btn btn--outline">Marcar spam</button>
            </form>
            <?php endif; ?>

            <?php if ($resena['estado'] !== 'pendiente'): ?>
            <form method="POST" action="<?= url("/admin/resenas/{$resena['id']}/estado") ?>" class="inline-form">
                <?= csrf_field() ?>
                <input type="hidden" name="estado" value="pendiente">
                <button type="submit" class="btn btn--outline">Devolver a pendiente</button>
            </form>
            <?php endif; ?>

            <form method="POST" action="<?= url("/admin/resenas/{$resena['id']}/eliminar") ?>" class="inline-form">
                <?= csrf_field() ?>
                <button type="submit" class="btn btn--danger"
                        data-confirm="¿Eliminar esta reseña permanentemente?">Eliminar</button>
            </form>
        </div>
    </fieldset>

    <!-- Respuesta del admin -->
    <fieldset class="form-fieldset">
        <legend>Respuesta del administrador</legend>

        <?php if (!empty($resena['respuesta_admin'])): ?>
        <div class="resena-respuesta-actual">
            <div class="resena-comentario__text"><?= nl2br(e($resena['respuesta_admin'])) ?></div>
            <small class="form-help">Respondida el <?= formatDate($resena['respuesta_fecha'], 'd/m/Y H:i') ?></small>
        </div>
        <?php endif; ?>

        <form method="POST" action="<?= url("/admin/resenas/{$resena['id']}/responder") ?>" class="admin-form">
            <?= csrf_field() ?>
            <div class="form-group">
                <label class="form-label" for="respuesta_admin">
                    <?= !empty($resena['respuesta_admin']) ? 'Editar respuesta' : 'Escribir respuesta' ?>
                </label>
                <textarea id="respuesta_admin" name="respuesta_admin" class="form-textarea" rows="4"
                          placeholder="Escribe una respuesta pública al visitante..."><?= e($resena['respuesta_admin'] ?? '') ?></textarea>
            </div>
            <div class="form-actions">
                <button type="submit" class="btn btn--primary">Guardar respuesta</button>
            </div>
        </form>
    </fieldset>
</div>
