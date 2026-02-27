<?php

require_once BASE_PATH . '/app/middleware/AuthMiddleware.php';

class AdminController extends Controller
{
    /** GET /admin → redirect */
    public function index(): void
    {
        if (!empty($_SESSION['usuario_id'])) {
            $this->redirect('/admin/dashboard');
        } else {
            $this->redirect('/admin/login');
        }
    }

    /** GET /admin/login */
    public function loginForm(): void
    {
        if (!empty($_SESSION['usuario_id'])) {
            $this->redirect('/admin/dashboard');
        }

        $this->renderStandalone('admin/login', [
            'pageTitle' => 'Iniciar sesión — ' . SITE_NAME,
        ]);
    }

    /** POST /admin/login */
    public function login(): void
    {
        $email    = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $ip       = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
        $ua       = $_SERVER['HTTP_USER_AGENT'] ?? '';

        // ── Rate limiting: 5 intentos en 15 min ──
        $stmt = $this->db->prepare(
            "SELECT COUNT(*) FROM login_intentos
             WHERE email = ? AND exitoso = 0
             AND created_at > DATE_SUB(NOW(), INTERVAL 15 MINUTE)"
        );
        $stmt->execute([$email]);
        $intentos = (int)$stmt->fetchColumn();

        if ($intentos >= 5) {
            $this->redirect('/admin/login', [
                'error' => 'Demasiados intentos. Espera 15 minutos.',
            ]);
        }

        // ── Buscar usuario ──
        $stmt = $this->db->prepare(
            "SELECT * FROM usuarios WHERE email = ? AND activo = 1 LIMIT 1"
        );
        $stmt->execute([$email]);
        $usuario = $stmt->fetch();

        // ── Verificar password ──
        if (!$usuario || !password_verify($password, $usuario['password'])) {
            // Registrar intento fallido
            $stmt = $this->db->prepare(
                "INSERT INTO login_intentos (email, ip, user_agent, exitoso) VALUES (?, ?, ?, 0)"
            );
            $stmt->execute([$email, $ip, $ua]);

            $this->redirect('/admin/login', [
                'error' => 'Credenciales incorrectas',
            ]);
        }

        // ── Login exitoso ──
        session_regenerate_id(true);
        $_SESSION['usuario_id'] = $usuario['id'];

        // Registrar intento exitoso
        $stmt = $this->db->prepare(
            "INSERT INTO login_intentos (email, ip, user_agent, exitoso) VALUES (?, ?, ?, 1)"
        );
        $stmt->execute([$email, $ip, $ua]);

        // Actualizar ultimo_login
        $stmt = $this->db->prepare("UPDATE usuarios SET ultimo_login = NOW() WHERE id = ?");
        $stmt->execute([$usuario['id']]);

        // Audit log
        $stmt = $this->db->prepare(
            "INSERT INTO audit_log (usuario_id, accion, modulo, ip, user_agent) VALUES (?, 'login', 'auth', ?, ?)"
        );
        $stmt->execute([$usuario['id'], $ip, $ua]);

        $this->redirect('/admin/dashboard', [
            'success' => 'Bienvenido, ' . $usuario['nombre'],
        ]);
    }

    /** GET /admin/logout */
    public function logout(): void
    {
        $usuarioId = $_SESSION['usuario_id'] ?? null;

        if ($usuarioId) {
            $ip = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
            $ua = $_SERVER['HTTP_USER_AGENT'] ?? '';
            $stmt = $this->db->prepare(
                "INSERT INTO audit_log (usuario_id, accion, modulo, ip, user_agent) VALUES (?, 'logout', 'auth', ?, ?)"
            );
            $stmt->execute([$usuarioId, $ip, $ua]);
        }

        $_SESSION = [];
        if (ini_get('session.use_cookies')) {
            $p = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $p['path'], $p['domain'], $p['secure'], $p['httponly']
            );
        }
        session_destroy();

        // Re-start session for flash
        session_start();
        $this->redirect('/admin/login', [
            'success' => 'Sesión cerrada correctamente',
        ]);
    }

    /** GET /admin/dashboard */
    public function dashboard(): void
    {
        $usuario = AuthMiddleware::check($this->db);

        // ── KPIs ──
        $kpis = [];

        $kpis['fichas'] = (int)$this->db->query(
            "SELECT COUNT(*) FROM fichas WHERE activo = 1 AND eliminado = 0"
        )->fetchColumn();

        $kpis['eventos'] = (int)$this->db->query(
            "SELECT COUNT(*) FROM eventos WHERE fecha_inicio > NOW() AND activo = 1 AND eliminado = 0"
        )->fetchColumn();

        $kpis['resenas'] = (int)$this->db->query(
            "SELECT COUNT(*) FROM resenas WHERE estado = 'pendiente'"
        )->fetchColumn();

        $kpis['mensajes'] = (int)$this->db->query(
            "SELECT COUNT(*) FROM contacto_mensajes WHERE leido = 0"
        )->fetchColumn();

        $kpis['visitas'] = (int)$this->db->query(
            "SELECT COALESCE(SUM(vistas), 0) FROM estadisticas WHERE fecha = CURDATE()"
        )->fetchColumn();

        $kpis['posts'] = (int)$this->db->query(
            "SELECT COUNT(*) FROM blog_posts WHERE estado = 'publicado' AND eliminado = 0"
        )->fetchColumn();

        // ── Actividad reciente ──
        $actividad = $this->db->query(
            "SELECT a.*, u.nombre AS usuario_nombre
             FROM audit_log a
             LEFT JOIN usuarios u ON u.id = a.usuario_id
             ORDER BY a.created_at DESC
             LIMIT 10"
        )->fetchAll();

        // ── Contadores para sidebar ──
        $sidebarCounts = [
            'fichas'   => $kpis['fichas'],
            'resenas'  => $kpis['resenas'],
            'mensajes' => $kpis['mensajes'],
        ];

        $this->renderAdmin('admin/dashboard', [
            'pageTitle'     => 'Dashboard — Admin',
            'usuario'       => $usuario,
            'kpis'          => $kpis,
            'actividad'     => $actividad,
            'sidebarCounts' => $sidebarCounts,
        ]);
    }
}
