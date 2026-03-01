<?php
/**
 * Modelo Evento — visitapurranque.cl
 * Eventos, fiestas y actividades turísticas
 */
class Evento
{
    private PDO $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    /**
     * Listar eventos con filtros y paginación
     */
    public function getAll(array $filtros = [], int $pagina = 1, int $porPagina = ADMIN_PER_PAGE): array
    {
        $where = ['e.eliminado = 0'];
        $params = [];

        if (!empty($filtros['categoria_id'])) {
            $where[] = 'e.categoria_id = ?';
            $params[] = (int)$filtros['categoria_id'];
        }

        if (isset($filtros['activo']) && $filtros['activo'] !== '') {
            $where[] = 'e.activo = ?';
            $params[] = (int)$filtros['activo'];
        }

        if (!empty($filtros['tiempo'])) {
            if ($filtros['tiempo'] === 'proximos') {
                $where[] = 'e.fecha_inicio >= NOW()';
            } elseif ($filtros['tiempo'] === 'pasados') {
                $where[] = 'e.fecha_inicio < NOW()';
            }
        }

        if (!empty($filtros['q'])) {
            $where[] = '(e.titulo LIKE ? OR e.lugar LIKE ? OR e.organizador LIKE ?)';
            $params[] = '%' . $filtros['q'] . '%';
            $params[] = '%' . $filtros['q'] . '%';
            $params[] = '%' . $filtros['q'] . '%';
        }

        $whereSql = implode(' AND ', $where);
        $offset = ($pagina - 1) * $porPagina;

        $sql = "SELECT e.*, c.nombre AS categoria_nombre
                FROM eventos e
                LEFT JOIN categorias c ON c.id = e.categoria_id
                WHERE {$whereSql}
                ORDER BY e.fecha_inicio DESC
                LIMIT {$porPagina} OFFSET {$offset}";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    /**
     * Contar eventos con filtros
     */
    public function count(array $filtros = []): int
    {
        $where = ['e.eliminado = 0'];
        $params = [];

        if (!empty($filtros['categoria_id'])) {
            $where[] = 'e.categoria_id = ?';
            $params[] = (int)$filtros['categoria_id'];
        }

        if (isset($filtros['activo']) && $filtros['activo'] !== '') {
            $where[] = 'e.activo = ?';
            $params[] = (int)$filtros['activo'];
        }

        if (!empty($filtros['tiempo'])) {
            if ($filtros['tiempo'] === 'proximos') {
                $where[] = 'e.fecha_inicio >= NOW()';
            } elseif ($filtros['tiempo'] === 'pasados') {
                $where[] = 'e.fecha_inicio < NOW()';
            }
        }

        if (!empty($filtros['q'])) {
            $where[] = '(e.titulo LIKE ? OR e.lugar LIKE ? OR e.organizador LIKE ?)';
            $params[] = '%' . $filtros['q'] . '%';
            $params[] = '%' . $filtros['q'] . '%';
            $params[] = '%' . $filtros['q'] . '%';
        }

        $whereSql = implode(' AND ', $where);

        $stmt = $this->db->prepare("SELECT COUNT(*) FROM eventos e WHERE {$whereSql}");
        $stmt->execute($params);
        return (int)$stmt->fetchColumn();
    }

    /**
     * Obtener evento por ID
     */
    public function getById(int $id): ?array
    {
        $stmt = $this->db->prepare(
            "SELECT e.*, c.nombre AS categoria_nombre
             FROM eventos e
             LEFT JOIN categorias c ON c.id = e.categoria_id
             WHERE e.id = ? AND e.eliminado = 0
             LIMIT 1"
        );
        $stmt->execute([$id]);
        $evento = $stmt->fetch();
        return $evento ?: null;
    }

    /**
     * Crear evento
     */
    public function create(array $data): int
    {
        $stmt = $this->db->prepare(
            "INSERT INTO eventos (titulo, slug, descripcion, descripcion_corta, imagen,
                    fecha_inicio, fecha_fin, lugar, direccion, latitud, longitud,
                    precio, organizador, contacto, url_externa, categoria_id,
                    recurrente, destacado, meta_title, meta_description, activo)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)"
        );
        $stmt->execute([
            $data['titulo'],
            $data['slug'],
            $data['descripcion'] ?: null,
            $data['descripcion_corta'] ?: null,
            $data['imagen'] ?: null,
            $data['fecha_inicio'],
            $data['fecha_fin'] ?: null,
            $data['lugar'] ?: null,
            $data['direccion'] ?: null,
            $data['latitud'] ?: null,
            $data['longitud'] ?: null,
            $data['precio'] ?: null,
            $data['organizador'] ?: null,
            $data['contacto'] ?: null,
            $data['url_externa'] ?: null,
            $data['categoria_id'] ?: null,
            (int)($data['recurrente'] ?? 0),
            (int)($data['destacado'] ?? 0),
            $data['meta_title'] ?: null,
            $data['meta_description'] ?: null,
            (int)($data['activo'] ?? 1),
        ]);
        return (int)$this->db->lastInsertId();
    }

    /**
     * Actualizar evento
     */
    public function update(int $id, array $data): bool
    {
        $stmt = $this->db->prepare(
            "UPDATE eventos SET titulo=?, slug=?, descripcion=?, descripcion_corta=?, imagen=?,
                    fecha_inicio=?, fecha_fin=?, lugar=?, direccion=?, latitud=?, longitud=?,
                    precio=?, organizador=?, contacto=?, url_externa=?, categoria_id=?,
                    recurrente=?, destacado=?, meta_title=?, meta_description=?, activo=?
             WHERE id = ? AND eliminado = 0"
        );
        return $stmt->execute([
            $data['titulo'],
            $data['slug'],
            $data['descripcion'] ?: null,
            $data['descripcion_corta'] ?: null,
            $data['imagen'] ?: null,
            $data['fecha_inicio'],
            $data['fecha_fin'] ?: null,
            $data['lugar'] ?: null,
            $data['direccion'] ?: null,
            $data['latitud'] ?: null,
            $data['longitud'] ?: null,
            $data['precio'] ?: null,
            $data['organizador'] ?: null,
            $data['contacto'] ?: null,
            $data['url_externa'] ?: null,
            $data['categoria_id'] ?: null,
            (int)($data['recurrente'] ?? 0),
            (int)($data['destacado'] ?? 0),
            $data['meta_title'] ?: null,
            $data['meta_description'] ?: null,
            (int)($data['activo'] ?? 1),
            $id,
        ]);
    }

    /**
     * Soft delete
     */
    public function softDelete(int $id): bool
    {
        $stmt = $this->db->prepare(
            "UPDATE eventos SET eliminado = 1, eliminado_at = NOW(), activo = 0 WHERE id = ? AND eliminado = 0"
        );
        return $stmt->execute([$id]);
    }

    /**
     * Toggle activo/inactivo
     */
    public function toggleActivo(int $id): bool
    {
        $stmt = $this->db->prepare(
            "UPDATE eventos SET activo = NOT activo WHERE id = ? AND eliminado = 0"
        );
        return $stmt->execute([$id]);
    }

    /**
     * Verificar si slug ya existe
     */
    public function slugExists(string $slug, ?int $excludeId = null): bool
    {
        $sql = "SELECT COUNT(*) FROM eventos WHERE slug = ? AND eliminado = 0";
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
     * Contar eventos próximos activos
     */
    public function countProximos(): int
    {
        return (int)$this->db->query(
            "SELECT COUNT(*) FROM eventos WHERE fecha_inicio >= NOW() AND activo = 1 AND eliminado = 0"
        )->fetchColumn();
    }

    // ── Métodos frontend público ─────────────────────────

    public function getProximos(int $limit = 6): array
    {
        $stmt = $this->db->prepare(
            "SELECT * FROM eventos
             WHERE activo = 1 AND eliminado = 0 AND fecha_fin >= NOW()
             ORDER BY fecha_inicio ASC
             LIMIT ?"
        );
        $stmt->execute([$limit]);
        return $stmt->fetchAll();
    }
}
