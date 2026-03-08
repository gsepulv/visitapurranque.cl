<?php

require_once BASE_PATH . '/app/middleware/AuthMiddleware.php';

class AdminBusquedaController extends Controller
{
    /** GET /admin/api/buscar?q=texto */
    public function apiBuscar(): void
    {
        AuthMiddleware::check($this->db);

        $q = trim($_GET['q'] ?? '');
        if (mb_strlen($q) < 2) {
            $this->json([]);
        }

        $like = '%' . $q . '%';
        $resultados = [];

        // Fichas
        $stmt = $this->db->prepare(
            "SELECT id, nombre AS titulo, 'ficha' AS tipo FROM fichas
             WHERE nombre LIKE ? AND eliminado = 0 LIMIT 5"
        );
        $stmt->execute([$like]);
        foreach ($stmt->fetchAll() as $r) {
            $r['url'] = '/admin/fichas/' . $r['id'] . '/editar';
            $r['icon'] = '&#128205;';
            $resultados[] = $r;
        }

        // Eventos
        $stmt = $this->db->prepare(
            "SELECT id, titulo, 'evento' AS tipo FROM eventos
             WHERE titulo LIKE ? AND eliminado = 0 LIMIT 5"
        );
        $stmt->execute([$like]);
        foreach ($stmt->fetchAll() as $r) {
            $r['url'] = '/admin/eventos/' . $r['id'] . '/editar';
            $r['icon'] = '&#128197;';
            $resultados[] = $r;
        }

        // Blog
        $stmt = $this->db->prepare(
            "SELECT id, titulo, 'blog' AS tipo FROM blog_posts
             WHERE titulo LIKE ? AND eliminado = 0 LIMIT 5"
        );
        $stmt->execute([$like]);
        foreach ($stmt->fetchAll() as $r) {
            $r['url'] = '/admin/blog/' . $r['id'] . '/editar';
            $r['icon'] = '&#128221;';
            $resultados[] = $r;
        }

        // Usuarios
        $stmt = $this->db->prepare(
            "SELECT id, nombre AS titulo, 'usuario' AS tipo FROM usuarios
             WHERE nombre LIKE ? OR email LIKE ? LIMIT 5"
        );
        $stmt->execute([$like, $like]);
        foreach ($stmt->fetchAll() as $r) {
            $r['url'] = '/admin/usuarios/' . $r['id'] . '/editar';
            $r['icon'] = '&#128101;';
            $resultados[] = $r;
        }

        // Páginas
        $stmt = $this->db->prepare(
            "SELECT id, titulo, 'pagina' AS tipo FROM paginas
             WHERE titulo LIKE ? LIMIT 5"
        );
        $stmt->execute([$like]);
        foreach ($stmt->fetchAll() as $r) {
            $r['url'] = '/admin/paginas/' . $r['id'] . '/editar';
            $r['icon'] = '&#128195;';
            $resultados[] = $r;
        }

        $this->json($resultados);
    }

    private function getSidebarCounts(): array
    {
        return [];
    }
}
