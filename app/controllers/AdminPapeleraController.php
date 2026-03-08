<?php

require_once BASE_PATH . '/app/middleware/AuthMiddleware.php';

class AdminPapeleraController extends Controller
{
    private const TIPOS = [
        'fichas'     => ['tabla' => 'fichas',     'nombre_col' => 'nombre', 'label' => 'Fichas'],
        'eventos'    => ['tabla' => 'eventos',    'nombre_col' => 'titulo', 'label' => 'Eventos'],
        'blog_posts' => ['tabla' => 'blog_posts', 'nombre_col' => 'titulo', 'label' => 'Blog'],
    ];

    /** GET /admin/papelera */
    public function index(): void
    {
        $usuario = AuthMiddleware::check($this->db);

        $tab = $_GET['tab'] ?? 'fichas';
        if (!isset(self::TIPOS[$tab])) $tab = 'fichas';

        $items = [];
        foreach (self::TIPOS as $key => $cfg) {
            $items[$key] = $this->db->query(
                "SELECT id, {$cfg['nombre_col']} AS nombre, eliminado_at
                 FROM {$cfg['tabla']}
                 WHERE eliminado = 1
                 ORDER BY eliminado_at DESC"
            )->fetchAll();
        }

        $this->renderAdmin('admin/papelera/index', [
            'pageTitle'     => 'Papelera',
            'usuario'       => $usuario,
            'sidebarCounts' => $this->getSidebarCounts(),
            'items'         => $items,
            'tab'           => $tab,
            'tipos'         => self::TIPOS,
        ]);
    }

    /** POST /admin/papelera/restaurar/{tipo}/{id} */
    public function restaurar(string $tipo, int $id): void
    {
        $usuario = AuthMiddleware::check($this->db);
        if (($_POST['_csrf'] ?? '') !== csrf_token()) {
            $this->redirect('/admin/papelera', ['error' => 'Token inválido.']);
        }

        if (!isset(self::TIPOS[$tipo])) {
            $this->redirect('/admin/papelera', ['error' => 'Tipo inválido.']);
        }

        $tabla = self::TIPOS[$tipo]['tabla'];
        $this->db->prepare("UPDATE {$tabla} SET eliminado = 0, eliminado_at = NULL WHERE id = ?")->execute([$id]);

        $this->audit($usuario['id'], 'restaurar', 'papelera', "Restaurado {$tipo} #{$id}", $id, $tipo);
        $this->redirect('/admin/papelera?tab=' . $tipo, ['success' => 'Elemento restaurado.']);
    }

    /** POST /admin/papelera/eliminar/{tipo}/{id} */
    public function eliminarPermanente(string $tipo, int $id): void
    {
        $usuario = AuthMiddleware::check($this->db);
        if (($_POST['_csrf'] ?? '') !== csrf_token()) {
            $this->redirect('/admin/papelera', ['error' => 'Token inválido.']);
        }

        if (!isset(self::TIPOS[$tipo])) {
            $this->redirect('/admin/papelera', ['error' => 'Tipo inválido.']);
        }

        $tabla = self::TIPOS[$tipo]['tabla'];
        $this->db->prepare("DELETE FROM {$tabla} WHERE id = ? AND eliminado = 1")->execute([$id]);

        $this->audit($usuario['id'], 'eliminar_permanente', 'papelera', "Eliminado permanente {$tipo} #{$id}", $id, $tipo);
        $this->redirect('/admin/papelera?tab=' . $tipo, ['success' => 'Eliminado permanentemente.']);
    }

    /** POST /admin/papelera/vaciar */
    public function vaciar(): void
    {
        $usuario = AuthMiddleware::check($this->db);
        if (($_POST['_csrf'] ?? '') !== csrf_token()) {
            $this->redirect('/admin/papelera', ['error' => 'Token inválido.']);
        }

        $total = 0;
        foreach (self::TIPOS as $cfg) {
            $stmt = $this->db->prepare("DELETE FROM {$cfg['tabla']} WHERE eliminado = 1");
            $stmt->execute();
            $total += $stmt->rowCount();
        }

        $this->audit($usuario['id'], 'vaciar', 'papelera', "Papelera vaciada: {$total} elementos");
        $this->redirect('/admin/papelera', ['success' => "Papelera vaciada ({$total} elementos eliminados)."]);
    }

    private function getSidebarCounts(): array
    {
        return [
            'fichas' => (int)$this->db->query("SELECT COUNT(*) FROM fichas WHERE activo = 1 AND eliminado = 0")->fetchColumn(),
            'categorias' => (int)$this->db->query("SELECT COUNT(*) FROM categorias WHERE activo = 1")->fetchColumn(),
        ];
    }
}
