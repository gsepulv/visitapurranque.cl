<?php
/**
 * Modelo BlogPost — visitapurranque.cl
 * Posts del blog: noticias, artículos, guías, opinión
 */
class BlogPost
{
    private PDO $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    /**
     * Listar posts con filtros y paginación
     */
    public function getAll(array $filtros = [], int $pagina = 1, int $porPagina = ADMIN_PER_PAGE): array
    {
        $where = ['p.eliminado = 0'];
        $params = [];

        if (!empty($filtros['categoria_id'])) {
            $where[] = 'p.categoria_id = ?';
            $params[] = (int)$filtros['categoria_id'];
        }

        if (!empty($filtros['estado'])) {
            $where[] = 'p.estado = ?';
            $params[] = $filtros['estado'];
        }

        if (!empty($filtros['tipo'])) {
            $where[] = 'p.tipo = ?';
            $params[] = $filtros['tipo'];
        }

        if (!empty($filtros['autor_id'])) {
            $where[] = 'p.autor_id = ?';
            $params[] = (int)$filtros['autor_id'];
        }

        if (!empty($filtros['q'])) {
            $where[] = '(p.titulo LIKE ? OR p.extracto LIKE ?)';
            $params[] = '%' . $filtros['q'] . '%';
            $params[] = '%' . $filtros['q'] . '%';
        }

        $whereSql = implode(' AND ', $where);
        $offset = ($pagina - 1) * $porPagina;

        $sql = "SELECT p.*, c.nombre AS categoria_nombre, a.nombre AS autor_nombre
                FROM blog_posts p
                LEFT JOIN blog_categorias c ON c.id = p.categoria_id
                LEFT JOIN blog_autores a ON a.id = p.autor_id
                WHERE {$whereSql}
                ORDER BY p.created_at DESC
                LIMIT {$porPagina} OFFSET {$offset}";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    /**
     * Contar posts con filtros
     */
    public function count(array $filtros = []): int
    {
        $where = ['p.eliminado = 0'];
        $params = [];

        if (!empty($filtros['categoria_id'])) {
            $where[] = 'p.categoria_id = ?';
            $params[] = (int)$filtros['categoria_id'];
        }

        if (!empty($filtros['estado'])) {
            $where[] = 'p.estado = ?';
            $params[] = $filtros['estado'];
        }

        if (!empty($filtros['tipo'])) {
            $where[] = 'p.tipo = ?';
            $params[] = $filtros['tipo'];
        }

        if (!empty($filtros['autor_id'])) {
            $where[] = 'p.autor_id = ?';
            $params[] = (int)$filtros['autor_id'];
        }

        if (!empty($filtros['q'])) {
            $where[] = '(p.titulo LIKE ? OR p.extracto LIKE ?)';
            $params[] = '%' . $filtros['q'] . '%';
            $params[] = '%' . $filtros['q'] . '%';
        }

        $whereSql = implode(' AND ', $where);

        $stmt = $this->db->prepare("SELECT COUNT(*) FROM blog_posts p WHERE {$whereSql}");
        $stmt->execute($params);
        return (int)$stmt->fetchColumn();
    }

    /**
     * Obtener post por ID
     */
    public function getById(int $id): ?array
    {
        $stmt = $this->db->prepare(
            "SELECT p.*, c.nombre AS categoria_nombre, a.nombre AS autor_nombre
             FROM blog_posts p
             LEFT JOIN blog_categorias c ON c.id = p.categoria_id
             LEFT JOIN blog_autores a ON a.id = p.autor_id
             WHERE p.id = ? AND p.eliminado = 0
             LIMIT 1"
        );
        $stmt->execute([$id]);
        $post = $stmt->fetch();
        return $post ?: null;
    }

    /**
     * Crear post
     */
    public function create(array $data): int
    {
        $stmt = $this->db->prepare(
            "INSERT INTO blog_posts (titulo, slug, extracto, contenido, imagen_portada,
                    tipo, categoria_id, autor_id, fuente_nombre, fuente_url,
                    estado, publicado_at, programado_at, destacado, permite_comentarios,
                    tiempo_lectura, meta_title, meta_description)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)"
        );
        $stmt->execute([
            $data['titulo'],
            $data['slug'],
            $data['extracto'] ?: null,
            $data['contenido'],
            $data['imagen_portada'] ?: null,
            $data['tipo'],
            $data['categoria_id'] ?: null,
            $data['autor_id'] ?: null,
            $data['fuente_nombre'] ?: null,
            $data['fuente_url'] ?: null,
            $data['estado'],
            $data['estado'] === 'publicado' ? date('Y-m-d H:i:s') : null,
            $data['programado_at'] ?: null,
            (int)($data['destacado'] ?? 0),
            (int)($data['permite_comentarios'] ?? 1),
            $this->calcularTiempoLectura($data['contenido']),
            $data['meta_title'] ?: null,
            $data['meta_description'] ?: null,
        ]);
        return (int)$this->db->lastInsertId();
    }

    /**
     * Actualizar post
     */
    public function update(int $id, array $data): bool
    {
        // Si cambia a publicado y no tenía fecha, setearla
        $post = $this->getById($id);
        $publicadoAt = $post['publicado_at'] ?? null;
        if ($data['estado'] === 'publicado' && !$publicadoAt) {
            $publicadoAt = date('Y-m-d H:i:s');
        }

        $stmt = $this->db->prepare(
            "UPDATE blog_posts SET titulo=?, slug=?, extracto=?, contenido=?, imagen_portada=?,
                    tipo=?, categoria_id=?, autor_id=?, fuente_nombre=?, fuente_url=?,
                    estado=?, publicado_at=?, programado_at=?, destacado=?, permite_comentarios=?,
                    tiempo_lectura=?, meta_title=?, meta_description=?
             WHERE id = ? AND eliminado = 0"
        );
        return $stmt->execute([
            $data['titulo'],
            $data['slug'],
            $data['extracto'] ?: null,
            $data['contenido'],
            $data['imagen_portada'] ?: null,
            $data['tipo'],
            $data['categoria_id'] ?: null,
            $data['autor_id'] ?: null,
            $data['fuente_nombre'] ?: null,
            $data['fuente_url'] ?: null,
            $data['estado'],
            $publicadoAt,
            $data['programado_at'] ?: null,
            (int)($data['destacado'] ?? 0),
            (int)($data['permite_comentarios'] ?? 1),
            $this->calcularTiempoLectura($data['contenido']),
            $data['meta_title'] ?: null,
            $data['meta_description'] ?: null,
            $id,
        ]);
    }

    /**
     * Soft delete
     */
    public function softDelete(int $id): bool
    {
        $stmt = $this->db->prepare(
            "UPDATE blog_posts SET eliminado = 1, eliminado_at = NOW(), estado = 'archivado' WHERE id = ? AND eliminado = 0"
        );
        return $stmt->execute([$id]);
    }

    /**
     * Verificar si slug ya existe
     */
    public function slugExists(string $slug, ?int $excludeId = null): bool
    {
        $sql = "SELECT COUNT(*) FROM blog_posts WHERE slug = ? AND eliminado = 0";
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
     * Contar posts publicados
     */
    public function countPublicados(): int
    {
        return (int)$this->db->query(
            "SELECT COUNT(*) FROM blog_posts WHERE estado = 'publicado' AND eliminado = 0"
        )->fetchColumn();
    }

    /**
     * Calcular tiempo de lectura (~200 palabras/minuto)
     */
    private function calcularTiempoLectura(string $contenido): int
    {
        $palabras = str_word_count(strip_tags($contenido));
        return max(1, (int)ceil($palabras / 200));
    }
}
