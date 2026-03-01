<?php
/**
 * AdminTextoController — visitapurranque.cl
 * CRUD para textos editables del sitio
 */

require_once BASE_PATH . '/app/middleware/AuthMiddleware.php';
require_once BASE_PATH . '/app/models/TextoEditable.php';

class AdminTextoController extends Controller
{
    private TextoEditable $texto;

    public function __construct(PDO $pdo)
    {
        parent::__construct($pdo);
        $this->texto = new TextoEditable($pdo);
    }

    /** GET /admin/textos */
    public function index(): void
    {
        $usuario = AuthMiddleware::check($this->db);

        $grupos = $this->texto->allGrouped();

        $this->renderAdmin('admin/textos/index', [
            'pageTitle'     => 'Textos Editables',
            'usuario'       => $usuario,
            'grupos'        => $grupos,
            'sidebarCounts' => $this->getSidebarCounts(),
        ]);
    }

    /** POST /admin/textos/guardar */
    public function guardar(): void
    {
        $usuario = AuthMiddleware::check($this->db);

        if (($_POST['_csrf'] ?? '') !== csrf_token()) {
            $this->redirect('/admin/textos', ['error' => 'Token CSRF inválido']);
        }

        $valores = $_POST['texto'] ?? [];
        if (!is_array($valores)) {
            $this->redirect('/admin/textos', ['error' => 'Datos inválidos']);
        }

        $count = $this->texto->saveBatch($valores);

        $this->audit($usuario['id'], 'guardar_textos', 'textos', "Actualizados {$count} textos editables");

        $this->redirect('/admin/textos', ['success' => "Textos guardados ({$count} campos)"]);
    }

    /** GET /admin/textos/crear */
    public function create(): void
    {
        $usuario = AuthMiddleware::check($this->db);

        $secciones = $this->texto->secciones();

        $this->renderAdmin('admin/textos/form', [
            'pageTitle'     => 'Nuevo Texto Editable',
            'usuario'       => $usuario,
            'texto'         => null,
            'secciones'     => $secciones,
            'sidebarCounts' => $this->getSidebarCounts(),
        ]);
    }

    /** POST /admin/textos/crear */
    public function store(): void
    {
        $usuario = AuthMiddleware::check($this->db);

        if (($_POST['_csrf'] ?? '') !== csrf_token()) {
            $this->redirect('/admin/textos', ['error' => 'Token CSRF inválido']);
        }

        $data = [
            'clave'       => trim($_POST['clave'] ?? ''),
            'valor'       => trim($_POST['valor'] ?? ''),
            'seccion'     => trim($_POST['seccion'] ?? $_POST['seccion_nueva'] ?? ''),
            'tipo'        => $_POST['tipo'] ?? 'text',
            'descripcion' => trim($_POST['descripcion'] ?? ''),
        ];

        if (empty($data['clave']) || empty($data['seccion'])) {
            $this->redirect('/admin/textos/crear', ['error' => 'Clave y sección son obligatorios']);
        }

        if (!preg_match('/^[a-z0-9_]+$/', $data['clave'])) {
            $this->redirect('/admin/textos/crear', ['error' => 'La clave solo acepta letras minúsculas, números y guion bajo']);
        }

        if ($this->texto->findByClave($data['clave'])) {
            $this->redirect('/admin/textos/crear', ['error' => "La clave '{$data['clave']}' ya existe"]);
        }

        $id = $this->texto->create($data);

        $this->audit($usuario['id'], 'crear', 'textos', "Texto #{$id}: {$data['clave']}", $id, 'texto_editable');

        $this->redirect('/admin/textos', ['success' => 'Texto creado correctamente']);
    }

    /** GET /admin/textos/{id}/editar */
    public function edit(string $id): void
    {
        $usuario = AuthMiddleware::check($this->db);

        $texto = $this->texto->find((int)$id);
        if (!$texto) {
            $this->redirect('/admin/textos', ['error' => 'Texto no encontrado']);
        }

        $secciones = $this->texto->secciones();

        $this->renderAdmin('admin/textos/form', [
            'pageTitle'     => 'Editar Texto',
            'usuario'       => $usuario,
            'texto'         => $texto,
            'secciones'     => $secciones,
            'sidebarCounts' => $this->getSidebarCounts(),
        ]);
    }

    /** POST /admin/textos/{id}/editar */
    public function update(string $id): void
    {
        $usuario = AuthMiddleware::check($this->db);

        if (($_POST['_csrf'] ?? '') !== csrf_token()) {
            $this->redirect('/admin/textos', ['error' => 'Token CSRF inválido']);
        }

        $texto = $this->texto->find((int)$id);
        if (!$texto) {
            $this->redirect('/admin/textos', ['error' => 'Texto no encontrado']);
        }

        $data = [
            'clave'         => trim($_POST['clave'] ?? ''),
            'valor'         => trim($_POST['valor'] ?? ''),
            'valor_default' => trim($_POST['valor_default'] ?? ''),
            'seccion'       => trim($_POST['seccion'] ?? $_POST['seccion_nueva'] ?? ''),
            'tipo'          => $_POST['tipo'] ?? 'text',
            'descripcion'   => trim($_POST['descripcion'] ?? ''),
        ];

        if (empty($data['clave']) || empty($data['seccion'])) {
            $this->redirect("/admin/textos/{$id}/editar", ['error' => 'Clave y sección son obligatorios']);
        }

        $this->texto->update((int)$id, $data);

        $this->audit($usuario['id'], 'editar', 'textos', "Texto #{$id}: {$data['clave']}", (int)$id, 'texto_editable');

        $this->redirect('/admin/textos', ['success' => 'Texto actualizado correctamente']);
    }

    /** POST /admin/textos/{id}/eliminar */
    public function delete(string $id): void
    {
        $usuario = AuthMiddleware::check($this->db);

        if (($_POST['_csrf'] ?? '') !== csrf_token()) {
            $this->redirect('/admin/textos', ['error' => 'Token CSRF inválido']);
        }

        $texto = $this->texto->find((int)$id);
        if (!$texto) {
            $this->redirect('/admin/textos', ['error' => 'Texto no encontrado']);
        }

        $this->texto->delete((int)$id);

        $this->audit($usuario['id'], 'eliminar', 'textos', "Texto #{$id}: {$texto['clave']}", (int)$id, 'texto_editable');

        $this->redirect('/admin/textos', ['success' => 'Texto eliminado']);
    }

    /** POST /admin/textos/{id}/restaurar */
    public function restaurar(string $id): void
    {
        $usuario = AuthMiddleware::check($this->db);

        if (($_POST['_csrf'] ?? '') !== csrf_token()) {
            $this->redirect('/admin/textos', ['error' => 'Token CSRF inválido']);
        }

        $texto = $this->texto->find((int)$id);
        if (!$texto) {
            $this->redirect('/admin/textos', ['error' => 'Texto no encontrado']);
        }

        $this->texto->restore((int)$id);

        $this->audit($usuario['id'], 'restaurar', 'textos', "Texto #{$id}: {$texto['clave']} restaurado a default", (int)$id, 'texto_editable');

        $this->redirect('/admin/textos', ['success' => "Texto '{$texto['clave']}' restaurado al valor original"]);
    }

    // ── Helpers privados ─────────────────────────────────

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
