<?php

require_once BASE_PATH . '/app/middleware/AuthMiddleware.php';

class AdminRolController extends Controller
{
    /** GET /admin/roles */
    public function index(): void
    {
        $usuario = AuthMiddleware::check($this->db);

        $roles = $this->db->query(
            "SELECT r.*,
                    (SELECT COUNT(*) FROM rol_permisos WHERE rol_id = r.id) AS total_permisos,
                    (SELECT COUNT(*) FROM usuarios WHERE rol_id = r.id) AS total_usuarios
             FROM roles r ORDER BY r.id"
        )->fetchAll();

        $this->renderAdmin('admin/roles/index', [
            'pageTitle'     => 'Roles y Permisos',
            'usuario'       => $usuario,
            'sidebarCounts' => $this->getSidebarCounts(),
            'roles'         => $roles,
        ]);
    }

    /** GET /admin/roles/crear */
    public function create(): void
    {
        $usuario = AuthMiddleware::check($this->db);
        $permisoModel = new Permiso($this->db);

        $this->renderAdmin('admin/roles/form', [
            'pageTitle'       => 'Nuevo Rol',
            'usuario'         => $usuario,
            'sidebarCounts'   => $this->getSidebarCounts(),
            'rol'             => null,
            'permisosGrouped' => $permisoModel->getAllGrouped(),
            'asignados'       => [],
        ]);
    }

    /** POST /admin/roles/crear */
    public function store(): void
    {
        $usuario = AuthMiddleware::check($this->db);

        if (($_POST['_csrf'] ?? '') !== csrf_token()) {
            $this->redirect('/admin/roles', ['error' => 'Token inválido.']);
        }

        $nombre = trim($_POST['nombre'] ?? '');
        $descripcion = trim($_POST['descripcion'] ?? '');

        if ($nombre === '') {
            $this->redirect('/admin/roles/crear', ['error' => 'El nombre es obligatorio.']);
        }

        $slug = preg_replace('/[^a-z0-9\-]/', '', strtolower(str_replace(' ', '-', $nombre)));

        $stmt = $this->db->prepare(
            "INSERT INTO roles (nombre, slug, descripcion) VALUES (?, ?, ?)"
        );
        $stmt->execute([$nombre, $slug, $descripcion]);
        $rolId = (int)$this->db->lastInsertId();

        $permisoIds = array_map('intval', $_POST['permisos'] ?? []);
        $permisoModel = new Permiso($this->db);
        $permisoModel->asignarARol($rolId, $permisoIds);

        $this->audit($usuario['id'], 'crear', 'roles', 'Rol creado: ' . $nombre, $rolId, 'rol');

        $this->redirect('/admin/roles', ['success' => 'Rol creado correctamente.']);
    }

    /** GET /admin/roles/{id}/editar */
    public function edit(int $id): void
    {
        $usuario = AuthMiddleware::check($this->db);
        $permisoModel = new Permiso($this->db);

        $stmt = $this->db->prepare("SELECT * FROM roles WHERE id = ?");
        $stmt->execute([$id]);
        $rol = $stmt->fetch();

        if (!$rol) {
            $this->redirect('/admin/roles', ['error' => 'Rol no encontrado.']);
        }

        $this->renderAdmin('admin/roles/form', [
            'pageTitle'       => 'Editar: ' . $rol['nombre'],
            'usuario'         => $usuario,
            'sidebarCounts'   => $this->getSidebarCounts(),
            'rol'             => $rol,
            'permisosGrouped' => $permisoModel->getAllGrouped(),
            'asignados'       => $permisoModel->getIdsByRol($id),
        ]);
    }

    /** POST /admin/roles/{id}/editar */
    public function update(int $id): void
    {
        $usuario = AuthMiddleware::check($this->db);

        if (($_POST['_csrf'] ?? '') !== csrf_token()) {
            $this->redirect('/admin/roles', ['error' => 'Token inválido.']);
        }

        $stmt = $this->db->prepare("SELECT * FROM roles WHERE id = ?");
        $stmt->execute([$id]);
        $rol = $stmt->fetch();

        if (!$rol) {
            $this->redirect('/admin/roles', ['error' => 'Rol no encontrado.']);
        }

        $nombre = trim($_POST['nombre'] ?? '');
        $descripcion = trim($_POST['descripcion'] ?? '');

        if ($nombre === '') {
            $this->redirect("/admin/roles/{$id}/editar", ['error' => 'El nombre es obligatorio.']);
        }

        $this->db->prepare("UPDATE roles SET nombre = ?, descripcion = ? WHERE id = ?")
            ->execute([$nombre, $descripcion, $id]);

        $permisoIds = array_map('intval', $_POST['permisos'] ?? []);

        // Admin siempre conserva todos los permisos
        if ($rol['slug'] === 'admin') {
            $permisoIds = array_column(
                $this->db->query("SELECT id FROM permisos")->fetchAll(),
                'id'
            );
        }

        $permisoModel = new Permiso($this->db);
        $permisoModel->asignarARol($id, $permisoIds);

        $this->audit($usuario['id'], 'editar', 'roles', 'Rol editado: ' . $nombre, $id, 'rol');

        $this->redirect('/admin/roles', ['success' => 'Rol actualizado.']);
    }

    /** POST /admin/roles/{id}/eliminar */
    public function delete(int $id): void
    {
        $usuario = AuthMiddleware::check($this->db);

        if (($_POST['_csrf'] ?? '') !== csrf_token()) {
            $this->redirect('/admin/roles', ['error' => 'Token inválido.']);
        }

        $stmt = $this->db->prepare("SELECT * FROM roles WHERE id = ?");
        $stmt->execute([$id]);
        $rol = $stmt->fetch();

        if (!$rol || $rol['slug'] === 'admin') {
            $this->redirect('/admin/roles', ['error' => 'No se puede eliminar este rol.']);
        }

        // Verificar si tiene usuarios
        $count = (int)$this->db->prepare("SELECT COUNT(*) FROM usuarios WHERE rol_id = ?")
            ->execute([$id]) ? $this->db->query("SELECT COUNT(*) FROM usuarios WHERE rol_id = $id")->fetchColumn() : 0;

        if ($count > 0) {
            $this->redirect('/admin/roles', ['error' => "No se puede eliminar: hay {$count} usuario(s) con este rol."]);
        }

        $this->db->prepare("DELETE FROM rol_permisos WHERE rol_id = ?")->execute([$id]);
        $this->db->prepare("DELETE FROM roles WHERE id = ?")->execute([$id]);

        $this->audit($usuario['id'], 'eliminar', 'roles', 'Rol eliminado: ' . $rol['nombre'], $id, 'rol');

        $this->redirect('/admin/roles', ['success' => 'Rol eliminado.']);
    }

    private function getSidebarCounts(): array
    {
        return [
            'fichas' => (int)$this->db->query("SELECT COUNT(*) FROM fichas WHERE activo = 1 AND eliminado = 0")->fetchColumn(),
            'categorias' => (int)$this->db->query("SELECT COUNT(*) FROM categorias WHERE activo = 1")->fetchColumn(),
        ];
    }
}
