<?php
/**
 * AdminPaginaController — visitapurranque.cl
 * CRUD de páginas estáticas con versionamiento
 */

require_once BASE_PATH . '/app/middleware/AuthMiddleware.php';
require_once BASE_PATH . '/app/models/Pagina.php';

class AdminPaginaController extends Controller
{
    private Pagina $pagina;

    public function __construct(PDO $pdo)
    {
        parent::__construct($pdo);
        $this->pagina = new Pagina($pdo);
    }

    /** GET /admin/paginas */
    public function index(): void
    {
        $usuario = AuthMiddleware::check($this->db);

        $q = trim($_GET['q'] ?? '');
        $paginas = $this->pagina->getAll($q ?: null);

        $this->renderAdmin('admin/paginas/index', [
            'pageTitle'     => 'Páginas Estáticas',
            'usuario'       => $usuario,
            'paginas'       => $paginas,
            'q'             => $q,
            'sidebarCounts' => $this->getSidebarCounts(),
        ]);
    }

    /** GET /admin/paginas/crear */
    public function create(): void
    {
        $usuario = AuthMiddleware::check($this->db);

        $this->renderAdmin('admin/paginas/form', [
            'pageTitle'     => 'Nueva Página',
            'usuario'       => $usuario,
            'pagina'        => null,
            'versiones'     => [],
            'sidebarCounts' => $this->getSidebarCounts(),
        ]);
    }

    /** POST /admin/paginas/crear */
    public function store(): void
    {
        $usuario = AuthMiddleware::check($this->db);

        if (($_POST['_csrf'] ?? '') !== csrf_token()) {
            $this->redirect('/admin/paginas', ['error' => 'Token CSRF inválido']);
        }

        $data = $this->buildData();

        if (empty($data['titulo'])) {
            $this->redirect('/admin/paginas/crear', ['error' => 'El título es obligatorio']);
        }

        $data['slug'] = $data['slug'] ?: slugify($data['titulo']);
        if ($this->pagina->slugExists($data['slug'])) {
            $this->redirect('/admin/paginas/crear', ['error' => "El slug '{$data['slug']}' ya existe"]);
        }

        $id = $this->pagina->create($data);

        $this->audit($usuario['id'], 'crear', 'paginas', "Página #{$id}: {$data['titulo']}", $id, 'pagina');

        $this->redirect('/admin/paginas', ['success' => 'Página creada correctamente']);
    }

    /** GET /admin/paginas/{id}/editar */
    public function edit(string $id): void
    {
        $usuario = AuthMiddleware::check($this->db);

        $pagina = $this->pagina->find((int)$id);
        if (!$pagina) {
            $this->redirect('/admin/paginas', ['error' => 'Página no encontrada']);
        }

        $versiones = $this->pagina->getVersiones((int)$id);

        $this->renderAdmin('admin/paginas/form', [
            'pageTitle'     => 'Editar Página',
            'usuario'       => $usuario,
            'pagina'        => $pagina,
            'versiones'     => $versiones,
            'sidebarCounts' => $this->getSidebarCounts(),
        ]);
    }

    /** POST /admin/paginas/{id}/editar */
    public function update(string $id): void
    {
        $usuario = AuthMiddleware::check($this->db);

        if (($_POST['_csrf'] ?? '') !== csrf_token()) {
            $this->redirect('/admin/paginas', ['error' => 'Token CSRF inválido']);
        }

        $pagina = $this->pagina->find((int)$id);
        if (!$pagina) {
            $this->redirect('/admin/paginas', ['error' => 'Página no encontrada']);
        }

        $data = $this->buildData();

        if (empty($data['titulo'])) {
            $this->redirect("/admin/paginas/{$id}/editar", ['error' => 'El título es obligatorio']);
        }

        $data['slug'] = $data['slug'] ?: slugify($data['titulo']);
        if ($this->pagina->slugExists($data['slug'], (int)$id)) {
            $this->redirect("/admin/paginas/{$id}/editar", ['error' => "El slug '{$data['slug']}' ya existe"]);
        }

        // Guardar versión anterior si el contenido cambió
        if ($pagina['contenido'] !== $data['contenido']) {
            $nota = trim($_POST['version_nota'] ?? '') ?: null;
            $this->pagina->saveVersion((int)$id, $pagina['contenido'], $usuario['id'], $nota);
        }

        $this->pagina->update((int)$id, $data);

        $this->audit($usuario['id'], 'editar', 'paginas', "Página #{$id}: {$data['titulo']}", (int)$id, 'pagina');

        $this->redirect("/admin/paginas/{$id}/editar", ['success' => 'Página actualizada']);
    }

    /** POST /admin/paginas/{id}/eliminar */
    public function delete(string $id): void
    {
        $usuario = AuthMiddleware::check($this->db);

        if (($_POST['_csrf'] ?? '') !== csrf_token()) {
            $this->redirect('/admin/paginas', ['error' => 'Token CSRF inválido']);
        }

        $pagina = $this->pagina->find((int)$id);
        if (!$pagina) {
            $this->redirect('/admin/paginas', ['error' => 'Página no encontrada']);
        }

        $this->pagina->delete((int)$id);

        $this->audit($usuario['id'], 'eliminar', 'paginas', "Página #{$id}: {$pagina['titulo']}", (int)$id, 'pagina');

        $this->redirect('/admin/paginas', ['success' => 'Página eliminada']);
    }

    /** POST /admin/paginas/{id}/toggle */
    public function toggle(string $id): void
    {
        $usuario = AuthMiddleware::check($this->db);

        if (($_POST['_csrf'] ?? '') !== csrf_token()) {
            $this->redirect('/admin/paginas', ['error' => 'Token CSRF inválido']);
        }

        $nuevoEstado = $this->pagina->toggleActivo((int)$id);

        $this->audit($usuario['id'], 'toggle', 'paginas', "Página #{$id} {$nuevoEstado}", (int)$id, 'pagina');

        $this->redirect('/admin/paginas', ['success' => "Página {$nuevoEstado}"]);
    }

    /** GET /admin/paginas/{id}/version/{versionId} */
    public function version(string $id, string $versionId): void
    {
        $usuario = AuthMiddleware::check($this->db);

        $pagina = $this->pagina->find((int)$id);
        if (!$pagina) {
            $this->redirect('/admin/paginas', ['error' => 'Página no encontrada']);
        }

        $version = $this->pagina->getVersion((int)$versionId);
        if (!$version || (int)$version['pagina_id'] !== (int)$id) {
            $this->redirect("/admin/paginas/{$id}/editar", ['error' => 'Versión no encontrada']);
        }

        $this->renderAdmin('admin/paginas/version', [
            'pageTitle'     => 'Ver Versión',
            'usuario'       => $usuario,
            'pagina'        => $pagina,
            'version'       => $version,
            'sidebarCounts' => $this->getSidebarCounts(),
        ]);
    }

    /** POST /admin/paginas/{id}/restaurar/{versionId} */
    public function restaurar(string $id, string $versionId): void
    {
        $usuario = AuthMiddleware::check($this->db);

        if (($_POST['_csrf'] ?? '') !== csrf_token()) {
            $this->redirect("/admin/paginas/{$id}/editar", ['error' => 'Token CSRF inválido']);
        }

        $pagina = $this->pagina->find((int)$id);
        if (!$pagina) {
            $this->redirect('/admin/paginas', ['error' => 'Página no encontrada']);
        }

        $version = $this->pagina->getVersion((int)$versionId);
        if (!$version || (int)$version['pagina_id'] !== (int)$id) {
            $this->redirect("/admin/paginas/{$id}/editar", ['error' => 'Versión no encontrada']);
        }

        // Guardar versión actual antes de restaurar
        $this->pagina->saveVersion((int)$id, $pagina['contenido'], $usuario['id'], 'Pre-restauración automática');

        $this->pagina->restoreVersion((int)$id, $version['contenido']);

        $this->audit($usuario['id'], 'restaurar', 'paginas', "Página #{$id}: restaurada desde versión #{$versionId}", (int)$id, 'pagina');

        $this->redirect("/admin/paginas/{$id}/editar", ['success' => 'Contenido restaurado desde versión anterior']);
    }

    // ── Helpers privados ─────────────────────────────────

    private function buildData(): array
    {
        return [
            'titulo'           => trim($_POST['titulo'] ?? ''),
            'slug'             => trim($_POST['slug'] ?? ''),
            'contenido'        => $_POST['contenido'] ?? '',
            'meta_title'       => trim($_POST['meta_title'] ?? '') ?: null,
            'meta_description' => trim($_POST['meta_description'] ?? '') ?: null,
            'template'         => $_POST['template'] ?? 'default',
            'activo'           => isset($_POST['activo']) ? 1 : 0,
            'orden'            => (int)($_POST['orden'] ?? 0),
        ];
    }

    private function getSidebarCounts(): array
    {
        return [
            'fichas' => (int)$this->db->query(
                "SELECT COUNT(*) FROM fichas WHERE activo = 1 AND eliminado = 0"
            )->fetchColumn(),
            'categorias' => (int)$this->db->query(
                "SELECT COUNT(*) FROM categorias WHERE activo = 1"
            )->fetchColumn(),
        ];
    }
}
