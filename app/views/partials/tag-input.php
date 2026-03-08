<?php
/**
 * Partial: Tag Input con autocompletado
 * Variables requeridas: $entidadTags (array de tags actuales, puede estar vacío)
 * Uso: <?php $entidadTags = $entidadTags ?? []; require BASE_PATH . '/app/views/partials/tag-input.php'; ?>
 */
$entidadTags = $entidadTags ?? [];
?>

<fieldset class="admin-fieldset">
    <legend>Tags</legend>
    <div class="form-group">
        <label class="form-label">Tags</label>
        <div class="tag-input-wrapper" id="tagInputWrapper">
            <div class="tag-input-chips" id="tagChipsContainer">
                <?php foreach ($entidadTags as $t): ?>
                <span class="tag-input-chip" data-id="<?= (int)$t['id'] ?>">
                    <?= e($t['nombre']) ?>
                    <button type="button" class="tag-input-chip__remove" onclick="removeTagChip(this)">&times;</button>
                    <input type="hidden" name="tag_ids[]" value="<?= (int)$t['id'] ?>">
                </span>
                <?php endforeach; ?>
                <input type="text" class="tag-input-field" id="tagAutoInput"
                       placeholder="Escribe para buscar tags..." autocomplete="off">
            </div>
            <div class="tag-input-dropdown" id="tagDropdown" style="display:none;"></div>
        </div>
        <small class="text-muted">Escribe para buscar tags existentes o crea uno nuevo presionando Enter.</small>
    </div>
</fieldset>

<script>
(function() {
    var input = document.getElementById('tagAutoInput');
    var dropdown = document.getElementById('tagDropdown');
    var container = document.getElementById('tagChipsContainer');
    var debounceTimer = null;
    var csrfToken = document.querySelector('input[name="_csrf"]').value;

    input.addEventListener('input', function() {
        clearTimeout(debounceTimer);
        var q = this.value.trim();
        if (q.length < 1) { dropdown.style.display = 'none'; return; }
        debounceTimer = setTimeout(function() {
            fetch('<?= url('/admin/tags/api/buscar') ?>?q=' + encodeURIComponent(q))
                .then(function(r) { return r.json(); })
                .then(function(tags) {
                    if (tags.length === 0) {
                        dropdown.innerHTML = '<div class="tag-dropdown-item tag-dropdown-create" data-name="' + q.replace(/"/g, '&quot;') + '">Crear: <strong>' + q + '</strong></div>';
                    } else {
                        var html = '';
                        var selectedIds = getSelectedIds();
                        tags.forEach(function(tag) {
                            if (selectedIds.indexOf(tag.id) === -1) {
                                html += '<div class="tag-dropdown-item" data-id="' + tag.id + '" data-name="' + tag.nombre.replace(/"/g, '&quot;') + '">' + tag.nombre + '</div>';
                            }
                        });
                        // Opción para crear si no es match exacto
                        var exactMatch = tags.some(function(t) { return t.nombre.toLowerCase() === q.toLowerCase(); });
                        if (!exactMatch) {
                            html += '<div class="tag-dropdown-item tag-dropdown-create" data-name="' + q.replace(/"/g, '&quot;') + '">Crear: <strong>' + q + '</strong></div>';
                        }
                        dropdown.innerHTML = html || '<div class="tag-dropdown-item" style="color:#999;">Todos ya seleccionados</div>';
                    }
                    dropdown.style.display = 'block';
                });
        }, 300);
    });

    input.addEventListener('keydown', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            var firstItem = dropdown.querySelector('.tag-dropdown-item[data-id], .tag-dropdown-create');
            if (firstItem) firstItem.click();
        }
        if (e.key === 'Escape') {
            dropdown.style.display = 'none';
        }
    });

    dropdown.addEventListener('click', function(e) {
        var item = e.target.closest('.tag-dropdown-item');
        if (!item) return;

        if (item.classList.contains('tag-dropdown-create')) {
            // Crear nuevo tag via AJAX
            var nombre = item.dataset.name;
            var fd = new FormData();
            fd.append('nombre', nombre);
            fd.append('_csrf', csrfToken);
            fetch('<?= url('/admin/tags/crear') ?>', {
                method: 'POST',
                headers: { 'X-Requested-With': 'XMLHttpRequest' },
                body: fd
            })
            .then(function(r) { return r.json(); })
            .then(function(res) {
                if (res.tag) {
                    addChip(res.tag.id, res.tag.nombre);
                }
            });
        } else {
            addChip(item.dataset.id, item.dataset.name);
        }

        input.value = '';
        dropdown.style.display = 'none';
        input.focus();
    });

    document.addEventListener('click', function(e) {
        if (!e.target.closest('#tagInputWrapper')) {
            dropdown.style.display = 'none';
        }
    });

    function addChip(id, nombre) {
        var chip = document.createElement('span');
        chip.className = 'tag-input-chip';
        chip.dataset.id = id;
        chip.innerHTML = nombre + '<button type="button" class="tag-input-chip__remove" onclick="removeTagChip(this)">&times;</button>' +
            '<input type="hidden" name="tag_ids[]" value="' + id + '">';
        container.insertBefore(chip, input);
    }

    function getSelectedIds() {
        var ids = [];
        container.querySelectorAll('input[name="tag_ids[]"]').forEach(function(inp) {
            ids.push(parseInt(inp.value));
        });
        return ids;
    }
})();

function removeTagChip(btn) {
    btn.closest('.tag-input-chip').remove();
}
</script>
