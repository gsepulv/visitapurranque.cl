<?php
/**
 * Admin — Editor de Menú (drag & drop) — visitapurranque.cl
 * Variables: $tab, $itemsPrincipal, $itemsFooter, $countPrincipal, $countFooter, $csrf
 */

/** Renderizar lista sorteable recursiva */
function renderMenuTree(array $items, string $csrf): void {
    foreach ($items as $item): ?>
        <li class="menu-sortable-item" data-id="<?= (int)$item['id'] ?>">
            <div class="menu-item-row <?= $item['activo'] ? '' : 'menu-item-row--inactive' ?>">
                <span class="menu-drag-handle" title="Arrastrar">&#10303;</span>
                <?php if (!empty($item['icono'])): ?>
                    <span class="menu-item-icon"><?= e($item['icono']) ?></span>
                <?php endif; ?>
                <span class="menu-item-title"><?= e($item['titulo']) ?></span>
                <span class="menu-item-url"><?= e($item['url'] ?? '') ?></span>
                <span class="badge badge--small badge--<?= match($item['tipo']) {
                    'enlace' => 'blue', 'categoria' => 'green', 'pagina' => 'purple', 'externo' => 'orange', default => 'gray'
                } ?>"><?= e($item['tipo']) ?></span>
                <?php if (!$item['activo']): ?>
                    <span class="badge badge--small badge--gray">inactivo</span>
                <?php endif; ?>
                <div class="menu-item-actions">
                    <a href="<?= url("/admin/menu/{$item['id']}/editar") ?>" class="btn btn--xs btn--outline" title="Editar">&#9998;</a>
                    <form method="POST" action="<?= url("/admin/menu/{$item['id']}/toggle") ?>" class="inline-form">
                        <input type="hidden" name="_csrf" value="<?= $csrf ?>">
                        <button type="submit" class="btn btn--xs btn--outline" title="<?= $item['activo'] ? 'Desactivar' : 'Activar' ?>">
                            <?= $item['activo'] ? '&#128064;' : '&#128065;' ?>
                        </button>
                    </form>
                    <form method="POST" action="<?= url("/admin/menu/{$item['id']}/eliminar") ?>" class="inline-form"
                          onsubmit="return confirm('¿Eliminar este ítem?')">
                        <input type="hidden" name="_csrf" value="<?= $csrf ?>">
                        <button type="submit" class="btn btn--xs btn--danger" title="Eliminar">&#128465;</button>
                    </form>
                </div>
            </div>
            <?php if (!empty($item['children'])): ?>
                <ul class="menu-sortable-list menu-sortable-nested">
                    <?php renderMenuTree($item['children'], $csrf); ?>
                </ul>
            <?php else: ?>
                <ul class="menu-sortable-list menu-sortable-nested"></ul>
            <?php endif; ?>
        </li>
    <?php endforeach;
}
?>

<div class="admin-page-header admin-page-header--flex">
    <div>
        <h1>Editor de Menú</h1>
        <p class="admin-page-subtitle">Arrastra para reordenar. Los sub-ítems se pueden anidar.</p>
    </div>
    <a href="<?= url('/admin/menu/crear?menu=' . urlencode($tab)) ?>" class="btn btn--primary">+ Nuevo ítem</a>
</div>

<!-- Tabs -->
<div class="admin-tabs">
    <a href="<?= url('/admin/menu?tab=principal') ?>"
       class="admin-tab <?= $tab === 'principal' ? 'admin-tab--active' : '' ?>">
        Menú principal <span class="admin-tab-count"><?= $countPrincipal ?></span>
    </a>
    <a href="<?= url('/admin/menu?tab=footer_legal') ?>"
       class="admin-tab <?= $tab === 'footer_legal' ? 'admin-tab--active' : '' ?>">
        Footer / Legal <span class="admin-tab-count"><?= $countFooter ?></span>
    </a>
</div>

<!-- Lista sorteable -->
<?php
$currentItems = ($tab === 'principal') ? $itemsPrincipal : $itemsFooter;
?>

<?php if (!empty($currentItems)): ?>
<div class="menu-sortable-container" id="menuContainer">
    <ul class="menu-sortable-list" id="menuSortable">
        <?php renderMenuTree($currentItems, $csrf); ?>
    </ul>
</div>

<div class="menu-save-bar">
    <button type="button" id="btnSaveOrder" class="btn btn--primary" disabled>Guardar orden</button>
    <span id="orderStatus" class="menu-order-status"></span>
</div>
<?php else: ?>
<div class="admin-empty">
    <p>No hay ítems en este menú.</p>
    <a href="<?= url('/admin/menu/crear?menu=' . urlencode($tab)) ?>" class="btn btn--primary">Crear primer ítem</a>
</div>
<?php endif; ?>

<style>
.admin-tabs { display: flex; gap: 0; margin-bottom: 1.5rem; border-bottom: 2px solid #e5e7eb; }
.admin-tab { padding: .75rem 1.25rem; text-decoration: none; color: #6b7280; font-weight: 500; border-bottom: 2px solid transparent; margin-bottom: -2px; transition: all .15s; }
.admin-tab:hover { color: #111827; }
.admin-tab--active { color: #2563eb; border-bottom-color: #2563eb; }
.admin-tab-count { font-size: .75rem; background: #e5e7eb; padding: .1rem .45rem; border-radius: 99px; margin-left: .35rem; }
.admin-tab--active .admin-tab-count { background: #dbeafe; color: #2563eb; }

.menu-sortable-container { background: #fff; border: 1px solid #e5e7eb; border-radius: .5rem; padding: .5rem; }
.menu-sortable-list { list-style: none; margin: 0; padding: 0; min-height: 20px; }
.menu-sortable-nested { margin-left: 2rem; }
.menu-sortable-item { margin: .25rem 0; }

.menu-item-row { display: flex; align-items: center; gap: .5rem; padding: .6rem .75rem; background: #f9fafb; border: 1px solid #e5e7eb; border-radius: .375rem; transition: background .15s; }
.menu-item-row:hover { background: #f3f4f6; }
.menu-item-row--inactive { opacity: .55; }

.menu-drag-handle { cursor: grab; font-size: 1.2rem; color: #9ca3af; user-select: none; flex-shrink: 0; }
.menu-item-icon { flex-shrink: 0; }
.menu-item-title { font-weight: 500; white-space: nowrap; }
.menu-item-url { color: #6b7280; font-size: .8rem; flex: 1; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }

.badge--small { font-size: .65rem; padding: .1rem .4rem; border-radius: 4px; text-transform: uppercase; font-weight: 600; flex-shrink: 0; }
.badge--blue { background: #dbeafe; color: #1d4ed8; }
.badge--green { background: #d1fae5; color: #065f46; }
.badge--purple { background: #ede9fe; color: #6d28d9; }
.badge--orange { background: #ffedd5; color: #c2410c; }
.badge--gray { background: #f3f4f6; color: #6b7280; }

.menu-item-actions { display: flex; gap: .25rem; margin-left: auto; flex-shrink: 0; }
.btn--xs { padding: .2rem .4rem; font-size: .75rem; line-height: 1; }
.btn--danger { color: #dc2626; border-color: #fca5a5; }
.btn--danger:hover { background: #fef2f2; }
.inline-form { display: inline; margin: 0; padding: 0; }

.menu-save-bar { margin-top: 1rem; display: flex; align-items: center; gap: 1rem; }
.menu-order-status { font-size: .85rem; color: #6b7280; }

.sortable-ghost { opacity: .4; background: #dbeafe !important; }
.sortable-chosen { box-shadow: 0 2px 8px rgba(0,0,0,.12); }

.admin-empty { text-align: center; padding: 3rem; color: #6b7280; }
</style>

<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.6/Sortable.min.js"></script>
<script>
(function() {
    const container = document.getElementById('menuSortable');
    if (!container) return;

    const btnSave = document.getElementById('btnSaveOrder');
    const statusEl = document.getElementById('orderStatus');
    let orderChanged = false;

    function initSortable(el) {
        new Sortable(el, {
            group: 'menu',
            animation: 150,
            handle: '.menu-drag-handle',
            fallbackOnBody: true,
            swapThreshold: 0.65,
            ghostClass: 'sortable-ghost',
            chosenClass: 'sortable-chosen',
            onEnd: function() {
                orderChanged = true;
                btnSave.disabled = false;
                statusEl.textContent = 'Hay cambios sin guardar';
                statusEl.style.color = '#d97706';
            }
        });
    }

    // Inicializar todos los sortables (raíz + anidados)
    document.querySelectorAll('.menu-sortable-list').forEach(initSortable);

    // Guardar orden
    btnSave.addEventListener('click', function() {
        const items = [];
        function collect(ul, parentId) {
            ul.querySelectorAll(':scope > .menu-sortable-item').forEach(function(li, index) {
                items.push({
                    id: parseInt(li.dataset.id),
                    orden: index,
                    parent_id: parentId
                });
                const nested = li.querySelector(':scope > .menu-sortable-nested');
                if (nested) {
                    collect(nested, parseInt(li.dataset.id));
                }
            });
        }
        collect(container, null);

        btnSave.disabled = true;
        statusEl.textContent = 'Guardando...';
        statusEl.style.color = '#6b7280';

        fetch('<?= url('/admin/menu/reordenar') ?>', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ csrf: '<?= $csrf ?>', items: items })
        })
        .then(function(r) { return r.json(); })
        .then(function(data) {
            if (data.ok) {
                orderChanged = false;
                statusEl.textContent = 'Orden guardado';
                statusEl.style.color = '#059669';
            } else {
                statusEl.textContent = 'Error: ' + (data.error || 'desconocido');
                statusEl.style.color = '#dc2626';
                btnSave.disabled = false;
            }
        })
        .catch(function() {
            statusEl.textContent = 'Error de conexión';
            statusEl.style.color = '#dc2626';
            btnSave.disabled = false;
        });
    });
})();
</script>
