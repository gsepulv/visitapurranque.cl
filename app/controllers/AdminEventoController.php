<?php
/**
 * AdminEventoController — visitapurranque.cl
 * CRUD de eventos, fiestas y actividades
 */

require_once BASE_PATH . '/app/middleware/AuthMiddleware.php';
require_once BASE_PATH . '/app/models/Evento.php';

class AdminEventoController extends Controller
{
    private Evento $evento;

    public function __construct(PDO $pdo)
    {
        parent::__construct($pdo);
        $this->evento = new Evento($pdo);
    }

    /** GET /admin/eventos */
    public function index(): void
    {
        $usuario = AuthMiddleware::check($this->db);

        $filtros = [
            'categoria_id' => $_GET['categoria_id'] ?? '',
            'activo'       => $_GET['activo'] ?? '',
            'tiempo'       => $_GET['tiempo'] ?? '',
            'q'            => trim($_GET['q'] ?? ''),
        ];

        $pagina = max(1, (int)($_GET['pagina'] ?? 1));
        $total = $this->evento->count($filtros);
        $eventos = $this->evento->getAll($filtros, $pagina);
        $totalPaginas = (int)ceil($total / ADMIN_PER_PAGE);

        $categorias = $this->db->query(
            "SELECT id, nombre FROM categorias WHERE activo = 1 ORDER BY nombre"
        )->fetchAll();

        $this->renderAdmin('admin/eventos/index', [
            'pageTitle'     => 'Eventos',
            'usuario'       => $usuario,
            'eventos'       => $eventos,
            'categorias'    => $categorias,
            'filtros'       => $filtros,
            'pagina'        => $pagina,
            'total'         => $total,
            'totalPaginas'  => $totalPaginas,
            'sidebarCounts' => $this->getSidebarCounts(),
        ]);
    }

    /** GET /admin/eventos/crear */
    public function create(): void
    {
        $usuario = AuthMiddleware::check($this->db);

        $this->renderAdmin('admin/eventos/form', [
            'pageTitle'     => 'Nuevo Evento',
            'usuario'       => $usuario,
            'evento'        => null,
            'categorias'    => $this->getCategorias(),
            'sidebarCounts' => $this->getSidebarCounts(),
        ]);
    }

    /** POST /admin/eventos/crear */
    public function store(): void
    {
        $usuario = AuthMiddleware::check($this->db);

        if (($_POST['_csrf'] ?? '') !== csrf_token()) {
            $this->redirect('/admin/eventos', ['error' => 'Token CSRF inválido']);
        }

        $data = $this->sanitizeInput($_POST);
        $errors = $this->validate($data);

        if (!empty($errors)) {
            $this->redirect('/admin/eventos/crear', ['error' => implode('. ', $errors)]);
        }

        $data['slug'] = slugify($data['titulo']);
        $suffix = 2;
        $baseSlug = $data['slug'];
        while ($this->evento->slugExists($data['slug'])) {
            $data['slug'] = $baseSlug . '-' . $suffix++;
        }

        $data['imagen'] = $this->handleUpload('imagen');
        $data['activo'] = isset($_POST['activo']) ? 1 : 0;
        $data['destacado'] = isset($_POST['destacado']) ? 1 : 0;
        $data['recurrente'] = isset($_POST['recurrente']) ? 1 : 0;

        if (empty($data['categoria_id'])) {
            $data['categoria_id'] = null;
        }

        $id = $this->evento->create($data);
        $this->audit($usuario['id'], 'crear', "Evento #{$id}: {$data['titulo']}", $id);

        $this->redirect('/admin/eventos', ['success' => 'Evento creado correctamente']);
    }

    /** GET /admin/eventos/{id}/editar */
    public function edit(string $id): void
    {
        $usuario = AuthMiddleware::check($this->db);
        $evento = $this->evento->getById((int)$id);

        if (!$evento) {
            $this->redirect('/admin/eventos', ['error' => 'Evento no encontrado']);
        }

        $this->renderAdmin('admin/eventos/form', [
            'pageTitle'     => 'Editar: ' . $evento['titulo'],
            'usuario'       => $usuario,
            'evento'        => $evento,
            'categorias'    => $this->getCategorias(),
            'sidebarCounts' => $this->getSidebarCounts(),
        ]);
    }

    /** POST /admin/eventos/{id}/editar */
    public function update(string $id): void
    {
        $usuario = AuthMiddleware::check($this->db);

        if (($_POST['_csrf'] ?? '') !== csrf_token()) {
            $this->redirect('/admin/eventos', ['error' => 'Token CSRF inválido']);
        }

        $evento = $this->evento->getById((int)$id);
        if (!$evento) {
            $this->redirect('/admin/eventos', ['error' => 'Evento no encontrado']);
        }

        $data = $this->sanitizeInput($_POST);
        $errors = $this->validate($data);

        if (!empty($errors)) {
            $this->redirect("/admin/eventos/{$id}/editar", ['error' => implode('. ', $errors)]);
        }

        $data['slug'] = slugify($data['titulo']);
        $suffix = 2;
        $baseSlug = $data['slug'];
        while ($this->evento->slugExists($data['slug'], (int)$id)) {
            $data['slug'] = $baseSlug . '-' . $suffix++;
        }

        $nuevaImagen = $this->handleUpload('imagen');
        if ($nuevaImagen) {
            if (!empty($evento['imagen'])) {
                $old = UPLOAD_PATH . '/eventos/' . $evento['imagen'];
                if (file_exists($old)) {
                    unlink($old);
                }
            }
            $data['imagen'] = $nuevaImagen;
        } else {
            $data['imagen'] = $evento['imagen'];
        }

        $data['activo'] = isset($_POST['activo']) ? 1 : 0;
        $data['destacado'] = isset($_POST['destacado']) ? 1 : 0;
        $data['recurrente'] = isset($_POST['recurrente']) ? 1 : 0;

        if (empty($data['categoria_id'])) {
            $data['categoria_id'] = null;
        }

        $this->evento->update((int)$id, $data);
        $this->audit($usuario['id'], 'editar', "Evento #{$id}: {$data['titulo']}", (int)$id);

        $this->redirect('/admin/eventos', ['success' => 'Evento actualizado correctamente']);
    }

    /** POST /admin/eventos/{id}/eliminar */
    public function delete(string $id): void
    {
        $usuario = AuthMiddleware::check($this->db);

        if (($_POST['_csrf'] ?? '') !== csrf_token()) {
            $this->redirect('/admin/eventos', ['error' => 'Token CSRF inválido']);
        }

        $evento = $this->evento->getById((int)$id);
        if (!$evento) {
            $this->redirect('/admin/eventos', ['error' => 'Evento no encontrado']);
        }

        $this->evento->softDelete((int)$id);
        $this->audit($usuario['id'], 'eliminar', "Evento #{$id}: {$evento['titulo']}", (int)$id);

        $this->redirect('/admin/eventos', ['success' => 'Evento eliminado correctamente']);
    }

    /** POST /admin/eventos/{id}/toggle */
    public function toggle(string $id): void
    {
        $usuario = AuthMiddleware::check($this->db);

        if (($_POST['_csrf'] ?? '') !== csrf_token()) {
            $this->redirect('/admin/eventos', ['error' => 'Token CSRF inválido']);
        }

        $evento = $this->evento->getById((int)$id);
        if (!$evento) {
            $this->redirect('/admin/eventos', ['error' => 'Evento no encontrado']);
        }

        $this->evento->toggleActivo((int)$id);
        $nuevoEstado = $evento['activo'] ? 'desactivado' : 'activado';
        $this->audit($usuario['id'], 'toggle', "Evento #{$id} {$nuevoEstado}", (int)$id);

        $this->redirect('/admin/eventos', [
            'success' => "Evento {$nuevoEstado} correctamente",
        ]);
    }

    // ── Helpers privados ─────────────────────────────────

    private function sanitizeInput(array $post): array
    {
        return [
            'titulo'           => trim($post['titulo'] ?? ''),
            'descripcion'      => trim($post['descripcion'] ?? ''),
            'descripcion_corta'=> trim($post['descripcion_corta'] ?? ''),
            'fecha_inicio'     => trim($post['fecha_inicio'] ?? ''),
            'fecha_fin'        => trim($post['fecha_fin'] ?? ''),
            'lugar'            => trim($post['lugar'] ?? ''),
            'direccion'        => trim($post['direccion'] ?? ''),
            'latitud'          => $post['latitud'] ?? '',
            'longitud'         => $post['longitud'] ?? '',
            'precio'           => trim($post['precio'] ?? ''),
            'organizador'      => trim($post['organizador'] ?? ''),
            'contacto'         => trim($post['contacto'] ?? ''),
            'url_externa'      => trim($post['url_externa'] ?? ''),
            'categoria_id'     => $post['categoria_id'] ?? '',
            'meta_title'       => trim($post['meta_title'] ?? ''),
            'meta_description' => trim($post['meta_description'] ?? ''),
        ];
    }

    private function validate(array $data): array
    {
        $errors = [];
        if (empty($data['titulo'])) {
            $errors[] = 'El título es obligatorio';
        }
        if (empty($data['fecha_inicio'])) {
            $errors[] = 'La fecha de inicio es obligatoria';
        }
        if (!empty($data['fecha_fin']) && !empty($data['fecha_inicio']) && $data['fecha_fin'] < $data['fecha_inicio']) {
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

        $filename = uniqid('evento_') . '.' . $ext;
        $destDir = UPLOAD_PATH . '/eventos';

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

    private function audit(int $usuarioId, string $accion, string $detalle, ?int $registroId = null): void
    {
        $stmt = $this->db->prepare(
            "INSERT INTO audit_log (usuario_id, accion, modulo, registro_id, registro_tipo, datos_despues, ip, user_agent)
             VALUES (?, ?, 'eventos', ?, 'evento', ?, ?, ?)"
        );
        $stmt->execute([
            $usuarioId,
            $accion,
            $registroId,
            json_encode(['detalle' => $detalle], JSON_UNESCAPED_UNICODE),
            $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0',
            $_SERVER['HTTP_USER_AGENT'] ?? '',
        ]);
    }
}
