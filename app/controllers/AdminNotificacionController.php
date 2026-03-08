<?php

require_once BASE_PATH . '/app/middleware/AuthMiddleware.php';
require_once BASE_PATH . '/app/models/Notificacion.php';

class AdminNotificacionController extends Controller
{
    private Notificacion $notif;

    public function __construct(\PDO $db)
    {
        parent::__construct($db);
        $this->notif = new Notificacion($db);
    }

    /** GET /admin/notificaciones */
    public function index(): void
    {
        $usuario = AuthMiddleware::check($this->db);

        $page    = max(1, (int)($_GET['page'] ?? 1));
        $perPage = 30;

        $todas = $this->notif->getByUsuario($usuario['id'], 200);
        $total = count($todas);
        $totalPages = max(1, (int)ceil($total / $perPage));
        $notificaciones = array_slice($todas, ($page - 1) * $perPage, $perPage);

        $this->renderAdmin('admin/notificaciones/index', [
            'pageTitle'       => 'Notificaciones',
            'usuario'         => $usuario,
            'sidebarCounts'   => $this->getSidebarCounts(),
            'notificaciones'  => $notificaciones,
            'page'            => $page,
            'totalPages'      => $totalPages,
            'total'           => $total,
            'noLeidas'        => $this->notif->getNoLeidas($usuario['id']),
        ]);
    }

    /** POST /admin/notificaciones/leer/{id} */
    public function marcarLeida(int $id): void
    {
        $usuario = AuthMiddleware::check($this->db);
        $this->notif->marcarLeida($id);

        if (!empty($_SERVER['HTTP_X_REQUESTED_WITH'])) {
            $this->json(['success' => true]);
        }
        $this->redirect('/admin/notificaciones');
    }

    /** POST /admin/notificaciones/leer-todas */
    public function marcarTodasLeidas(): void
    {
        $usuario = AuthMiddleware::check($this->db);
        $this->notif->marcarTodasLeidas($usuario['id']);

        if (!empty($_SERVER['HTTP_X_REQUESTED_WITH'])) {
            $this->json(['success' => true]);
        }
        $this->redirect('/admin/notificaciones', ['success' => 'Todas las notificaciones marcadas como leídas.']);
    }

    /** GET /admin/notificaciones/api */
    public function api(): void
    {
        $usuario = AuthMiddleware::check($this->db);
        $noLeidas = $this->notif->getNoLeidas($usuario['id']);
        $ultimas = $this->notif->getByUsuario($usuario['id'], 5);

        $this->json([
            'no_leidas' => $noLeidas,
            'items'     => $ultimas,
        ]);
    }

    private function getSidebarCounts(): array
    {
        return [
            'fichas' => (int)$this->db->query("SELECT COUNT(*) FROM fichas WHERE activo = 1 AND eliminado = 0")->fetchColumn(),
            'categorias' => (int)$this->db->query("SELECT COUNT(*) FROM categorias WHERE activo = 1")->fetchColumn(),
        ];
    }
}
