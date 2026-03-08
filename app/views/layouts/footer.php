<?php
/**
 * Footer layout — visitapurranque.cl
 */
?>
    </main><!-- /#main-content -->

    <footer class="site-footer">
        <div class="container footer-grid">
            <!-- Columna 1: Acerca de -->
            <div class="footer-col">
                <h3><?= e(SITE_NAME) ?></h3>
                <p><?= texto('footer_descripcion', 'Guía turística de Purranque. Descubre la naturaleza, cultura y tradiciones de la Región de Los Lagos.') ?></p>
            </div>

            <!-- Columna 2: Navegacion -->
            <div class="footer-col">
                <h3>Explorar</h3>
                <ul>
                    <li><a href="<?= url('/categorias') ?>">Categorías</a></li>
                    <li><a href="<?= url('/eventos') ?>">Eventos</a></li>
                    <li><a href="<?= url('/blog') ?>">Blog</a></li>
                    <li><a href="<?= url('/mapa') ?>">Mapa</a></li>
                </ul>
            </div>

            <!-- Columna 3: Legal -->
            <div class="footer-col">
                <h3>Información</h3>
                <ul>
                    <li><a href="<?= url('/contacto') ?>">Contacto</a></li>
                    <li><a href="<?= url('/faq') ?>">Preguntas frecuentes</a></li>
                    <li><a href="<?= url('/terminos') ?>">Términos de uso</a></li>
                    <li><a href="<?= url('/privacidad') ?>">Privacidad</a></li>
                </ul>
            </div>

            <!-- Columna 4: Contacto -->
            <div class="footer-col">
                <h3>Contacto</h3>
                <p><?= texto('footer_email', 'contacto@purranque.info') ?></p>
                <p><?= texto('footer_ciudad', 'Purranque, Región de Los Lagos') ?></p>
                <div class="footer-social">
                    <?php $fb = texto('facebook_url'); if ($fb): ?>
                        <a href="<?= e($fb) ?>" target="_blank" rel="noopener" aria-label="Facebook">FB</a>
                    <?php endif; ?>
                    <?php $ig = texto('instagram_url'); if ($ig): ?>
                        <a href="<?= e($ig) ?>" target="_blank" rel="noopener" aria-label="Instagram">IG</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="footer-bottom">
            <div class="container">
                <p>&copy; <?= date('Y') ?> <?= e(SITE_NAME) ?> &mdash;
                    Un proyecto de <a href="https://purranque.info" target="_blank" rel="noopener">PurranQUE.INFO</a>
                </p>
            </div>
        </div>
    </footer>

    <!-- Beta badge -->
    <div class="beta-badge"><?= texto('beta_mensaje', 'Beta') ?></div>

    <!-- Scroll to top -->
    <button class="back-to-top" id="backToTop" aria-label="Volver arriba">&#8593;</button>

    <?php
    // Popup activo
    try {
        $popupModel = new Popup($GLOBALS['pdo'] ?? $pdo);
        $popupActivo = $popupModel->getActivo();
        if ($popupActivo) {
            require BASE_PATH . '/app/views/partials/popup.php';
        }
    } catch (Throwable $e) {
        // Silenciar si la tabla no existe o hay error
    }
    ?>

    <!-- Cookie banner -->
    <div id="cookie-banner" class="cookie-banner" style="display:none;">
        <div class="cookie-banner__content">
            <p>Usamos cookies para mejorar tu experiencia. Al continuar navegando, aceptas nuestra
                <a href="<?= url('/privacidad') ?>">política de privacidad</a>.</p>
            <div class="cookie-banner__actions">
                <button id="cookie-accept" class="cookie-btn cookie-btn--accept">Aceptar</button>
                <button id="cookie-reject" class="cookie-btn cookie-btn--reject">Rechazar</button>
            </div>
        </div>
    </div>

    <script src="<?= asset('js/app.js?v=' . APP_VERSION) ?>"></script>
    <script>if('serviceWorker' in navigator){navigator.serviceWorker.register('/sw.js').catch(function(){});}</script>
    <script>
    (function(){
        var b=document.getElementById('cookie-banner');
        if(!b)return;
        if(localStorage.getItem('cookies-accepted')===null){b.style.display='flex';}
        document.getElementById('cookie-accept').addEventListener('click',function(){
            localStorage.setItem('cookies-accepted','true');b.style.display='none';
        });
        document.getElementById('cookie-reject').addEventListener('click',function(){
            localStorage.setItem('cookies-accepted','false');b.style.display='none';
        });
    })();
    </script>
</body>
</html>
