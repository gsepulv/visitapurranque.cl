<?php
/**
 * AdminBannerController — visitapurranque.cl
 * CRUD de banners con A/B testing y estadísticas CTR
 */

require_once BASE_PATH . '/app/middleware/AuthMiddleware.php';
require_once BASE_PATH . '/app/models/Banner.php';

class AdminBannerController extends Controller
{
    private Banner $banner;

    public function __construct(PDO $pdo)
    {
        parent::__construct($pdo);
        $this->banner = new Banner($pdo);
    }

    /** GET /admin/banners */
    public function index(): void
    {
        $usuario = AuthMiddleware::check($this->db);

        $filtros = [
            'posicion' => $_GET['posicion'] ?? '',
            'activo'   => $_GET['activo'] ?? '',
            'variante' => $_GET['variante'] ?? '',
            'q'        => trim($_GET['q'] ?? ''),
        ];

        $pagina = max(1, (int)($_GET['pagina'] ?? 1));
        $total = $this->banner->count($filtros);
        $banners = $this->banner->getAll($filtros, $pagina);
        $totalPaginas = (int)ceil($total / ADMIN_PER_PAGE);

        $this->renderAdmin('admin/banners/index', [
            'pageTitle'     => 'Banners',
            'usuario'       => $usuario,
            'banners'       => $banners,
            'filtros'       => $filtros,
            'pagina'        => $pagina,
            'total'         => $total,
            'totalPaginas'  => $totalPaginas,
            'posiciones'    => Banner::posiciones(),
            'sidebarCounts' => $this->getSidebarCounts(),
        ]);
    }

    /** GET /admin/banners/crear */
    public function create(): void
    {
        $usuario = AuthMiddleware::check($this->db);

        $this->renderAdmin('admin/banners/form', [
            'pageTitle'     => 'Nuevo Banner',
            'usuario'       => $usuario,
            'banner'        => null,
            'posiciones'    => Banner::posiciones(),
            'sidebarCounts' => $this->getSidebarCounts(),
        ]);
    }

    /** POST /admin/banners/crear */
    public function store(): void
    {
        $usuario = AuthMiddleware::check($this->db);

        if (($_POST['_csrf'] ?? '') !== csrf_token()) {
            $this->redirect('/admin/banners', ['error' => 'Token CSRF inválido']);
        }

        $data = $this->sanitizeInput($_POST);
        $errors = $this->validate($data);

        if (!empty($errors)) {
            $this->redirect('/admin/banners/crear', ['error' => implode('. ', $errors)]);
        }

        $data['imagen'] = $this->handleUpload('imagen');
        if (!$data['imagen']) {
            $this->redirect('/admin/banners/crear', ['error' => 'La imagen es obligatoria']);
        }

        $data['imagen_mobile'] = $this->handleUpload('imagen_mobile');
        $data['activo'] = isset($_POST['activo']) ? 1 : 0;

        $id = $this->banner->create($data);
        $this->audit($usuario['id'], 'crear', 'banners', "Banner #{$id}: {$data['titulo']}", $id);

        $this->redirect('/admin/banners', ['success' => 'Banner creado correctamente']);
    }

    /** GET /admin/banners/{id}/editar */
    public function edit(string $id): void
    {
        $usuario = AuthMiddleware::check($this->db);
        $banner = $this->banner->getById((int)$id);

        if (!$banner) {
            $this->redirect('/admin/banners', ['error' => 'Banner no encontrado']);
        }

        $this->renderAdmin('admin/banners/form', [
            'pageTitle'     => 'Editar: ' . $banner['titulo'],
            'usuario'       => $usuario,
            'banner'        => $banner,
            'posiciones'    => Banner::posiciones(),
            'sidebarCounts' => $this->getSidebarCounts(),
        ]);
    }

    /** POST /admin/banners/{id}/editar */
    public function update(string $id): void
    {
        $usuario = AuthMiddleware::check($this->db);

        if (($_POST['_csrf'] ?? '') !== csrf_token()) {
            $this->redirect('/admin/banners', ['error' => 'Token CSRF inválido']);
        }

        $banner = $this->banner->getById((int)$id);
        if (!$banner) {
            $this->redirect('/admin/banners', ['error' => 'Banner no encontrado']);
        }

        $data = $this->sanitizeInput($_POST);
        $errors = $this->validate($data);

        if (!empty($errors)) {
            $this->redirect("/admin/banners/{$id}/editar", ['error' => implode('. ', $errors)]);
        }

        // Imagen principal
        $nuevaImagen = $this->handleUpload('imagen');
        if ($nuevaImagen) {
            $this->deleteFile('banners/' . $banner['imagen']);
            $data['imagen'] = $nuevaImagen;
        } else {
            $data['imagen'] = $banner['imagen'];
        }

        // Imagen mobile
        $nuevaMobile = $this->handleUpload('imagen_mobile');
        if ($nuevaMobile) {
            if (!empty($banner['imagen_mobile'])) {
                $this->deleteFile('banners/' . $banner['imagen_mobile']);
            }
            $data['imagen_mobile'] = $nuevaMobile;
        } else {
            $data['imagen_mobile'] = $banner['imagen_mobile'];
        }

        $data['activo'] = isset($_POST['activo']) ? 1 : 0;

        $this->banner->update((int)$id, $data);
        $this->audit($usuario['id'], 'editar', 'banners', "Banner #{$id}: {$data['titulo']}", (int)$id);

        $this->redirect('/admin/banners', ['success' => 'Banner actualizado correctamente']);
    }

    /** POST /admin/banners/{id}/eliminar */
    public function delete(string $id): void
    {
        $usuario = AuthMiddleware::check($this->db);

        if (($_POST['_csrf'] ?? '') !== csrf_token()) {
            $this->redirect('/admin/banners', ['error' => 'Token CSRF inválido']);
        }

        $banner = $this->banner->getById((int)$id);
        if (!$banner) {
            $this->redirect('/admin/banners', ['error' => 'Banner no encontrado']);
        }

        $this->deleteFile('banners/' . $banner['imagen']);
        if (!empty($banner['imagen_mobile'])) {
            $this->deleteFile('banners/' . $banner['imagen_mobile']);
        }

        $this->banner->delete((int)$id);
        $this->audit($usuario['id'], 'eliminar', 'banners', "Banner #{$id}: {$banner['titulo']}", (int)$id);

        $this->redirect('/admin/banners', ['success' => 'Banner eliminado correctamente']);
    }

    /** POST /admin/banners/{id}/toggle */
    public function toggle(string $id): void
    {
        $usuario = AuthMiddleware::check($this->db);

        if (($_POST['_csrf'] ?? '') !== csrf_token()) {
            $this->redirect('/admin/banners', ['error' => 'Token CSRF inválido']);
        }

        $banner = $this->banner->getById((int)$id);
        if (!$banner) {
            $this->redirect('/admin/banners', ['error' => 'Banner no encontrado']);
        }

        $this->banner->toggleActivo((int)$id);
        $nuevoEstado = $banner['activo'] ? 'desactivado' : 'activado';
        $this->audit($usuario['id'], 'toggle', 'banners', "Banner #{$id} {$nuevoEstado}", (int)$id);

        $this->redirect('/admin/banners', ['success' => "Banner {$nuevoEstado}"]);
    }

    /** POST /admin/banners/{id}/reset-stats */
    public function resetStats(string $id): void
    {
        $usuario = AuthMiddleware::check($this->db);

        if (($_POST['_csrf'] ?? '') !== csrf_token()) {
            $this->redirect('/admin/banners', ['error' => 'Token CSRF inválido']);
        }

        $banner = $this->banner->getById((int)$id);
        if (!$banner) {
            $this->redirect('/admin/banners', ['error' => 'Banner no encontrado']);
        }

        $this->banner->resetStats((int)$id);
        $this->audit($usuario['id'], 'reset_stats', 'banners', "Banner #{$id}: stats reseteadas", (int)$id);

        $this->redirect("/admin/banners/{$id}/editar", ['success' => 'Estadísticas reseteadas']);
    }

    // ── Helpers privados ─────────────────────────────────

    private function sanitizeInput(array $post): array
    {
        return [
            'titulo'       => trim($post['titulo'] ?? ''),
            'url'          => trim($post['url'] ?? ''),
            'posicion'     => $post['posicion'] ?? 'home_top',
            'fecha_inicio' => trim($post['fecha_inicio'] ?? ''),
            'fecha_fin'    => trim($post['fecha_fin'] ?? ''),
            'variante'     => $post['variante'] ?? 'A',
            'orden'        => (int)($post['orden'] ?? 0),
        ];
    }

    private function validate(array $data): array
    {
        $errors = [];
        if (empty($data['titulo'])) {
            $errors[] = 'El título es obligatorio';
        }
        $posicionesValidas = array_keys(Banner::posiciones());
        if (!in_array($data['posicion'], $posicionesValidas)) {
            $errors[] = 'Posición inválida';
        }
        if (!in_array($data['variante'], ['A', 'B'])) {
            $errors[] = 'Variante inválida';
        }
        if (!empty($data['fecha_inicio']) && !empty($data['fecha_fin']) && $data['fecha_fin'] < $data['fecha_inicio']) {
            $errors[] = 'La fecha de fin no puede ser anterior a la de inicio';
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

        $filename = uniqid('banner_') . '.' . $ext;
        $destDir = UPLOAD_PATH . '/banners';

        if (!is_dir($destDir)) {
            mkdir($destDir, 0755, true);
        }

        move_uploaded_file($file['tmp_name'], $destDir . '/' . $filename);
        return $filename;
    }

    private function deleteFile(string $relativePath): void
    {
        $path = UPLOAD_PATH . '/' . $relativePath;
        if (file_exists($path)) {
            unlink($path);
        }
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
