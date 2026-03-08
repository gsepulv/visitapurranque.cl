<?php

require_once BASE_PATH . '/app/middleware/AuthMiddleware.php';

class AdminLogController extends Controller
{
    /** GET /admin/logs */
    public function index(): void
    {
        $usuario = AuthMiddleware::check($this->db);

        // Filtros
        $modulo  = trim($_GET['modulo'] ?? '');
        $accion  = trim($_GET['accion'] ?? '');
        $desde   = trim($_GET['desde'] ?? '');
        $hasta   = trim($_GET['hasta'] ?? '');
        $page    = max(1, (int)($_GET['page'] ?? 1));
        $perPage = 50;
        $offset  = ($page - 1) * $perPage;

        // Construir query
        $where  = [];
        $params = [];

        if ($modulo !== '') {
            $where[]  = 'a.modulo = ?';
            $params[] = $modulo;
        }
        if ($accion !== '') {
            $where[]  = 'a.accion = ?';
            $params[] = $accion;
        }
        if ($desde !== '') {
            $where[]  = 'a.created_at >= ?';
            $params[] = $desde . ' 00:00:00';
        }
        if ($hasta !== '') {
            $where[]  = 'a.created_at <= ?';
            $params[] = $hasta . ' 23:59:59';
        }

        $whereSQL = $where ? 'WHERE ' . implode(' AND ', $where) : '';

        // Total para paginación
        $stmtCount = $this->db->prepare("SELECT COUNT(*) FROM audit_log a {$whereSQL}");
        $stmtCount->execute($params);
        $total = (int)$stmtCount->fetchColumn();
        $totalPages = max(1, (int)ceil($total / $perPage));

        // Logs con nombre de usuario
        $stmt = $this->db->prepare(
            "SELECT a.*, u.nombre AS usuario_nombre
             FROM audit_log a
             LEFT JOIN usuarios u ON u.id = a.usuario_id
             {$whereSQL}
             ORDER BY a.created_at DESC
             LIMIT {$perPage} OFFSET {$offset}"
        );
        $stmt->execute($params);
        $logs = $stmt->fetchAll();

        // Módulos y acciones para filtros
        $modulos  = $this->db->query("SELECT DISTINCT modulo FROM audit_log ORDER BY modulo")->fetchAll(\PDO::FETCH_COLUMN);
        $acciones = $this->db->query("SELECT DISTINCT accion FROM audit_log ORDER BY accion")->fetchAll(\PDO::FETCH_COLUMN);

        $this->renderAdmin('admin/logs/index', [
            'pageTitle'     => 'Registro de Actividad',
            'usuario'       => $usuario,
            'sidebarCounts' => $this->getSidebarCounts(),
            'logs'          => $logs,
            'modulos'       => $modulos,
            'acciones'      => $acciones,
            'filtroModulo'  => $modulo,
            'filtroAccion'  => $accion,
            'filtroDesde'   => $desde,
            'filtroHasta'   => $hasta,
            'page'          => $page,
            'totalPages'    => $totalPages,
            'total'         => $total,
        ]);
    }

    /** GET /admin/logs/salud */
    public function salud(): void
    {
        $usuario = AuthMiddleware::check($this->db);

        // Tamaño de la base de datos
        $dbSize = $this->db->query(
            "SELECT ROUND(SUM(data_length + index_length) / 1024 / 1024, 2) AS size_mb
             FROM information_schema.tables WHERE table_schema = DATABASE()"
        )->fetchColumn();

        // Tablas con más registros
        $tablas = $this->db->query(
            "SELECT table_name, table_rows, ROUND((data_length + index_length) / 1024 / 1024, 2) AS size_mb
             FROM information_schema.tables WHERE table_schema = DATABASE()
             ORDER BY table_rows DESC LIMIT 15"
        )->fetchAll();

        // Últimas 10 acciones
        $ultimasAcciones = $this->db->query(
            "SELECT a.*, u.nombre AS usuario_nombre
             FROM audit_log a LEFT JOIN usuarios u ON u.id = a.usuario_id
             ORDER BY a.created_at DESC LIMIT 10"
        )->fetchAll();

        // Conteos generales
        $stats = [
            'fichas'     => (int)$this->db->query("SELECT COUNT(*) FROM fichas WHERE eliminado = 0")->fetchColumn(),
            'eventos'    => (int)$this->db->query("SELECT COUNT(*) FROM eventos WHERE eliminado = 0")->fetchColumn(),
            'blog_posts' => (int)$this->db->query("SELECT COUNT(*) FROM blog_posts WHERE estado != 'borrador'")->fetchColumn(),
            'usuarios'   => (int)$this->db->query("SELECT COUNT(*) FROM usuarios")->fetchColumn(),
            'resenas'    => (int)$this->db->query("SELECT COUNT(*) FROM resenas")->fetchColumn(),
            'audit_logs' => (int)$this->db->query("SELECT COUNT(*) FROM audit_log")->fetchColumn(),
        ];

        $this->renderAdmin('admin/logs/salud', [
            'pageTitle'       => 'Salud del Sistema',
            'usuario'         => $usuario,
            'sidebarCounts'   => $this->getSidebarCounts(),
            'dbSize'          => $dbSize,
            'tablas'          => $tablas,
            'ultimasAcciones' => $ultimasAcciones,
            'stats'           => $stats,
            'phpVersion'      => PHP_VERSION,
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
