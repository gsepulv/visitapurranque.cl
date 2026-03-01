<?php
/**
 * Modelo Ficha — visitapurranque.cl
 * Atractivos turisticos, comercios, servicios
 */
class Ficha
{
    private PDO $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    /**
     * Listar fichas con filtros y paginacion
     */
    public function getAll(array $filtros = [], int $pagina = 1, int $porPagina = ADMIN_PER_PAGE): array
    {
        $where = ['f.eliminado = 0'];
        $params = [];

        if (!empty($filtros['categoria_id'])) {
            $where[] = 'f.categoria_id = ?';
            $params[] = (int)$filtros['categoria_id'];
        }

        if (isset($filtros['activo']) && $filtros['activo'] !== '') {
            $where[] = 'f.activo = ?';
            $params[] = (int)$filtros['activo'];
        }

        if (!empty($filtros['q'])) {
            $where[] = '(f.nombre LIKE ? OR f.direccion LIKE ?)';
            $params[] = '%' . $filtros['q'] . '%';
            $params[] = '%' . $filtros['q'] . '%';
        }

        $whereSql = implode(' AND ', $where);
        $offset = ($pagina - 1) * $porPagina;

        $sql = "SELECT f.*, c.nombre AS categoria_nombre
                FROM fichas f
                LEFT JOIN categorias c ON c.id = f.categoria_id
                WHERE {$whereSql}
                ORDER BY f.created_at DESC
                LIMIT {$porPagina} OFFSET {$offset}";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    /**
     * Contar fichas con filtros (para paginacion)
     */
    public function count(array $filtros = []): int
    {
        $where = ['f.eliminado = 0'];
        $params = [];

        if (!empty($filtros['categoria_id'])) {
            $where[] = 'f.categoria_id = ?';
            $params[] = (int)$filtros['categoria_id'];
        }

        if (isset($filtros['activo']) && $filtros['activo'] !== '') {
            $where[] = 'f.activo = ?';
            $params[] = (int)$filtros['activo'];
        }

        if (!empty($filtros['q'])) {
            $where[] = '(f.nombre LIKE ? OR f.direccion LIKE ?)';
            $params[] = '%' . $filtros['q'] . '%';
            $params[] = '%' . $filtros['q'] . '%';
        }

        $whereSql = implode(' AND ', $where);

        $stmt = $this->db->prepare("SELECT COUNT(*) FROM fichas f WHERE {$whereSql}");
        $stmt->execute($params);
        return (int)$stmt->fetchColumn();
    }

    /**
     * Obtener ficha por ID con categoria
     */
    public function getById(int $id): ?array
    {
        $stmt = $this->db->prepare(
            "SELECT f.*, c.nombre AS categoria_nombre
             FROM fichas f
             LEFT JOIN categorias c ON c.id = f.categoria_id
             WHERE f.id = ? AND f.eliminado = 0
             LIMIT 1"
        );
        $stmt->execute([$id]);
        $ficha = $stmt->fetch();
        return $ficha ?: null;
    }

    /**
     * Crear ficha
     */
    public function create(array $data): int
    {
        $campos = [
            'categoria_id', 'subcategoria_id', 'nombre', 'slug',
            'descripcion', 'descripcion_corta', 'direccion',
            'telefono', 'whatsapp', 'email', 'sitio_web',
            'facebook', 'instagram', 'latitud', 'longitud',
            'como_llegar', 'info_practica', 'horarios',
            'precio_desde', 'precio_hasta', 'precio_texto',
            'temporada', 'dificultad', 'duracion_estimada', 'que_llevar',
            'imagen_portada', 'verificado', 'destacado', 'imperdible',
            'meta_title', 'meta_description', 'plan_id', 'activo',
        ];

        $insert = [];
        $params = [];

        foreach ($campos as $campo) {
            if (array_key_exists($campo, $data)) {
                $insert[] = $campo;
                $params[] = $data[$campo] !== '' ? $data[$campo] : null;
            }
        }

        $placeholders = implode(', ', array_fill(0, count($insert), '?'));
        $columns = implode(', ', $insert);

        $stmt = $this->db->prepare("INSERT INTO fichas ({$columns}) VALUES ({$placeholders})");
        $stmt->execute($params);

        return (int)$this->db->lastInsertId();
    }

    /**
     * Actualizar ficha
     */
    public function update(int $id, array $data): bool
    {
        $campos = [
            'categoria_id', 'subcategoria_id', 'nombre', 'slug',
            'descripcion', 'descripcion_corta', 'direccion',
            'telefono', 'whatsapp', 'email', 'sitio_web',
            'facebook', 'instagram', 'latitud', 'longitud',
            'como_llegar', 'info_practica', 'horarios',
            'precio_desde', 'precio_hasta', 'precio_texto',
            'temporada', 'dificultad', 'duracion_estimada', 'que_llevar',
            'imagen_portada', 'verificado', 'destacado', 'imperdible',
            'meta_title', 'meta_description', 'plan_id', 'activo',
        ];

        $set = [];
        $params = [];

        foreach ($campos as $campo) {
            if (array_key_exists($campo, $data)) {
                $set[] = "{$campo} = ?";
                $params[] = $data[$campo] !== '' ? $data[$campo] : null;
            }
        }

        if (empty($set)) {
            return false;
        }

        $params[] = $id;
        $setSql = implode(', ', $set);

        $stmt = $this->db->prepare("UPDATE fichas SET {$setSql} WHERE id = ? AND eliminado = 0");
        return $stmt->execute($params);
    }

    /**
     * Soft delete
     */
    public function softDelete(int $id): bool
    {
        $stmt = $this->db->prepare(
            "UPDATE fichas SET eliminado = 1, eliminado_at = NOW(), activo = 0 WHERE id = ? AND eliminado = 0"
        );
        return $stmt->execute([$id]);
    }

    /**
     * Toggle activo/inactivo
     */
    public function toggleActivo(int $id): bool
    {
        $stmt = $this->db->prepare(
            "UPDATE fichas SET activo = NOT activo WHERE id = ? AND eliminado = 0"
        );
        return $stmt->execute([$id]);
    }

    /**
     * Verificar si slug ya existe (excluyendo un ID)
     */
    public function slugExists(string $slug, ?int $excludeId = null): bool
    {
        $sql = "SELECT COUNT(*) FROM fichas WHERE slug = ? AND eliminado = 0";
        $params = [$slug];

        if ($excludeId) {
            $sql .= " AND id != ?";
            $params[] = $excludeId;
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return (int)$stmt->fetchColumn() > 0;
    }

    // ── Métodos frontend público ─────────────────────────

    public function getActivos(?int $limit = null): array
    {
        $sql = "SELECT f.*, c.nombre AS categoria_nombre, c.slug AS categoria_slug, c.emoji AS categoria_emoji
                FROM fichas f
                LEFT JOIN categorias c ON c.id = f.categoria_id
                WHERE f.activo = 1 AND f.eliminado = 0
                ORDER BY f.destacado DESC, f.created_at DESC";
        if ($limit) $sql .= " LIMIT " . (int)$limit;
        return $this->db->query($sql)->fetchAll();
    }

    public function getDestacados(int $limit = 6): array
    {
        $stmt = $this->db->prepare(
            "SELECT f.*, c.nombre AS categoria_nombre, c.slug AS categoria_slug, c.emoji AS categoria_emoji
             FROM fichas f
             LEFT JOIN categorias c ON c.id = f.categoria_id
             WHERE f.activo = 1 AND f.eliminado = 0 AND f.destacado = 1
             ORDER BY f.created_at DESC
             LIMIT ?"
        );
        $stmt->execute([$limit]);
        return $stmt->fetchAll();
    }

    public function getBySlugPublico(string $slug): ?array
    {
        $stmt = $this->db->prepare(
            "SELECT f.*, c.nombre AS categoria_nombre, c.slug AS categoria_slug, c.emoji AS categoria_emoji
             FROM fichas f
             LEFT JOIN categorias c ON c.id = f.categoria_id
             WHERE f.slug = ? AND f.activo = 1 AND f.eliminado = 0
             LIMIT 1"
        );
        $stmt->execute([$slug]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function getByCategoria(int $categoriaId, int $limit = 12, int $offset = 0): array
    {
        $stmt = $this->db->prepare(
            "SELECT f.*, c.nombre AS categoria_nombre, c.slug AS categoria_slug, c.emoji AS categoria_emoji
             FROM fichas f
             LEFT JOIN categorias c ON c.id = f.categoria_id
             WHERE f.activo = 1 AND f.eliminado = 0 AND f.categoria_id = ?
             ORDER BY f.destacado DESC, f.nombre ASC
             LIMIT ? OFFSET ?"
        );
        $stmt->execute([$categoriaId, $limit, $offset]);
        return $stmt->fetchAll();
    }

    public function countByCategoria(int $categoriaId): int
    {
        $stmt = $this->db->prepare(
            "SELECT COUNT(*) FROM fichas WHERE activo = 1 AND eliminado = 0 AND categoria_id = ?"
        );
        $stmt->execute([$categoriaId]);
        return (int)$stmt->fetchColumn();
    }

    public function getRelacionadas(int $fichaId, int $categoriaId, int $limit = 4): array
    {
        $stmt = $this->db->prepare(
            "SELECT f.*, c.nombre AS categoria_nombre, c.slug AS categoria_slug
             FROM fichas f
             LEFT JOIN categorias c ON c.id = f.categoria_id
             WHERE f.activo = 1 AND f.eliminado = 0 AND f.categoria_id = ? AND f.id != ?
             ORDER BY RAND()
             LIMIT ?"
        );
        $stmt->execute([$categoriaId, $fichaId, $limit]);
        return $stmt->fetchAll();
    }

    public function getAllParaMapa(): array
    {
        return $this->db->query(
            "SELECT f.id, f.nombre, f.slug, f.descripcion_corta, f.latitud, f.longitud, f.categoria_id,
                    c.nombre AS categoria_nombre, c.emoji AS categoria_emoji
             FROM fichas f
             LEFT JOIN categorias c ON c.id = f.categoria_id
             WHERE f.activo = 1 AND f.eliminado = 0 AND f.latitud IS NOT NULL AND f.longitud IS NOT NULL"
        )->fetchAll();
    }
}
