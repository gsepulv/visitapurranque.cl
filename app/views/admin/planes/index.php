<?php
/**
 * Admin — Listado de Planes — visitapurranque.cl
 * Variables: $planes, $csrf
 */
?>

<div class="admin-page-header admin-page-header--flex">
    <div>
        <h1>Planes</h1>
        <p class="admin-page-subtitle">
            <?= count($planes) ?> plan<?= count($planes) !== 1 ? 'es' : '' ?> configurados
            &nbsp;·&nbsp;
            <a href="<?= url('/admin/suscripciones') ?>">Ver suscripciones &rarr;</a>
        </p>
    </div>
    <a href="<?= url('/admin/planes/crear') ?>" class="btn btn--primary">+ Nuevo Plan</a>
</div>

<?php if (!empty($planes)): ?>
<div class="planes-grid">
    <?php foreach ($planes as $p):
        $precio = $p['precio_mensual'] > 0
            ? '$' . number_format($p['precio_mensual'], 0, ',', '.')
            : 'Gratis';
        $caracteristicas = !empty($p['caracteristicas']) ? json_decode($p['caracteristicas'], true) : [];
    ?>
    <div class="plan-card <?= !$p['activo'] ? 'plan-card--inactive' : '' ?>">
        <div class="plan-card__header">
            <h3 class="plan-card__nombre"><?= e($p['nombre']) ?></h3>
            <span class="plan-card__precio"><?= $precio ?></span>
            <?php if ($p['precio_mensual'] > 0): ?>
                <small class="plan-card__periodo">/mes</small>
            <?php endif; ?>
        </div>

        <div class="plan-card__body">
            <?php if (!empty($p['descripcion'])): ?>
                <p class="plan-card__desc"><?= e($p['descripcion']) ?></p>
            <?php endif; ?>

            <div class="plan-card__meta">
                <span class="badge badge--outline">Orden: <?= $p['orden'] ?></span>
                <span class="badge badge--outline"><?= $p['max_imagenes'] ?> imgs</span>
                <?php if ($p['tiene_badge']): ?>
                    <span class="badge badge--yellow">Badge</span>
                <?php endif; ?>
                <?php if ($p['destacado_home']): ?>
                    <span class="badge badge--blue">Home</span>
                <?php endif; ?>
                <span class="badge <?= $p['activo'] ? 'badge--green' : 'badge--gray' ?>">
                    <?= $p['activo'] ? 'Activo' : 'Inactivo' ?>
                </span>
            </div>

            <?php if (!empty($caracteristicas)): ?>
            <ul class="plan-card__features">
                <?php foreach ($caracteristicas as $feat): ?>
                    <li><?= e($feat) ?></li>
                <?php endforeach; ?>
            </ul>
            <?php endif; ?>

            <?php if ($p['precio_anual']): ?>
                <p class="plan-card__anual">Anual: $<?= number_format($p['precio_anual'], 0, ',', '.') ?></p>
            <?php endif; ?>
        </div>

        <div class="plan-card__actions">
            <a href="<?= url("/admin/planes/{$p['id']}/editar") ?>" class="btn btn--small btn--outline">Editar</a>
            <form method="POST" action="<?= url("/admin/planes/{$p['id']}/toggle") ?>" class="inline-form">
                <?= csrf_field() ?>
                <button type="submit" class="btn btn--small btn--outline">
                    <?= $p['activo'] ? 'Desactivar' : 'Activar' ?>
                </button>
            </form>
            <form method="POST" action="<?= url("/admin/planes/{$p['id']}/eliminar") ?>" class="inline-form">
                <?= csrf_field() ?>
                <button type="submit" class="btn btn--small btn--danger"
                        data-confirm="¿Eliminar plan &laquo;<?= e($p['nombre']) ?>&raquo;?">Eliminar</button>
            </form>
        </div>
    </div>
    <?php endforeach; ?>
</div>

<?php else: ?>
<p class="admin-empty">
    No hay planes configurados. <a href="<?= url('/admin/planes/crear') ?>">Crear el primero</a>
</p>
<?php endif; ?>
