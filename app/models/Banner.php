<?php
/**
 * Modelo Banner — visitapurranque.cl
 * Banners publicitarios con A/B testing y stats CTR
 */
class Banner
{
    private PDO $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    /**
     * Listar banners con filtros y paginación
     */
    public function getAll(array $filtros = [], int $pagina = 1, int $porPagina = ADMIN_PER_PAGE): array
    {
        $where = ['1=1'];
        $params = [];

        if (!empty($filtros['posicion'])) {
            $where[] = 'posicion = ?';
            $params[] = $filtros['posicion'];
        }

        if ($filtros['activo'] !== '') {
            $where[] = 'activo = ?';
            $params[] = (int)$filtros['activo'];
        }

        if (!empty($filtros['variante'])) {
            $where[] = 'variante = ?';
            $params[] = $filtros['variante'];
        }

        if (!empty($filtros['q'])) {
            $where[] = 'titulo LIKE ?';
            $params[] = '%' . $filtros['q'] . '%';
        }

        $whereSql = implode(' AND ', $where);
        $offset = ($pagina - 1) * $porPagina;

        $sql = "SELECT * FROM banners
                WHERE {$whereSql}
                ORDER BY posicion, orden, id DESC
                LIMIT {$porPagina} OFFSET {$offset}";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    /**
     * Contar banners con filtros
     */
    public function count(array $filtros = []): int
    {
        $where = ['1=1'];
        $params = [];

        if (!empty($filtros['posicion'])) {
            $where[] = 'posicion = ?';
            $params[] = $filtros['posicion'];
        }

        if ($filtros['activo'] !== '') {
            $where[] = 'activo = ?';
            $params[] = (int)$filtros['activo'];
        }

        if (!empty($filtros['variante'])) {
            $where[] = 'variante = ?';
            $params[] = $filtros['variante'];
        }

        if (!empty($filtros['q'])) {
            $where[] = 'titulo LIKE ?';
            $params[] = '%' . $filtros['q'] . '%';
        }

        $whereSql = implode(' AND ', $where);

        $stmt = $this->db->prepare("SELECT COUNT(*) FROM banners WHERE {$whereSql}");
        $stmt->execute($params);
        return (int)$stmt->fetchColumn();
    }

    /**
     * Obtener banner por ID
     */
    public function getById(int $id): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM banners WHERE id = ? LIMIT 1");
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    /**
     * Crear banner
     */
    public function create(array $data): int
    {
        $stmt = $this->db->prepare(
            "INSERT INTO banners (titulo, imagen, imagen_mobile, url, posicion,
                    fecha_inicio, fecha_fin, variante, activo, orden)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)"
        );
        $stmt->execute([
            $data['titulo'],
            $data['imagen'],
            $data['imagen_mobile'] ?: null,
            $data['url'] ?: null,
            $data['posicion'],
            $data['fecha_inicio'] ?: null,
            $data['fecha_fin'] ?: null,
            $data['variante'] ?: 'A',
            (int)($data['activo'] ?? 1),
            (int)($data['orden'] ?? 0),
        ]);
        return (int)$this->db->lastInsertId();
    }

    /**
     * Actualizar banner
     */
    public function update(int $id, array $data): bool
    {
        $stmt = $this->db->prepare(
            "UPDATE banners SET titulo=?, imagen=?, imagen_mobile=?, url=?, posicion=?,
                    fecha_inicio=?, fecha_fin=?, variante=?, activo=?, orden=?
             WHERE id = ?"
        );
        return $stmt->execute([
            $data['titulo'],
            $data['imagen'],
            $data['imagen_mobile'] ?: null,
            $data['url'] ?: null,
            $data['posicion'],
            $data['fecha_inicio'] ?: null,
            $data['fecha_fin'] ?: null,
            $data['variante'] ?: 'A',
            (int)($data['activo'] ?? 1),
            (int)($data['orden'] ?? 0),
            $id,
        ]);
    }

    /**
     * Eliminar banner
     */
    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare("DELETE FROM banners WHERE id = ?");
        return $stmt->execute([$id]);
    }

    /**
     * Toggle activo
     */
    public function toggleActivo(int $id): bool
    {
        $stmt = $this->db->prepare("UPDATE banners SET activo = NOT activo WHERE id = ?");
        return $stmt->execute([$id]);
    }

    /**
     * Resetear estadísticas
     */
    public function resetStats(int $id): bool
    {
        $stmt = $this->db->prepare("UPDATE banners SET impresiones = 0, clics = 0 WHERE id = ?");
        return $stmt->execute([$id]);
    }

    /**
     * Posiciones disponibles
     */
    public static function posiciones(): array
    {
        return [
            'home_top'    => 'Home — Superior',
            'home_medio'  => 'Home — Medio',
            'sidebar'     => 'Sidebar',
            'footer'      => 'Footer',
            'blog_top'    => 'Blog — Superior',
            'ficha_lateral' => 'Ficha — Lateral',
        ];
    }
}
