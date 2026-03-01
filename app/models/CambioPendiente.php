<?php
/**
 * Modelo CambioPendiente — visitapurranque.cl
 * Cola de cambios propuestos a fichas para revisión admin
 */
class CambioPendiente
{
    private PDO $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    /**
     * Listar cambios con filtros y paginación
     */
    public function getAll(array $filtros = [], int $pagina = 1, int $porPagina = ADMIN_PER_PAGE): array
    {
        $where = ['1=1'];
        $params = [];

        if (!empty($filtros['estado'])) {
            $where[] = 'cp.estado = ?';
            $params[] = $filtros['estado'];
        }

        if (!empty($filtros['tipo'])) {
            $where[] = 'cp.tipo = ?';
            $params[] = $filtros['tipo'];
        }

        if (!empty($filtros['q'])) {
            $where[] = '(f.nombre LIKE ? OR u.nombre LIKE ? OR cp.motivo LIKE ?)';
            $params[] = '%' . $filtros['q'] . '%';
            $params[] = '%' . $filtros['q'] . '%';
            $params[] = '%' . $filtros['q'] . '%';
        }

        $whereSql = implode(' AND ', $where);
        $offset = ($pagina - 1) * $porPagina;

        $sql = "SELECT cp.*, f.nombre AS ficha_nombre, f.slug AS ficha_slug,
                       u.nombre AS usuario_nombre, r.nombre AS revisor_nombre
                FROM cambios_pendientes cp
                LEFT JOIN fichas f ON f.id = cp.ficha_id
                LEFT JOIN usuarios u ON u.id = cp.usuario_id
                LEFT JOIN usuarios r ON r.id = cp.revisado_por
                WHERE {$whereSql}
                ORDER BY cp.created_at DESC
                LIMIT {$porPagina} OFFSET {$offset}";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    /**
     * Contar cambios con filtros
     */
    public function count(array $filtros = []): int
    {
        $where = ['1=1'];
        $params = [];

        if (!empty($filtros['estado'])) {
            $where[] = 'cp.estado = ?';
            $params[] = $filtros['estado'];
        }

        if (!empty($filtros['tipo'])) {
            $where[] = 'cp.tipo = ?';
            $params[] = $filtros['tipo'];
        }

        if (!empty($filtros['q'])) {
            $where[] = '(f.nombre LIKE ? OR u.nombre LIKE ? OR cp.motivo LIKE ?)';
            $params[] = '%' . $filtros['q'] . '%';
            $params[] = '%' . $filtros['q'] . '%';
            $params[] = '%' . $filtros['q'] . '%';
        }

        $whereSql = implode(' AND ', $where);

        $stmt = $this->db->prepare(
            "SELECT COUNT(*) FROM cambios_pendientes cp
             LEFT JOIN fichas f ON f.id = cp.ficha_id
             LEFT JOIN usuarios u ON u.id = cp.usuario_id
             WHERE {$whereSql}"
        );
        $stmt->execute($params);
        return (int)$stmt->fetchColumn();
    }

    /**
     * Obtener cambio por ID con detalle
     */
    public function getById(int $id): ?array
    {
        $stmt = $this->db->prepare(
            "SELECT cp.*, f.nombre AS ficha_nombre, f.slug AS ficha_slug,
                    u.nombre AS usuario_nombre, u.email AS usuario_email,
                    r.nombre AS revisor_nombre
             FROM cambios_pendientes cp
             LEFT JOIN fichas f ON f.id = cp.ficha_id
             LEFT JOIN usuarios u ON u.id = cp.usuario_id
             LEFT JOIN usuarios r ON r.id = cp.revisado_por
             WHERE cp.id = ?
             LIMIT 1"
        );
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    /**
     * Aprobar cambio y aplicar datos a la ficha
     */
    public function aprobar(int $id, int $revisorId, ?string $nota = null): bool
    {
        $cambio = $this->getById($id);
        if (!$cambio || $cambio['estado'] !== 'pendiente') {
            return false;
        }

        $this->db->beginTransaction();
        try {
            // Marcar como aprobado
            $stmt = $this->db->prepare(
                "UPDATE cambios_pendientes
                 SET estado = 'aprobado', revisado_por = ?, revisado_at = NOW(), nota_revision = ?
                 WHERE id = ?"
            );
            $stmt->execute([$revisorId, $nota, $id]);

            // Aplicar datos nuevos a la ficha
            $datos = json_decode($cambio['datos_nuevos'], true);
            if ($datos && $cambio['tipo'] === 'edicion') {
                $sets = [];
                $params = [];
                $allowed = [
                    'nombre', 'descripcion', 'descripcion_corta', 'direccion',
                    'telefono', 'whatsapp', 'email', 'sitio_web', 'facebook',
                    'instagram', 'horarios', 'precio_desde', 'precio_hasta',
                    'precio_texto', 'temporada', 'como_llegar', 'info_practica',
                    'que_llevar', 'latitud', 'longitud',
                ];
                foreach ($datos as $campo => $valor) {
                    if (in_array($campo, $allowed)) {
                        $sets[] = "{$campo} = ?";
                        $params[] = $valor;
                    }
                }
                if (!empty($sets)) {
                    $params[] = $cambio['ficha_id'];
                    $sql = "UPDATE fichas SET " . implode(', ', $sets) . " WHERE id = ?";
                    $this->db->prepare($sql)->execute($params);
                }
            }

            $this->db->commit();
            return true;
        } catch (\Throwable $e) {
            $this->db->rollBack();
            return false;
        }
    }

    /**
     * Rechazar cambio
     */
    public function rechazar(int $id, int $revisorId, ?string $nota = null): bool
    {
        $stmt = $this->db->prepare(
            "UPDATE cambios_pendientes
             SET estado = 'rechazado', revisado_por = ?, revisado_at = NOW(), nota_revision = ?
             WHERE id = ? AND estado = 'pendiente'"
        );
        return $stmt->execute([$revisorId, $nota, $id]);
    }

    /**
     * Eliminar cambio
     */
    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare("DELETE FROM cambios_pendientes WHERE id = ?");
        return $stmt->execute([$id]);
    }

    /**
     * Contar pendientes
     */
    public function countPendientes(): int
    {
        return (int)$this->db->query(
            "SELECT COUNT(*) FROM cambios_pendientes WHERE estado = 'pendiente'"
        )->fetchColumn();
    }

    /**
     * Stats por estado
     */
    public function statsPorEstado(): array
    {
        $rows = $this->db->query(
            "SELECT estado, COUNT(*) AS total FROM cambios_pendientes GROUP BY estado"
        )->fetchAll();

        $stats = ['pendiente' => 0, 'aprobado' => 0, 'rechazado' => 0];
        foreach ($rows as $r) {
            $stats[$r['estado']] = (int)$r['total'];
        }
        return $stats;
    }
}
