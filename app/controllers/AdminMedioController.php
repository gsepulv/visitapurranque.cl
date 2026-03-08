<?php

require_once BASE_PATH . '/app/middleware/AuthMiddleware.php';

class AdminMedioController extends Controller
{
    private const ALLOWED_TYPES = [
        'image/jpeg', 'image/png', 'image/gif', 'image/webp',
        'application/pdf',
        'application/msword',
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
    ];
    private const MAX_SIZE = 5 * 1024 * 1024;

    /** GET /admin/medios */
    public function index(): void
    {
        $usuario = AuthMiddleware::check($this->db);
        $model = new Medio($this->db);

        $tipo = $_GET['tipo'] ?? '';
        $q    = trim($_GET['q'] ?? '');
        $pagina = max(1, (int)($_GET['p'] ?? 1));
        $porPagina = 20;

        $total = $model->count($tipo, $q);
        $totalPaginas = max(1, (int)ceil($total / $porPagina));
        $pagina = min($pagina, $totalPaginas);
        $offset = ($pagina - 1) * $porPagina;

        $medios = $model->getAll($porPagina, $offset, $tipo, $q);
        $totalSize = $model->getTotalSize();

        $this->renderAdmin('admin/medios/index', [
            'pageTitle'     => 'Galería de Medios',
            'usuario'       => $usuario,
            'sidebarCounts' => $this->getSidebarCounts(),
            'medios'        => $medios,
            'filtros'       => ['tipo' => $tipo, 'q' => $q],
            'pagina'        => $pagina,
            'totalPaginas'  => $totalPaginas,
            'total'         => $total,
            'totalSize'     => $totalSize,
        ]);
    }

    /** GET /admin/medios/crear */
    public function create(): void
    {
        $usuario = AuthMiddleware::check($this->db);

        $this->renderAdmin('admin/medios/form', [
            'pageTitle'     => 'Subir Archivo',
            'usuario'       => $usuario,
            'sidebarCounts' => $this->getSidebarCounts(),
            'medio'         => null,
        ]);
    }

    /** POST /admin/medios/crear */
    public function store(): void
    {
        $usuario = AuthMiddleware::check($this->db);

        if (($_POST['_csrf'] ?? '') !== csrf_token()) {
            $this->redirect('/admin/medios', ['error' => 'Token inválido.']);
        }

        if (empty($_FILES['archivo']) || $_FILES['archivo']['error'] !== UPLOAD_ERR_OK) {
            $this->redirect('/admin/medios/crear', ['error' => 'No se recibió ningún archivo.']);
        }

        $file = $_FILES['archivo'];

        if (!in_array($file['type'], self::ALLOWED_TYPES)) {
            $this->redirect('/admin/medios/crear', ['error' => 'Tipo de archivo no permitido.']);
        }

        if ($file['size'] > self::MAX_SIZE) {
            $this->redirect('/admin/medios/crear', ['error' => 'El archivo excede el límite de 5 MB.']);
        }

        // Generar ruta: medios/YYYY/MM/
        $carpeta = 'medios/' . date('Y/m');
        $uploadDir = UPLOAD_PATH . '/' . $carpeta;
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        // Nombre único
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $slug = preg_replace('/[^a-z0-9\-]/', '', strtolower(pathinfo($file['name'], PATHINFO_FILENAME)));
        $nombre = date('Ymd_His') . '_' . ($slug ?: 'archivo') . '.' . $ext;
        $rutaFinal = $uploadDir . '/' . $nombre;

        if (!move_uploaded_file($file['tmp_name'], $rutaFinal)) {
            $this->redirect('/admin/medios/crear', ['error' => 'Error al guardar el archivo.']);
        }

        // Dimensiones si es imagen
        $ancho = null;
        $alto = null;
        if (str_starts_with($file['type'], 'image/')) {
            $dims = @getimagesize($rutaFinal);
            if ($dims) {
                $ancho = $dims[0];
                $alto = $dims[1];
            }
        }

        $model = new Medio($this->db);
        $id = $model->crear([
            'nombre'     => trim($_POST['titulo'] ?? '') ?: $file['name'],
            'archivo'    => $carpeta . '/' . $nombre,
            'tipo'       => $file['type'],
            'tamano'     => $file['size'],
            'ancho'      => $ancho,
            'alto'       => $alto,
            'alt'        => trim($_POST['alt'] ?? ''),
            'carpeta'    => trim($_POST['carpeta'] ?? 'general'),
            'usuario_id' => $usuario['id'],
        ]);

        $this->audit($usuario['id'], 'crear', 'medios', 'Archivo subido: ' . $file['name'], $id, 'medio');

        $this->redirect('/admin/medios', ['success' => 'Archivo subido correctamente.']);
    }

    /** GET /admin/medios/{id}/editar */
    public function edit(int $id): void
    {
        $usuario = AuthMiddleware::check($this->db);
        $model = new Medio($this->db);
        $medio = $model->getById($id);

        if (!$medio) {
            $this->redirect('/admin/medios', ['error' => 'Archivo no encontrado.']);
        }

        $this->renderAdmin('admin/medios/form', [
            'pageTitle'     => 'Editar: ' . $medio['nombre'],
            'usuario'       => $usuario,
            'sidebarCounts' => $this->getSidebarCounts(),
            'medio'         => $medio,
        ]);
    }

    /** POST /admin/medios/{id}/editar */
    public function update(int $id): void
    {
        $usuario = AuthMiddleware::check($this->db);

        if (($_POST['_csrf'] ?? '') !== csrf_token()) {
            $this->redirect('/admin/medios', ['error' => 'Token inválido.']);
        }

        $model = new Medio($this->db);
        $medio = $model->getById($id);
        if (!$medio) {
            $this->redirect('/admin/medios', ['error' => 'Archivo no encontrado.']);
        }

        $model->actualizar($id, [
            'nombre'  => trim($_POST['titulo'] ?? '') ?: $medio['nombre'],
            'alt'     => trim($_POST['alt'] ?? ''),
            'carpeta' => trim($_POST['carpeta'] ?? 'general'),
        ]);

        $this->audit($usuario['id'], 'editar', 'medios', 'Archivo editado: ' . $medio['nombre'], $id, 'medio');

        $this->redirect('/admin/medios', ['success' => 'Archivo actualizado.']);
    }

    /** POST /admin/medios/{id}/eliminar */
    public function delete(int $id): void
    {
        $usuario = AuthMiddleware::check($this->db);

        if (($_POST['_csrf'] ?? '') !== csrf_token()) {
            $this->redirect('/admin/medios', ['error' => 'Token inválido.']);
        }

        $model = new Medio($this->db);
        $archivo = $model->eliminar($id);

        if ($archivo) {
            $ruta = UPLOAD_PATH . '/' . $archivo;
            if (file_exists($ruta)) {
                unlink($ruta);
            }
            $this->audit($usuario['id'], 'eliminar', 'medios', 'Archivo eliminado: ' . $archivo, $id, 'medio');
        }

        $this->redirect('/admin/medios', ['success' => 'Archivo eliminado.']);
    }

    /** GET /admin/medios/api/buscar — JSON */
    public function apiBuscar(): void
    {
        AuthMiddleware::check($this->db);
        $model = new Medio($this->db);

        $q    = trim($_GET['q'] ?? '');
        $tipo = $_GET['tipo'] ?? '';

        $resultados = $model->buscar($q, $tipo, 30);

        $this->json(['data' => $resultados]);
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
