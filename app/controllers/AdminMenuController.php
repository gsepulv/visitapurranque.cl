<?php
/**
 * AdminMenuController — visitapurranque.cl
 * CRUD + drag & drop reorder para ítems de menú
 */

require_once BASE_PATH . '/app/middleware/AuthMiddleware.php';
require_once BASE_PATH . '/app/models/MenuItem.php';

class AdminMenuController extends Controller
{
    private MenuItem $menuItem;

    public function __construct(PDO $pdo)
    {
        parent::__construct($pdo);
        $this->menuItem = new MenuItem($pdo);
    }

    /** GET /admin/menu */
    public function index(): void
    {
        $usuario = AuthMiddleware::check($this->db);

        $tab = $_GET['tab'] ?? 'principal';
        if (!in_array($tab, ['principal', 'footer_legal'])) {
            $tab = 'principal';
        }

        $itemsPrincipal   = $this->menuItem->getByMenu('principal');
        $itemsFooter      = $this->menuItem->getByMenu('footer_legal');

        $this->renderAdmin('admin/menu/index', [
            'pageTitle'        => 'Editor de Menú',
            'usuario'          => $usuario,
            'tab'              => $tab,
            'itemsPrincipal'   => $this->buildTree($itemsPrincipal),
            'itemsFooter'      => $this->buildTree($itemsFooter),
            'countPrincipal'   => count($itemsPrincipal),
            'countFooter'      => count($itemsFooter),
            'sidebarCounts'    => $this->getSidebarCounts(),
        ]);
    }

    /** GET /admin/menu/crear */
    public function create(): void
    {
        $usuario = AuthMiddleware::check($this->db);

        $menu = $_GET['menu'] ?? 'principal';

        $this->renderAdmin('admin/menu/form', [
            'pageTitle'    => 'Nuevo ítem de menú',
            'usuario'      => $usuario,
            'item'         => null,
            'menu'         => $menu,
            'categorias'   => $this->menuItem->getCategoriasActivas(),
            'paginas'      => $this->menuItem->getPaginasActivas(),
            'padres'       => $this->menuItem->getItemsRaiz($menu),
            'sidebarCounts' => $this->getSidebarCounts(),
        ]);
    }

    /** POST /admin/menu/crear */
    public function store(): void
    {
        $usuario = AuthMiddleware::check($this->db);

        if (($_POST['_csrf'] ?? '') !== csrf_token()) {
            $this->redirect('/admin/menu', ['error' => 'Token CSRF inválido']);
        }

        $data = $this->sanitizeInput($_POST);
        $errors = $this->validate($data);

        if (!empty($errors)) {
            $this->redirect('/admin/menu/crear?menu=' . urlencode($data['menu']), [
                'error' => implode('. ', $errors),
            ]);
        }

        $this->resolveUrl($data);

        $id = $this->menuItem->crear($data);
        $this->audit($usuario['id'], 'crear', 'menu_items', "Ítem de menú #{$id}: {$data['titulo']}");

        $this->redirect('/admin/menu?tab=' . urlencode($data['menu']), [
            'success' => 'Ítem de menú creado correctamente',
        ]);
    }

    /** GET /admin/menu/{id}/editar */
    public function edit(string $id): void
    {
        $usuario = AuthMiddleware::check($this->db);
        $item = $this->menuItem->getById((int)$id);

        if (!$item) {
            $this->redirect('/admin/menu', ['error' => 'Ítem no encontrado']);
        }

        $this->renderAdmin('admin/menu/form', [
            'pageTitle'    => 'Editar: ' . $item['titulo'],
            'usuario'      => $usuario,
            'item'         => $item,
            'menu'         => $item['menu'],
            'categorias'   => $this->menuItem->getCategoriasActivas(),
            'paginas'      => $this->menuItem->getPaginasActivas(),
            'padres'       => $this->menuItem->getItemsRaiz($item['menu'], (int)$id),
            'sidebarCounts' => $this->getSidebarCounts(),
        ]);
    }

    /** POST /admin/menu/{id}/editar */
    public function update(string $id): void
    {
        $usuario = AuthMiddleware::check($this->db);

        if (($_POST['_csrf'] ?? '') !== csrf_token()) {
            $this->redirect('/admin/menu', ['error' => 'Token CSRF inválido']);
        }

        $item = $this->menuItem->getById((int)$id);
        if (!$item) {
            $this->redirect('/admin/menu', ['error' => 'Ítem no encontrado']);
        }

        $data = $this->sanitizeInput($_POST);
        $errors = $this->validate($data);

        if (!empty($errors)) {
            $this->redirect("/admin/menu/{$id}/editar", [
                'error' => implode('. ', $errors),
            ]);
        }

        $this->resolveUrl($data);

        $this->menuItem->actualizar((int)$id, $data);
        $this->audit($usuario['id'], 'editar', 'menu_items', "Ítem de menú #{$id}: {$data['titulo']}");

        $this->redirect('/admin/menu?tab=' . urlencode($data['menu']), [
            'success' => 'Ítem de menú actualizado correctamente',
        ]);
    }

    /** POST /admin/menu/{id}/eliminar */
    public function delete(string $id): void
    {
        $usuario = AuthMiddleware::check($this->db);

        if (($_POST['_csrf'] ?? '') !== csrf_token()) {
            $this->redirect('/admin/menu', ['error' => 'Token CSRF inválido']);
        }

        $item = $this->menuItem->getById((int)$id);
        if (!$item) {
            $this->redirect('/admin/menu', ['error' => 'Ítem no encontrado']);
        }

        $tab = $item['menu'];
        $this->menuItem->eliminar((int)$id);
        $this->audit($usuario['id'], 'eliminar', 'menu_items', "Ítem de menú #{$id}: {$item['titulo']}");

        $this->redirect('/admin/menu?tab=' . urlencode($tab), [
            'success' => 'Ítem de menú eliminado correctamente',
        ]);
    }

    /** POST /admin/menu/{id}/toggle */
    public function toggle(string $id): void
    {
        $usuario = AuthMiddleware::check($this->db);

        if (($_POST['_csrf'] ?? '') !== csrf_token()) {
            $this->redirect('/admin/menu', ['error' => 'Token CSRF inválido']);
        }

        $item = $this->menuItem->getById((int)$id);
        if (!$item) {
            $this->redirect('/admin/menu', ['error' => 'Ítem no encontrado']);
        }

        $this->menuItem->toggleActivo((int)$id);
        $nuevoEstado = $item['activo'] ? 'desactivado' : 'activado';
        $this->audit($usuario['id'], 'toggle', 'menu_items', "Ítem #{$id} {$nuevoEstado}");

        $this->redirect('/admin/menu?tab=' . urlencode($item['menu']), [
            'success' => "Ítem {$nuevoEstado} correctamente",
        ]);
    }

    /** POST /admin/menu/reordenar — JSON endpoint */
    public function reorder(): void
    {
        $usuario = AuthMiddleware::check($this->db);

        $input = json_decode(file_get_contents('php://input'), true);

        if (empty($input['csrf']) || $input['csrf'] !== csrf_token()) {
            $this->json(['ok' => false, 'error' => 'Token CSRF inválido'], 403);
        }

        if (empty($input['items']) || !is_array($input['items'])) {
            $this->json(['ok' => false, 'error' => 'Datos inválidos'], 400);
        }

        $this->menuItem->reordenar($input['items']);
        $this->audit($usuario['id'], 'reordenar', 'menu_items', 'Orden actualizado');

        $this->json(['ok' => true]);
    }

    // ── Helpers privados ─────────────────────────────────

    private function sanitizeInput(array $post): array
    {
        return [
            'titulo'       => trim($post['titulo'] ?? ''),
            'tipo'         => in_array($post['tipo'] ?? '', ['enlace', 'categoria', 'pagina', 'externo'])
                              ? $post['tipo'] : 'enlace',
            'url'          => trim($post['url'] ?? ''),
            'referencia_id' => (int)($post['referencia_id'] ?? 0) ?: null,
            'menu'         => in_array($post['menu'] ?? '', ['principal', 'footer_legal'])
                              ? $post['menu'] : 'principal',
            'parent_id'    => (int)($post['parent_id'] ?? 0) ?: null,
            'icono'        => trim($post['icono'] ?? ''),
            'target'       => ($post['target'] ?? '_self') === '_blank' ? '_blank' : '_self',
            'orden'        => (int)($post['orden'] ?? 0),
            'activo'       => isset($post['activo']) ? 1 : 0,
        ];
    }

    private function validate(array $data): array
    {
        $errors = [];
        if (empty($data['titulo'])) {
            $errors[] = 'El título es obligatorio';
        }
        if (mb_strlen($data['titulo']) > 100) {
            $errors[] = 'El título no puede exceder 100 caracteres';
        }
        if ($data['tipo'] === 'externo' && empty($data['url'])) {
            $errors[] = 'La URL es obligatoria para enlaces externos';
        }
        return $errors;
    }

    /**
     * Resolver URL según tipo de enlace
     */
    private function resolveUrl(array &$data): void
    {
        switch ($data['tipo']) {
            case 'categoria':
                if ($data['referencia_id']) {
                    $stmt = $this->db->prepare("SELECT slug FROM categorias WHERE id = ?");
                    $stmt->execute([$data['referencia_id']]);
                    $slug = $stmt->fetchColumn();
                    $data['url'] = $slug ? '/categoria/' . $slug : '/categorias';
                }
                break;
            case 'pagina':
                if ($data['referencia_id']) {
                    $stmt = $this->db->prepare("SELECT slug FROM paginas WHERE id = ?");
                    $stmt->execute([$data['referencia_id']]);
                    $slug = $stmt->fetchColumn();
                    $data['url'] = $slug ? '/' . $slug : '/';
                }
                break;
            case 'enlace':
                // URL se usa tal cual (ruta interna)
                break;
            case 'externo':
                // URL se usa tal cual (URL absoluta)
                break;
        }
    }

    /**
     * Armar árbol jerárquico a partir de lista plana
     */
    private function buildTree(array $items, ?int $parentId = null): array
    {
        $tree = [];
        foreach ($items as $item) {
            $itemParent = $item['parent_id'] ? (int)$item['parent_id'] : null;
            if ($itemParent === $parentId) {
                $item['children'] = $this->buildTree($items, (int)$item['id']);
                $tree[] = $item;
            }
        }
        return $tree;
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
