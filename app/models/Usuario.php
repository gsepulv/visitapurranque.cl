<?php

class Usuario
{
    private PDO $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    public function getAll(): array
    {
        return $this->db->query(
            "SELECT u.*, r.nombre AS rol_nombre
             FROM usuarios u
             JOIN roles r ON r.id = u.rol_id
             ORDER BY u.id ASC"
        )->fetchAll();
    }

    public function getById(int $id): ?array
    {
        $stmt = $this->db->prepare(
            "SELECT u.*, r.nombre AS rol_nombre
             FROM usuarios u
             JOIN roles r ON r.id = u.rol_id
             WHERE u.id = ?"
        );
        $stmt->execute([$id]);
        return $stmt->fetch() ?: null;
    }

    public function getByEmail(string $email): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM usuarios WHERE email = ? LIMIT 1");
        $stmt->execute([$email]);
        return $stmt->fetch() ?: null;
    }

    public function crear(array $datos): int
    {
        $stmt = $this->db->prepare(
            "INSERT INTO usuarios (nombre, email, password, rol_id, telefono, activo)
             VALUES (?, ?, ?, ?, ?, ?)"
        );
        $stmt->execute([
            $datos['nombre'],
            $datos['email'],
            $datos['password'],
            $datos['rol_id'],
            $datos['telefono'] ?? null,
            $datos['activo'] ?? 1,
        ]);
        return (int)$this->db->lastInsertId();
    }

    public function actualizar(int $id, array $datos): void
    {
        $stmt = $this->db->prepare(
            "UPDATE usuarios SET nombre = ?, email = ?, rol_id = ?, telefono = ?, activo = ?
             WHERE id = ?"
        );
        $stmt->execute([
            $datos['nombre'],
            $datos['email'],
            $datos['rol_id'],
            $datos['telefono'] ?? null,
            $datos['activo'] ?? 1,
            $id,
        ]);
    }

    public function eliminar(int $id): void
    {
        $this->db->prepare("DELETE FROM usuarios WHERE id = ?")->execute([$id]);
    }

    public function cambiarEstado(int $id, int $activo): void
    {
        $this->db->prepare("UPDATE usuarios SET activo = ? WHERE id = ?")->execute([$activo, $id]);
    }

    public function getRoles(): array
    {
        return $this->db->query("SELECT * FROM roles ORDER BY id")->fetchAll();
    }
}
