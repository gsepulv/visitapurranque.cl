<?php
/**
 * Admin — Notificaciones
 * Variables: $notificaciones, $page, $totalPages, $total, $noLeidas
 */
?>

<div class="admin-page-header admin-page-header--flex">
    <div>
        <h1>Notificaciones</h1>
        <p class="admin-page-subtitle"><?= $total ?> notificaci<?= $total !== 1 ? 'ones' : 'ón' ?> — <?= $noLeidas ?> sin leer</p>
    </div>
    <?php if ($noLeidas > 0): ?>
    <form method="POST" action="<?= url('/admin/notificaciones/leer-todas') ?>">
        <?= csrf_field() ?>
        <button type="submit" class="btn btn--outline">Marcar todas como leídas</button>
    </form>
    <?php endif; ?>
</div>

<?php if (empty($notificaciones)): ?>
    <div class="admin-empty">No tienes notificaciones.</div>
<?php else: ?>
<div class="notif-list">
    <?php foreach ($notificaciones as $n): ?>
    <div class="notif-item<?= $n['leida'] ? '' : ' notif-item--unread' ?>">
        <div class="notif-icon">
            <?php
            $icons = [
                'resena'    => '&#11088;',
                'contacto'  => '&#128231;',
                'sistema'   => '&#9881;',
                'ficha'     => '&#128205;',
                'evento'    => '&#128197;',
                'blog'      => '&#128221;',
            ];
            echo $icons[$n['tipo']] ?? '&#128276;';
            ?>
        </div>
        <div class="notif-body">
            <div class="notif-title">
                <?php if ($n['url']): ?>
                    <a href="<?= url($n['url']) ?>"><?= e($n['titulo']) ?></a>
                <?php else: ?>
                    <?= e($n['titulo']) ?>
                <?php endif; ?>
            </div>
            <?php if ($n['mensaje']): ?>
                <div class="notif-message"><?= e($n['mensaje']) ?></div>
            <?php endif; ?>
            <div class="notif-time"><?= tiempoRelativo($n['created_at']) ?></div>
        </div>
        <?php if (!$n['leida']): ?>
        <form method="POST" action="<?= url("/admin/notificaciones/leer/{$n['id']}") ?>" style="flex-shrink:0;">
            <?= csrf_field() ?>
            <button type="submit" class="btn btn--small btn--outline" title="Marcar como leída">&#10003;</button>
        </form>
        <?php endif; ?>
    </div>
    <?php endforeach; ?>
</div>

<?php if ($totalPages > 1): ?>
<div class="admin-pagination">
    <?php if ($page > 1): ?>
        <a href="<?= url('/admin/notificaciones?page=' . ($page - 1)) ?>" class="btn btn--small btn--outline">&laquo; Anterior</a>
    <?php endif; ?>
    <span class="text-muted" style="padding:0 12px;">Página <?= $page ?> de <?= $totalPages ?></span>
    <?php if ($page < $totalPages): ?>
        <a href="<?= url('/admin/notificaciones?page=' . ($page + 1)) ?>" class="btn btn--small btn--outline">Siguiente &raquo;</a>
    <?php endif; ?>
</div>
<?php endif; ?>

<?php endif; ?>
