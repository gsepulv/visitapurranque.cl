<?php
/**
 * AdminFichaController — visitapurranque.cl
 * CRUD de fichas de atractivos turisticos
 */

require_once BASE_PATH . '/app/middleware/AuthMiddleware.php';
require_once BASE_PATH . '/app/models/Ficha.php';

class AdminFichaController extends Controller
{
    private Ficha $ficha;

    public function __construct(PDO $pdo)
    {
        parent::__construct($pdo);
        $this->ficha = new Ficha($pdo);
    }

    /** GET /admin/fichas */
    public function index(): void
    {
        $usuario = AuthMiddleware::check($this->db);

        $filtros = [
            'categoria_id' => $_GET['categoria_id'] ?? '',
            'activo'       => $_GET['activo'] ?? '',
            'q'            => trim($_GET['q'] ?? ''),
        ];

        $pagina = max(1, (int)($_GET['pagina'] ?? 1));
        $total = $this->ficha->count($filtros);
        $fichas = $this->ficha->getAll($filtros, $pagina);
        $totalPaginas = (int)ceil($total / ADMIN_PER_PAGE);

        // Categorias para filtro dropdown
        $categorias = $this->db->query(
            "SELECT id, nombre FROM categorias WHERE activo = 1 ORDER BY nombre"
        )->fetchAll();

        $this->renderAdmin('admin/fichas/index', [
            'pageTitle'     => 'Fichas de Atractivos',
            'usuario'       => $usuario,
            'fichas'        => $fichas,
            'categorias'    => $categorias,
            'filtros'       => $filtros,
            'pagina'        => $pagina,
            'total'         => $total,
            'totalPaginas'  => $totalPaginas,
            'sidebarCounts' => $this->getSidebarCounts(),
        ]);
    }

    /** GET /admin/fichas/crear */
    public function create(): void
    {
        $usuario = AuthMiddleware::check($this->db);

        $this->renderAdmin('admin/fichas/form', [
            'pageTitle'     => 'Nueva Ficha',
            'usuario'       => $usuario,
            'ficha'         => null,
            'categorias'    => $this->getCategorias(),
            'subcategorias' => $this->getSubcategorias(),
            'planes'        => $this->getPlanes(),
            'sidebarCounts' => $this->getSidebarCounts(),
        ]);
    }

    /** POST /admin/fichas/crear */
    public function store(): void
    {
        $usuario = AuthMiddleware::check($this->db);

        if (($_POST['_csrf'] ?? '') !== csrf_token()) {
            $this->redirect('/admin/fichas', ['error' => 'Token CSRF inválido']);
        }

        $data = $this->sanitizeInput($_POST);
        $errors = $this->validate($data);

        if (!empty($errors)) {
            $this->redirect('/admin/fichas/crear', ['error' => implode('. ', $errors)]);
        }

        // Slug unico
        $data['slug'] = slugify($data['nombre']);
        $suffix = 2;
        $baseSlug = $data['slug'];
        while ($this->ficha->slugExists($data['slug'])) {
            $data['slug'] = $baseSlug . '-' . $suffix++;
        }

        // Upload imagen portada
        $data['imagen_portada'] = $this->handleUpload('imagen_portada');

        // Checkboxes
        $data['activo']     = isset($_POST['activo']) ? 1 : 0;
        $data['verificado'] = isset($_POST['verificado']) ? 1 : 0;
        $data['destacado']  = isset($_POST['destacado']) ? 1 : 0;
        $data['imperdible'] = isset($_POST['imperdible']) ? 1 : 0;

        // Campos numericos vacios → null
        foreach (['precio_desde', 'precio_hasta', 'plan_id', 'subcategoria_id'] as $campo) {
            if (empty($data[$campo])) {
                $data[$campo] = null;
            }
        }

        $id = $this->ficha->create($data);

        // Audit log
        $this->audit($usuario['id'], 'crear', "Ficha #{$id}: {$data['nombre']}");

        $this->redirect('/admin/fichas', ['success' => 'Ficha creada correctamente']);
    }

    /** GET /admin/fichas/{id}/editar */
    public function edit(string $id): void
    {
        $usuario = AuthMiddleware::check($this->db);
        $ficha = $this->ficha->getById((int)$id);

        if (!$ficha) {
            $this->redirect('/admin/fichas', ['error' => 'Ficha no encontrada']);
        }

        $this->renderAdmin('admin/fichas/form', [
            'pageTitle'     => 'Editar: ' . $ficha['nombre'],
            'usuario'       => $usuario,
            'ficha'         => $ficha,
            'categorias'    => $this->getCategorias(),
            'subcategorias' => $this->getSubcategorias(),
            'planes'        => $this->getPlanes(),
            'sidebarCounts' => $this->getSidebarCounts(),
        ]);
    }

    /** POST /admin/fichas/{id}/editar */
    public function update(string $id): void
    {
        $usuario = AuthMiddleware::check($this->db);

        if (($_POST['_csrf'] ?? '') !== csrf_token()) {
            $this->redirect('/admin/fichas', ['error' => 'Token CSRF inválido']);
        }

        $ficha = $this->ficha->getById((int)$id);
        if (!$ficha) {
            $this->redirect('/admin/fichas', ['error' => 'Ficha no encontrada']);
        }

        $data = $this->sanitizeInput($_POST);
        $errors = $this->validate($data);

        if (!empty($errors)) {
            $this->redirect("/admin/fichas/{$id}/editar", ['error' => implode('. ', $errors)]);
        }

        // Slug unico (si cambio el nombre)
        $data['slug'] = slugify($data['nombre']);
        $suffix = 2;
        $baseSlug = $data['slug'];
        while ($this->ficha->slugExists($data['slug'], (int)$id)) {
            $data['slug'] = $baseSlug . '-' . $suffix++;
        }

        // Upload imagen (solo si se subio una nueva)
        $nuevaImagen = $this->handleUpload('imagen_portada');
        if ($nuevaImagen) {
            // Eliminar imagen anterior
            if (!empty($ficha['imagen_portada'])) {
                $old = UPLOAD_PATH . '/fichas/' . $ficha['imagen_portada'];
                if (file_exists($old)) {
                    unlink($old);
                }
            }
            $data['imagen_portada'] = $nuevaImagen;
        }

        // Checkboxes
        $data['activo']     = isset($_POST['activo']) ? 1 : 0;
        $data['verificado'] = isset($_POST['verificado']) ? 1 : 0;
        $data['destacado']  = isset($_POST['destacado']) ? 1 : 0;
        $data['imperdible'] = isset($_POST['imperdible']) ? 1 : 0;

        // Campos numericos vacios → null
        foreach (['precio_desde', 'precio_hasta', 'plan_id', 'subcategoria_id'] as $campo) {
            if (empty($data[$campo])) {
                $data[$campo] = null;
            }
        }

        $this->ficha->update((int)$id, $data);

        $this->audit($usuario['id'], 'editar', "Ficha #{$id}: {$data['nombre']}");

        $this->redirect('/admin/fichas', ['success' => 'Ficha actualizada correctamente']);
    }

    /** POST /admin/fichas/{id}/eliminar */
    public function delete(string $id): void
    {
        $usuario = AuthMiddleware::check($this->db);

        if (($_POST['_csrf'] ?? '') !== csrf_token()) {
            $this->redirect('/admin/fichas', ['error' => 'Token CSRF inválido']);
        }

        $ficha = $this->ficha->getById((int)$id);
        if (!$ficha) {
            $this->redirect('/admin/fichas', ['error' => 'Ficha no encontrada']);
        }

        $this->ficha->softDelete((int)$id);
        $this->audit($usuario['id'], 'eliminar', "Ficha #{$id}: {$ficha['nombre']}");

        $this->redirect('/admin/fichas', ['success' => 'Ficha eliminada correctamente']);
    }

    /** POST /admin/fichas/{id}/toggle */
    public function toggle(string $id): void
    {
        $usuario = AuthMiddleware::check($this->db);

        if (($_POST['_csrf'] ?? '') !== csrf_token()) {
            $this->redirect('/admin/fichas', ['error' => 'Token CSRF inválido']);
        }

        $ficha = $this->ficha->getById((int)$id);
        if (!$ficha) {
            $this->redirect('/admin/fichas', ['error' => 'Ficha no encontrada']);
        }

        $this->ficha->toggleActivo((int)$id);
        $nuevoEstado = $ficha['activo'] ? 'desactivada' : 'activada';
        $this->audit($usuario['id'], 'toggle', "Ficha #{$id} {$nuevoEstado}");

        $this->redirect('/admin/fichas', [
            'success' => "Ficha {$nuevoEstado} correctamente",
        ]);
    }

    // ── Helpers privados ─────────────────────────────────

    private function sanitizeInput(array $post): array
    {
        return [
            'nombre'           => trim($post['nombre'] ?? ''),
            'categoria_id'     => (int)($post['categoria_id'] ?? 0),
            'subcategoria_id'  => $post['subcategoria_id'] ?? '',
            'descripcion_corta'=> trim($post['descripcion_corta'] ?? ''),
            'descripcion'      => trim($post['descripcion'] ?? ''),
            'direccion'        => trim($post['direccion'] ?? ''),
            'latitud'          => $post['latitud'] ?? '',
            'longitud'         => $post['longitud'] ?? '',
            'telefono'         => trim($post['telefono'] ?? ''),
            'whatsapp'         => trim($post['whatsapp'] ?? ''),
            'email'            => trim($post['email'] ?? ''),
            'sitio_web'        => trim($post['sitio_web'] ?? ''),
            'instagram'        => trim($post['instagram'] ?? ''),
            'facebook'         => trim($post['facebook'] ?? ''),
            'horarios'         => trim($post['horarios'] ?? ''),
            'temporada'        => trim($post['temporada'] ?? ''),
            'dificultad'       => $post['dificultad'] ?? '',
            'duracion_estimada'=> trim($post['duracion_estimada'] ?? ''),
            'precio_desde'     => $post['precio_desde'] ?? '',
            'precio_hasta'     => $post['precio_hasta'] ?? '',
            'precio_texto'     => trim($post['precio_texto'] ?? ''),
            'que_llevar'       => trim($post['que_llevar'] ?? ''),
            'como_llegar'      => trim($post['como_llegar'] ?? ''),
            'info_practica'    => trim($post['info_practica'] ?? ''),
            'meta_title'       => trim($post['meta_title'] ?? ''),
            'meta_description' => trim($post['meta_description'] ?? ''),
            'plan_id'          => $post['plan_id'] ?? '',
        ];
    }

    private function validate(array $data): array
    {
        $errors = [];
        if (empty($data['nombre'])) {
            $errors[] = 'El nombre es obligatorio';
        }
        if (empty($data['categoria_id'])) {
            $errors[] = 'La categoría es obligatoria';
        }
        return $errors;
    }

    private function handleUpload(string $field): ?string
    {
        if (empty($_FILES[$field]['tmp_name']) || $_FILES[$field]['error'] !== UPLOAD_ERR_OK) {
            return null;
        }

        $file = $_FILES[$field];

        // Validar tipo
        $mime = mime_content_type($file['tmp_name']);
        if (!in_array($mime, UPLOAD_ALLOWED_TYPES)) {
            return null;
        }

        // Validar tamano
        if ($file['size'] > UPLOAD_MAX_SIZE) {
            return null;
        }

        $ext = match ($mime) {
            'image/jpeg' => 'jpg',
            'image/png'  => 'png',
            'image/webp' => 'webp',
            default      => 'jpg',
        };

        $filename = uniqid('ficha_') . '.' . $ext;
        $destDir = UPLOAD_PATH . '/fichas';

        if (!is_dir($destDir)) {
            mkdir($destDir, 0755, true);
        }

        move_uploaded_file($file['tmp_name'], $destDir . '/' . $filename);
        return $filename;
    }

    private function getCategorias(): array
    {
        return $this->db->query(
            "SELECT id, nombre FROM categorias WHERE activo = 1 ORDER BY nombre"
        )->fetchAll();
    }

    private function getSubcategorias(): array
    {
        return $this->db->query(
            "SELECT id, nombre, categoria_id FROM subcategorias WHERE activo = 1 ORDER BY nombre"
        )->fetchAll();
    }

    private function getPlanes(): array
    {
        return $this->db->query(
            "SELECT id, nombre FROM planes WHERE activo = 1 ORDER BY orden"
        )->fetchAll();
    }

    private function getSidebarCounts(): array
    {
        return [
            'fichas' => (int)$this->db->query(
                "SELECT COUNT(*) FROM fichas WHERE activo = 1 AND eliminado = 0"
            )->fetchColumn(),
        ];
    }

    private function audit(int $usuarioId, string $accion, string $detalle): void
    {
        $stmt = $this->db->prepare(
            "INSERT INTO audit_log (usuario_id, accion, modulo, detalle, ip, user_agent)
             VALUES (?, ?, 'fichas', ?, ?, ?)"
        );
        $stmt->execute([
            $usuarioId,
            $accion,
            $detalle,
            $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0',
            $_SERVER['HTTP_USER_AGENT'] ?? '',
        ]);
    }
}
