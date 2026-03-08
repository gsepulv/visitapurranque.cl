<?php

class Notificacion
{
    private \PDO $db;

    public function __construct(\PDO $db)
    {
        $this->db = $db;
    }

    /** Últimas N del usuario */
    public function getByUsuario(int $usuarioId, int $limit = 20): array
    {
        $stmt = $this->db->prepare(
            "SELECT * FROM notificaciones WHERE usuario_id = ? ORDER BY created_at DESC LIMIT ?"
        );
        $stmt->execute([$usuarioId, $limit]);
        return $stmt->fetchAll();
    }

    /** Conteo de no leídas */
    public function getNoLeidas(int $usuarioId): int
    {
        $stmt = $this->db->prepare(
            "SELECT COUNT(*) FROM notificaciones WHERE usuario_id = ? AND leida = 0"
        );
        $stmt->execute([$usuarioId]);
        return (int)$stmt->fetchColumn();
    }

    /** Crear notificación */
    public function crear(array $datos): int
    {
        $this->db->prepare(
            "INSERT INTO notificaciones (usuario_id, tipo, titulo, mensaje, url)
             VALUES (?, ?, ?, ?, ?)"
        )->execute([
            $datos['usuario_id'],
            $datos['tipo'],
            $datos['titulo'],
            $datos['mensaje'] ?? null,
            $datos['url'] ?? null,
        ]);
        return (int)$this->db->lastInsertId();
    }

    /** Notificar a todos los admins (rol_id = 1) */
    public function notificarAdmins(string $tipo, string $titulo, ?string $mensaje = null, ?string $url = null): void
    {
        $admins = $this->db->query("SELECT id FROM usuarios WHERE rol_id = 1 AND activo = 1")->fetchAll(\PDO::FETCH_COLUMN);
        foreach ($admins as $adminId) {
            $this->crear([
                'usuario_id' => (int)$adminId,
                'tipo'       => $tipo,
                'titulo'     => $titulo,
                'mensaje'    => $mensaje,
                'url'        => $url,
            ]);
        }
    }

    /** Marcar como leída */
    public function marcarLeida(int $id): void
    {
        $this->db->prepare("UPDATE notificaciones SET leida = 1 WHERE id = ?")->execute([$id]);
    }

    /** Marcar todas como leídas para un usuario */
    public function marcarTodasLeidas(int $usuarioId): void
    {
        $this->db->prepare("UPDATE notificaciones SET leida = 1 WHERE usuario_id = ? AND leida = 0")->execute([$usuarioId]);
    }

    /** Eliminar */
    public function eliminar(int $id): void
    {
        $this->db->prepare("DELETE FROM notificaciones WHERE id = ?")->execute([$id]);
    }
}
