<?php
/**
 * AdminReporteController — visitapurranque.cl
 * Estadísticas, gráficos y exportación CSV
 */

require_once BASE_PATH . '/app/middleware/AuthMiddleware.php';
require_once BASE_PATH . '/app/models/Reporte.php';

class AdminReporteController extends Controller
{
    private Reporte $reporte;

    public function __construct(PDO $pdo)
    {
        parent::__construct($pdo);
        $this->reporte = new Reporte($pdo);
    }

    /** GET /admin/estadisticas */
    public function index(): void
    {
        $usuario = AuthMiddleware::check($this->db);

        $dias = max(7, min(365, (int)($_GET['dias'] ?? 30)));

        $kpis              = $this->reporte->kpis();
        $visitasPorDia     = $this->reporte->visitasPorDia($dias);
        $topFichas         = $this->reporte->topFichas(10, $dias);
        $resenasPorRating  = $this->reporte->resenasPorRating();
        $resenasPorMes     = $this->reporte->resenasPorMes(12);
        $bannersRendimiento = $this->reporte->bannersRendimiento();
        $mensajesPorMes    = $this->reporte->mensajesPorMes(12);
        $actividadPorModulo = $this->reporte->actividadPorModulo($dias);
        $fichasPorCategoria = $this->reporte->fichasPorCategoria();

        $this->renderAdmin('admin/reportes/index', [
            'pageTitle'          => 'Estadísticas y Reportes',
            'usuario'            => $usuario,
            'dias'               => $dias,
            'kpis'               => $kpis,
            'visitasPorDia'      => $visitasPorDia,
            'topFichas'          => $topFichas,
            'resenasPorRating'   => $resenasPorRating,
            'resenasPorMes'      => $resenasPorMes,
            'bannersRendimiento' => $bannersRendimiento,
            'mensajesPorMes'     => $mensajesPorMes,
            'actividadPorModulo' => $actividadPorModulo,
            'fichasPorCategoria' => $fichasPorCategoria,
            'sidebarCounts'      => $this->getSidebarCounts(),
        ]);
    }

    /** GET /admin/estadisticas/csv */
    public function csv(): void
    {
        $usuario = AuthMiddleware::check($this->db);

        $tipo = $_GET['tipo'] ?? '';
        $dias = max(7, min(365, (int)($_GET['dias'] ?? 30)));

        switch ($tipo) {
            case 'visitas':
                $data = $this->reporte->csvVisitas($dias);
                $filename = "visitas_{$dias}dias_" . date('Y-m-d') . '.csv';
                $headers = ['Fecha', 'Ficha', 'Vistas', 'Clics Tel.', 'Clics WhatsApp', 'Clics Mapa', 'Clics Web', 'Compartidos'];
                break;

            case 'resenas':
                $data = $this->reporte->csvResenas();
                $filename = 'resenas_' . date('Y-m-d') . '.csv';
                $headers = ['ID', 'Nombre', 'Email', 'Rating', 'Tipo Experiencia', 'Estado', 'Ficha', 'Comentario', 'Fecha'];
                break;

            case 'mensajes':
                $data = $this->reporte->csvMensajes();
                $filename = 'mensajes_' . date('Y-m-d') . '.csv';
                $headers = ['ID', 'Nombre', 'Email', 'Teléfono', 'Asunto', 'Mensaje', 'Leído', 'Respondido', 'Fecha'];
                break;

            case 'fichas':
                $data = $this->reporte->csvFichas();
                $filename = 'fichas_' . date('Y-m-d') . '.csv';
                $headers = ['ID', 'Nombre', 'Slug', 'Categoría', 'Dirección', 'Teléfono', 'WhatsApp', 'Activo', 'Creado', 'Total Vistas', 'Total Clics'];
                break;

            default:
                $this->redirect('/admin/estadisticas', ['error' => 'Tipo de reporte no válido']);
                return;
        }

        // Audit
        $this->audit($usuario['id'], 'exportar_csv', 'reportes', "Exportación CSV: {$tipo}");

        // Output CSV
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');

        $output = fopen('php://output', 'w');

        // BOM for Excel UTF-8 compat
        fwrite($output, "\xEF\xBB\xBF");

        fputcsv($output, $headers, ';');
        foreach ($data as $row) {
            fputcsv($output, array_values($row), ';');
        }

        fclose($output);
        exit;
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
