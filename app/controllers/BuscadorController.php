<?php

class BuscadorController extends Controller
{
    public function index(): void
    {
        $q = trim($_GET['q'] ?? '');
        $resultados = ['fichas' => [], 'eventos' => [], 'blog' => []];
        $totalResultados = 0;

        if (mb_strlen($q) >= 2) {
            $like = '%' . $q . '%';

            // Fichas
            $stmt = $this->db->prepare(
                "SELECT id, nombre, slug, descripcion_corta, 'ficha' AS tipo
                 FROM fichas
                 WHERE activo = 1 AND eliminado = 0
                   AND (nombre LIKE ? OR descripcion_corta LIKE ? OR descripcion LIKE ? OR lugar LIKE ?)
                 ORDER BY destacado DESC, nombre ASC
                 LIMIT 20"
            );
            $stmt->execute([$like, $like, $like, $like]);
            $resultados['fichas'] = $stmt->fetchAll();

            // Eventos
            $stmt = $this->db->prepare(
                "SELECT id, titulo AS nombre, slug, descripcion_corta, 'evento' AS tipo
                 FROM eventos
                 WHERE activo = 1 AND eliminado = 0
                   AND (titulo LIKE ? OR descripcion_corta LIKE ? OR descripcion LIKE ? OR lugar LIKE ?)
                 ORDER BY fecha_inicio DESC
                 LIMIT 20"
            );
            $stmt->execute([$like, $like, $like, $like]);
            $resultados['eventos'] = $stmt->fetchAll();

            // Blog
            $stmt = $this->db->prepare(
                "SELECT id, titulo AS nombre, slug, extracto AS descripcion_corta, 'blog' AS tipo
                 FROM blog_posts
                 WHERE estado = 'publicado' AND eliminado = 0
                   AND (titulo LIKE ? OR extracto LIKE ? OR contenido LIKE ?)
                 ORDER BY publicado_at DESC
                 LIMIT 20"
            );
            $stmt->execute([$like, $like, $like]);
            $resultados['blog'] = $stmt->fetchAll();

            $totalResultados = count($resultados['fichas']) + count($resultados['eventos']) + count($resultados['blog']);
        }

        $this->render('public/buscar', [
            'pageTitle'       => ($q ? e($q) . ' — Buscar en ' : 'Buscar — ') . SITE_NAME,
            'pageDescription' => 'Busca atractivos turisticos, eventos y articulos en Purranque.',
            'q'               => $q,
            'resultados'      => $resultados,
            'totalResultados' => $totalResultados,
        ]);
    }
}
