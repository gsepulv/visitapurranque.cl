<?php

class Popup
{
    private \PDO $db;

    public function __construct(\PDO $db)
    {
        $this->db = $db;
    }

    public function getAll(): array
    {
        return $this->db->query("SELECT * FROM popups ORDER BY created_at DESC")->fetchAll();
    }

    public function getById(int $id): array|false
    {
        $stmt = $this->db->prepare("SELECT * FROM popups WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function crear(array $datos): int
    {
        $this->db->prepare(
            "INSERT INTO popups (titulo, contenido, tipo, trigger_type, trigger_valor, paginas, fecha_inicio, fecha_fin, activo)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)"
        )->execute([
            $datos['titulo'],
            $datos['contenido'],
            $datos['tipo'],
            $datos['trigger_type'],
            $datos['trigger_valor'] ?: '5',
            $datos['paginas'] ?: null,
            $datos['fecha_inicio'] ?: null,
            $datos['fecha_fin'] ?: null,
            $datos['activo'] ?? 0,
        ]);
        return (int)$this->db->lastInsertId();
    }

    public function actualizar(int $id, array $datos): void
    {
        $this->db->prepare(
            "UPDATE popups SET titulo = ?, contenido = ?, tipo = ?, trigger_type = ?, trigger_valor = ?,
             paginas = ?, fecha_inicio = ?, fecha_fin = ?, activo = ?
             WHERE id = ?"
        )->execute([
            $datos['titulo'],
            $datos['contenido'],
            $datos['tipo'],
            $datos['trigger_type'],
            $datos['trigger_valor'] ?: '5',
            $datos['paginas'] ?: null,
            $datos['fecha_inicio'] ?: null,
            $datos['fecha_fin'] ?: null,
            $datos['activo'] ?? 0,
            $id,
        ]);
    }

    public function eliminar(int $id): void
    {
        $this->db->prepare("DELETE FROM popups WHERE id = ?")->execute([$id]);
    }

    /** Obtener popup activo actual (dentro del rango de fechas) */
    public function getActivo(): array|false
    {
        $stmt = $this->db->prepare(
            "SELECT * FROM popups
             WHERE activo = 1
               AND (fecha_inicio IS NULL OR fecha_inicio <= CURDATE())
               AND (fecha_fin IS NULL OR fecha_fin >= CURDATE())
             ORDER BY created_at DESC
             LIMIT 1"
        );
        $stmt->execute();
        return $stmt->fetch();
    }
}
