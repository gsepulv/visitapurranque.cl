<?php
/**
 * Admin — Detalle de Mensaje — visitapurranque.cl
 * Variables: $msg, $csrf
 */
?>

<div class="admin-page-header">
    <h1>Mensaje de <?= e($msg['nombre']) ?></h1>
    <p class="admin-page-subtitle">
        <a href="<?= url('/admin/mensajes') ?>">&larr; Volver a la bandeja</a>
    </p>
</div>

<div class="msg-detail">
    <!-- Mensaje original -->
    <fieldset class="form-fieldset">
        <legend>Mensaje recibido</legend>

        <div class="resena-meta">
            <div class="resena-meta__item">
                <span class="resena-meta__label">De</span>
                <span class="resena-meta__value"><?= e($msg['nombre']) ?></span>
            </div>
            <div class="resena-meta__item">
                <span class="resena-meta__label">Email</span>
                <span class="resena-meta__value">
                    <a href="mailto:<?= e($msg['email']) ?>"><?= e($msg['email']) ?></a>
                </span>
            </div>
            <?php if (!empty($msg['telefono'])): ?>
            <div class="resena-meta__item">
                <span class="resena-meta__label">Teléfono</span>
                <span class="resena-meta__value"><?= e($msg['telefono']) ?></span>
            </div>
            <?php endif; ?>
            <div class="resena-meta__item">
                <span class="resena-meta__label">Fecha</span>
                <span class="resena-meta__value"><?= formatDate($msg['created_at'], 'd/m/Y H:i') ?></span>
            </div>
            <div class="resena-meta__item">
                <span class="resena-meta__label">IP</span>
                <span class="resena-meta__value text-mono"><?= e($msg['ip'] ?? '—') ?></span>
            </div>
            <div class="resena-meta__item">
                <span class="resena-meta__label">Estado</span>
                <span class="resena-meta__value">
                    <?php if ($msg['respondido']): ?>
                        <span class="badge badge--green">Respondido</span>
                    <?php elseif ($msg['leido']): ?>
                        <span class="badge badge--gray">Leído</span>
                    <?php else: ?>
                        <span class="badge badge--yellow">Nuevo</span>
                    <?php endif; ?>
                </span>
            </div>
        </div>

        <?php if (!empty($msg['asunto'])): ?>
        <div class="msg-asunto">
            <span class="resena-meta__label">Asunto</span>
            <strong><?= e($msg['asunto']) ?></strong>
        </div>
        <?php endif; ?>

        <div class="resena-comentario">
            <span class="resena-meta__label">Mensaje</span>
            <div class="resena-comentario__text"><?= nl2br(e($msg['mensaje'])) ?></div>
        </div>
    </fieldset>

    <!-- Acciones rápidas -->
    <fieldset class="form-fieldset">
        <legend>Acciones</legend>
        <div class="resena-actions-bar">
            <a href="mailto:<?= e($msg['email']) ?>?subject=Re: <?= e($msg['asunto'] ?? 'Contacto Visita Purranque') ?>"
               class="btn btn--outline">Responder por email</a>

            <form method="POST" action="<?= url("/admin/mensajes/{$msg['id']}/toggle-leido") ?>" class="inline-form">
                <?= csrf_field() ?>
                <button type="submit" class="btn btn--outline">
                    <?= $msg['leido'] ? 'Marcar como no leído' : 'Marcar como leído' ?>
                </button>
            </form>

            <form method="POST" action="<?= url("/admin/mensajes/{$msg['id']}/eliminar") ?>" class="inline-form">
                <?= csrf_field() ?>
                <button type="submit" class="btn btn--danger"
                        data-confirm="¿Eliminar este mensaje?">Eliminar</button>
            </form>
        </div>
    </fieldset>

    <!-- Respuesta del admin -->
    <fieldset class="form-fieldset">
        <legend>Respuesta interna</legend>

        <?php if (!empty($msg['respuesta'])): ?>
        <div class="resena-respuesta-actual">
            <div class="resena-comentario__text"><?= nl2br(e($msg['respuesta'])) ?></div>
            <small class="form-help">Respondido el <?= formatDate($msg['respuesta_fecha'], 'd/m/Y H:i') ?></small>
        </div>
        <?php endif; ?>

        <form method="POST" action="<?= url("/admin/mensajes/{$msg['id']}/responder") ?>" class="admin-form">
            <?= csrf_field() ?>
            <div class="form-group">
                <label class="form-label" for="respuesta">
                    <?= !empty($msg['respuesta']) ? 'Editar nota/respuesta' : 'Agregar nota/respuesta' ?>
                </label>
                <textarea id="respuesta" name="respuesta" class="form-textarea" rows="4"
                          placeholder="Registra la respuesta enviada o notas sobre este mensaje..."><?= e($msg['respuesta'] ?? '') ?></textarea>
                <small class="form-help">Esta nota es interna. Para responder al visitante, usa el botón "Responder por email" arriba.</small>
            </div>
            <div class="form-actions">
                <button type="submit" class="btn btn--primary">Guardar respuesta</button>
            </div>
        </form>
    </fieldset>
</div>
