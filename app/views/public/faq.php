<?php
/**
 * Preguntas Frecuentes — visitapurranque.cl
 * Variables: $grupos (agrupadas por categoria), $totalFaqs
 */
$catLabels = [
    'general'   => 'General',
    'turismo'   => 'Turismo',
    'servicios' => 'Servicios',
];
?>

<!-- Schema.org JSON-LD -->
<script type="application/ld+json">
{
    "@context": "https://schema.org",
    "@type": "FAQPage",
    "mainEntity": [
        <?php $first = true; foreach ($grupos as $cat => $faqs): foreach ($faqs as $faq): ?>
        <?= $first ? '' : ',' ?>
        {
            "@type": "Question",
            "name": <?= json_encode($faq['pregunta'], JSON_UNESCAPED_UNICODE) ?>,
            "acceptedAnswer": {
                "@type": "Answer",
                "text": <?= json_encode($faq['respuesta'], JSON_UNESCAPED_UNICODE) ?>
            }
        }
        <?php $first = false; endforeach; endforeach; ?>
    ]
}
</script>

<section class="hero-section">
    <div class="container">
        <nav class="breadcrumb breadcrumb--light" aria-label="Migas de pan">
            <a href="<?= url('/') ?>">Inicio</a> <span class="breadcrumb-sep">/</span>
            <span>Preguntas Frecuentes</span>
        </nav>
        <h1>Preguntas Frecuentes</h1>
        <p class="hero-subtitle">Respuestas a las preguntas mas comunes sobre turismo en Purranque.</p>
    </div>
</section>

<section class="page-section">
    <div class="container">

        <?php if ($totalFaqs === 0): ?>
            <p class="text-muted text-center" style="padding:40px 0">No hay preguntas frecuentes por el momento.</p>
        <?php else: ?>

        <div class="faq-list">
            <?php foreach ($grupos as $cat => $faqs): ?>
                <h2 class="mb-2" style="font-size:1.15rem;margin-top:24px"><?= e($catLabels[$cat] ?? ucfirst($cat)) ?></h2>
                <?php foreach ($faqs as $faq): ?>
                <div class="faq-item">
                    <button type="button" class="faq-question" onclick="this.parentElement.classList.toggle('open')">
                        <span><?= e($faq['pregunta']) ?></span>
                        <span class="faq-icon">+</span>
                    </button>
                    <div class="faq-answer">
                        <p><?= nl2br(e($faq['respuesta'])) ?></p>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php endforeach; ?>
        </div>

        <?php endif; ?>

        <div class="text-center mt-4">
            <p class="text-muted">No encontraste lo que buscabas?</p>
            <a href="<?= url('/contacto') ?>" class="btn btn-primary">Contactanos</a>
        </div>

    </div>
</section>
