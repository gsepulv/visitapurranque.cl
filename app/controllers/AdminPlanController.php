<?php
/**
 * AdminPlanController — visitapurranque.cl
 * CRUD de planes + suscripciones de fichas
 */

require_once BASE_PATH . '/app/middleware/AuthMiddleware.php';
require_once BASE_PATH . '/app/models/Plan.php';
require_once BASE_PATH . '/app/models/Suscripcion.php';

class AdminPlanController extends Controller
{
    private Plan $plan;
    private Suscripcion $suscripcion;

    public function __construct(PDO $pdo)
    {
        parent::__construct($pdo);
        $this->plan = new Plan($pdo);
        $this->suscripcion = new Suscripcion($pdo);
    }

    // ══════════════════════════════════════════════════
    //  PLANES
    // ══════════════════════════════════════════════════

    /** GET /admin/planes */
    public function index(): void
    {
        $usuario = AuthMiddleware::check($this->db);
        $planes = $this->plan->getAll();

        $this->renderAdmin('admin/planes/index', [
            'pageTitle'     => 'Planes',
            'usuario'       => $usuario,
            'planes'        => $planes,
            'sidebarCounts' => $this->getSidebarCounts(),
        ]);
    }

    /** GET /admin/planes/crear */
    public function create(): void
    {
        $usuario = AuthMiddleware::check($this->db);

        $this->renderAdmin('admin/planes/form', [
            'pageTitle'     => 'Nuevo Plan',
            'usuario'       => $usuario,
            'plan'          => null,
            'sidebarCounts' => $this->getSidebarCounts(),
        ]);
    }

    /** POST /admin/planes/crear */
    public function store(): void
    {
        $usuario = AuthMiddleware::check($this->db);

        if (($_POST['_csrf'] ?? '') !== csrf_token()) {
            $this->redirect('/admin/planes', ['error' => 'Token CSRF inválido']);
        }

        $data = $this->sanitizePlan($_POST);
        $errors = $this->validatePlan($data);

        if (!empty($errors)) {
            $this->redirect('/admin/planes/crear', ['error' => implode('. ', $errors)]);
        }

        $data['slug'] = slugify($data['nombre']);
        $suffix = 2;
        $baseSlug = $data['slug'];
        while ($this->plan->slugExists($data['slug'])) {
            $data['slug'] = $baseSlug . '-' . $suffix++;
        }

        $data['activo'] = isset($_POST['activo']) ? 1 : 0;
        $data['destacado_home'] = isset($_POST['destacado_home']) ? 1 : 0;
        $data['tiene_badge'] = isset($_POST['tiene_badge']) ? 1 : 0;

        $id = $this->plan->create($data);
        $this->audit($usuario['id'], 'crear', "Plan #{$id}: {$data['nombre']}", $id);

        $this->redirect('/admin/planes', ['success' => 'Plan creado correctamente']);
    }

    /** GET /admin/planes/{id}/editar */
    public function edit(string $id): void
    {
        $usuario = AuthMiddleware::check($this->db);
        $plan = $this->plan->getById((int)$id);

        if (!$plan) {
            $this->redirect('/admin/planes', ['error' => 'Plan no encontrado']);
        }

        $this->renderAdmin('admin/planes/form', [
            'pageTitle'     => 'Editar: ' . $plan['nombre'],
            'usuario'       => $usuario,
            'plan'          => $plan,
            'suscripciones' => $this->plan->countSuscripciones((int)$id),
            'sidebarCounts' => $this->getSidebarCounts(),
        ]);
    }

    /** POST /admin/planes/{id}/editar */
    public function update(string $id): void
    {
        $usuario = AuthMiddleware::check($this->db);

        if (($_POST['_csrf'] ?? '') !== csrf_token()) {
            $this->redirect('/admin/planes', ['error' => 'Token CSRF inválido']);
        }

        $plan = $this->plan->getById((int)$id);
        if (!$plan) {
            $this->redirect('/admin/planes', ['error' => 'Plan no encontrado']);
        }

        $data = $this->sanitizePlan($_POST);
        $errors = $this->validatePlan($data);

        if (!empty($errors)) {
            $this->redirect("/admin/planes/{$id}/editar", ['error' => implode('. ', $errors)]);
        }

        $data['slug'] = slugify($data['nombre']);
        $suffix = 2;
        $baseSlug = $data['slug'];
        while ($this->plan->slugExists($data['slug'], (int)$id)) {
            $data['slug'] = $baseSlug . '-' . $suffix++;
        }

        $data['activo'] = isset($_POST['activo']) ? 1 : 0;
        $data['destacado_home'] = isset($_POST['destacado_home']) ? 1 : 0;
        $data['tiene_badge'] = isset($_POST['tiene_badge']) ? 1 : 0;

        $this->plan->update((int)$id, $data);
        $this->audit($usuario['id'], 'editar', "Plan #{$id}: {$data['nombre']}", (int)$id);

        $this->redirect('/admin/planes', ['success' => 'Plan actualizado correctamente']);
    }

    /** POST /admin/planes/{id}/eliminar */
    public function deletePlan(string $id): void
    {
        $usuario = AuthMiddleware::check($this->db);

        if (($_POST['_csrf'] ?? '') !== csrf_token()) {
            $this->redirect('/admin/planes', ['error' => 'Token CSRF inválido']);
        }

        $plan = $this->plan->getById((int)$id);
        if (!$plan) {
            $this->redirect('/admin/planes', ['error' => 'Plan no encontrado']);
        }

        if ($this->plan->countSuscripciones((int)$id) > 0) {
            $this->redirect('/admin/planes', ['error' => 'No se puede eliminar: el plan tiene suscripciones asociadas']);
        }

        $this->plan->delete((int)$id);
        $this->audit($usuario['id'], 'eliminar', "Plan #{$id}: {$plan['nombre']}", (int)$id);

        $this->redirect('/admin/planes', ['success' => 'Plan eliminado correctamente']);
    }

    /** POST /admin/planes/{id}/toggle */
    public function togglePlan(string $id): void
    {
        $usuario = AuthMiddleware::check($this->db);

        if (($_POST['_csrf'] ?? '') !== csrf_token()) {
            $this->redirect('/admin/planes', ['error' => 'Token CSRF inválido']);
        }

        $plan = $this->plan->getById((int)$id);
        if (!$plan) {
            $this->redirect('/admin/planes', ['error' => 'Plan no encontrado']);
        }

        $this->plan->toggleActivo((int)$id);
        $nuevoEstado = $plan['activo'] ? 'desactivado' : 'activado';
        $this->audit($usuario['id'], 'toggle', "Plan #{$id} {$nuevoEstado}", (int)$id);

        $this->redirect('/admin/planes', ['success' => "Plan {$nuevoEstado}"]);
    }

    // ══════════════════════════════════════════════════
    //  SUSCRIPCIONES
    // ══════════════════════════════════════════════════

    /** GET /admin/suscripciones */
    public function suscripciones(): void
    {
        $usuario = AuthMiddleware::check($this->db);

        $filtros = [
            'estado'   => $_GET['estado'] ?? '',
            'plan_id'  => $_GET['plan_id'] ?? '',
            'ficha_id' => $_GET['ficha_id'] ?? '',
            'q'        => trim($_GET['q'] ?? ''),
        ];

        $pagina = max(1, (int)($_GET['pagina'] ?? 1));
        $total = $this->suscripcion->count($filtros);
        $suscripciones = $this->suscripcion->getAll($filtros, $pagina);
        $totalPaginas = (int)ceil($total / ADMIN_PER_PAGE);
        $stats = $this->suscripcion->statsPorEstado();

        $this->renderAdmin('admin/planes/suscripciones', [
            'pageTitle'     => 'Suscripciones',
            'usuario'       => $usuario,
            'suscripciones' => $suscripciones,
            'planes'        => $this->plan->getAll(),
            'fichas'        => $this->getFichas(),
            'filtros'       => $filtros,
            'pagina'        => $pagina,
            'total'         => $total,
            'totalPaginas'  => $totalPaginas,
            'stats'         => $stats,
            'sidebarCounts' => $this->getSidebarCounts(),
        ]);
    }

    /** GET /admin/suscripciones/crear */
    public function createSuscripcion(): void
    {
        $usuario = AuthMiddleware::check($this->db);

        $this->renderAdmin('admin/planes/suscripcion-form', [
            'pageTitle'     => 'Nueva Suscripción',
            'usuario'       => $usuario,
            'suscripcion'   => null,
            'planes'        => $this->plan->getAll(),
            'fichas'        => $this->getFichas(),
            'sidebarCounts' => $this->getSidebarCounts(),
        ]);
    }

    /** POST /admin/suscripciones/crear */
    public function storeSuscripcion(): void
    {
        $usuario = AuthMiddleware::check($this->db);

        if (($_POST['_csrf'] ?? '') !== csrf_token()) {
            $this->redirect('/admin/suscripciones', ['error' => 'Token CSRF inválido']);
        }

        $data = $this->sanitizeSuscripcion($_POST);
        $errors = $this->validateSuscripcion($data);

        if (!empty($errors)) {
            $this->redirect('/admin/suscripciones/crear', ['error' => implode('. ', $errors)]);
        }

        $id = $this->suscripcion->create($data);
        $this->audit($usuario['id'], 'crear', "Suscripción #{$id}", $id);

        $this->redirect('/admin/suscripciones', ['success' => 'Suscripción creada correctamente']);
    }

    /** GET /admin/suscripciones/{id}/editar */
    public function editSuscripcion(string $id): void
    {
        $usuario = AuthMiddleware::check($this->db);
        $suscripcion = $this->suscripcion->getById((int)$id);

        if (!$suscripcion) {
            $this->redirect('/admin/suscripciones', ['error' => 'Suscripción no encontrada']);
        }

        $this->renderAdmin('admin/planes/suscripcion-form', [
            'pageTitle'     => 'Editar Suscripción #' . $suscripcion['id'],
            'usuario'       => $usuario,
            'suscripcion'   => $suscripcion,
            'planes'        => $this->plan->getAll(),
            'fichas'        => $this->getFichas(),
            'sidebarCounts' => $this->getSidebarCounts(),
        ]);
    }

    /** POST /admin/suscripciones/{id}/editar */
    public function updateSuscripcion(string $id): void
    {
        $usuario = AuthMiddleware::check($this->db);

        if (($_POST['_csrf'] ?? '') !== csrf_token()) {
            $this->redirect('/admin/suscripciones', ['error' => 'Token CSRF inválido']);
        }

        $suscripcion = $this->suscripcion->getById((int)$id);
        if (!$suscripcion) {
            $this->redirect('/admin/suscripciones', ['error' => 'Suscripción no encontrada']);
        }

        $data = $this->sanitizeSuscripcion($_POST);
        $errors = $this->validateSuscripcion($data);

        if (!empty($errors)) {
            $this->redirect("/admin/suscripciones/{$id}/editar", ['error' => implode('. ', $errors)]);
        }

        $this->suscripcion->update((int)$id, $data);
        $this->audit($usuario['id'], 'editar', "Suscripción #{$id}", (int)$id);

        $this->redirect('/admin/suscripciones', ['success' => 'Suscripción actualizada correctamente']);
    }

    /** POST /admin/suscripciones/{id}/eliminar */
    public function deleteSuscripcion(string $id): void
    {
        $usuario = AuthMiddleware::check($this->db);

        if (($_POST['_csrf'] ?? '') !== csrf_token()) {
            $this->redirect('/admin/suscripciones', ['error' => 'Token CSRF inválido']);
        }

        $suscripcion = $this->suscripcion->getById((int)$id);
        if (!$suscripcion) {
            $this->redirect('/admin/suscripciones', ['error' => 'Suscripción no encontrada']);
        }

        $this->suscripcion->delete((int)$id);
        $this->audit($usuario['id'], 'eliminar', "Suscripción #{$id}", (int)$id);

        $this->redirect('/admin/suscripciones', ['success' => 'Suscripción eliminada']);
    }

    // ── Helpers privados ─────────────────────────────────

    private function sanitizePlan(array $post): array
    {
        $caract = trim($post['caracteristicas'] ?? '');
        // Convertir lista de texto a JSON array
        if (!empty($caract)) {
            $lines = array_filter(array_map('trim', explode("\n", $caract)));
            $caract = json_encode(array_values($lines), JSON_UNESCAPED_UNICODE);
        }

        return [
            'nombre'          => trim($post['nombre'] ?? ''),
            'descripcion'     => trim($post['descripcion'] ?? ''),
            'precio_mensual'  => trim($post['precio_mensual'] ?? '0'),
            'precio_anual'    => trim($post['precio_anual'] ?? ''),
            'caracteristicas' => $caract,
            'max_imagenes'    => (int)($post['max_imagenes'] ?? 5),
            'orden'           => (int)($post['orden'] ?? 0),
        ];
    }

    private function validatePlan(array $data): array
    {
        $errors = [];
        if (empty($data['nombre'])) {
            $errors[] = 'El nombre es obligatorio';
        }
        if (!is_numeric($data['precio_mensual']) || (int)$data['precio_mensual'] < 0) {
            $errors[] = 'El precio mensual debe ser un número positivo';
        }
        return $errors;
    }

    private function sanitizeSuscripcion(array $post): array
    {
        return [
            'ficha_id'     => $post['ficha_id'] ?? '',
            'plan_id'      => $post['plan_id'] ?? '',
            'fecha_inicio' => trim($post['fecha_inicio'] ?? ''),
            'fecha_fin'    => trim($post['fecha_fin'] ?? ''),
            'monto'        => trim($post['monto'] ?? '0'),
            'estado'       => $post['estado'] ?? 'pendiente',
            'notas'        => trim($post['notas'] ?? ''),
        ];
    }

    private function validateSuscripcion(array $data): array
    {
        $errors = [];
        if (empty($data['ficha_id'])) {
            $errors[] = 'Debes seleccionar una ficha';
        }
        if (empty($data['plan_id'])) {
            $errors[] = 'Debes seleccionar un plan';
        }
        if (empty($data['fecha_inicio'])) {
            $errors[] = 'La fecha de inicio es obligatoria';
        }
        if (empty($data['fecha_fin'])) {
            $errors[] = 'La fecha de fin es obligatoria';
        }
        if (!empty($data['fecha_inicio']) && !empty($data['fecha_fin']) && $data['fecha_fin'] < $data['fecha_inicio']) {
            $errors[] = 'La fecha de fin no puede ser anterior a la de inicio';
        }
        $estadosValidos = ['activa', 'expirada', 'cancelada', 'pendiente'];
        if (!in_array($data['estado'], $estadosValidos)) {
            $errors[] = 'Estado inválido';
        }
        return $errors;
    }

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

    private function audit(int $usuarioId, string $accion, string $detalle, ?int $registroId = null): void
    {
        $stmt = $this->db->prepare(
            "INSERT INTO audit_log (usuario_id, accion, modulo, registro_id, registro_tipo, datos_despues, ip, user_agent)
             VALUES (?, ?, 'planes', ?, 'plan', ?, ?, ?)"
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
