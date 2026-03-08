<?php
/**
 * BackupService — visitapurranque.cl
 * Crea backups de la BD, sube a Google Drive, limpia locales antiguos.
 */
class BackupService
{
    private \PDO $pdo;
    private array $config;

    public function __construct(\PDO $pdo)
    {
        $this->pdo = $pdo;
        $this->config = [
            'client_id'     => getenv('GOOGLE_CLIENT_ID') ?: '',
            'client_secret' => getenv('GOOGLE_CLIENT_SECRET') ?: '',
            'refresh_token' => getenv('GOOGLE_REFRESH_TOKEN') ?: '',
            'folder_id'     => getenv('GOOGLE_DRIVE_FOLDER_ID') ?: '',
        ];
    }

    /** Crear backup SQL de la BD y comprimir con gzip */
    public function crearBackupBD(): string
    {
        $fecha   = date('Y-m-d_H-i-s');
        $archivo = "backup_visitapurranque_{$fecha}.sql";
        $dir     = BASE_PATH . '/storage/backups';
        $ruta    = $dir . '/' . $archivo;

        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        // Leer credenciales de database.php (las mismas que usa la app)
        $dbConfig = [];
        // Extraer config re-leyendo el archivo
        $configFile = BASE_PATH . '/app/config/database.php';
        if (!file_exists($configFile)) {
            throw new \Exception("Archivo de configuración de BD no encontrado.");
        }

        // Parsear las credenciales del archivo
        $content = file_get_contents($configFile);
        preg_match("/'host'\s*=>\s*'([^']*)'/", $content, $m);
        $host = $m[1] ?? 'localhost';
        preg_match("/'database'\s*=>\s*'([^']*)'/", $content, $m);
        $database = $m[1] ?? '';
        preg_match("/'username'\s*=>\s*'([^']*)'/", $content, $m);
        $username = $m[1] ?? 'root';
        preg_match("/'password'\s*=>\s*'([^']*)'/", $content, $m);
        $password = $m[1] ?? '';

        // Generar dump sin exec() — usar PDO para exportar tablas
        $dump = $this->generarDump($database);
        file_put_contents($ruta, $dump);

        // Comprimir con gzip
        $rutaGz = $ruta . '.gz';
        $gz = gzopen($rutaGz, 'w9');
        gzwrite($gz, $dump);
        gzclose($gz);
        unlink($ruta);

        return $rutaGz;
    }

    /** Generar dump SQL usando PDO (sin exec/mysqldump) */
    private function generarDump(string $database): string
    {
        $dump = "-- Backup visitapurranque.cl\n";
        $dump .= "-- Fecha: " . date('Y-m-d H:i:s') . "\n";
        $dump .= "-- Base de datos: {$database}\n\n";
        $dump .= "SET NAMES utf8mb4;\nSET FOREIGN_KEY_CHECKS = 0;\n\n";

        $tables = $this->pdo->query("SHOW TABLES")->fetchAll(\PDO::FETCH_COLUMN);

        foreach ($tables as $table) {
            // CREATE TABLE
            $create = $this->pdo->query("SHOW CREATE TABLE `{$table}`")->fetch();
            $dump .= "DROP TABLE IF EXISTS `{$table}`;\n";
            $dump .= $create['Create Table'] . ";\n\n";

            // INSERT datos
            $rows = $this->pdo->query("SELECT * FROM `{$table}`")->fetchAll();
            if (!empty($rows)) {
                $columns = array_keys($rows[0]);
                $colList = '`' . implode('`, `', $columns) . '`';

                foreach (array_chunk($rows, 100) as $chunk) {
                    $dump .= "INSERT INTO `{$table}` ({$colList}) VALUES\n";
                    $values = [];
                    foreach ($chunk as $row) {
                        $vals = [];
                        foreach ($row as $val) {
                            if ($val === null) {
                                $vals[] = 'NULL';
                            } else {
                                $vals[] = $this->pdo->quote($val);
                            }
                        }
                        $values[] = '(' . implode(', ', $vals) . ')';
                    }
                    $dump .= implode(",\n", $values) . ";\n";
                }
                $dump .= "\n";
            }
        }

        $dump .= "SET FOREIGN_KEY_CHECKS = 1;\n";
        return $dump;
    }

    /** Subir archivo a Google Drive */
    public function subirADrive(string $rutaArchivo): array
    {
        if (empty($this->config['refresh_token'])) {
            throw new \Exception("Google Drive no configurado. Falta refresh_token.");
        }

        $accessToken = $this->refreshAccessToken();

        $nombre   = basename($rutaArchivo);
        $metadata = json_encode([
            'name'    => $nombre,
            'parents' => [$this->config['folder_id']],
        ]);

        $boundary = 'backup_boundary_' . uniqid();
        $body = "--{$boundary}\r\n"
            . "Content-Type: application/json; charset=UTF-8\r\n\r\n"
            . $metadata . "\r\n"
            . "--{$boundary}\r\n"
            . "Content-Type: application/gzip\r\n\r\n"
            . file_get_contents($rutaArchivo) . "\r\n"
            . "--{$boundary}--";

        $ch = curl_init('https://www.googleapis.com/upload/drive/v3/files?uploadType=multipart');
        curl_setopt_array($ch, [
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => $body,
            CURLOPT_HTTPHEADER     => [
                "Authorization: Bearer {$accessToken}",
                "Content-Type: multipart/related; boundary={$boundary}",
            ],
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT        => 120,
        ]);
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode !== 200) {
            throw new \Exception("Error al subir a Drive: HTTP {$httpCode} — {$response}");
        }

        return json_decode($response, true);
    }

    /** Verificar si Google Drive está configurado */
    public function isDriveConfigurado(): bool
    {
        return !empty($this->config['refresh_token'])
            && !empty($this->config['client_id'])
            && !empty($this->config['client_secret']);
    }

    /** Limpiar backups locales antiguos */
    public function limpiarLocales(int $mantener = 7): int
    {
        $dir = BASE_PATH . '/storage/backups/';
        $archivos = glob($dir . 'backup_*.gz');
        if (!$archivos) return 0;
        rsort($archivos);
        $eliminados = 0;
        foreach (array_slice($archivos, $mantener) as $viejo) {
            unlink($viejo);
            $eliminados++;
        }
        return $eliminados;
    }

    /** Listar backups locales */
    public function listarLocales(): array
    {
        $dir = BASE_PATH . '/storage/backups/';
        $archivos = glob($dir . 'backup_*.gz');
        if (!$archivos) return [];
        rsort($archivos);

        $lista = [];
        foreach ($archivos as $archivo) {
            $lista[] = [
                'nombre'  => basename($archivo),
                'ruta'    => $archivo,
                'tamano'  => filesize($archivo),
                'fecha'   => date('Y-m-d H:i:s', filemtime($archivo)),
            ];
        }
        return $lista;
    }

    private function refreshAccessToken(): string
    {
        $ch = curl_init('https://oauth2.googleapis.com/token');
        curl_setopt_array($ch, [
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => http_build_query([
                'client_id'     => $this->config['client_id'],
                'client_secret' => $this->config['client_secret'],
                'refresh_token' => $this->config['refresh_token'],
                'grant_type'    => 'refresh_token',
            ]),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT        => 30,
        ]);
        $response = json_decode(curl_exec($ch), true);
        curl_close($ch);

        if (empty($response['access_token'])) {
            throw new \Exception("No se pudo obtener access token de Google: " . json_encode($response));
        }

        return $response['access_token'];
    }
}
