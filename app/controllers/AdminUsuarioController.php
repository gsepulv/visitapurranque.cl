<?php

require_once BASE_PATH . '/app/middleware/AuthMiddleware.php';

class AdminUsuarioController extends Controller
{
    /** GET /admin/usuarios */
    public function index(): void
    {
        $usuario = AuthMiddleware::check($this->db);
        $model = new Usuario($this->db);

        $this->renderAdmin('admin/usuarios/index', [
            'pageTitle'     => 'Usuarios',
            'usuario'       => $usuario,
            'sidebarCounts' => $this->getSidebarCounts(),
            'usuarios'      => $model->getAll(),
        ]);
    }

    /** GET /admin/usuarios/crear */
    public function create(): void
    {
        $usuario = AuthMiddleware::check($this->db);
        $model = new Usuario($this->db);

        $this->renderAdmin('admin/usuarios/form', [
            'pageTitle'     => 'Nuevo Usuario',
            'usuario'       => $usuario,
            'sidebarCounts' => $this->getSidebarCounts(),
            'editUser'      => null,
            'roles'         => $model->getRoles(),
        ]);
    }

    /** POST /admin/usuarios/crear */
    public function store(): void
    {
        $usuario = AuthMiddleware::check($this->db);

        if (($_POST['_csrf'] ?? '') !== csrf_token()) {
            $this->redirect('/admin/usuarios', ['error' => 'Token inválido.']);
        }

        $nombre   = trim($_POST['nombre'] ?? '');
        $email    = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $confirmar = $_POST['password_confirmar'] ?? '';
        $rolId    = (int)($_POST['rol_id'] ?? 4);
        $telefono = trim($_POST['telefono'] ?? '');
        $activo   = isset($_POST['activo']) ? 1 : 0;

        // Validaciones
        if ($nombre === '' || $email === '') {
            $this->redirect('/admin/usuarios/crear', ['error' => 'Nombre y email son obligatorios.']);
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->redirect('/admin/usuarios/crear', ['error' => 'Email inválido.']);
        }

        $model = new Usuario($this->db);
        if ($model->getByEmail($email)) {
            $this->redirect('/admin/usuarios/crear', ['error' => 'Ya existe un usuario con ese email.']);
        }

        if (strlen($password) < 8) {
            $this->redirect('/admin/usuarios/crear', ['error' => 'La contraseña debe tener al menos 8 caracteres.']);
        }

        if (!preg_match('/[A-Z]/', $password) || !preg_match('/[a-z]/', $password) || !preg_match('/[0-9]/', $password)) {
            $this->redirect('/admin/usuarios/crear', ['error' => 'La contraseña debe incluir mayúscula, minúscula y número.']);
        }

        if ($password !== $confirmar) {
            $this->redirect('/admin/usuarios/crear', ['error' => 'Las contraseñas no coinciden.']);
        }

        $id = $model->crear([
            'nombre'   => $nombre,
            'email'    => $email,
            'password' => password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]),
            'rol_id'   => $rolId,
            'telefono' => $telefono,
            'activo'   => $activo,
        ]);

        $this->audit($usuario['id'], 'crear', 'usuarios', 'Usuario creado: ' . $nombre, $id, 'usuario');

        $this->redirect('/admin/usuarios', ['success' => 'Usuario creado correctamente.']);
    }

    /** GET /admin/usuarios/{id}/editar */
    public function edit(int $id): void
    {
        $usuario = AuthMiddleware::check($this->db);
        $model = new Usuario($this->db);
        $editUser = $model->getById($id);

        if (!$editUser) {
            $this->redirect('/admin/usuarios', ['error' => 'Usuario no encontrado.']);
        }

        $this->renderAdmin('admin/usuarios/form', [
            'pageTitle'     => 'Editar: ' . $editUser['nombre'],
            'usuario'       => $usuario,
            'sidebarCounts' => $this->getSidebarCounts(),
            'editUser'      => $editUser,
            'roles'         => $model->getRoles(),
        ]);
    }

    /** POST /admin/usuarios/{id}/editar */
    public function update(int $id): void
    {
        $usuario = AuthMiddleware::check($this->db);

        if (($_POST['_csrf'] ?? '') !== csrf_token()) {
            $this->redirect('/admin/usuarios', ['error' => 'Token inválido.']);
        }

        $model = new Usuario($this->db);
        $editUser = $model->getById($id);
        if (!$editUser) {
            $this->redirect('/admin/usuarios', ['error' => 'Usuario no encontrado.']);
        }

        $nombre   = trim($_POST['nombre'] ?? '');
        $email    = trim($_POST['email'] ?? '');
        $rolId    = (int)($_POST['rol_id'] ?? $editUser['rol_id']);
        $telefono = trim($_POST['telefono'] ?? '');
        $activo   = isset($_POST['activo']) ? 1 : 0;

        if ($nombre === '' || $email === '') {
            $this->redirect("/admin/usuarios/{$id}/editar", ['error' => 'Nombre y email son obligatorios.']);
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->redirect("/admin/usuarios/{$id}/editar", ['error' => 'Email inválido.']);
        }

        // No duplicar email
        $existente = $model->getByEmail($email);
        if ($existente && (int)$existente['id'] !== $id) {
            $this->redirect("/admin/usuarios/{$id}/editar", ['error' => 'Ese email ya está en uso.']);
        }

        // No permitir quitarse admin a sí mismo
        if ($id === (int)$usuario['id'] && $rolId !== (int)$usuario['rol_id']) {
            $this->redirect("/admin/usuarios/{$id}/editar", ['error' => 'No puedes cambiar tu propio rol.']);
        }

        // No desactivarse a sí mismo
        if ($id === (int)$usuario['id'] && $activo === 0) {
            $this->redirect("/admin/usuarios/{$id}/editar", ['error' => 'No puedes desactivar tu propia cuenta.']);
        }

        $model->actualizar($id, [
            'nombre'   => $nombre,
            'email'    => $email,
            'rol_id'   => $rolId,
            'telefono' => $telefono,
            'activo'   => $activo,
        ]);

        $this->audit($usuario['id'], 'editar', 'usuarios', 'Usuario editado: ' . $nombre, $id, 'usuario');

        $this->redirect('/admin/usuarios', ['success' => 'Usuario actualizado.']);
    }

    /** POST /admin/usuarios/{id}/eliminar */
    public function delete(int $id): void
    {
        $usuario = AuthMiddleware::check($this->db);

        if (($_POST['_csrf'] ?? '') !== csrf_token()) {
            $this->redirect('/admin/usuarios', ['error' => 'Token inválido.']);
        }

        if ($id === (int)$usuario['id']) {
            $this->redirect('/admin/usuarios', ['error' => 'No puedes eliminar tu propia cuenta.']);
        }

        $model = new Usuario($this->db);
        $target = $model->getById($id);
        if (!$target) {
            $this->redirect('/admin/usuarios', ['error' => 'Usuario no encontrado.']);
        }

        $model->eliminar($id);
        $this->audit($usuario['id'], 'eliminar', 'usuarios', 'Usuario eliminado: ' . $target['nombre'], $id, 'usuario');

        $this->redirect('/admin/usuarios', ['success' => 'Usuario eliminado.']);
    }

    /** POST /admin/usuarios/{id}/toggle */
    public function toggle(int $id): void
    {
        $usuario = AuthMiddleware::check($this->db);

        if (($_POST['_csrf'] ?? '') !== csrf_token()) {
            $this->redirect('/admin/usuarios', ['error' => 'Token inválido.']);
        }

        if ($id === (int)$usuario['id']) {
            $this->redirect('/admin/usuarios', ['error' => 'No puedes desactivar tu propia cuenta.']);
        }

        $model = new Usuario($this->db);
        $target = $model->getById($id);
        if (!$target) {
            $this->redirect('/admin/usuarios', ['error' => 'Usuario no encontrado.']);
        }

        $nuevoEstado = $target['activo'] ? 0 : 1;
        $model->cambiarEstado($id, $nuevoEstado);

        $accion = $nuevoEstado ? 'activar' : 'desactivar';
        $this->audit($usuario['id'], $accion, 'usuarios', 'Usuario ' . $accion . ': ' . $target['nombre'], $id, 'usuario');

        $this->redirect('/admin/usuarios', [
            'success' => 'Usuario ' . ($nuevoEstado ? 'activado' : 'desactivado') . '.',
        ]);
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
