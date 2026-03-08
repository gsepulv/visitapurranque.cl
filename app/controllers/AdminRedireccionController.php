<?php

require_once BASE_PATH . '/app/middleware/AuthMiddleware.php';

class AdminRedireccionController extends Controller
{
    /** GET /admin/redirecciones */
    public function index(): void
    {
        $usuario = AuthMiddleware::check($this->db);

        $redirecciones = $this->db->query(
            "SELECT * FROM redirecciones ORDER BY created_at DESC"
        )->fetchAll();

        $this->renderAdmin('admin/redirecciones/index', [
            'pageTitle'       => 'Redirecciones',
            'usuario'         => $usuario,
            'sidebarCounts'   => $this->getSidebarCounts(),
            'redirecciones'   => $redirecciones,
        ]);
    }

    /** POST /admin/redirecciones/crear */
    public function store(): void
    {
        $usuario = AuthMiddleware::check($this->db);
        if (($_POST['_csrf'] ?? '') !== csrf_token()) {
            $this->redirect('/admin/redirecciones', ['error' => 'Token inválido.']);
        }

        $origen  = trim($_POST['url_origen'] ?? '');
        $destino = trim($_POST['url_destino'] ?? '');
        $tipo    = (int)($_POST['tipo'] ?? 301);

        if ($origen === '' || $destino === '') {
            $this->redirect('/admin/redirecciones', ['error' => 'URL origen y destino son obligatorias.']);
        }

        if (!in_array($tipo, [301, 302])) $tipo = 301;

        // Asegurar que empiece con /
        if (!str_starts_with($origen, '/')) $origen = '/' . $origen;

        try {
            $this->db->prepare(
                "INSERT INTO redirecciones (url_origen, url_destino, tipo) VALUES (?, ?, ?)"
            )->execute([$origen, $destino, $tipo]);

            $this->audit($usuario['id'], 'crear', 'redirecciones', "{$origen} -> {$destino} ({$tipo})");
            $this->redirect('/admin/redirecciones', ['success' => 'Redirección creada.']);
        } catch (\PDOException $e) {
            $this->redirect('/admin/redirecciones', ['error' => 'Esa URL origen ya existe.']);
        }
    }

    /** POST /admin/redirecciones/{id}/eliminar */
    public function delete(int $id): void
    {
        $usuario = AuthMiddleware::check($this->db);
        if (($_POST['_csrf'] ?? '') !== csrf_token()) {
            $this->redirect('/admin/redirecciones', ['error' => 'Token inválido.']);
        }

        $this->db->prepare("DELETE FROM redirecciones WHERE id = ?")->execute([$id]);
        $this->audit($usuario['id'], 'eliminar', 'redirecciones', "Redirección #{$id} eliminada");
        $this->redirect('/admin/redirecciones', ['success' => 'Redirección eliminada.']);
    }

    /** POST /admin/redirecciones/{id}/toggle */
    public function toggle(int $id): void
    {
        $usuario = AuthMiddleware::check($this->db);
        if (($_POST['_csrf'] ?? '') !== csrf_token()) {
            $this->redirect('/admin/redirecciones', ['error' => 'Token inválido.']);
        }

        $this->db->prepare("UPDATE redirecciones SET activo = NOT activo WHERE id = ?")->execute([$id]);
        $this->redirect('/admin/redirecciones', ['success' => 'Estado actualizado.']);
    }

    private function getSidebarCounts(): array
    {
        return [
            'fichas' => (int)$this->db->query("SELECT COUNT(*) FROM fichas WHERE activo = 1 AND eliminado = 0")->fetchColumn(),
            'categorias' => (int)$this->db->query("SELECT COUNT(*) FROM categorias WHERE activo = 1")->fetchColumn(),
        ];
    }
}
