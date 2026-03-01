<?php
/**
 * AdminCambioController — visitapurranque.cl
 * Cambios pendientes de fichas + renovaciones de suscripciones
 */

require_once BASE_PATH . '/app/middleware/AuthMiddleware.php';
require_once BASE_PATH . '/app/models/CambioPendiente.php';
require_once BASE_PATH . '/app/models/Suscripcion.php';

class AdminCambioController extends Controller
{
    private CambioPendiente $cambio;
    private Suscripcion $suscripcion;

    public function __construct(PDO $pdo)
    {
        parent::__construct($pdo);
        $this->cambio = new CambioPendiente($pdo);
        $this->suscripcion = new Suscripcion($pdo);
    }

    // ═══════════════════════════════════════════════════════
    // CAMBIOS PENDIENTES
    // ═══════════════════════════════════════════════════════

    /** GET /admin/cambios */
    public function index(): void
    {
        $usuario = AuthMiddleware::check($this->db);

        $filtros = [
            'estado' => $_GET['estado'] ?? '',
            'tipo'   => $_GET['tipo'] ?? '',
            'q'      => trim($_GET['q'] ?? ''),
        ];

        $pagina = max(1, (int)($_GET['pagina'] ?? 1));
        $total = $this->cambio->count($filtros);
        $cambios = $this->cambio->getAll($filtros, $pagina);
        $totalPaginas = (int)ceil($total / ADMIN_PER_PAGE);
        $stats = $this->cambio->statsPorEstado();

        $this->renderAdmin('admin/cambios/index', [
            'pageTitle'     => 'Cambios Pendientes',
            'usuario'       => $usuario,
            'cambios'       => $cambios,
            'filtros'       => $filtros,
            'pagina'        => $pagina,
            'total'         => $total,
            'totalPaginas'  => $totalPaginas,
            'stats'         => $stats,
            'sidebarCounts' => $this->getSidebarCounts(),
        ]);
    }

    /** GET /admin/cambios/{id} */
    public function show(string $id): void
    {
        $usuario = AuthMiddleware::check($this->db);
        $cambio = $this->cambio->getById((int)$id);

        if (!$cambio) {
            $this->redirect('/admin/cambios', ['error' => 'Cambio no encontrado']);
        }

        // Decodificar JSON para mostrar diff
        $datosNuevos = json_decode($cambio['datos_nuevos'], true) ?: [];
        $datosAnteriores = json_decode($cambio['datos_anteriores'], true) ?: [];

        // Obtener datos actuales de la ficha para comparar
        $fichaActual = null;
        if ($cambio['ficha_id']) {
            $stmt = $this->db->prepare("SELECT * FROM fichas WHERE id = ? LIMIT 1");
            $stmt->execute([$cambio['ficha_id']]);
            $fichaActual = $stmt->fetch();
        }

        $this->renderAdmin('admin/cambios/show', [
            'pageTitle'       => 'Revisar cambio #' . $id,
            'usuario'         => $usuario,
            'cambio'          => $cambio,
            'datosNuevos'     => $datosNuevos,
            'datosAnteriores' => $datosAnteriores,
            'fichaActual'     => $fichaActual,
            'sidebarCounts'   => $this->getSidebarCounts(),
        ]);
    }

    /** POST /admin/cambios/{id}/aprobar */
    public function aprobar(string $id): void
    {
        $usuario = AuthMiddleware::check($this->db);

        if (($_POST['_csrf'] ?? '') !== csrf_token()) {
            $this->redirect("/admin/cambios/{$id}", ['error' => 'Token CSRF inválido']);
        }

        $cambio = $this->cambio->getById((int)$id);
        if (!$cambio) {
            $this->redirect('/admin/cambios', ['error' => 'Cambio no encontrado']);
        }

        if ($cambio['estado'] !== 'pendiente') {
            $this->redirect("/admin/cambios/{$id}", ['error' => 'Este cambio ya fue revisado']);
        }

        $nota = trim($_POST['nota_revision'] ?? '');
        $ok = $this->cambio->aprobar((int)$id, $usuario['id'], $nota ?: null);

        if ($ok) {
            $this->audit($usuario['id'], 'aprobar_cambio', 'cambios', "Cambio #{$id} aprobado — Ficha: {$cambio['ficha_nombre']}", (int)$id);
            $this->redirect('/admin/cambios', ['success' => 'Cambio aprobado y aplicado a la ficha']);
        } else {
            $this->redirect("/admin/cambios/{$id}", ['error' => 'No se pudo aprobar el cambio']);
        }
    }

    /** POST /admin/cambios/{id}/rechazar */
    public function rechazar(string $id): void
    {
        $usuario = AuthMiddleware::check($this->db);

        if (($_POST['_csrf'] ?? '') !== csrf_token()) {
            $this->redirect("/admin/cambios/{$id}", ['error' => 'Token CSRF inválido']);
        }

        $cambio = $this->cambio->getById((int)$id);
        if (!$cambio) {
            $this->redirect('/admin/cambios', ['error' => 'Cambio no encontrado']);
        }

        if ($cambio['estado'] !== 'pendiente') {
            $this->redirect("/admin/cambios/{$id}", ['error' => 'Este cambio ya fue revisado']);
        }

        $nota = trim($_POST['nota_revision'] ?? '');
        if (empty($nota)) {
            $this->redirect("/admin/cambios/{$id}", ['error' => 'Debes indicar el motivo del rechazo']);
        }

        $this->cambio->rechazar((int)$id, $usuario['id'], $nota);
        $this->audit($usuario['id'], 'rechazar_cambio', 'cambios', "Cambio #{$id} rechazado — Ficha: {$cambio['ficha_nombre']}", (int)$id);

        $this->redirect('/admin/cambios', ['success' => 'Cambio rechazado']);
    }

    /** POST /admin/cambios/{id}/eliminar */
    public function delete(string $id): void
    {
        $usuario = AuthMiddleware::check($this->db);

        if (($_POST['_csrf'] ?? '') !== csrf_token()) {
            $this->redirect('/admin/cambios', ['error' => 'Token CSRF inválido']);
        }

        $cambio = $this->cambio->getById((int)$id);
        if (!$cambio) {
            $this->redirect('/admin/cambios', ['error' => 'Cambio no encontrado']);
        }

        $this->cambio->delete((int)$id);
        $this->audit($usuario['id'], 'eliminar_cambio', 'cambios', "Cambio #{$id} eliminado — Ficha: {$cambio['ficha_nombre']}", (int)$id);

        $this->redirect('/admin/cambios', ['success' => 'Cambio eliminado']);
    }

    // ═══════════════════════════════════════════════════════
    // RENOVACIONES
    // ═══════════════════════════════════════════════════════

    /** GET /admin/renovaciones */
    public function renovaciones(): void
    {
        $usuario = AuthMiddleware::check($this->db);

        $dias = max(7, min(180, (int)($_GET['dias'] ?? 30)));

        // Próximas a vencer
        $proximasVencer = $this->suscripcion->proximasAVencer($dias);

        // Ya expiradas (activas con fecha_fin pasada)
        $expiradas = $this->db->query(
            "SELECT s.*, f.nombre AS ficha_nombre, p.nombre AS plan_nombre,
                    DATEDIFF(CURDATE(), s.fecha_fin) AS dias_expirada
             FROM suscripciones s
             LEFT JOIN fichas f ON f.id = s.ficha_id
             LEFT JOIN planes p ON p.id = s.plan_id
             WHERE s.estado = 'activa' AND s.fecha_fin < CURDATE()
             ORDER BY s.fecha_fin ASC"
        )->fetchAll();

        // Stats generales de suscripciones
        $stats = $this->suscripcion->statsPorEstado();

        // Ingresos del mes actual
        $ingresosMes = (int)$this->db->query(
            "SELECT COALESCE(SUM(monto), 0) FROM suscripciones
             WHERE estado = 'activa' AND MONTH(fecha_inicio) = MONTH(CURDATE()) AND YEAR(fecha_inicio) = YEAR(CURDATE())"
        )->fetchColumn();

        // Total ingresos activos
        $ingresosTotales = (int)$this->db->query(
            "SELECT COALESCE(SUM(monto), 0) FROM suscripciones WHERE estado = 'activa'"
        )->fetchColumn();

        $this->renderAdmin('admin/cambios/renovaciones', [
            'pageTitle'       => 'Renovaciones',
            'usuario'         => $usuario,
            'dias'            => $dias,
            'proximasVencer'  => $proximasVencer,
            'expiradas'       => $expiradas,
            'stats'           => $stats,
            'ingresosMes'     => $ingresosMes,
            'ingresosTotales' => $ingresosTotales,
            'sidebarCounts'   => $this->getSidebarCounts(),
        ]);
    }

    /** POST /admin/renovaciones/{id}/renovar */
    public function renovar(string $id): void
    {
        $usuario = AuthMiddleware::check($this->db);

        if (($_POST['_csrf'] ?? '') !== csrf_token()) {
            $this->redirect('/admin/renovaciones', ['error' => 'Token CSRF inválido']);
        }

        $sub = $this->suscripcion->getById((int)$id);
        if (!$sub) {
            $this->redirect('/admin/renovaciones', ['error' => 'Suscripción no encontrada']);
        }

        $meses = max(1, min(24, (int)($_POST['meses'] ?? 12)));

        // Calcular nueva fecha: desde hoy o desde fecha_fin si aún no venció
        $desde = ($sub['fecha_fin'] >= date('Y-m-d')) ? $sub['fecha_fin'] : date('Y-m-d');
        $nuevaFin = date('Y-m-d', strtotime("{$desde} +{$meses} months"));

        $this->suscripcion->update((int)$id, [
            'ficha_id'     => $sub['ficha_id'],
            'plan_id'      => $sub['plan_id'],
            'fecha_inicio' => $sub['fecha_inicio'],
            'fecha_fin'    => $nuevaFin,
            'monto'        => $sub['monto'],
            'estado'       => 'activa',
            'notas'        => trim($sub['notas'] . "\nRenovada el " . date('d/m/Y') . " por {$meses} mes(es)"),
        ]);

        // Actualizar plan_expira en la ficha
        $stmt = $this->db->prepare("UPDATE fichas SET plan_expira = ? WHERE id = ?");
        $stmt->execute([$nuevaFin, $sub['ficha_id']]);

        $this->audit($usuario['id'], 'renovar', 'cambios', "Suscripción #{$id} renovada {$meses} meses — Ficha: {$sub['ficha_nombre']}", (int)$id);

        $this->redirect('/admin/renovaciones', ['success' => "Suscripción renovada hasta {$nuevaFin}"]);
    }

    /** POST /admin/renovaciones/{id}/expirar */
    public function expirar(string $id): void
    {
        $usuario = AuthMiddleware::check($this->db);

        if (($_POST['_csrf'] ?? '') !== csrf_token()) {
            $this->redirect('/admin/renovaciones', ['error' => 'Token CSRF inválido']);
        }

        $sub = $this->suscripcion->getById((int)$id);
        if (!$sub) {
            $this->redirect('/admin/renovaciones', ['error' => 'Suscripción no encontrada']);
        }

        $this->suscripcion->cambiarEstado((int)$id, 'expirada');

        // Quitar plan de la ficha
        $stmt = $this->db->prepare("UPDATE fichas SET plan_id = NULL, plan_expira = NULL WHERE id = ?");
        $stmt->execute([$sub['ficha_id']]);

        $this->audit($usuario['id'], 'expirar', 'cambios', "Suscripción #{$id} marcada expirada — Ficha: {$sub['ficha_nombre']}", (int)$id);

        $this->redirect('/admin/renovaciones', ['success' => 'Suscripción marcada como expirada']);
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
