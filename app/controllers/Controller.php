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
}
