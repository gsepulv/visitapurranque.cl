<?php
/**
 * AdminBlogController — visitapurranque.cl
 * CRUD de posts del blog: noticias, artículos, guías
 */

require_once BASE_PATH . '/app/middleware/AuthMiddleware.php';
require_once BASE_PATH . '/app/models/BlogPost.php';

class AdminBlogController extends Controller
{
    private BlogPost $post;

    public function __construct(PDO $pdo)
    {
        parent::__construct($pdo);
        $this->post = new BlogPost($pdo);
    }

    /** GET /admin/blog */
    public function index(): void
    {
        $usuario = AuthMiddleware::check($this->db);

        $filtros = [
            'categoria_id' => $_GET['categoria_id'] ?? '',
            'estado'       => $_GET['estado'] ?? '',
            'tipo'         => $_GET['tipo'] ?? '',
            'autor_id'     => $_GET['autor_id'] ?? '',
            'q'            => trim($_GET['q'] ?? ''),
        ];

        $pagina = max(1, (int)($_GET['pagina'] ?? 1));
        $total = $this->post->count($filtros);
        $posts = $this->post->getAll($filtros, $pagina);
        $totalPaginas = (int)ceil($total / ADMIN_PER_PAGE);

        $this->renderAdmin('admin/blog/index', [
            'pageTitle'     => 'Blog',
            'usuario'       => $usuario,
            'posts'         => $posts,
            'categorias'    => $this->getCategorias(),
            'autores'       => $this->getAutores(),
            'filtros'       => $filtros,
            'pagina'        => $pagina,
            'total'         => $total,
            'totalPaginas'  => $totalPaginas,
            'sidebarCounts' => $this->getSidebarCounts(),
        ]);
    }

    /** GET /admin/blog/crear */
    public function create(): void
    {
        $usuario = AuthMiddleware::check($this->db);

        $this->renderAdmin('admin/blog/form', [
            'pageTitle'     => 'Nuevo Post',
            'usuario'       => $usuario,
            'post'          => null,
            'categorias'    => $this->getCategorias(),
            'autores'       => $this->getAutores(),
            'sidebarCounts' => $this->getSidebarCounts(),
        ]);
    }

    /** POST /admin/blog/crear */
    public function store(): void
    {
        $usuario = AuthMiddleware::check($this->db);

        if (($_POST['_csrf'] ?? '') !== csrf_token()) {
            $this->redirect('/admin/blog', ['error' => 'Token CSRF inválido']);
        }

        $data = $this->sanitizeInput($_POST);
        $errors = $this->validate($data);

        if (!empty($errors)) {
            $this->redirect('/admin/blog/crear', ['error' => implode('. ', $errors)]);
        }

        $data['slug'] = slugify($data['titulo']);
        $suffix = 2;
        $baseSlug = $data['slug'];
        while ($this->post->slugExists($data['slug'])) {
            $data['slug'] = $baseSlug . '-' . $suffix++;
        }

        $data['imagen_portada'] = $this->handleUpload('imagen_portada');
        $data['destacado'] = isset($_POST['destacado']) ? 1 : 0;
        $data['permite_comentarios'] = isset($_POST['permite_comentarios']) ? 1 : 0;

        if (empty($data['categoria_id'])) $data['categoria_id'] = null;
        if (empty($data['autor_id'])) $data['autor_id'] = null;

        $id = $this->post->create($data);
        $this->audit($usuario['id'], 'crear', "Post #{$id}: {$data['titulo']}", $id);

        $this->redirect('/admin/blog', ['success' => 'Post creado correctamente']);
    }

    /** GET /admin/blog/{id}/editar */
    public function edit(string $id): void
    {
        $usuario = AuthMiddleware::check($this->db);
        $post = $this->post->getById((int)$id);

        if (!$post) {
            $this->redirect('/admin/blog', ['error' => 'Post no encontrado']);
        }

        $this->renderAdmin('admin/blog/form', [
            'pageTitle'     => 'Editar: ' . $post['titulo'],
            'usuario'       => $usuario,
            'post'          => $post,
            'categorias'    => $this->getCategorias(),
            'autores'       => $this->getAutores(),
            'sidebarCounts' => $this->getSidebarCounts(),
        ]);
    }

    /** POST /admin/blog/{id}/editar */
    public function update(string $id): void
    {
        $usuario = AuthMiddleware::check($this->db);

        if (($_POST['_csrf'] ?? '') !== csrf_token()) {
            $this->redirect('/admin/blog', ['error' => 'Token CSRF inválido']);
        }

        $post = $this->post->getById((int)$id);
        if (!$post) {
            $this->redirect('/admin/blog', ['error' => 'Post no encontrado']);
        }

        $data = $this->sanitizeInput($_POST);
        $errors = $this->validate($data);

        if (!empty($errors)) {
            $this->redirect("/admin/blog/{$id}/editar", ['error' => implode('. ', $errors)]);
        }

        $data['slug'] = slugify($data['titulo']);
        $suffix = 2;
        $baseSlug = $data['slug'];
        while ($this->post->slugExists($data['slug'], (int)$id)) {
            $data['slug'] = $baseSlug . '-' . $suffix++;
        }

        $nuevaImagen = $this->handleUpload('imagen_portada');
        if ($nuevaImagen) {
            if (!empty($post['imagen_portada'])) {
                $old = UPLOAD_PATH . '/blog/' . $post['imagen_portada'];
                if (file_exists($old)) {
                    unlink($old);
                }
            }
            $data['imagen_portada'] = $nuevaImagen;
        } else {
            $data['imagen_portada'] = $post['imagen_portada'];
        }

        $data['destacado'] = isset($_POST['destacado']) ? 1 : 0;
        $data['permite_comentarios'] = isset($_POST['permite_comentarios']) ? 1 : 0;

        if (empty($data['categoria_id'])) $data['categoria_id'] = null;
        if (empty($data['autor_id'])) $data['autor_id'] = null;

        $this->post->update((int)$id, $data);
        $this->audit($usuario['id'], 'editar', "Post #{$id}: {$data['titulo']}", (int)$id);

        $this->redirect('/admin/blog', ['success' => 'Post actualizado correctamente']);
    }

    /** POST /admin/blog/{id}/eliminar */
    public function delete(string $id): void
    {
        $usuario = AuthMiddleware::check($this->db);

        if (($_POST['_csrf'] ?? '') !== csrf_token()) {
            $this->redirect('/admin/blog', ['error' => 'Token CSRF inválido']);
        }

        $post = $this->post->getById((int)$id);
        if (!$post) {
            $this->redirect('/admin/blog', ['error' => 'Post no encontrado']);
        }

        $this->post->softDelete((int)$id);
        $this->audit($usuario['id'], 'eliminar', "Post #{$id}: {$post['titulo']}", (int)$id);

        $this->redirect('/admin/blog', ['success' => 'Post eliminado correctamente']);
    }

    // ── Helpers privados ─────────────────────────────────

    private function sanitizeInput(array $post): array
    {
        return [
            'titulo'             => trim($post['titulo'] ?? ''),
            'extracto'           => trim($post['extracto'] ?? ''),
            'contenido'          => trim($post['contenido'] ?? ''),
            'tipo'               => $post['tipo'] ?? 'articulo',
            'categoria_id'       => $post['categoria_id'] ?? '',
            'autor_id'           => $post['autor_id'] ?? '',
            'fuente_nombre'      => trim($post['fuente_nombre'] ?? ''),
            'fuente_url'         => trim($post['fuente_url'] ?? ''),
            'estado'             => $post['estado'] ?? 'borrador',
            'programado_at'      => trim($post['programado_at'] ?? ''),
            'meta_title'         => trim($post['meta_title'] ?? ''),
            'meta_description'   => trim($post['meta_description'] ?? ''),
        ];
    }

    private function validate(array $data): array
    {
        $errors = [];
        if (empty($data['titulo'])) {
            $errors[] = 'El título es obligatorio';
        }
        if (empty($data['contenido'])) {
            $errors[] = 'El contenido es obligatorio';
        }
        $estadosValidos = ['borrador', 'revision', 'programado', 'publicado', 'archivado'];
        if (!in_array($data['estado'], $estadosValidos)) {
            $errors[] = 'Estado inválido';
        }
        $tiposValidos = ['noticia', 'articulo', 'guia', 'opinion', 'entrevista', 'galeria'];
        if (!in_array($data['tipo'], $tiposValidos)) {
            $errors[] = 'Tipo inválido';
        }
        if ($data['estado'] === 'programado' && empty($data['programado_at'])) {
            $errors[] = 'Debes indicar fecha de publicación para posts programados';
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

        $filename = uniqid('blog_') . '.' . $ext;
        $destDir = UPLOAD_PATH . '/blog';

        if (!is_dir($destDir)) {
            mkdir($destDir, 0755, true);
        }

        move_uploaded_file($file['tmp_name'], $destDir . '/' . $filename);
        return $filename;
    }

    private function getCategorias(): array
    {
        return $this->db->query(
            "SELECT id, nombre FROM blog_categorias WHERE activo = 1 ORDER BY orden, nombre"
        )->fetchAll();
    }

    private function getAutores(): array
    {
        return $this->db->query(
            "SELECT id, nombre FROM blog_autores WHERE activo = 1 ORDER BY nombre"
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
             VALUES (?, ?, 'blog', ?, 'blog_post', ?, ?, ?)"
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
