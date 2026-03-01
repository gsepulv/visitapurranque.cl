<?php
/**
 * Modelo Suscripcion — visitapurranque.cl
 * Suscripciones de fichas/comercios a planes
 */
class Suscripcion
{
    private PDO $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    /**
     * Listar suscripciones con filtros y paginación
     */
    public function getAll(array $filtros = [], int $pagina = 1, int $porPagina = ADMIN_PER_PAGE): array
    {
        $where = ['1=1'];
        $params = [];

        if (!empty($filtros['estado'])) {
            $where[] = 's.estado = ?';
            $params[] = $filtros['estado'];
        }

        if (!empty($filtros['plan_id'])) {
            $where[] = 's.plan_id = ?';
            $params[] = (int)$filtros['plan_id'];
        }

        if (!empty($filtros['ficha_id'])) {
            $where[] = 's.ficha_id = ?';
            $params[] = (int)$filtros['ficha_id'];
        }

        if (!empty($filtros['q'])) {
            $where[] = 'f.nombre LIKE ?';
            $params[] = '%' . $filtros['q'] . '%';
        }

        $whereSql = implode(' AND ', $where);
        $offset = ($pagina - 1) * $porPagina;

        $sql = "SELECT s.*, f.nombre AS ficha_nombre, p.nombre AS plan_nombre
                FROM suscripciones s
                LEFT JOIN fichas f ON f.id = s.ficha_id
                LEFT JOIN planes p ON p.id = s.plan_id
                WHERE {$whereSql}
                ORDER BY s.created_at DESC
                LIMIT {$porPagina} OFFSET {$offset}";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    /**
     * Contar suscripciones con filtros
     */
    public function count(array $filtros = []): int
    {
        $where = ['1=1'];
        $params = [];

        if (!empty($filtros['estado'])) {
            $where[] = 's.estado = ?';
            $params[] = $filtros['estado'];
        }

        if (!empty($filtros['plan_id'])) {
            $where[] = 's.plan_id = ?';
            $params[] = (int)$filtros['plan_id'];
        }

        if (!empty($filtros['ficha_id'])) {
            $where[] = 's.ficha_id = ?';
            $params[] = (int)$filtros['ficha_id'];
        }

        if (!empty($filtros['q'])) {
            $where[] = 'f.nombre LIKE ?';
            $params[] = '%' . $filtros['q'] . '%';
        }

        $whereSql = implode(' AND ', $where);

        $stmt = $this->db->prepare(
            "SELECT COUNT(*) FROM suscripciones s LEFT JOIN fichas f ON f.id = s.ficha_id WHERE {$whereSql}"
        );
        $stmt->execute($params);
        return (int)$stmt->fetchColumn();
    }

    /**
     * Obtener suscripción por ID
     */
    public function getById(int $id): ?array
    {
        $stmt = $this->db->prepare(
            "SELECT s.*, f.nombre AS ficha_nombre, p.nombre AS plan_nombre
             FROM suscripciones s
             LEFT JOIN fichas f ON f.id = s.ficha_id
             LEFT JOIN planes p ON p.id = s.plan_id
             WHERE s.id = ?
             LIMIT 1"
        );
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    /**
     * Crear suscripción
     */
    public function create(array $data): int
    {
        $stmt = $this->db->prepare(
            "INSERT INTO suscripciones (ficha_id, plan_id, fecha_inicio, fecha_fin, monto, estado, notas)
             VALUES (?, ?, ?, ?, ?, ?, ?)"
        );
        $stmt->execute([
            (int)$data['ficha_id'],
            (int)$data['plan_id'],
            $data['fecha_inicio'],
            $data['fecha_fin'],
            (int)$data['monto'],
            $data['estado'],
            $data['notas'] ?: null,
        ]);
        return (int)$this->db->lastInsertId();
    }

    /**
     * Actualizar suscripción
     */
    public function update(int $id, array $data): bool
    {
        $stmt = $this->db->prepare(
            "UPDATE suscripciones SET ficha_id=?, plan_id=?, fecha_inicio=?, fecha_fin=?,
                    monto=?, estado=?, notas=?
             WHERE id = ?"
        );
        return $stmt->execute([
            (int)$data['ficha_id'],
            (int)$data['plan_id'],
            $data['fecha_inicio'],
            $data['fecha_fin'],
            (int)$data['monto'],
            $data['estado'],
            $data['notas'] ?: null,
            $id,
        ]);
    }

    /**
     * Eliminar suscripción
     */
    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare("DELETE FROM suscripciones WHERE id = ?");
        return $stmt->execute([$id]);
    }

    /**
     * Cambiar estado
     */
    public function cambiarEstado(int $id, string $estado): bool
    {
        $stmt = $this->db->prepare("UPDATE suscripciones SET estado = ? WHERE id = ?");
        return $stmt->execute([$estado, $id]);
    }

    /**
     * Stats por estado
     */
    public function statsPorEstado(): array
    {
        $rows = $this->db->query(
            "SELECT estado, COUNT(*) AS total FROM suscripciones GROUP BY estado"
        )->fetchAll();

        $stats = ['activa' => 0, 'expirada' => 0, 'cancelada' => 0, 'pendiente' => 0];
        foreach ($rows as $r) {
            $stats[$r['estado']] = (int)$r['total'];
        }
        return $stats;
    }

    /**
     * Suscripciones próximas a vencer (30 días)
     */
    public function proximasAVencer(int $dias = 30): array
    {
        $stmt = $this->db->prepare(
            "SELECT s.*, f.nombre AS ficha_nombre, p.nombre AS plan_nombre
             FROM suscripciones s
             LEFT JOIN fichas f ON f.id = s.ficha_id
             LEFT JOIN planes p ON p.id = s.plan_id
             WHERE s.estado = 'activa' AND s.fecha_fin BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL ? DAY)
             ORDER BY s.fecha_fin"
        );
        $stmt->execute([$dias]);
        return $stmt->fetchAll();
    }
}
