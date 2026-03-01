<?php
/**
 * Contacto — visitapurranque.cl
 */
?>

<section class="hero-section">
    <div class="container">
        <nav class="breadcrumb breadcrumb--light" aria-label="Migas de pan">
            <a href="<?= url('/') ?>">Inicio</a> <span class="breadcrumb-sep">/</span>
            <span>Contacto</span>
        </nav>
        <h1>Contacto</h1>
        <p class="hero-subtitle">Tienes alguna consulta, sugerencia o quieres agregar tu negocio? Escribenos.</p>
    </div>
</section>

<section class="page-section">
    <div class="container">
        <div class="contacto-grid">

            <!-- Formulario -->
            <div>
                <h2>Envianos un mensaje</h2>
                <form method="post" action="<?= url('/contacto') ?>" class="mt-2">
                    <?= csrf_field() ?>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="c-nombre">Nombre *</label>
                            <input type="text" id="c-nombre" name="nombre" required maxlength="100" placeholder="Tu nombre completo">
                        </div>
                        <div class="form-group">
                            <label for="c-email">Email *</label>
                            <input type="email" id="c-email" name="email" required maxlength="200" placeholder="tu@email.com">
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="c-telefono">Telefono (opcional)</label>
                            <input type="tel" id="c-telefono" name="telefono" maxlength="20" placeholder="+56 9 1234 5678">
                        </div>
                        <div class="form-group">
                            <label for="c-asunto">Asunto</label>
                            <input type="text" id="c-asunto" name="asunto" maxlength="200" placeholder="Motivo de tu mensaje">
                        </div>
                    </div>

                    <div class="form-group mb-2">
                        <label for="c-mensaje">Mensaje *</label>
                        <textarea id="c-mensaje" name="mensaje" required maxlength="2000" rows="5" placeholder="Escribe tu consulta o sugerencia..."></textarea>
                    </div>

                    <button type="submit" class="btn btn-primary">Enviar mensaje</button>
                </form>
            </div>

            <!-- Info de contacto -->
            <div>
                <div class="contacto-info">
                    <h3>Informacion de contacto</h3>
                    <div class="contacto-info-item">
                        <span>&#128205;</span>
                        <div>
                            <strong>Purranque</strong><br>
                            Region de Los Lagos, Chile
                        </div>
                    </div>
                    <div class="contacto-info-item">
                        <span>&#9993;</span>
                        <div>
                            <a href="mailto:contacto@purranque.info">contacto@purranque.info</a>
                        </div>
                    </div>
                    <div class="contacto-info-item">
                        <span>&#127760;</span>
                        <div>
                            Siguenos en redes sociales para enterarte de novedades y eventos.
                        </div>
                    </div>
                </div>

                <div class="mt-3">
                    <h3>Preguntas frecuentes</h3>
                    <p class="text-sm text-muted">Antes de escribir, revisa si tu pregunta ya esta respondida en nuestra seccion de <a href="<?= url('/faq') ?>">Preguntas Frecuentes</a>.</p>
                </div>
            </div>

        </div>
    </div>
</section>
