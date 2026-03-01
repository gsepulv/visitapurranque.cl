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
