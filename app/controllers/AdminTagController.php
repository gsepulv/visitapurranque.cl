<?php

require_once BASE_PATH . '/app/middleware/AuthMiddleware.php';

class AdminTagController extends Controller
{
    private Tag $tag;

    public function __construct(\PDO $db)
    {
        parent::__construct($db);
        $this->tag = new Tag($db);
    }

    /** GET /admin/tags */
    public function index(): void
    {
        $usuario = AuthMiddleware::check($this->db);

        $this->renderAdmin('admin/tags/index', [
            'pageTitle'     => 'Tags',
            'usuario'       => $usuario,
            'sidebarCounts' => $this->getSidebarCounts(),
            'tags'          => $this->tag->getAll(),
        ]);
    }

    /** POST /admin/tags/crear */
    public function store(): void
    {
        $usuario = AuthMiddleware::check($this->db);
        if (($_POST['_csrf'] ?? '') !== csrf_token()) {
            // AJAX response
            if ($this->isAjax()) {
                $this->json(['error' => 'Token inválido'], 403);
            }
            $this->redirect('/admin/tags', ['error' => 'Token inválido.']);
        }

        $nombre = trim($_POST['nombre'] ?? '');
        if ($nombre === '') {
            if ($this->isAjax()) {
                $this->json(['error' => 'Nombre requerido'], 400);
            }
            $this->redirect('/admin/tags', ['error' => 'Nombre requerido.']);
        }

        $id = $this->tag->crear(['nombre' => $nombre]);
        $tag = $this->tag->getById($id);

        $this->audit($usuario['id'], 'crear', 'tags', "Tag: {$nombre}");

        if ($this->isAjax()) {
            $this->json(['success' => true, 'tag' => $tag]);
        }
        $this->redirect('/admin/tags', ['success' => 'Tag creado.']);
    }

    /** POST /admin/tags/editar/{id} */
    public function update(int $id): void
    {
        $usuario = AuthMiddleware::check($this->db);
        if (($_POST['_csrf'] ?? '') !== csrf_token()) {
            $this->redirect('/admin/tags', ['error' => 'Token inválido.']);
        }

        $nombre = trim($_POST['nombre'] ?? '');
        if ($nombre === '') {
            $this->redirect('/admin/tags', ['error' => 'Nombre requerido.']);
        }

        $this->tag->actualizar($id, ['nombre' => $nombre]);
        $this->audit($usuario['id'], 'editar', 'tags', "Tag #{$id}: {$nombre}");
        $this->redirect('/admin/tags', ['success' => 'Tag actualizado.']);
    }

    /** POST /admin/tags/eliminar/{id} */
    public function eliminar(int $id): void
    {
        $usuario = AuthMiddleware::check($this->db);
        if (($_POST['_csrf'] ?? '') !== csrf_token()) {
            $this->redirect('/admin/tags', ['error' => 'Token inválido.']);
        }

        $this->tag->eliminar($id);
        $this->audit($usuario['id'], 'eliminar', 'tags', "Tag #{$id} eliminado");
        $this->redirect('/admin/tags', ['success' => 'Tag eliminado.']);
    }

    /** GET /admin/tags/api/buscar */
    public function apiBuscar(): void
    {
        AuthMiddleware::check($this->db);
        $q = trim($_GET['q'] ?? '');
        if (strlen($q) < 1) {
            $this->json([]);
        }
        $this->json($this->tag->buscar($q, 15));
    }

    private function isAjax(): bool
    {
        return !empty($_SERVER['HTTP_X_REQUESTED_WITH'])
            && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
    }

    private function getSidebarCounts(): array
    {
        return [
            'fichas' => (int)$this->db->query("SELECT COUNT(*) FROM fichas WHERE activo = 1 AND eliminado = 0")->fetchColumn(),
            'categorias' => (int)$this->db->query("SELECT COUNT(*) FROM categorias WHERE activo = 1")->fetchColumn(),
        ];
    }
}
