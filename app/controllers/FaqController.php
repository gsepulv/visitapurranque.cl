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

        // Agrupar por categoria
        $grupos = [];
        foreach ($faqs as $faq) {
            $cat = $faq['categoria'] ?: 'general';
            $grupos[$cat][] = $faq;
        }

        $this->render('public/faq', [
            'pageTitle'       => 'Preguntas Frecuentes — ' . SITE_NAME,
            'pageDescription' => 'Respuestas a las preguntas mas comunes sobre turismo en Purranque.',
            'grupos'          => $grupos,
            'totalFaqs'       => count($faqs),
        ]);
    }
}
