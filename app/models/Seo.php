<?php
/**
 * Modelo Seo — visitapurranque.cl
 * Análisis SEO, configuración y estadísticas de compartidos
 */
class Seo
{
    private PDO $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    // ── Cobertura SEO por tabla ────────────────────────────

    public function cobertura(): array
    {
        $tablas = [
            'fichas' => ['tabla' => 'fichas', 'where' => 'eliminado = 0', 'label' => 'Fichas'],
            'blog'   => ['tabla' => 'blog_posts', 'where' => 'eliminado = 0', 'label' => 'Blog Posts'],
            'eventos' => ['tabla' => 'eventos', 'where' => 'eliminado = 0', 'label' => 'Eventos'],
            'categorias' => ['tabla' => 'categorias', 'where' => '1=1', 'label' => 'Categorías'],
            'paginas' => ['tabla' => 'paginas', 'where' => '1=1', 'label' => 'Páginas'],
        ];

        $result = [];
        foreach ($tablas as $key => $t) {
            $total = (int)$this->db->query(
                "SELECT COUNT(*) FROM {$t['tabla']} WHERE {$t['where']}"
            )->fetchColumn();

            $conTitle = (int)$this->db->query(
                "SELECT COUNT(*) FROM {$t['tabla']} WHERE {$t['where']} AND meta_title IS NOT NULL AND meta_title != ''"
            )->fetchColumn();

            $conDesc = (int)$this->db->query(
                "SELECT COUNT(*) FROM {$t['tabla']} WHERE {$t['where']} AND meta_description IS NOT NULL AND meta_description != ''"
            )->fetchColumn();

            $result[$key] = [
                'label'     => $t['label'],
                'total'     => $total,
                'con_title' => $conTitle,
                'con_desc'  => $conDesc,
                'pct_title' => $total > 0 ? round($conTitle * 100 / $total) : 0,
                'pct_desc'  => $total > 0 ? round($conDesc * 100 / $total) : 0,
            ];
        }

        return $result;
    }

    // ── Registros sin SEO (para acción rápida) ─────────────

    public function sinMeta(string $tabla, int $limit = 20): array
    {
        $config = [
            'fichas'   => ['tabla' => 'fichas', 'campos' => 'id, nombre, slug', 'where' => 'eliminado = 0', 'url' => '/admin/fichas/{id}/editar'],
            'blog'     => ['tabla' => 'blog_posts', 'campos' => 'id, titulo AS nombre, slug', 'where' => 'eliminado = 0', 'url' => '/admin/blog/{id}/editar'],
            'eventos'  => ['tabla' => 'eventos', 'campos' => 'id, titulo AS nombre, slug', 'where' => 'eliminado = 0', 'url' => '/admin/eventos/{id}/editar'],
            'categorias' => ['tabla' => 'categorias', 'campos' => 'id, nombre, slug', 'where' => '1=1', 'url' => '/admin/categorias/{id}/editar'],
            'paginas'  => ['tabla' => 'paginas', 'campos' => 'id, titulo AS nombre, slug', 'where' => '1=1', 'url' => '#'],
        ];

        if (!isset($config[$tabla])) return [];

        $c = $config[$tabla];
        $rows = $this->db->query(
            "SELECT {$c['campos']} FROM {$c['tabla']}
             WHERE {$c['where']} AND (meta_title IS NULL OR meta_title = '' OR meta_description IS NULL OR meta_description = '')
             ORDER BY id DESC LIMIT {$limit}"
        )->fetchAll();

        foreach ($rows as &$row) {
            $row['edit_url'] = str_replace('{id}', $row['id'], $c['url']);
        }

        return $rows;
    }

    // ── Configuración SEO/Redes ────────────────────────────

    public function getConfig(string $grupo): array
    {
        $stmt = $this->db->prepare(
            "SELECT clave, valor, tipo, descripcion FROM configuracion WHERE grupo = ? ORDER BY clave"
        );
        $stmt->execute([$grupo]);
        return $stmt->fetchAll();
    }

    public function saveConfig(string $clave, string $valor): bool
    {
        $stmt = $this->db->prepare(
            "UPDATE configuracion SET valor = ? WHERE clave = ?"
        );
        return $stmt->execute([$valor, $clave]);
    }

    // ── Compartidos (estadísticas redes sociales) ──────────

    public function compartidosPorRed(int $dias = 30): array
    {
        $stmt = $this->db->prepare(
            "SELECT red_social, COUNT(*) AS total
             FROM compartidos
             WHERE created_at >= DATE_SUB(NOW(), INTERVAL ? DAY)
             GROUP BY red_social
             ORDER BY total DESC"
        );
        $stmt->execute([$dias]);
        return $stmt->fetchAll();
    }

    public function compartidosPorTipo(int $dias = 30): array
    {
        $stmt = $this->db->prepare(
            "SELECT tipo, COUNT(*) AS total
             FROM compartidos
             WHERE created_at >= DATE_SUB(NOW(), INTERVAL ? DAY)
             GROUP BY tipo
             ORDER BY total DESC"
        );
        $stmt->execute([$dias]);
        return $stmt->fetchAll();
    }

    public function compartidosPorDia(int $dias = 30): array
    {
        $stmt = $this->db->prepare(
            "SELECT DATE(created_at) AS fecha, COUNT(*) AS total
             FROM compartidos
             WHERE created_at >= DATE_SUB(NOW(), INTERVAL ? DAY)
             GROUP BY fecha
             ORDER BY fecha ASC"
        );
        $stmt->execute([$dias]);
        return $stmt->fetchAll();
    }

    public function topCompartidos(int $dias = 30, int $limit = 10): array
    {
        $stmt = $this->db->prepare(
            "SELECT c.tipo, c.registro_id, COUNT(*) AS total,
                    CASE c.tipo
                        WHEN 'ficha' THEN (SELECT nombre FROM fichas WHERE id = c.registro_id)
                        WHEN 'blog_post' THEN (SELECT titulo FROM blog_posts WHERE id = c.registro_id)
                        WHEN 'evento' THEN (SELECT titulo FROM eventos WHERE id = c.registro_id)
                        ELSE CONCAT('#', c.registro_id)
                    END AS nombre
             FROM compartidos c
             WHERE c.created_at >= DATE_SUB(NOW(), INTERVAL ? DAY)
             GROUP BY c.tipo, c.registro_id
             ORDER BY total DESC
             LIMIT {$limit}"
        );
        $stmt->execute([$dias]);
        return $stmt->fetchAll();
    }

    public function totalCompartidos(int $dias = 30): int
    {
        $stmt = $this->db->prepare(
            "SELECT COUNT(*) FROM compartidos WHERE created_at >= DATE_SUB(NOW(), INTERVAL ? DAY)"
        );
        $stmt->execute([$dias]);
        return (int)$stmt->fetchColumn();
    }
}
