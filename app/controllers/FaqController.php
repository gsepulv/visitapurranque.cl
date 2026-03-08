<?php

class FaqController extends Controller
{
    public function index(): void
    {
        $stmt = $this->db->prepare(
            "SELECT id, pregunta, respuesta, categoria
             FROM faq
             WHERE activo = 1
             ORDER BY orden ASC, id ASC"
        );
        $stmt->execute();
        $faqs = $stmt->fetchAll();

        // Agrupar por categoría
        $grupos = [];
        foreach ($faqs as $faq) {
            $cat = $faq['categoria'] ?: 'general';
            $grupos[$cat][] = $faq;
        }

        $this->render('public/faq', [
            'meta' => [
                'title'       => 'Preguntas Frecuentes — ' . SITE_NAME,
                'description' => 'Respuestas a las preguntas más comunes sobre turismo en Purranque.',
            ],
            'grupos'          => $grupos,
            'totalFaqs'       => count($faqs),
        ]);
    }
}
