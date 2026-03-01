<?php
/**
 * AdminResenaController — visitapurranque.cl
 * Moderación de reseñas: aprobar, rechazar, responder, eliminar
 */

require_once BASE_PATH . '/app/middleware/AuthMiddleware.php';
require_once BASE_PATH . '/app/models/Resena.php';

class AdminResenaController extends Controller
{
    private Resena $resena;

    public function __construct(PDO $pdo)
    {
        parent::__construct($pdo);
        $this->resena = new Resena($pdo);
    }

    /** GET /admin/resenas */
    public function index(): void
    {
        $usuario = AuthMiddleware::check($this->db);

        $filtros = [
            'estado'           => $_GET['estado'] ?? '',
            'ficha_id'         => $_GET['ficha_id'] ?? '',
            'rating'           => $_GET['rating'] ?? '',
            'tipo_experiencia' => $_GET['tipo_experiencia'] ?? '',
            'q'                => trim($_GET['q'] ?? ''),
        ];

        $pagina = max(1, (int)($_GET['pagina'] ?? 1));
        $total = $this->resena->count($filtros);
        $resenas = $this->resena->getAll($filtros, $pagina);
        $totalPaginas = (int)ceil($total / ADMIN_PER_PAGE);
        $stats = $this->resena->statsPorEstado();

        $this->renderAdmin('admin/resenas/index', [
            'pageTitle'     => 'Reseñas',
            'usuario'       => $usuario,
            'resenas'       => $resenas,
            'fichas'        => $this->getFichas(),
            'filtros'       => $filtros,
            'pagina'        => $pagina,
            'total'         => $total,
            'totalPaginas'  => $totalPaginas,
            'stats'         => $stats,
            'sidebarCounts' => $this->getSidebarCounts(),
        ]);
    }

    /** GET /admin/resenas/{id} */
    public function show(string $id): void
    {
        $usuario = AuthMiddleware::check($this->db);
        $resena = $this->resena->getById((int)$id);

        if (!$resena) {
            $this->redirect('/admin/resenas', ['error' => 'Reseña no encontrada']);
        }

        $this->renderAdmin('admin/resenas/show', [
            'pageTitle'     => 'Reseña #' . $resena['id'],
            'usuario'       => $usuario,
            'resena'        => $resena,
            'sidebarCounts' => $this->getSidebarCounts(),
        ]);
    }

    /** POST /admin/resenas/{id}/estado */
    public function cambiarEstado(string $id): void
    {
        $usuario = AuthMiddleware::check($this->db);

        if (($_POST['_csrf'] ?? '') !== csrf_token()) {
            $this->redirect('/admin/resenas', ['error' => 'Token CSRF inválido']);
        }

        $resena = $this->resena->getById((int)$id);
        if (!$resena) {
            $this->redirect('/admin/resenas', ['error' => 'Reseña no encontrada']);
        }

        $estado = $_POST['estado'] ?? '';
        $estadosValidos = ['pendiente', 'aprobada', 'rechazada', 'spam'];
        if (!in_array($estado, $estadosValidos)) {
            $this->redirect('/admin/resenas', ['error' => 'Estado inválido']);
        }

        $this->resena->cambiarEstado((int)$id, $estado);
        $this->audit($usuario['id'], 'moderar', 'resenas', "Reseña #{$id} → {$estado}", (int)$id);

        $labels = ['aprobada' => 'aprobada', 'rechazada' => 'rechazada', 'spam' => 'marcada como spam', 'pendiente' => 'devuelta a pendiente'];
        $this->redirect('/admin/resenas', ['success' => "Reseña {$labels[$estado]}"]);
    }

    /** POST /admin/resenas/{id}/responder */
    public function responder(string $id): void
    {
        $usuario = AuthMiddleware::check($this->db);

        if (($_POST['_csrf'] ?? '') !== csrf_token()) {
            $this->redirect("/admin/resenas/{$id}", ['error' => 'Token CSRF inválido']);
        }

        $resena = $this->resena->getById((int)$id);
        if (!$resena) {
            $this->redirect('/admin/resenas', ['error' => 'Reseña no encontrada']);
        }

        $respuesta = trim($_POST['respuesta_admin'] ?? '');
        if (empty($respuesta)) {
            $this->redirect("/admin/resenas/{$id}", ['error' => 'La respuesta no puede estar vacía']);
        }

        $this->resena->responder((int)$id, $respuesta);

        // Si estaba pendiente, aprobarla automáticamente al responder
        if ($resena['estado'] === 'pendiente') {
            $this->resena->cambiarEstado((int)$id, 'aprobada');
        }

        $this->audit($usuario['id'], 'responder', 'resenas', "Reseña #{$id}: respuesta admin", (int)$id);

        $this->redirect("/admin/resenas/{$id}", ['success' => 'Respuesta guardada']);
    }

    /** POST /admin/resenas/{id}/eliminar */
    public function delete(string $id): void
    {
        $usuario = AuthMiddleware::check($this->db);

        if (($_POST['_csrf'] ?? '') !== csrf_token()) {
            $this->redirect('/admin/resenas', ['error' => 'Token CSRF inválido']);
        }

        $resena = $this->resena->getById((int)$id);
        if (!$resena) {
            $this->redirect('/admin/resenas', ['error' => 'Reseña no encontrada']);
        }

        $this->resena->delete((int)$id);
        $this->audit($usuario['id'], 'eliminar', 'resenas', "Reseña #{$id} de {$resena['nombre']}", (int)$id);

        $this->redirect('/admin/resenas', ['success' => 'Reseña eliminada permanentemente']);
    }

    // ── Helpers privados ─────────────────────────────────

    private function getFichas(): array
    {
        return $this->db->query(
            "SELECT id, nombre FROM fichas WHERE eliminado = 0 ORDER BY nombre"
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

}
