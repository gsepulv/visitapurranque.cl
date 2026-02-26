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
            $pageTitle = 'Pagina no encontrada — ' . SITE_NAME;
            require BASE_PATH . '/app/views/layouts/header.php';
            require BASE_PATH . '/app/views/public/404.php';
            require BASE_PATH . '/app/views/layouts/footer.php';
            return;
        }

        $this->render('public/placeholder', [
            'pageTitle'   => e($pagina['titulo']) . ' — ' . SITE_NAME,
            'sectionName' => $pagina['titulo'],
        ]);
    }
}
