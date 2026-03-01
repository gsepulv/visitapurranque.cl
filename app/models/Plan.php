<?php
/**
 * Modelo Plan — visitapurranque.cl
 * Planes de suscripción para fichas/comercios
 */
class Plan
{
    private PDO $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    /**
     * Listar todos los planes ordenados
     */
    public function getAll(): array
    {
        return $this->db->query(
            "SELECT * FROM planes ORDER BY orden, id"
        )->fetchAll();
    }

    /**
     * Obtener plan por ID
     */
    public function getById(int $id): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM planes WHERE id = ? LIMIT 1");
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    /**
     * Crear plan
     */
    public function create(array $data): int
    {
        $stmt = $this->db->prepare(
            "INSERT INTO planes (nombre, slug, descripcion, precio_mensual, precio_anual,
                    caracteristicas, destacado_home, max_imagenes, tiene_badge, orden, activo)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)"
        );
        $stmt->execute([
            $data['nombre'],
            $data['slug'],
            $data['descripcion'] ?: null,
            (int)$data['precio_mensual'],
            $data['precio_anual'] !== '' ? (int)$data['precio_anual'] : null,
            !empty($data['caracteristicas']) ? $data['caracteristicas'] : null,
            (int)($data['destacado_home'] ?? 0),
            (int)($data['max_imagenes'] ?? 5),
            (int)($data['tiene_badge'] ?? 0),
            (int)($data['orden'] ?? 0),
            (int)($data['activo'] ?? 1),
        ]);
        return (int)$this->db->lastInsertId();
    }

    /**
     * Actualizar plan
     */
    public function update(int $id, array $data): bool
    {
        $stmt = $this->db->prepare(
            "UPDATE planes SET nombre=?, slug=?, descripcion=?, precio_mensual=?, precio_anual=?,
                    caracteristicas=?, destacado_home=?, max_imagenes=?, tiene_badge=?, orden=?, activo=?
             WHERE id = ?"
        );
        return $stmt->execute([
            $data['nombre'],
            $data['slug'],
            $data['descripcion'] ?: null,
            (int)$data['precio_mensual'],
            $data['precio_anual'] !== '' ? (int)$data['precio_anual'] : null,
            !empty($data['caracteristicas']) ? $data['caracteristicas'] : null,
            (int)($data['destacado_home'] ?? 0),
            (int)($data['max_imagenes'] ?? 5),
            (int)($data['tiene_badge'] ?? 0),
            (int)($data['orden'] ?? 0),
            (int)($data['activo'] ?? 1),
            $id,
        ]);
    }

    /**
     * Eliminar plan (solo si no tiene suscripciones)
     */
    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare("DELETE FROM planes WHERE id = ?");
        return $stmt->execute([$id]);
    }

    /**
     * Contar suscripciones de un plan
     */
    public function countSuscripciones(int $id): int
    {
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM suscripciones WHERE plan_id = ?");
        $stmt->execute([$id]);
        return (int)$stmt->fetchColumn();
    }

    /**
     * Verificar si slug ya existe
     */
    public function slugExists(string $slug, ?int $excludeId = null): bool
    {
        $sql = "SELECT COUNT(*) FROM planes WHERE slug = ?";
        $params = [$slug];

        if ($excludeId) {
            $sql .= " AND id != ?";
            $params[] = $excludeId;
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return (int)$stmt->fetchColumn() > 0;
    }

    /**
     * Toggle activo
     */
    public function toggleActivo(int $id): bool
    {
        $stmt = $this->db->prepare("UPDATE planes SET activo = NOT activo WHERE id = ?");
        return $stmt->execute([$id]);
    }
}
