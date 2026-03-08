<?php
/**
 * Clase base para todos los controllers — visitapurranque.cl
 */
class Controller
{
    protected PDO $db;

    public function __construct(PDO $pdo)
    {
        $this->db = $pdo;
    }

    /**
     * Renderizar vista con layout (header + view + footer)
     */
    protected function render(string $view, array $data = []): void
    {
        // Extraer variables al scope local
        extract($data);

        // Variables comunes disponibles en todas las vistas
        $csrf = csrf_token();
        $flash = $_SESSION['flash'] ?? [];
        unset($_SESSION['flash']);

        // Incluir header, vista, footer
        require BASE_PATH . '/app/views/layouts/header.php';
        require BASE_PATH . '/app/views/' . $view . '.php';
        require BASE_PATH . '/app/views/layouts/footer.php';
    }

    /**
     * Renderizar vista con layout admin (sidebar + header + view + footer)
     */
    protected function renderAdmin(string $view, array $data = []): void
    {
        extract($data);

        $csrf = csrf_token();
        $flash = $_SESSION['flash'] ?? [];
        unset($_SESSION['flash']);

        require BASE_PATH . '/app/views/layouts/admin-header.php';
        require BASE_PATH . '/app/views/layouts/admin-sidebar.php';
        require BASE_PATH . '/app/views/' . $view . '.php';
        require BASE_PATH . '/app/views/layouts/admin-footer.php';
    }

    /**
     * Renderizar vista standalone (sin layout)
     */
    protected function renderStandalone(string $view, array $data = []): void
    {
        extract($data);

        $csrf = csrf_token();
        $flash = $_SESSION['flash'] ?? [];
        unset($_SESSION['flash']);

        require BASE_PATH . '/app/views/' . $view . '.php';
    }

    /**
     * Redireccionar con flash messages opcionales
     */
    protected function redirect(string $path, array $flash = []): void
    {
        if (!empty($flash)) {
            $_SESSION['flash'] = $flash;
        }
        header('Location: ' . url($path));
        exit;
    }

    /**
     * Responder JSON
     */
    protected function json(array $data, int $status = 200): void
    {
        http_response_code($status);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        exit;
    }

    /**
     * Verificar si el usuario actual tiene un permiso
     */
    protected function tienePermiso(string $slug): bool
    {
        $rolId = $_SESSION['usuario_rol_id'] ?? null;
        if (!$rolId) {
            // Cargar rol del usuario
            $stmt = $this->db->prepare("SELECT rol_id FROM usuarios WHERE id = ?");
            $stmt->execute([$_SESSION['usuario_id'] ?? 0]);
            $rolId = (int)$stmt->fetchColumn();
            $_SESSION['usuario_rol_id'] = $rolId;
        }

        // Admin (rol_id=1) siempre tiene todo
        if ((int)$rolId === 1) return true;

        $stmt = $this->db->prepare(
            "SELECT COUNT(*) FROM rol_permisos rp
             JOIN permisos p ON p.id = rp.permiso_id
             WHERE rp.rol_id = ? AND p.slug = ?"
        );
        $stmt->execute([$rolId, $slug]);
        return (int)$stmt->fetchColumn() > 0;
    }

    /**
     * Registrar acción en audit_log
     */
    protected function audit(
        int     $usuarioId,
        string  $accion,
        string  $modulo,
        string  $detalle,
        ?int    $registroId   = null,
        ?string $registroTipo = null
    ): void {
        $stmt = $this->db->prepare(
            "INSERT INTO audit_log (usuario_id, accion, modulo, registro_id, registro_tipo, datos_despues, ip, user_agent)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?)"
        );
        $stmt->execute([
            $usuarioId,
            $accion,
            $modulo,
            $registroId,
            $registroTipo,
            json_encode(['detalle' => $detalle], JSON_UNESCAPED_UNICODE),
            $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0',
            $_SERVER['HTTP_USER_AGENT'] ?? '',
        ]);
    }
}
