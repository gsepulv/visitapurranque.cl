<?php
/**
 * Modelo Resena — visitapurranque.cl
 * Reseñas de visitantes sobre fichas/atractivos
 */
class Resena
{
    private PDO $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    /**
     * Listar reseñas con filtros y paginación
     */
    public function getAll(array $filtros = [], int $pagina = 1, int $porPagina = ADMIN_PER_PAGE): array
    {
        $where = ['1=1'];
        $params = [];

        if (!empty($filtros['estado'])) {
            $where[] = 'r.estado = ?';
            $params[] = $filtros['estado'];
        }

        if (!empty($filtros['ficha_id'])) {
            $where[] = 'r.ficha_id = ?';
            $params[] = (int)$filtros['ficha_id'];
        }

        if (!empty($filtros['rating'])) {
            $where[] = 'r.rating = ?';
            $params[] = (int)$filtros['rating'];
        }

        if (!empty($filtros['tipo_experiencia'])) {
            $where[] = 'r.tipo_experiencia = ?';
            $params[] = $filtros['tipo_experiencia'];
        }

        if (!empty($filtros['q'])) {
            $where[] = '(r.nombre LIKE ? OR r.comentario LIKE ? OR r.email LIKE ?)';
            $params[] = '%' . $filtros['q'] . '%';
            $params[] = '%' . $filtros['q'] . '%';
            $params[] = '%' . $filtros['q'] . '%';
        }

        $whereSql = implode(' AND ', $where);
        $offset = ($pagina - 1) * $porPagina;

        $sql = "SELECT r.*, f.nombre AS ficha_nombre
                FROM resenas r
                LEFT JOIN fichas f ON f.id = r.ficha_id
                WHERE {$whereSql}
                ORDER BY r.created_at DESC
                LIMIT {$porPagina} OFFSET {$offset}";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    /**
     * Contar reseñas con filtros
     */
    public function count(array $filtros = []): int
    {
        $where = ['1=1'];
        $params = [];

        if (!empty($filtros['estado'])) {
            $where[] = 'r.estado = ?';
            $params[] = $filtros['estado'];
        }

        if (!empty($filtros['ficha_id'])) {
            $where[] = 'r.ficha_id = ?';
            $params[] = (int)$filtros['ficha_id'];
        }

        if (!empty($filtros['rating'])) {
            $where[] = 'r.rating = ?';
            $params[] = (int)$filtros['rating'];
        }

        if (!empty($filtros['tipo_experiencia'])) {
            $where[] = 'r.tipo_experiencia = ?';
            $params[] = $filtros['tipo_experiencia'];
        }

        if (!empty($filtros['q'])) {
            $where[] = '(r.nombre LIKE ? OR r.comentario LIKE ? OR r.email LIKE ?)';
            $params[] = '%' . $filtros['q'] . '%';
            $params[] = '%' . $filtros['q'] . '%';
            $params[] = '%' . $filtros['q'] . '%';
        }

        $whereSql = implode(' AND ', $where);

        $stmt = $this->db->prepare("SELECT COUNT(*) FROM resenas r WHERE {$whereSql}");
        $stmt->execute($params);
        return (int)$stmt->fetchColumn();
    }

    /**
     * Obtener reseña por ID
     */
    public function getById(int $id): ?array
    {
        $stmt = $this->db->prepare(
            "SELECT r.*, f.nombre AS ficha_nombre
             FROM resenas r
             LEFT JOIN fichas f ON f.id = r.ficha_id
             WHERE r.id = ?
             LIMIT 1"
        );
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    /**
     * Cambiar estado (aprobar, rechazar, marcar spam)
     */
    public function cambiarEstado(int $id, string $estado): bool
    {
        $stmt = $this->db->prepare(
            "UPDATE resenas SET estado = ? WHERE id = ?"
        );
        return $stmt->execute([$estado, $id]);
    }

    /**
     * Guardar respuesta del admin
     */
    public function responder(int $id, string $respuesta): bool
    {
        $stmt = $this->db->prepare(
            "UPDATE resenas SET respuesta_admin = ?, respuesta_fecha = NOW() WHERE id = ?"
        );
        return $stmt->execute([$respuesta, $id]);
    }

    /**
     * Eliminar reseña definitivamente
     */
    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare("DELETE FROM resenas WHERE id = ?");
        return $stmt->execute([$id]);
    }

    /**
     * Contar pendientes de moderación
     */
    public function countPendientes(): int
    {
        return (int)$this->db->query(
            "SELECT COUNT(*) FROM resenas WHERE estado = 'pendiente'"
        )->fetchColumn();
    }

    /**
     * Estadísticas rápidas por estado
     */
    public function statsPorEstado(): array
    {
        $rows = $this->db->query(
            "SELECT estado, COUNT(*) AS total FROM resenas GROUP BY estado"
        )->fetchAll();

        $stats = ['pendiente' => 0, 'aprobada' => 0, 'rechazada' => 0, 'spam' => 0];
        foreach ($rows as $r) {
            $stats[$r['estado']] = (int)$r['total'];
        }
        return $stats;
    }

    // ── Métodos frontend público ─────────────────────────

    public function getAprobadasByFicha(int $fichaId): array
    {
        $stmt = $this->db->prepare(
            "SELECT * FROM resenas WHERE ficha_id = ? AND estado = 'aprobada' ORDER BY created_at DESC"
        );
        $stmt->execute([$fichaId]);
        return $stmt->fetchAll();
    }

    public function getPromedioByFicha(int $fichaId): array
    {
        $stmt = $this->db->prepare(
            "SELECT AVG(rating) AS promedio, COUNT(*) AS total
             FROM resenas WHERE ficha_id = ? AND estado = 'aprobada'"
        );
        $stmt->execute([$fichaId]);
        return $stmt->fetch();
    }

    public function create(array $data): int
    {
        $stmt = $this->db->prepare(
            "INSERT INTO resenas (ficha_id, nombre, email, rating, tipo_experiencia, comentario, estado, ip)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?)"
        );
        $stmt->execute([
            (int)$data['ficha_id'],
            $data['nombre'],
            $data['email'] ?: null,
            (int)$data['rating'],
            $data['tipo_experiencia'] ?? 'otro',
            $data['comentario'],
            $data['estado'] ?? 'pendiente',
            $_SERVER['REMOTE_ADDR'] ?? null,
        ]);
        return (int)$this->db->lastInsertId();
    }
}
