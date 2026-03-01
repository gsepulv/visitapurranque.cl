<?php
/**
 * AdminSeoController — visitapurranque.cl
 * SEO, redes sociales y estadísticas de compartidos
 */

require_once BASE_PATH . '/app/middleware/AuthMiddleware.php';
require_once BASE_PATH . '/app/models/Seo.php';

class AdminSeoController extends Controller
{
    private Seo $seo;

    public function __construct(PDO $pdo)
    {
        parent::__construct($pdo);
        $this->seo = new Seo($pdo);
    }

    /** GET /admin/seo */
    public function index(): void
    {
        $usuario = AuthMiddleware::check($this->db);

        $cobertura = $this->seo->cobertura();
        $configSeo = $this->seo->getConfig('seo');
        $configRedes = $this->seo->getConfig('redes');

        // Registros sin meta tags para acción rápida
        $sinMeta = [];
        foreach (['fichas', 'blog', 'eventos', 'categorias'] as $t) {
            $items = $this->seo->sinMeta($t, 5);
            if (!empty($items)) {
                $sinMeta[$t] = $items;
            }
        }

        $this->renderAdmin('admin/seo/index', [
            'pageTitle'    => 'SEO y Redes Sociales',
            'usuario'      => $usuario,
            'cobertura'    => $cobertura,
            'configSeo'    => $configSeo,
            'configRedes'  => $configRedes,
            'sinMeta'      => $sinMeta,
            'sidebarCounts' => $this->getSidebarCounts(),
        ]);
    }

    /** POST /admin/seo/guardar */
    public function guardar(): void
    {
        $usuario = AuthMiddleware::check($this->db);

        if (($_POST['_csrf'] ?? '') !== csrf_token()) {
            $this->redirect('/admin/seo', ['error' => 'Token CSRF inválido']);
        }

        $campos = $_POST['config'] ?? [];
        if (!is_array($campos)) {
            $this->redirect('/admin/seo', ['error' => 'Datos inválidos']);
        }

        $count = 0;
        foreach ($campos as $clave => $valor) {
            // Solo permitir claves que empiecen con seo_ o social_
            if (preg_match('/^(seo_|social_)\w+$/', $clave)) {
                $this->seo->saveConfig($clave, trim($valor));
                $count++;
            }
        }

        $this->audit($usuario['id'], 'guardar_seo', "Actualizadas {$count} configuraciones SEO/redes");

        $this->redirect('/admin/seo', ['success' => "Configuración guardada ({$count} campos actualizados)"]);
    }

    /** GET /admin/seo/compartidos */
    public function compartidos(): void
    {
        $usuario = AuthMiddleware::check($this->db);

        $dias = max(7, min(365, (int)($_GET['dias'] ?? 30)));

        $porRed = $this->seo->compartidosPorRed($dias);
        $porTipo = $this->seo->compartidosPorTipo($dias);
        $porDia = $this->seo->compartidosPorDia($dias);
        $topCompartidos = $this->seo->topCompartidos($dias);
        $totalCompartidos = $this->seo->totalCompartidos($dias);

        $this->renderAdmin('admin/seo/compartidos', [
            'pageTitle'        => 'Compartidos en Redes',
            'usuario'          => $usuario,
            'dias'             => $dias,
            'porRed'           => $porRed,
            'porTipo'          => $porTipo,
            'porDia'           => $porDia,
            'topCompartidos'   => $topCompartidos,
            'totalCompartidos' => $totalCompartidos,
            'sidebarCounts'    => $this->getSidebarCounts(),
        ]);
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

    private function audit(int $usuarioId, string $accion, string $detalle): void
    {
        $stmt = $this->db->prepare(
            "INSERT INTO audit_log (usuario_id, accion, modulo, datos_despues, ip, user_agent)
             VALUES (?, ?, 'seo', ?, ?, ?)"
        );
        $stmt->execute([
            $usuarioId,
            $accion,
            json_encode(['detalle' => $detalle], JSON_UNESCAPED_UNICODE),
            $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0',
            $_SERVER['HTTP_USER_AGENT'] ?? '',
        ]);
    }
}
