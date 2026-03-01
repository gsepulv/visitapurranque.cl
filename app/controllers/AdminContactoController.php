<?php
/**
 * AdminContactoController — visitapurranque.cl
 * Bandeja de mensajes de contacto: leer, responder, eliminar
 */

require_once BASE_PATH . '/app/middleware/AuthMiddleware.php';
require_once BASE_PATH . '/app/models/ContactoMensaje.php';

class AdminContactoController extends Controller
{
    private ContactoMensaje $mensaje;

    public function __construct(PDO $pdo)
    {
        parent::__construct($pdo);
        $this->mensaje = new ContactoMensaje($pdo);
    }

    /** GET /admin/mensajes */
    public function index(): void
    {
        $usuario = AuthMiddleware::check($this->db);

        $filtros = [
            'leido'      => $_GET['leido'] ?? '',
            'respondido' => $_GET['respondido'] ?? '',
            'q'          => trim($_GET['q'] ?? ''),
        ];

        $pagina = max(1, (int)($_GET['pagina'] ?? 1));
        $total = $this->mensaje->count($filtros);
        $mensajes = $this->mensaje->getAll($filtros, $pagina);
        $totalPaginas = (int)ceil($total / ADMIN_PER_PAGE);
        $stats = $this->mensaje->stats();

        $this->renderAdmin('admin/mensajes/index', [
            'pageTitle'     => 'Mensajes',
            'usuario'       => $usuario,
            'mensajes'      => $mensajes,
            'filtros'       => $filtros,
            'pagina'        => $pagina,
            'total'         => $total,
            'totalPaginas'  => $totalPaginas,
            'stats'         => $stats,
            'sidebarCounts' => $this->getSidebarCounts(),
        ]);
    }

    /** GET /admin/mensajes/{id} */
    public function show(string $id): void
    {
        $usuario = AuthMiddleware::check($this->db);
        $msg = $this->mensaje->getById((int)$id);

        if (!$msg) {
            $this->redirect('/admin/mensajes', ['error' => 'Mensaje no encontrado']);
        }

        // Marcar como leído al abrir
        if (!$msg['leido']) {
            $this->mensaje->marcarLeido((int)$id);
            $msg['leido'] = 1;
        }

        $this->renderAdmin('admin/mensajes/show', [
            'pageTitle'     => 'Mensaje de ' . $msg['nombre'],
            'usuario'       => $usuario,
            'msg'           => $msg,
            'sidebarCounts' => $this->getSidebarCounts(),
        ]);
    }

    /** POST /admin/mensajes/{id}/responder */
    public function responder(string $id): void
    {
        $usuario = AuthMiddleware::check($this->db);

        if (($_POST['_csrf'] ?? '') !== csrf_token()) {
            $this->redirect("/admin/mensajes/{$id}", ['error' => 'Token CSRF inválido']);
        }

        $msg = $this->mensaje->getById((int)$id);
        if (!$msg) {
            $this->redirect('/admin/mensajes', ['error' => 'Mensaje no encontrado']);
        }

        $respuesta = trim($_POST['respuesta'] ?? '');
        if (empty($respuesta)) {
            $this->redirect("/admin/mensajes/{$id}", ['error' => 'La respuesta no puede estar vacía']);
        }

        $this->mensaje->responder((int)$id, $respuesta);
        $this->audit($usuario['id'], 'responder', 'contacto', "Mensaje #{$id} de {$msg['nombre']}", (int)$id);

        $this->redirect("/admin/mensajes/{$id}", ['success' => 'Respuesta guardada']);
    }

    /** POST /admin/mensajes/{id}/toggle-leido */
    public function toggleLeido(string $id): void
    {
        $usuario = AuthMiddleware::check($this->db);

        if (($_POST['_csrf'] ?? '') !== csrf_token()) {
            $this->redirect('/admin/mensajes', ['error' => 'Token CSRF inválido']);
        }

        $msg = $this->mensaje->getById((int)$id);
        if (!$msg) {
            $this->redirect('/admin/mensajes', ['error' => 'Mensaje no encontrado']);
        }

        $this->mensaje->toggleLeido((int)$id);
        $nuevoEstado = $msg['leido'] ? 'no leído' : 'leído';

        $this->redirect('/admin/mensajes', ['success' => "Mensaje marcado como {$nuevoEstado}"]);
    }

    /** POST /admin/mensajes/{id}/eliminar */
    public function delete(string $id): void
    {
        $usuario = AuthMiddleware::check($this->db);

        if (($_POST['_csrf'] ?? '') !== csrf_token()) {
            $this->redirect('/admin/mensajes', ['error' => 'Token CSRF inválido']);
        }

        $msg = $this->mensaje->getById((int)$id);
        if (!$msg) {
            $this->redirect('/admin/mensajes', ['error' => 'Mensaje no encontrado']);
        }

        $this->mensaje->delete((int)$id);
        $this->audit($usuario['id'], 'eliminar', 'contacto', "Mensaje #{$id} de {$msg['nombre']}", (int)$id);

        $this->redirect('/admin/mensajes', ['success' => 'Mensaje eliminado']);
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
