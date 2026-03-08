<?php

class SeoController extends Controller
{
    /** GET /sitemap.xml */
    public function sitemap(): void
    {
        header('Content-Type: application/xml; charset=UTF-8');

        $base = SITE_URL;
        $now  = date('Y-m-d');

        $urls = [];

        // Páginas estáticas
        $urls[] = ['loc' => '/',          'lastmod' => $now, 'changefreq' => 'daily',   'priority' => '1.0'];
        $urls[] = ['loc' => '/categorias','lastmod' => $now, 'changefreq' => 'weekly',  'priority' => '0.8'];
        $urls[] = ['loc' => '/eventos',   'lastmod' => $now, 'changefreq' => 'weekly',  'priority' => '0.7'];
        $urls[] = ['loc' => '/blog',      'lastmod' => $now, 'changefreq' => 'daily',   'priority' => '0.7'];
        $urls[] = ['loc' => '/mapa',      'lastmod' => $now, 'changefreq' => 'weekly',  'priority' => '0.7'];
        $urls[] = ['loc' => '/contacto',  'lastmod' => $now, 'changefreq' => 'monthly', 'priority' => '0.5'];
        $urls[] = ['loc' => '/faq',       'lastmod' => $now, 'changefreq' => 'monthly', 'priority' => '0.5'];

        // Categorías activas
        $rows = $this->db->query(
            "SELECT slug, updated_at FROM categorias WHERE activo = 1 ORDER BY nombre"
        )->fetchAll();
        foreach ($rows as $r) {
            $urls[] = [
                'loc'        => '/categoria/' . $r['slug'],
                'lastmod'    => substr($r['updated_at'] ?? $now, 0, 10),
                'changefreq' => 'weekly',
                'priority'   => '0.7',
            ];
        }

        // Fichas publicadas
        $rows = $this->db->query(
            "SELECT slug, updated_at FROM fichas WHERE activo = 1 AND eliminado = 0 ORDER BY nombre"
        )->fetchAll();
        foreach ($rows as $r) {
            $urls[] = [
                'loc'        => '/atractivo/' . $r['slug'],
                'lastmod'    => substr($r['updated_at'] ?? $now, 0, 10),
                'changefreq' => 'weekly',
                'priority'   => '0.8',
            ];
        }

        // Eventos publicados
        $rows = $this->db->query(
            "SELECT slug, updated_at FROM eventos WHERE activo = 1 AND eliminado = 0 ORDER BY fecha_inicio DESC"
        )->fetchAll();
        foreach ($rows as $r) {
            $urls[] = [
                'loc'        => '/evento/' . $r['slug'],
                'lastmod'    => substr($r['updated_at'] ?? $now, 0, 10),
                'changefreq' => 'weekly',
                'priority'   => '0.6',
            ];
        }

        // Blog posts publicados
        $rows = $this->db->query(
            "SELECT slug, updated_at FROM blog_posts WHERE estado = 'publicado' AND eliminado = 0 ORDER BY publicado_at DESC"
        )->fetchAll();
        foreach ($rows as $r) {
            $urls[] = [
                'loc'        => '/blog/' . $r['slug'],
                'lastmod'    => substr($r['updated_at'] ?? $now, 0, 10),
                'changefreq' => 'monthly',
                'priority'   => '0.6',
            ];
        }

        // Páginas estáticas de BD
        $rows = $this->db->query(
            "SELECT slug, updated_at FROM paginas WHERE activo = 1 ORDER BY titulo"
        )->fetchAll();
        foreach ($rows as $r) {
            $urls[] = [
                'loc'        => '/' . $r['slug'],
                'lastmod'    => substr($r['updated_at'] ?? $now, 0, 10),
                'changefreq' => 'monthly',
                'priority'   => '0.3',
            ];
        }

        echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";
        foreach ($urls as $u) {
            echo "  <url>\n";
            echo "    <loc>" . htmlspecialchars($base . $u['loc']) . "</loc>\n";
            echo "    <lastmod>" . $u['lastmod'] . "</lastmod>\n";
            echo "    <changefreq>" . $u['changefreq'] . "</changefreq>\n";
            echo "    <priority>" . $u['priority'] . "</priority>\n";
            echo "  </url>\n";
        }
        echo '</urlset>';
        exit;
    }

    /** GET /blog/feed */
    public function rss(): void
    {
        header('Content-Type: application/rss+xml; charset=UTF-8');

        $base = SITE_URL;

        $posts = $this->db->query(
            "SELECT bp.titulo, bp.slug, bp.extracto, bp.publicado_at, bp.updated_at,
                    u.nombre AS autor_nombre
             FROM blog_posts bp
             LEFT JOIN usuarios u ON u.id = bp.autor_id
             WHERE bp.estado = 'publicado' AND bp.eliminado = 0
             ORDER BY bp.publicado_at DESC
             LIMIT 20"
        )->fetchAll();

        echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        echo '<rss version="2.0" xmlns:atom="http://www.w3.org/2005/Atom">' . "\n";
        echo "<channel>\n";
        echo "  <title>" . htmlspecialchars(SITE_NAME . ' — Blog') . "</title>\n";
        echo "  <link>" . htmlspecialchars($base . '/blog') . "</link>\n";
        echo "  <description>" . htmlspecialchars(SITE_DESCRIPTION) . "</description>\n";
        echo "  <language>es-cl</language>\n";
        echo "  <lastBuildDate>" . date('r') . "</lastBuildDate>\n";
        echo '  <atom:link href="' . htmlspecialchars($base . '/blog/feed') . '" rel="self" type="application/rss+xml"/>' . "\n";

        foreach ($posts as $p) {
            $pubDate = date('r', strtotime($p['publicado_at']));
            echo "  <item>\n";
            echo "    <title>" . htmlspecialchars($p['titulo']) . "</title>\n";
            echo "    <link>" . htmlspecialchars($base . '/blog/' . $p['slug']) . "</link>\n";
            echo "    <guid isPermaLink=\"true\">" . htmlspecialchars($base . '/blog/' . $p['slug']) . "</guid>\n";
            echo "    <pubDate>" . $pubDate . "</pubDate>\n";
            if (!empty($p['autor_nombre'])) {
                echo "    <author>" . htmlspecialchars($p['autor_nombre']) . "</author>\n";
            }
            if (!empty($p['extracto'])) {
                echo "    <description>" . htmlspecialchars($p['extracto']) . "</description>\n";
            }
            echo "  </item>\n";
        }

        echo "</channel>\n";
        echo '</rss>';
        exit;
    }
}
