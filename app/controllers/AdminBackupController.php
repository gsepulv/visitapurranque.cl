<?php

require_once BASE_PATH . '/app/middleware/AuthMiddleware.php';
require_once BASE_PATH . '/app/services/BackupService.php';

class AdminBackupController extends Controller
{
    private BackupService $backup;

    public function __construct(\PDO $db)
    {
        parent::__construct($db);
        $this->backup = new BackupService($db);
    }

    /** GET /admin/backups */
    public function index(): void
    {
        $usuario = AuthMiddleware::check($this->db);

        $backups = $this->backup->listarLocales();

        // Último cron exitoso
        $ultimoCron = $this->db->query(
            "SELECT * FROM cron_log WHERE tarea = 'backup' ORDER BY created_at DESC LIMIT 1"
        )->fetch();

        $this->renderAdmin('admin/backups/index', [
            'pageTitle'       => 'Backups',
            'usuario'         => $usuario,
            'sidebarCounts'   => $this->getSidebarCounts(),
            'backups'         => $backups,
            'ultimoCron'      => $ultimoCron,
            'driveConfigurado' => $this->backup->isDriveConfigurado(),
        ]);
    }

    /** POST /admin/backups/crear */
    public function crear(): void
    {
        $usuario = AuthMiddleware::check($this->db);
        if (($_POST['_csrf'] ?? '') !== csrf_token()) {
            $this->redirect('/admin/backups', ['error' => 'Token inválido.']);
        }

        try {
            $archivo = $this->backup->crearBackupBD();
            $tamano  = round(filesize($archivo) / 1024, 1);

            $this->audit($usuario['id'], 'crear', 'backups', "Backup manual: " . basename($archivo) . " ({$tamano} KB)");
            $this->redirect('/admin/backups', ['success' => "Backup creado: " . basename($archivo) . " ({$tamano} KB)"]);
        } catch (\Exception $e) {
            $this->redirect('/admin/backups', ['error' => 'Error al crear backup: ' . $e->getMessage()]);
        }
    }

    /** POST /admin/backups/subir-drive */
    public function subirDrive(): void
    {
        $usuario = AuthMiddleware::check($this->db);
        if (($_POST['_csrf'] ?? '') !== csrf_token()) {
            $this->redirect('/admin/backups', ['error' => 'Token inválido.']);
        }

        $nombre = basename($_POST['archivo'] ?? '');
        $ruta   = BASE_PATH . '/storage/backups/' . $nombre;

        if (!$nombre || !file_exists($ruta)) {
            $this->redirect('/admin/backups', ['error' => 'Archivo no encontrado.']);
        }

        try {
            $resultado = $this->backup->subirADrive($ruta);
            $this->audit($usuario['id'], 'subir', 'backups', "Subido a Drive: {$nombre} (ID: {$resultado['id']})");
            $this->redirect('/admin/backups', ['success' => "Subido a Google Drive correctamente."]);
        } catch (\Exception $e) {
            $this->redirect('/admin/backups', ['error' => 'Error Drive: ' . $e->getMessage()]);
        }
    }

    /** GET /admin/backups/descargar */
    public function descargar(): void
    {
        AuthMiddleware::check($this->db);

        $nombre = basename($_GET['archivo'] ?? '');
        $ruta   = BASE_PATH . '/storage/backups/' . $nombre;

        if (!$nombre || !file_exists($ruta)) {
            $this->redirect('/admin/backups', ['error' => 'Archivo no encontrado.']);
        }

        header('Content-Type: application/gzip');
        header('Content-Disposition: attachment; filename="' . $nombre . '"');
        header('Content-Length: ' . filesize($ruta));
        readfile($ruta);
        exit;
    }

    /** POST /admin/backups/eliminar */
    public function eliminar(): void
    {
        $usuario = AuthMiddleware::check($this->db);
        if (($_POST['_csrf'] ?? '') !== csrf_token()) {
            $this->redirect('/admin/backups', ['error' => 'Token inválido.']);
        }

        $nombre = basename($_POST['archivo'] ?? '');
        $ruta   = BASE_PATH . '/storage/backups/' . $nombre;

        if ($nombre && file_exists($ruta)) {
            unlink($ruta);
            $this->audit($usuario['id'], 'eliminar', 'backups', "Backup eliminado: {$nombre}");
            $this->redirect('/admin/backups', ['success' => 'Backup eliminado.']);
        }

        $this->redirect('/admin/backups', ['error' => 'Archivo no encontrado.']);
    }

    private function getSidebarCounts(): array
    {
        return [
            'fichas' => (int)$this->db->query("SELECT COUNT(*) FROM fichas WHERE activo = 1 AND eliminado = 0")->fetchColumn(),
            'categorias' => (int)$this->db->query("SELECT COUNT(*) FROM categorias WHERE activo = 1")->fetchColumn(),
        ];
    }
}
