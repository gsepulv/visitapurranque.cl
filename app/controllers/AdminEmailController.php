<?php

require_once BASE_PATH . '/app/middleware/AuthMiddleware.php';

class AdminEmailController extends Controller
{
    /** GET /admin/emails */
    public function index(): void
    {
        $usuario = AuthMiddleware::check($this->db);

        $templates = $this->db->query(
            "SELECT * FROM email_templates ORDER BY nombre ASC"
        )->fetchAll();

        $this->renderAdmin('admin/emails/index', [
            'pageTitle'     => 'Plantillas de Email',
            'usuario'       => $usuario,
            'sidebarCounts' => $this->getSidebarCounts(),
            'templates'     => $templates,
        ]);
    }

    /** GET /admin/emails/crear */
    public function create(): void
    {
        $usuario = AuthMiddleware::check($this->db);

        $this->renderAdmin('admin/emails/form', [
            'pageTitle'     => 'Nueva Plantilla',
            'usuario'       => $usuario,
            'sidebarCounts' => $this->getSidebarCounts(),
            'template'      => null,
        ]);
    }

    /** POST /admin/emails/crear */
    public function store(): void
    {
        $usuario = AuthMiddleware::check($this->db);
        if (($_POST['_csrf'] ?? '') !== csrf_token()) {
            $this->redirect('/admin/emails', ['error' => 'Token inválido.']);
        }

        $nombre    = trim($_POST['nombre'] ?? '');
        $slug      = trim($_POST['slug'] ?? '');
        $asunto    = trim($_POST['asunto'] ?? '');
        $cuerpo    = trim($_POST['cuerpo_html'] ?? '');
        $variables = trim($_POST['variables'] ?? '[]');
        $activo    = isset($_POST['activo']) ? 1 : 0;

        if ($nombre === '' || $slug === '' || $asunto === '' || $cuerpo === '') {
            $this->redirect('/admin/emails/crear', ['error' => 'Todos los campos son obligatorios.']);
        }

        // Validar JSON de variables
        $varsDecoded = json_decode($variables);
        if ($variables !== '[]' && $varsDecoded === null) {
            $this->redirect('/admin/emails/crear', ['error' => 'Las variables deben ser un JSON válido.']);
        }

        try {
            $this->db->prepare(
                "INSERT INTO email_templates (nombre, slug, asunto, cuerpo_html, variables, activo)
                 VALUES (?, ?, ?, ?, ?, ?)"
            )->execute([$nombre, $slug, $asunto, $cuerpo, $variables, $activo]);

            $this->audit($usuario['id'], 'crear', 'email_templates', "Plantilla: {$nombre}");
            $this->redirect('/admin/emails', ['success' => 'Plantilla creada.']);
        } catch (\PDOException $e) {
            $this->redirect('/admin/emails/crear', ['error' => 'El slug ya existe.']);
        }
    }

    /** GET /admin/emails/{id}/editar */
    public function edit(int $id): void
    {
        $usuario = AuthMiddleware::check($this->db);

        $stmt = $this->db->prepare("SELECT * FROM email_templates WHERE id = ?");
        $stmt->execute([$id]);
        $template = $stmt->fetch();

        if (!$template) {
            $this->redirect('/admin/emails', ['error' => 'Plantilla no encontrada.']);
        }

        $this->renderAdmin('admin/emails/form', [
            'pageTitle'     => 'Editar Plantilla',
            'usuario'       => $usuario,
            'sidebarCounts' => $this->getSidebarCounts(),
            'template'      => $template,
        ]);
    }

    /** POST /admin/emails/{id}/editar */
    public function update(int $id): void
    {
        $usuario = AuthMiddleware::check($this->db);
        if (($_POST['_csrf'] ?? '') !== csrf_token()) {
            $this->redirect('/admin/emails', ['error' => 'Token inválido.']);
        }

        $nombre    = trim($_POST['nombre'] ?? '');
        $slug      = trim($_POST['slug'] ?? '');
        $asunto    = trim($_POST['asunto'] ?? '');
        $cuerpo    = trim($_POST['cuerpo_html'] ?? '');
        $variables = trim($_POST['variables'] ?? '[]');
        $activo    = isset($_POST['activo']) ? 1 : 0;

        if ($nombre === '' || $slug === '' || $asunto === '' || $cuerpo === '') {
            $this->redirect("/admin/emails/{$id}/editar", ['error' => 'Todos los campos son obligatorios.']);
        }

        $varsDecoded = json_decode($variables);
        if ($variables !== '[]' && $varsDecoded === null) {
            $this->redirect("/admin/emails/{$id}/editar", ['error' => 'Las variables deben ser un JSON válido.']);
        }

        try {
            $this->db->prepare(
                "UPDATE email_templates SET nombre = ?, slug = ?, asunto = ?, cuerpo_html = ?, variables = ?, activo = ?
                 WHERE id = ?"
            )->execute([$nombre, $slug, $asunto, $cuerpo, $variables, $activo, $id]);

            $this->audit($usuario['id'], 'editar', 'email_templates', "Plantilla: {$nombre}");
            $this->redirect('/admin/emails', ['success' => 'Plantilla actualizada.']);
        } catch (\PDOException $e) {
            $this->redirect("/admin/emails/{$id}/editar", ['error' => 'El slug ya existe.']);
        }
    }

    /** POST /admin/emails/{id}/eliminar */
    public function delete(int $id): void
    {
        $usuario = AuthMiddleware::check($this->db);
        if (($_POST['_csrf'] ?? '') !== csrf_token()) {
            $this->redirect('/admin/emails', ['error' => 'Token inválido.']);
        }

        $this->db->prepare("DELETE FROM email_templates WHERE id = ?")->execute([$id]);
        $this->audit($usuario['id'], 'eliminar', 'email_templates', "Plantilla #{$id} eliminada");
        $this->redirect('/admin/emails', ['success' => 'Plantilla eliminada.']);
    }

    /** GET /admin/emails/{id}/preview */
    public function preview(int $id): void
    {
        $usuario = AuthMiddleware::check($this->db);

        $stmt = $this->db->prepare("SELECT * FROM email_templates WHERE id = ?");
        $stmt->execute([$id]);
        $template = $stmt->fetch();

        if (!$template) {
            $this->redirect('/admin/emails', ['error' => 'Plantilla no encontrada.']);
        }

        // Reemplazar variables con datos de ejemplo
        $html = $template['cuerpo_html'];
        $vars = json_decode($template['variables'] ?? '[]', true) ?: [];
        foreach ($vars as $var) {
            $html = str_replace('{{' . $var . '}}', '<span style="background:#fef3c7;padding:2px 4px;border-radius:3px;">[' . $var . ']</span>', $html);
        }

        // Renderizar standalone
        echo '<!DOCTYPE html><html><head><meta charset="UTF-8"><title>Preview: ' . htmlspecialchars($template['nombre']) . '</title>';
        echo '<style>body{font-family:Arial,sans-serif;margin:20px;background:#f5f5f5;}';
        echo '.preview-bar{background:#1e293b;color:#fff;padding:10px 20px;margin:-20px -20px 20px;font-size:.9rem;}';
        echo '.preview-bar a{color:#93c5fd;margin-left:15px;}';
        echo '.preview-frame{background:#fff;max-width:600px;margin:0 auto;padding:30px;border-radius:8px;box-shadow:0 2px 8px rgba(0,0,0,.1);}</style></head><body>';
        echo '<div class="preview-bar">Vista previa: <strong>' . htmlspecialchars($template['nombre']) . '</strong>';
        echo '<a href="' . url('/admin/emails') . '">← Volver</a></div>';
        echo '<div class="preview-frame">' . $html . '</div>';
        echo '</body></html>';
        exit;
    }

    private function getSidebarCounts(): array
    {
        return [
            'fichas' => (int)$this->db->query("SELECT COUNT(*) FROM fichas WHERE activo = 1 AND eliminado = 0")->fetchColumn(),
            'categorias' => (int)$this->db->query("SELECT COUNT(*) FROM categorias WHERE activo = 1")->fetchColumn(),
        ];
    }
}
