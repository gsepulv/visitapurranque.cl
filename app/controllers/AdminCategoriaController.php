<?php
/**
 * AdminCategoriaController — visitapurranque.cl
 * CRUD de categorías y subcategorías
 */

require_once BASE_PATH . '/app/middleware/AuthMiddleware.php';
require_once BASE_PATH . '/app/models/Categoria.php';

class AdminCategoriaController extends Controller
{
    private Categoria $categoria;

    public function __construct(PDO $pdo)
    {
        parent::__construct($pdo);
        $this->categoria = new Categoria($pdo);
    }

    /** GET /admin/categorias */
    public function index(): void
    {
        $usuario = AuthMiddleware::check($this->db);

        $filtros = [
            'activo' => $_GET['activo'] ?? '',
            'q'      => trim($_GET['q'] ?? ''),
        ];

        $pagina = max(1, (int)($_GET['pagina'] ?? 1));
        $total = $this->categoria->count($filtros);
        $categorias = $this->categoria->getAll($filtros, $pagina);
        $totalPaginas = (int)ceil($total / ADMIN_PER_PAGE);

        $this->renderAdmin('admin/categorias/index', [
            'pageTitle'     => 'Categorías',
            'usuario'       => $usuario,
            'categorias'    => $categorias,
            'filtros'       => $filtros,
            'pagina'        => $pagina,
            'total'         => $total,
            'totalPaginas'  => $totalPaginas,
            'sidebarCounts' => $this->getSidebarCounts(),
        ]);
    }

    /** GET /admin/categorias/crear */
    public function create(): void
    {
        $usuario = AuthMiddleware::check($this->db);

        $this->renderAdmin('admin/categorias/form', [
            'pageTitle'      => 'Nueva Categoría',
            'usuario'        => $usuario,
            'cat'            => null,
            'subcategorias'  => [],
            'sidebarCounts'  => $this->getSidebarCounts(),
        ]);
    }

    /** POST /admin/categorias/crear */
    public function store(): void
    {
        $usuario = AuthMiddleware::check($this->db);

        if (($_POST['_csrf'] ?? '') !== csrf_token()) {
            $this->redirect('/admin/categorias', ['error' => 'Token CSRF inválido']);
        }

        $data = $this->sanitizeInput($_POST);
        $errors = $this->validate($data);

        if (!empty($errors)) {
            $this->redirect('/admin/categorias/crear', ['error' => implode('. ', $errors)]);
        }

        // Slug único
        $data['slug'] = slugify($data['nombre']);
        $suffix = 2;
        $baseSlug = $data['slug'];
        while ($this->categoria->slugExists($data['slug'])) {
            $data['slug'] = $baseSlug . '-' . $suffix++;
        }

        $data['activo'] = isset($_POST['activo']) ? 1 : 0;

        $id = $this->categoria->create($data);
        $this->audit($usuario['id'], 'crear', 'categorias', "Categoría #{$id}: {$data['nombre']}");

        $this->redirect('/admin/categorias', ['success' => 'Categoría creada correctamente']);
    }

    /** GET /admin/categorias/{id}/editar */
    public function edit(string $id): void
    {
        $usuario = AuthMiddleware::check($this->db);
        $cat = $this->categoria->getById((int)$id);

        if (!$cat) {
            $this->redirect('/admin/categorias', ['error' => 'Categoría no encontrada']);
        }

        $subcategorias = $this->categoria->getSubcategorias((int)$id);

        $this->renderAdmin('admin/categorias/form', [
            'pageTitle'      => 'Editar: ' . $cat['nombre'],
            'usuario'        => $usuario,
            'cat'            => $cat,
            'subcategorias'  => $subcategorias,
            'sidebarCounts'  => $this->getSidebarCounts(),
        ]);
    }

    /** POST /admin/categorias/{id}/editar */
    public function update(string $id): void
    {
        $usuario = AuthMiddleware::check($this->db);

        if (($_POST['_csrf'] ?? '') !== csrf_token()) {
            $this->redirect('/admin/categorias', ['error' => 'Token CSRF inválido']);
        }

        $cat = $this->categoria->getById((int)$id);
        if (!$cat) {
            $this->redirect('/admin/categorias', ['error' => 'Categoría no encontrada']);
        }

        $data = $this->sanitizeInput($_POST);
        $errors = $this->validate($data);

        if (!empty($errors)) {
            $this->redirect("/admin/categorias/{$id}/editar", ['error' => implode('. ', $errors)]);
        }

        // Slug único (si cambió el nombre)
        $data['slug'] = slugify($data['nombre']);
        $suffix = 2;
        $baseSlug = $data['slug'];
        while ($this->categoria->slugExists($data['slug'], (int)$id)) {
            $data['slug'] = $baseSlug . '-' . $suffix++;
        }

        // Upload imagen
        $nuevaImagen = $this->handleUpload('imagen');
        if ($nuevaImagen) {
            if (!empty($cat['imagen'])) {
                $old = UPLOAD_PATH . '/categorias/' . $cat['imagen'];
                if (file_exists($old)) {
                    unlink($old);
                }
            }
            $data['imagen'] = $nuevaImagen;
        } else {
            $data['imagen'] = $cat['imagen'];
        }

        $data['activo'] = isset($_POST['activo']) ? 1 : 0;

        $this->categoria->update((int)$id, $data);
        $this->audit($usuario['id'], 'editar', 'categorias', "Categoría #{$id}: {$data['nombre']}");

        $this->redirect('/admin/categorias', ['success' => 'Categoría actualizada correctamente']);
    }

    /** POST /admin/categorias/{id}/eliminar */
    public function delete(string $id): void
    {
        $usuario = AuthMiddleware::check($this->db);

        if (($_POST['_csrf'] ?? '') !== csrf_token()) {
            $this->redirect('/admin/categorias', ['error' => 'Token CSRF inválido']);
        }

        $cat = $this->categoria->getById((int)$id);
        if (!$cat) {
            $this->redirect('/admin/categorias', ['error' => 'Categoría no encontrada']);
        }

        if (!$this->categoria->delete((int)$id)) {
            $this->redirect('/admin/categorias', [
                'error' => 'No se puede eliminar: tiene fichas asociadas. Desactívala en su lugar.',
            ]);
        }

        $this->audit($usuario['id'], 'eliminar', 'categorias', "Categoría #{$id}: {$cat['nombre']}");

        $this->redirect('/admin/categorias', ['success' => 'Categoría eliminada correctamente']);
    }

    /** POST /admin/categorias/{id}/toggle */
    public function toggle(string $id): void
    {
        $usuario = AuthMiddleware::check($this->db);

        if (($_POST['_csrf'] ?? '') !== csrf_token()) {
            $this->redirect('/admin/categorias', ['error' => 'Token CSRF inválido']);
        }

        $cat = $this->categoria->getById((int)$id);
        if (!$cat) {
            $this->redirect('/admin/categorias', ['error' => 'Categoría no encontrada']);
        }

        $this->categoria->toggleActivo((int)$id);
        $nuevoEstado = $cat['activo'] ? 'desactivada' : 'activada';
        $this->audit($usuario['id'], 'toggle', 'categorias', "Categoría #{$id} {$nuevoEstado}");

        $this->redirect('/admin/categorias', [
            'success' => "Categoría {$nuevoEstado} correctamente",
        ]);
    }

    // ── Subcategorías ────────────────────────────────────

    /** POST /admin/categorias/{id}/subcategorias/crear */
    public function storeSub(string $id): void
    {
        $usuario = AuthMiddleware::check($this->db);

        if (($_POST['_csrf'] ?? '') !== csrf_token()) {
            $this->redirect("/admin/categorias/{$id}/editar", ['error' => 'Token CSRF inválido']);
        }

        $cat = $this->categoria->getById((int)$id);
        if (!$cat) {
            $this->redirect('/admin/categorias', ['error' => 'Categoría no encontrada']);
        }

        $nombre = trim($_POST['sub_nombre'] ?? '');
        if (empty($nombre)) {
            $this->redirect("/admin/categorias/{$id}/editar", ['error' => 'El nombre de la subcategoría es obligatorio']);
        }

        $slug = slugify($nombre);
        $suffix = 2;
        $baseSlug = $slug;
        while ($this->categoria->subcategoriaSlugExists($slug, (int)$id)) {
            $slug = $baseSlug . '-' . $suffix++;
        }

        $subId = $this->categoria->createSubcategoria([
            'categoria_id' => (int)$id,
            'nombre'       => $nombre,
            'slug'         => $slug,
            'descripcion'  => trim($_POST['sub_descripcion'] ?? ''),
            'orden'        => (int)($_POST['sub_orden'] ?? 0),
            'activo'       => isset($_POST['sub_activo']) ? 1 : 0,
        ]);

        $this->audit($usuario['id'], 'crear', 'categorias', "Subcategoría #{$subId}: {$nombre} (en {$cat['nombre']})");

        $this->redirect("/admin/categorias/{$id}/editar", ['success' => 'Subcategoría creada correctamente']);
    }

    /** POST /admin/categorias/{catId}/subcategorias/{subId}/eliminar */
    public function deleteSub(string $catId, string $subId): void
    {
        $usuario = AuthMiddleware::check($this->db);

        if (($_POST['_csrf'] ?? '') !== csrf_token()) {
            $this->redirect("/admin/categorias/{$catId}/editar", ['error' => 'Token CSRF inválido']);
        }

        $sub = $this->categoria->getSubcategoriaById((int)$subId);
        if (!$sub) {
            $this->redirect("/admin/categorias/{$catId}/editar", ['error' => 'Subcategoría no encontrada']);
        }

        if (!$this->categoria->deleteSubcategoria((int)$subId)) {
            $this->redirect("/admin/categorias/{$catId}/editar", [
                'error' => 'No se puede eliminar: tiene fichas asociadas.',
            ]);
        }

        $this->audit($usuario['id'], 'eliminar', 'categorias', "Subcategoría #{$subId}: {$sub['nombre']}");

        $this->redirect("/admin/categorias/{$catId}/editar", ['success' => 'Subcategoría eliminada correctamente']);
    }

    // ── Helpers privados ─────────────────────────────────

    private function sanitizeInput(array $post): array
    {
        return [
            'nombre'           => trim($post['nombre'] ?? ''),
            'descripcion'      => trim($post['descripcion'] ?? ''),
            'emoji'            => trim($post['emoji'] ?? ''),
            'icono'            => trim($post['icono'] ?? ''),
            'imagen'           => '',
            'color'            => trim($post['color'] ?? '#3b82f6'),
            'meta_title'       => trim($post['meta_title'] ?? ''),
            'meta_description' => trim($post['meta_description'] ?? ''),
            'orden'            => (int)($post['orden'] ?? 0),
        ];
    }

    private function validate(array $data): array
    {
        $errors = [];
        if (empty($data['nombre'])) {
            $errors[] = 'El nombre es obligatorio';
        }
        if (mb_strlen($data['nombre']) > 100) {
            $errors[] = 'El nombre no puede exceder 100 caracteres';
        }
        return $errors;
    }

    private function handleUpload(string $field): ?string
    {
        if (empty($_FILES[$field]['tmp_name']) || $_FILES[$field]['error'] !== UPLOAD_ERR_OK) {
            return null;
        }

        $file = $_FILES[$field];
        $mime = mime_content_type($file['tmp_name']);

        if (!in_array($mime, UPLOAD_ALLOWED_TYPES)) {
            return null;
        }

        if ($file['size'] > UPLOAD_MAX_SIZE) {
            return null;
        }

        $ext = match ($mime) {
            'image/jpeg' => 'jpg',
            'image/png'  => 'png',
            'image/webp' => 'webp',
            default      => 'jpg',
        };

        $filename = uniqid('cat_') . '.' . $ext;
        $destDir = UPLOAD_PATH . '/categorias';

        if (!is_dir($destDir)) {
            mkdir($destDir, 0755, true);
        }

        move_uploaded_file($file['tmp_name'], $destDir . '/' . $filename);
        return $filename;
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
