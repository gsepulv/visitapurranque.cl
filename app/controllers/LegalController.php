<?php

class LegalController extends Controller
{
    public function show(string $slug): void
    {
        // Buscar en tabla paginas
        $stmt = $this->db->prepare(
            'SELECT titulo, contenido FROM paginas WHERE slug = ? AND activo = 1 LIMIT 1'
        );
        $stmt->execute([$slug]);
        $pagina = $stmt->fetch();

        if (!$pagina) {
            http_response_code(404);
            $this->render('public/404', [
                'meta' => ['title' => 'Página no encontrada — ' . SITE_NAME],
            ]);
            return;
        }

        $this->render('public/placeholder', [
            'meta' => [
                'title'       => $pagina['titulo'] . ' — ' . SITE_NAME,
                'description' => mb_strimwidth(strip_tags($pagina['contenido']), 0, 160, '...'),
                'url'         => SITE_URL . '/' . $slug,
            ],
            'sectionName' => $pagina['titulo'],
        ]);
    }
}
