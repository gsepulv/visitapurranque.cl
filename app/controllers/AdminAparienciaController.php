<?php
/**
 * AdminAparienciaController — visitapurranque.cl
 * Personalización visual: colores, fuentes, logo, CSS/JS custom
 */

require_once BASE_PATH . '/app/middleware/AuthMiddleware.php';

class AdminAparienciaController extends Controller
{
    /** GET /admin/apariencia */
    public function index(): void
    {
        $usuario = AuthMiddleware::check($this->db);

        $config = $this->getGroupConfig('apariencia');

        $this->renderAdmin('admin/apariencia/index', [
            'pageTitle'     => 'Apariencia',
            'usuario'       => $usuario,
            'config'        => $config,
            'sidebarCounts' => $this->getSidebarCounts(),
        ]);
    }

    /** POST /admin/apariencia/guardar */
    public function guardar(): void
    {
        $usuario = AuthMiddleware::check($this->db);

        if (($_POST['_csrf'] ?? '') !== csrf_token()) {
            $this->redirect('/admin/apariencia', ['error' => 'Token CSRF inválido']);
        }

        $campos = $_POST['config'] ?? [];
        if (!is_array($campos)) {
            $this->redirect('/admin/apariencia', ['error' => 'Datos inválidos']);
        }

        $count = 0;
        foreach ($campos as $clave => $valor) {
            if (str_starts_with($clave, 'apariencia_')) {
                $stmt = $this->db->prepare("UPDATE configuracion SET valor = ? WHERE clave = ?");
                $stmt->execute([trim($valor), $clave]);
                $count++;
            }
        }

        // Handle file uploads (logo, favicon)
        foreach (['apariencia_logo', 'apariencia_favicon'] as $fileKey) {
            if (!empty($_FILES[$fileKey]['tmp_name']) && $_FILES[$fileKey]['error'] === UPLOAD_ERR_OK) {
                $uploaded = $this->handleUpload($fileKey);
                if ($uploaded) {
                    $stmt = $this->db->prepare("UPDATE configuracion SET valor = ? WHERE clave = ?");
                    $stmt->execute([$uploaded, $fileKey]);
                    $count++;
                }
            }
        }

        $this->audit($usuario['id'], 'guardar_apariencia', 'apariencia', "Actualizados {$count} campos de apariencia");

        $this->redirect('/admin/apariencia', ['success' => "Apariencia guardada ({$count} campos)"]);
    }

    // ── Helpers privados ─────────────────────────────────

    private function getGroupConfig(string $grupo): array
    {
        $stmt = $this->db->prepare(
            "SELECT clave, valor, tipo, descripcion FROM configuracion WHERE grupo = ? ORDER BY clave"
        );
        $stmt->execute([$grupo]);
        $rows = $stmt->fetchAll();

        $map = [];
        foreach ($rows as $r) {
            $map[$r['clave']] = $r;
        }
        return $map;
    }

    private function handleUpload(string $key): ?string
    {
        $file = $_FILES[$key] ?? null;
        if (!$file || $file['error'] !== UPLOAD_ERR_OK) return null;

        $allowedTypes = ['image/png', 'image/jpeg', 'image/webp', 'image/svg+xml', 'image/x-icon'];
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);

        if (!in_array($mime, $allowedTypes)) return null;
        if ($file['size'] > 2 * 1024 * 1024) return null;

        $ext = match($mime) {
            'image/png'     => 'png',
            'image/jpeg'    => 'jpg',
            'image/webp'    => 'webp',
            'image/svg+xml' => 'svg',
            'image/x-icon'  => 'ico',
            default         => 'png',
        };

        $filename = $key . '_' . time() . '.' . $ext;
        $destDir = UPLOAD_PATH . '/config';
        if (!is_dir($destDir)) {
            mkdir($destDir, 0755, true);
        }

        $dest = $destDir . '/' . $filename;
        if (move_uploaded_file($file['tmp_name'], $dest)) {
            return '/uploads/config/' . $filename;
        }

        return null;
    }

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
