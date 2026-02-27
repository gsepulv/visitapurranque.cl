<?php
/**
 * AuthMiddleware — visitapurranque.cl
 * Verifica sesión admin y carga datos del usuario
 */
class AuthMiddleware
{
    public static function check(PDO $db): array
    {
        if (empty($_SESSION['usuario_id'])) {
            $_SESSION['flash'] = ['error' => 'Debes iniciar sesión'];
            header('Location: ' . url('/admin/login'));
            exit;
        }

        $stmt = $db->prepare(
            "SELECT u.*, r.nombre AS rol_nombre
             FROM usuarios u
             JOIN roles r ON r.id = u.rol_id
             WHERE u.id = ? AND u.activo = 1
             LIMIT 1"
        );
        $stmt->execute([$_SESSION['usuario_id']]);
        $usuario = $stmt->fetch();

        if (!$usuario) {
            unset($_SESSION['usuario_id']);
            $_SESSION['flash'] = ['error' => 'Sesión inválida'];
            header('Location: ' . url('/admin/login'));
            exit;
        }

        return $usuario;
    }
}
