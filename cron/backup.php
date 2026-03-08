<?php
/**
 * Cron: Backup automático de la BD
 * Uso: php cron/backup.php
 * Cron sugerido: 0 3 * * * (diario a las 3am)
 */

define('BASE_PATH', dirname(__DIR__));
require BASE_PATH . '/app/config/app.php';
require BASE_PATH . '/app/config/database.php';
require BASE_PATH . '/app/services/BackupService.php';

$inicio = microtime(true);
$backup = new BackupService($pdo);

try {
    // 1. Crear backup de BD
    $archivo = $backup->crearBackupBD();
    $tamano  = round(filesize($archivo) / 1024, 1);
    echo "Backup creado: " . basename($archivo) . " ({$tamano} KB)\n";

    // 2. Subir a Google Drive (si está configurado)
    $driveMsg = '';
    if ($backup->isDriveConfigurado()) {
        try {
            $resultado = $backup->subirADrive($archivo);
            $driveMsg = " | Drive: {$resultado['id']}";
            echo "Subido a Drive: {$resultado['id']}\n";
        } catch (\Exception $e) {
            $driveMsg = " | Drive error: {$e->getMessage()}";
            echo "Error Drive: {$e->getMessage()}\n";
        }
    } else {
        $driveMsg = ' | Drive: no configurado';
        echo "Google Drive no configurado, solo backup local.\n";
    }

    // 3. Limpiar backups antiguos
    $eliminados = $backup->limpiarLocales(7);
    echo "Limpieza: {$eliminados} backup(s) antiguo(s) eliminado(s)\n";

    // 4. Registrar en cron_log
    $duracion = (int)((microtime(true) - $inicio) * 1000);
    $stmt = $pdo->prepare(
        "INSERT INTO cron_log (tarea, resultado, detalles, duracion_ms) VALUES (?, ?, ?, ?)"
    );
    $stmt->execute([
        'backup',
        'ok',
        basename($archivo) . " ({$tamano} KB){$driveMsg}",
        $duracion,
    ]);

    echo "Completado en {$duracion}ms\n";

} catch (\Exception $e) {
    $duracion = (int)((microtime(true) - $inicio) * 1000);
    echo "Error: {$e->getMessage()}\n";

    $stmt = $pdo->prepare(
        "INSERT INTO cron_log (tarea, resultado, detalles, duracion_ms) VALUES (?, ?, ?, ?)"
    );
    $stmt->execute(['backup', 'error', $e->getMessage(), $duracion]);
    exit(1);
}
