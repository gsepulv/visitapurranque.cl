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
                <p><?= texto('footer_descripcion', 'Guia turistica de Purranque. Descubre la naturaleza, cultura y tradiciones de la Region de Los Lagos.') ?></p>
            </div>

            <!-- Columna 2: Navegacion -->
            <div class="footer-col">
                <h3>Explorar</h3>
                <ul>
                    <li><a href="<?= url('/categorias') ?>">Categorias</a></li>
                    <li><a href="<?= url('/eventos') ?>">Eventos</a></li>
                    <li><a href="<?= url('/blog') ?>">Blog</a></li>
                    <li><a href="<?= url('/mapa') ?>">Mapa</a></li>
                </ul>
            </div>

            <!-- Columna 3: Legal -->
            <div class="footer-col">
                <h3>Informacion</h3>
                <ul>
                    <li><a href="<?= url('/contacto') ?>">Contacto</a></li>
                    <li><a href="<?= url('/faq') ?>">Preguntas frecuentes</a></li>
                    <li><a href="<?= url('/terminos') ?>">Terminos de uso</a></li>
                    <li><a href="<?= url('/privacidad') ?>">Privacidad</a></li>
                </ul>
            </div>

            <!-- Columna 4: Contacto -->
            <div class="footer-col">
                <h3>Contacto</h3>
                <p><?= texto('footer_email', 'contacto@purranque.info') ?></p>
                <p><?= texto('footer_ciudad', 'Purranque, Region de Los Lagos') ?></p>
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

    <script src="<?= asset('js/app.js?v=' . APP_VERSION) ?>"></script>
</body>
</html>
