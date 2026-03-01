<?php
/**
 * Modelo Categoria — visitapurranque.cl
 * Categorías y subcategorías de atractivos turísticos
 */
class Categoria
{
    private PDO $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    /**
     * Listar categorías con filtros y paginación
     */
    public function getAll(array $filtros = [], int $pagina = 1, int $porPagina = ADMIN_PER_PAGE): array
    {
        $where = ['1=1'];
        $params = [];

        if (isset($filtros['activo']) && $filtros['activo'] !== '') {
            $where[] = 'c.activo = ?';
            $params[] = (int)$filtros['activo'];
        }

        if (!empty($filtros['q'])) {
            $where[] = '(c.nombre LIKE ? OR c.descripcion LIKE ?)';
            $params[] = '%' . $filtros['q'] . '%';
            $params[] = '%' . $filtros['q'] . '%';
        }

        $whereSql = implode(' AND ', $where);
        $offset = ($pagina - 1) * $porPagina;

        $sql = "SELECT c.*,
                    (SELECT COUNT(*) FROM subcategorias s WHERE s.categoria_id = c.id) AS total_subcategorias,
                    (SELECT COUNT(*) FROM fichas f WHERE f.categoria_id = c.id AND f.eliminado = 0) AS total_fichas
                FROM categorias c
                WHERE {$whereSql}
                ORDER BY c.orden ASC, c.nombre ASC
                LIMIT {$porPagina} OFFSET {$offset}";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    /**
     * Contar categorías con filtros
     */
    public function count(array $filtros = []): int
    {
        $where = ['1=1'];
        $params = [];

        if (isset($filtros['activo']) && $filtros['activo'] !== '') {
            $where[] = 'c.activo = ?';
            $params[] = (int)$filtros['activo'];
        }

        if (!empty($filtros['q'])) {
            $where[] = '(c.nombre LIKE ? OR c.descripcion LIKE ?)';
            $params[] = '%' . $filtros['q'] . '%';
            $params[] = '%' . $filtros['q'] . '%';
        }

        $whereSql = implode(' AND ', $where);

        $stmt = $this->db->prepare("SELECT COUNT(*) FROM categorias c WHERE {$whereSql}");
        $stmt->execute($params);
        return (int)$stmt->fetchColumn();
    }

    /**
     * Obtener categoría por ID con subcategorías
     */
    public function getById(int $id): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM categorias WHERE id = ? LIMIT 1");
        $stmt->execute([$id]);
        $cat = $stmt->fetch();
        return $cat ?: null;
    }

    /**
     * Crear categoría
     */
    public function create(array $data): int
    {
        $stmt = $this->db->prepare(
            "INSERT INTO categorias (nombre, slug, descripcion, emoji, icono, imagen, color, meta_title, meta_description, orden, activo)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)"
        );
        $stmt->execute([
            $data['nombre'],
            $data['slug'],
            $data['descripcion'] ?: null,
            $data['emoji'] ?: null,
            $data['icono'] ?: null,
            $data['imagen'] ?: null,
            $data['color'] ?: '#3b82f6',
            $data['meta_title'] ?: null,
            $data['meta_description'] ?: null,
            (int)($data['orden'] ?? 0),
            (int)($data['activo'] ?? 1),
        ]);
        return (int)$this->db->lastInsertId();
    }

    /**
     * Actualizar categoría
     */
    public function update(int $id, array $data): bool
    {
        $stmt = $this->db->prepare(
            "UPDATE categorias SET nombre=?, slug=?, descripcion=?, emoji=?, icono=?, imagen=?, color=?,
                    meta_title=?, meta_description=?, orden=?, activo=?
             WHERE id = ?"
        );
        return $stmt->execute([
            $data['nombre'],
            $data['slug'],
            $data['descripcion'] ?: null,
            $data['emoji'] ?: null,
            $data['icono'] ?: null,
            $data['imagen'] ?: null,
            $data['color'] ?: '#3b82f6',
            $data['meta_title'] ?: null,
            $data['meta_description'] ?: null,
            (int)($data['orden'] ?? 0),
            (int)($data['activo'] ?? 1),
            $id,
        ]);
    }

    /**
     * Eliminar categoría (solo si no tiene fichas asociadas)
     */
    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare(
            "DELETE FROM categorias WHERE id = ? AND NOT EXISTS (SELECT 1 FROM fichas WHERE categoria_id = ? AND eliminado = 0)"
        );
        $stmt->execute([$id, $id]);
        return $stmt->rowCount() > 0;
    }

    /**
     * Toggle activo/inactivo
     */
    public function toggleActivo(int $id): bool
    {
        $stmt = $this->db->prepare("UPDATE categorias SET activo = NOT activo WHERE id = ?");
        return $stmt->execute([$id]);
    }

    /**
     * Verificar si slug ya existe
     */
    public function slugExists(string $slug, ?int $excludeId = null): bool
    {
        $sql = "SELECT COUNT(*) FROM categorias WHERE slug = ?";
        $params = [$slug];

        if ($excludeId) {
            $sql .= " AND id != ?";
            $params[] = $excludeId;
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return (int)$stmt->fetchColumn() > 0;
    }

    // ── Subcategorías ────────────────────────────────────

    /**
     * Obtener subcategorías de una categoría
     */
    public function getSubcategorias(int $categoriaId): array
    {
        $stmt = $this->db->prepare(
            "SELECT s.*,
                    (SELECT COUNT(*) FROM fichas f WHERE f.subcategoria_id = s.id AND f.eliminado = 0) AS total_fichas
             FROM subcategorias s
             WHERE s.categoria_id = ?
             ORDER BY s.orden ASC, s.nombre ASC"
        );
        $stmt->execute([$categoriaId]);
        return $stmt->fetchAll();
    }

    /**
     * Obtener subcategoría por ID
     */
    public function getSubcategoriaById(int $id): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM subcategorias WHERE id = ? LIMIT 1");
        $stmt->execute([$id]);
        $sub = $stmt->fetch();
        return $sub ?: null;
    }

    /**
     * Crear subcategoría
     */
    public function createSubcategoria(array $data): int
    {
        $stmt = $this->db->prepare(
            "INSERT INTO subcategorias (categoria_id, nombre, slug, descripcion, orden, activo)
             VALUES (?, ?, ?, ?, ?, ?)"
        );
        $stmt->execute([
            (int)$data['categoria_id'],
            $data['nombre'],
            $data['slug'],
            $data['descripcion'] ?: null,
            (int)($data['orden'] ?? 0),
            (int)($data['activo'] ?? 1),
        ]);
        return (int)$this->db->lastInsertId();
    }

    /**
     * Actualizar subcategoría
     */
    public function updateSubcategoria(int $id, array $data): bool
    {
        $stmt = $this->db->prepare(
            "UPDATE subcategorias SET nombre=?, slug=?, descripcion=?, orden=?, activo=? WHERE id = ?"
        );
        return $stmt->execute([
            $data['nombre'],
            $data['slug'],
            $data['descripcion'] ?: null,
            (int)($data['orden'] ?? 0),
            (int)($data['activo'] ?? 1),
            $id,
        ]);
    }

    /**
     * Eliminar subcategoría (solo si no tiene fichas asociadas)
     */
    public function deleteSubcategoria(int $id): bool
    {
        $stmt = $this->db->prepare(
            "DELETE FROM subcategorias WHERE id = ? AND NOT EXISTS (SELECT 1 FROM fichas WHERE subcategoria_id = ? AND eliminado = 0)"
        );
        $stmt->execute([$id, $id]);
        return $stmt->rowCount() > 0;
    }

    /**
     * Verificar si slug de subcategoría ya existe en la misma categoría
     */
    public function subcategoriaSlugExists(string $slug, int $categoriaId, ?int $excludeId = null): bool
    {
        $sql = "SELECT COUNT(*) FROM subcategorias WHERE slug = ? AND categoria_id = ?";
        $params = [$slug, $categoriaId];

        if ($excludeId) {
            $sql .= " AND id != ?";
            $params[] = $excludeId;
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return (int)$stmt->fetchColumn() > 0;
    }

    /**
     * Contar total de categorías activas
     */
    public function countActivas(): int
    {
        return (int)$this->db->query("SELECT COUNT(*) FROM categorias WHERE activo = 1")->fetchColumn();
    }
}
