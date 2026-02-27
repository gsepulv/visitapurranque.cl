<?php

class HomeController extends Controller
{
    public function index(): void
    {
        // Página "en construcción" — standalone, sin queries
        $this->renderStandalone('public/home');
    }

    /* ── Home completo (restaurar cuando el sitio esté listo) ──
    public function indexFull(): void
    {
        $categorias = $this->db->query(
            "SELECT c.*,
                    (SELECT COUNT(*) FROM fichas f
                     WHERE f.categoria_id = c.id AND f.activo = 1 AND f.eliminado = 0) AS total_fichas
             FROM categorias c
             WHERE c.activo = 1
             ORDER BY c.orden ASC"
        )->fetchAll();

        $destacados = $this->db->query(
            "SELECT f.*, c.nombre AS categoria_nombre, c.emoji AS categoria_emoji, c.color AS categoria_color
             FROM fichas f
             LEFT JOIN categorias c ON c.id = f.categoria_id
             WHERE f.destacado = 1 AND f.activo = 1 AND f.eliminado = 0
             ORDER BY f.updated_at DESC
             LIMIT 6"
        )->fetchAll();

        $proximoEvento = $this->db->query(
            "SELECT id, titulo, slug, fecha_inicio, lugar
             FROM eventos
             WHERE fecha_inicio > NOW() AND activo = 1 AND eliminado = 0
             ORDER BY fecha_inicio ASC
             LIMIT 1"
        )->fetch();

        $eventos = $this->db->query(
            "SELECT *
             FROM eventos
             WHERE fecha_inicio > NOW() AND activo = 1 AND eliminado = 0
             ORDER BY fecha_inicio ASC
             LIMIT 3"
        )->fetchAll();

        $posts = $this->db->query(
            "SELECT p.*, bc.nombre AS categoria_nombre, bc.emoji AS categoria_emoji
             FROM blog_posts p
             LEFT JOIN blog_categorias bc ON bc.id = p.categoria_id
             WHERE p.estado = 'publicado' AND p.eliminado = 0
             ORDER BY p.publicado_at DESC
             LIMIT 3"
        )->fetchAll();

        $this->render('public/home', [
            'pageTitle'      => SITE_NAME . ' — Guía turística de Purranque',
            'categorias'     => $categorias,
            'destacados'     => $destacados,
            'proximoEvento'  => $proximoEvento ?: null,
            'eventos'        => $eventos,
            'posts'          => $posts,
        ]);
    }
    */
}
