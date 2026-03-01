<?php
/**
 * Modelo Reporte — visitapurranque.cl
 * Consultas agregadas para el módulo de estadísticas/reportes
 */
class Reporte
{
    private PDO $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    // ── KPIs generales ─────────────────────────────────────

    public function kpis(): array
    {
        return [
            'fichas_activas' => (int)$this->db->query(
                "SELECT COUNT(*) FROM fichas WHERE activo = 1 AND eliminado = 0"
            )->fetchColumn(),

            'fichas_total' => (int)$this->db->query(
                "SELECT COUNT(*) FROM fichas WHERE eliminado = 0"
            )->fetchColumn(),

            'categorias' => (int)$this->db->query(
                "SELECT COUNT(*) FROM categorias WHERE activo = 1"
            )->fetchColumn(),

            'eventos_proximos' => (int)$this->db->query(
                "SELECT COUNT(*) FROM eventos WHERE fecha_inicio > NOW() AND activo = 1 AND eliminado = 0"
            )->fetchColumn(),

            'resenas_total' => (int)$this->db->query(
                "SELECT COUNT(*) FROM resenas"
            )->fetchColumn(),

            'resenas_pendientes' => (int)$this->db->query(
                "SELECT COUNT(*) FROM resenas WHERE estado = 'pendiente'"
            )->fetchColumn(),

            'rating_promedio' => (float)$this->db->query(
                "SELECT COALESCE(AVG(rating), 0) FROM resenas WHERE estado = 'aprobada'"
            )->fetchColumn(),

            'mensajes_total' => (int)$this->db->query(
                "SELECT COUNT(*) FROM contacto_mensajes"
            )->fetchColumn(),

            'mensajes_no_leidos' => (int)$this->db->query(
                "SELECT COUNT(*) FROM contacto_mensajes WHERE leido = 0"
            )->fetchColumn(),

            'posts_publicados' => (int)$this->db->query(
                "SELECT COUNT(*) FROM blog_posts WHERE estado = 'publicado' AND eliminado = 0"
            )->fetchColumn(),

            'suscripciones_activas' => (int)$this->db->query(
                "SELECT COUNT(*) FROM suscripciones WHERE estado = 'activa'"
            )->fetchColumn(),

            'banners_activos' => (int)$this->db->query(
                "SELECT COUNT(*) FROM banners WHERE activo = 1"
            )->fetchColumn(),
        ];
    }

    // ── Visitas por día (últimos N días) ───────────────────

    public function visitasPorDia(int $dias = 30): array
    {
        $stmt = $this->db->prepare(
            "SELECT fecha,
                    SUM(vistas) AS vistas,
                    SUM(clics_telefono) AS clics_telefono,
                    SUM(clics_whatsapp) AS clics_whatsapp,
                    SUM(clics_mapa) AS clics_mapa,
                    SUM(clics_web) AS clics_web,
                    SUM(compartidos) AS compartidos
             FROM estadisticas
             WHERE fecha >= DATE_SUB(CURDATE(), INTERVAL ? DAY)
             GROUP BY fecha
             ORDER BY fecha ASC"
        );
        $stmt->execute([$dias]);
        return $stmt->fetchAll();
    }

    // ── Top fichas por vistas ──────────────────────────────

    public function topFichas(int $limit = 10, int $dias = 30): array
    {
        $stmt = $this->db->prepare(
            "SELECT f.id, f.nombre,
                    SUM(e.vistas) AS vistas,
                    SUM(e.clics_telefono + e.clics_whatsapp + e.clics_mapa + e.clics_web) AS clics,
                    SUM(e.compartidos) AS compartidos
             FROM estadisticas e
             JOIN fichas f ON f.id = e.ficha_id
             WHERE e.fecha >= DATE_SUB(CURDATE(), INTERVAL ? DAY)
               AND f.eliminado = 0
             GROUP BY f.id, f.nombre
             ORDER BY vistas DESC
             LIMIT {$limit}"
        );
        $stmt->execute([$dias]);
        return $stmt->fetchAll();
    }

    // ── Distribución de reseñas por rating ─────────────────

    public function resenasPorRating(): array
    {
        $rows = $this->db->query(
            "SELECT rating, COUNT(*) AS total
             FROM resenas
             WHERE estado = 'aprobada'
             GROUP BY rating
             ORDER BY rating ASC"
        )->fetchAll();

        $dist = [1 => 0, 2 => 0, 3 => 0, 4 => 0, 5 => 0];
        foreach ($rows as $r) {
            $dist[(int)$r['rating']] = (int)$r['total'];
        }
        return $dist;
    }

    // ── Reseñas por mes (últimos 12 meses) ─────────────────

    public function resenasPorMes(int $meses = 12): array
    {
        $stmt = $this->db->prepare(
            "SELECT DATE_FORMAT(created_at, '%Y-%m') AS mes,
                    COUNT(*) AS total,
                    AVG(rating) AS rating_promedio
             FROM resenas
             WHERE created_at >= DATE_SUB(CURDATE(), INTERVAL ? MONTH)
             GROUP BY mes
             ORDER BY mes ASC"
        );
        $stmt->execute([$meses]);
        return $stmt->fetchAll();
    }

    // ── Banners: rendimiento CTR ───────────────────────────

    public function bannersRendimiento(): array
    {
        return $this->db->query(
            "SELECT id, titulo, posicion, variante, impresiones, clics,
                    CASE WHEN impresiones > 0
                         THEN ROUND(clics * 100.0 / impresiones, 2)
                         ELSE 0 END AS ctr
             FROM banners
             WHERE activo = 1
             ORDER BY ctr DESC"
        )->fetchAll();
    }

    // ── Mensajes por mes ───────────────────────────────────

    public function mensajesPorMes(int $meses = 12): array
    {
        $stmt = $this->db->prepare(
            "SELECT DATE_FORMAT(created_at, '%Y-%m') AS mes,
                    COUNT(*) AS total,
                    SUM(respondido) AS respondidos
             FROM contacto_mensajes
             WHERE created_at >= DATE_SUB(CURDATE(), INTERVAL ? MONTH)
             GROUP BY mes
             ORDER BY mes ASC"
        );
        $stmt->execute([$meses]);
        return $stmt->fetchAll();
    }

    // ── Actividad audit_log por módulo ─────────────────────

    public function actividadPorModulo(int $dias = 30): array
    {
        $stmt = $this->db->prepare(
            "SELECT modulo, COUNT(*) AS total
             FROM audit_log
             WHERE created_at >= DATE_SUB(NOW(), INTERVAL ? DAY)
             GROUP BY modulo
             ORDER BY total DESC"
        );
        $stmt->execute([$dias]);
        return $stmt->fetchAll();
    }

    // ── Fichas por categoría ───────────────────────────────

    public function fichasPorCategoria(): array
    {
        return $this->db->query(
            "SELECT c.nombre, COUNT(f.id) AS total
             FROM categorias c
             LEFT JOIN fichas f ON f.categoria_id = c.id AND f.eliminado = 0
             WHERE c.activo = 1
             GROUP BY c.id, c.nombre
             ORDER BY total DESC"
        )->fetchAll();
    }

    // ── CSV exports ────────────────────────────────────────

    public function csvVisitas(int $dias = 30): array
    {
        $stmt = $this->db->prepare(
            "SELECT e.fecha, f.nombre AS ficha, e.vistas,
                    e.clics_telefono, e.clics_whatsapp, e.clics_mapa,
                    e.clics_web, e.compartidos
             FROM estadisticas e
             JOIN fichas f ON f.id = e.ficha_id
             WHERE e.fecha >= DATE_SUB(CURDATE(), INTERVAL ? DAY)
             ORDER BY e.fecha DESC, e.vistas DESC"
        );
        $stmt->execute([$dias]);
        return $stmt->fetchAll();
    }

    public function csvResenas(): array
    {
        return $this->db->query(
            "SELECT r.id, r.nombre, r.email, r.rating, r.tipo_experiencia,
                    r.estado, f.nombre AS ficha, r.comentario,
                    r.created_at
             FROM resenas r
             LEFT JOIN fichas f ON f.id = r.ficha_id
             ORDER BY r.created_at DESC"
        )->fetchAll();
    }

    public function csvMensajes(): array
    {
        return $this->db->query(
            "SELECT id, nombre, email, telefono, asunto, mensaje,
                    leido, respondido, created_at
             FROM contacto_mensajes
             ORDER BY created_at DESC"
        )->fetchAll();
    }

    public function csvFichas(): array
    {
        return $this->db->query(
            "SELECT f.id, f.nombre, f.slug, c.nombre AS categoria,
                    f.direccion, f.telefono, f.whatsapp, f.activo,
                    f.created_at,
                    COALESCE(SUM(e.vistas), 0) AS total_vistas,
                    COALESCE(SUM(e.clics_telefono + e.clics_whatsapp + e.clics_mapa + e.clics_web), 0) AS total_clics
             FROM fichas f
             LEFT JOIN categorias c ON c.id = f.categoria_id
             LEFT JOIN estadisticas e ON e.ficha_id = f.id
             WHERE f.eliminado = 0
             GROUP BY f.id
             ORDER BY total_vistas DESC"
        )->fetchAll();
    }
}
