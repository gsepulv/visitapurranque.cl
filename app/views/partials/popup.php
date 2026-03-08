<?php
/**
 * Partial: Popup frontend
 * Se incluye en footer.php si hay un popup activo
 * Variable: $popupActivo
 */
if (empty($popupActivo)) return;

$popupId    = (int)$popupActivo['id'];
$tipo       = $popupActivo['tipo'];
$trigger    = $popupActivo['trigger_type'];
$triggerVal = $popupActivo['trigger_valor'] ?? '5';
$paginas    = json_decode($popupActivo['paginas'] ?? 'null', true);

// Si hay restricción de páginas, verificar URI actual
if (is_array($paginas) && !empty($paginas)) {
    $currentUri = strtok($_SERVER['REQUEST_URI'] ?? '/', '?');
    $currentUri = '/' . trim($currentUri, '/');
    if ($currentUri !== '/') $currentUri = rtrim($currentUri, '/');
    if (!in_array($currentUri, $paginas)) return;
}

// Clases CSS según tipo
$modalClass = 'popup-modal';
if ($tipo === 'banner_top') $modalClass = 'popup-banner popup-banner--top';
elseif ($tipo === 'banner_bottom') $modalClass = 'popup-banner popup-banner--bottom';
elseif ($tipo === 'slide_in') $modalClass = 'popup-slidein';
?>

<div id="sitePopup" class="<?= $modalClass ?>" data-popup-id="<?= $popupId ?>"
     data-trigger="<?= e($trigger) ?>" data-trigger-val="<?= e($triggerVal) ?>" style="display:none;">
    <?php if ($tipo === 'modal'): ?>
    <div class="popup-overlay" onclick="closePopup()"></div>
    <?php endif; ?>
    <div class="popup-content">
        <button type="button" class="popup-close" onclick="closePopup()" aria-label="Cerrar">&times;</button>
        <h3 class="popup-title"><?= e($popupActivo['titulo']) ?></h3>
        <div class="popup-body"><?= $popupActivo['contenido'] ?></div>
    </div>
</div>

<style>
.popup-modal {
    position: fixed; inset: 0; z-index: 9999;
    display: flex; align-items: center; justify-content: center;
    animation: popupFadeIn .3s ease;
}
.popup-overlay {
    position: absolute; inset: 0; background: rgba(0,0,0,.6);
}
.popup-modal .popup-content {
    position: relative; background: #fff; border-radius: 12px;
    padding: 32px; max-width: 500px; width: 90%;
    box-shadow: 0 20px 60px rgba(0,0,0,.3);
}
.popup-banner {
    position: fixed; left: 0; right: 0; z-index: 9999;
    animation: popupSlideIn .3s ease;
}
.popup-banner--top { top: 0; }
.popup-banner--bottom { bottom: 0; }
.popup-banner .popup-content {
    background: #1a5632; color: #fff; padding: 16px 24px;
    display: flex; align-items: center; gap: 16px;
}
.popup-banner .popup-close { color: #fff; }
.popup-banner .popup-title { margin: 0; font-size: 1rem; }
.popup-banner .popup-body { font-size: .9rem; }
.popup-slidein {
    position: fixed; bottom: 20px; right: 20px; z-index: 9999;
    animation: popupSlideUp .3s ease;
}
.popup-slidein .popup-content {
    background: #fff; border-radius: 12px; padding: 24px;
    max-width: 350px; box-shadow: 0 10px 40px rgba(0,0,0,.2);
}
.popup-close {
    position: absolute; top: 8px; right: 12px;
    background: none; border: none; font-size: 1.5rem;
    cursor: pointer; color: #666; line-height: 1;
}
.popup-close:hover { color: #333; }
.popup-title { margin: 0 0 12px; font-size: 1.2rem; }
.popup-body { font-size: .95rem; line-height: 1.6; }
.popup-body a { color: #1a5632; font-weight: 600; }
@keyframes popupFadeIn { from { opacity: 0; } to { opacity: 1; } }
@keyframes popupSlideIn { from { transform: translateY(-100%); } to { transform: translateY(0); } }
@keyframes popupSlideUp { from { transform: translateY(30px); opacity: 0; } to { transform: translateY(0); opacity: 1; } }
</style>

<script>
(function() {
    var el = document.getElementById('sitePopup');
    if (!el) return;

    var popupId = el.dataset.popupId;
    var trigger = el.dataset.trigger;
    var val = el.dataset.triggerVal;

    // Si ya se mostró en esta sesión
    if (sessionStorage.getItem('popup_shown_' + popupId)) return;

    function showPopup() {
        el.style.display = '';
        sessionStorage.setItem('popup_shown_' + popupId, '1');
    }

    if (trigger === 'tiempo') {
        setTimeout(showPopup, (parseInt(val) || 5) * 1000);
    } else if (trigger === 'scroll') {
        var pct = parseInt(val) || 50;
        var fired = false;
        window.addEventListener('scroll', function() {
            if (fired) return;
            var scrollPct = (window.scrollY / (document.documentElement.scrollHeight - window.innerHeight)) * 100;
            if (scrollPct >= pct) { fired = true; showPopup(); }
        });
    } else if (trigger === 'exit_intent') {
        document.addEventListener('mouseout', function(e) {
            if (e.clientY < 5 && !sessionStorage.getItem('popup_shown_' + popupId)) {
                showPopup();
            }
        });
    }
    // 'click' requires manual trigger via data attributes elsewhere
})();

function closePopup() {
    var el = document.getElementById('sitePopup');
    if (el) el.style.display = 'none';
}
</script>
