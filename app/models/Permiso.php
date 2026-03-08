<?php

class Permiso
{
    private PDO $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    public function getAll(): array
    {
        return $this->db->query("SELECT * FROM permisos ORDER BY modulo, nombre")->fetchAll();
    }

    public function getAllGrouped(): array
    {
        $permisos = $this->getAll();
        $grouped = [];
        foreach ($permisos as $p) {
            $grouped[$p['modulo']][] = $p;
        }
        return $grouped;
    }

    public function getByRol(int $rolId): array
    {
        $stmt = $this->db->prepare(
            "SELECT p.* FROM permisos p
             JOIN rol_permisos rp ON rp.permiso_id = p.id
             WHERE rp.rol_id = ?
             ORDER BY p.modulo, p.nombre"
        );
        $stmt->execute([$rolId]);
        return $stmt->fetchAll();
    }

    public function getIdsByRol(int $rolId): array
    {
        $stmt = $this->db->prepare("SELECT permiso_id FROM rol_permisos WHERE rol_id = ?");
        $stmt->execute([$rolId]);
        return $stmt->fetchAll(\PDO::FETCH_COLUMN);
    }

    public function asignarARol(int $rolId, array $permisoIds): void
    {
        $this->db->prepare("DELETE FROM rol_permisos WHERE rol_id = ?")->execute([$rolId]);

        if (!empty($permisoIds)) {
            $stmt = $this->db->prepare("INSERT INTO rol_permisos (rol_id, permiso_id) VALUES (?, ?)");
            foreach ($permisoIds as $pid) {
                $stmt->execute([$rolId, (int)$pid]);
            }
        }
    }

    public function tienePermiso(int $rolId, string $slug): bool
    {
        // Admin siempre tiene todo
        $stmt = $this->db->prepare("SELECT slug FROM roles WHERE id = ?");
        $stmt->execute([$rolId]);
        if ($stmt->fetchColumn() === 'admin') {
            return true;
        }

        $stmt = $this->db->prepare(
            "SELECT COUNT(*) FROM rol_permisos rp
             JOIN permisos p ON p.id = rp.permiso_id
             WHERE rp.rol_id = ? AND p.slug = ?"
        );
        $stmt->execute([$rolId, $slug]);
        return (int)$stmt->fetchColumn() > 0;
    }
}
