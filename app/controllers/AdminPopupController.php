<?php

require_once BASE_PATH . '/app/middleware/AuthMiddleware.php';
require_once BASE_PATH . '/app/models/Popup.php';

class AdminPopupController extends Controller
{
    private Popup $popup;

    public function __construct(\PDO $db)
    {
        parent::__construct($db);
        $this->popup = new Popup($db);
    }

    /** GET /admin/popups */
    public function index(): void
    {
        $usuario = AuthMiddleware::check($this->db);

        $this->renderAdmin('admin/popups/index', [
            'pageTitle'     => 'Popups',
            'usuario'       => $usuario,
            'sidebarCounts' => $this->getSidebarCounts(),
            'popups'        => $this->popup->getAll(),
        ]);
    }

    /** GET /admin/popups/crear */
    public function create(): void
    {
        $usuario = AuthMiddleware::check($this->db);

        $this->renderAdmin('admin/popups/form', [
            'pageTitle'     => 'Nuevo Popup',
            'usuario'       => $usuario,
            'sidebarCounts' => $this->getSidebarCounts(),
            'popup'         => null,
        ]);
    }

    /** POST /admin/popups/crear */
    public function store(): void
    {
        $usuario = AuthMiddleware::check($this->db);
        if (($_POST['_csrf'] ?? '') !== csrf_token()) {
            $this->redirect('/admin/popups', ['error' => 'Token inválido.']);
        }

        $data = $this->sanitize();

        if ($data['titulo'] === '' || $data['contenido'] === '') {
            $this->redirect('/admin/popups/crear', ['error' => 'Título y contenido son obligatorios.']);
        }

        $id = $this->popup->crear($data);
        $this->audit($usuario['id'], 'crear', 'popups', "Popup #{$id}: {$data['titulo']}");
        $this->redirect('/admin/popups', ['success' => 'Popup creado.']);
    }

    /** GET /admin/popups/{id}/editar */
    public function edit(int $id): void
    {
        $usuario = AuthMiddleware::check($this->db);
        $popup = $this->popup->getById($id);

        if (!$popup) {
            $this->redirect('/admin/popups', ['error' => 'Popup no encontrado.']);
        }

        $this->renderAdmin('admin/popups/form', [
            'pageTitle'     => 'Editar Popup',
            'usuario'       => $usuario,
            'sidebarCounts' => $this->getSidebarCounts(),
            'popup'         => $popup,
        ]);
    }

    /** POST /admin/popups/{id}/editar */
    public function update(int $id): void
    {
        $usuario = AuthMiddleware::check($this->db);
        if (($_POST['_csrf'] ?? '') !== csrf_token()) {
            $this->redirect('/admin/popups', ['error' => 'Token inválido.']);
        }

        $data = $this->sanitize();

        if ($data['titulo'] === '' || $data['contenido'] === '') {
            $this->redirect("/admin/popups/{$id}/editar", ['error' => 'Título y contenido son obligatorios.']);
        }

        $this->popup->actualizar($id, $data);
        $this->audit($usuario['id'], 'editar', 'popups', "Popup #{$id}: {$data['titulo']}");
        $this->redirect('/admin/popups', ['success' => 'Popup actualizado.']);
    }

    /** POST /admin/popups/{id}/eliminar */
    public function delete(int $id): void
    {
        $usuario = AuthMiddleware::check($this->db);
        if (($_POST['_csrf'] ?? '') !== csrf_token()) {
            $this->redirect('/admin/popups', ['error' => 'Token inválido.']);
        }

        $this->popup->eliminar($id);
        $this->audit($usuario['id'], 'eliminar', 'popups', "Popup #{$id} eliminado");
        $this->redirect('/admin/popups', ['success' => 'Popup eliminado.']);
    }

    private function sanitize(): array
    {
        return [
            'titulo'        => trim($_POST['titulo'] ?? ''),
            'contenido'     => trim($_POST['contenido'] ?? ''),
            'tipo'          => in_array($_POST['tipo'] ?? '', ['modal', 'banner_top', 'banner_bottom', 'slide_in']) ? $_POST['tipo'] : 'modal',
            'trigger_type'  => in_array($_POST['trigger_type'] ?? '', ['tiempo', 'scroll', 'exit_intent', 'click']) ? $_POST['trigger_type'] : 'tiempo',
            'trigger_valor' => trim($_POST['trigger_valor'] ?? '5'),
            'paginas'       => trim($_POST['paginas'] ?? '') ?: null,
            'fecha_inicio'  => trim($_POST['fecha_inicio'] ?? '') ?: null,
            'fecha_fin'     => trim($_POST['fecha_fin'] ?? '') ?: null,
            'activo'        => isset($_POST['activo']) ? 1 : 0,
        ];
    }

    private function getSidebarCounts(): array
    {
        return [
            'fichas' => (int)$this->db->query("SELECT COUNT(*) FROM fichas WHERE activo = 1 AND eliminado = 0")->fetchColumn(),
            'categorias' => (int)$this->db->query("SELECT COUNT(*) FROM categorias WHERE activo = 1")->fetchColumn(),
        ];
    }
}
