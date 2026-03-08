<?php

class BlogController extends Controller
{
    public function index(): void
    {
        $blogModel = new BlogPost($this->db);

        $pagina = max(1, (int)($_GET['p'] ?? 1));
        $porPagina = 12;
        $total = $blogModel->countPublicados();
        $totalPaginas = max(1, (int)ceil($total / $porPagina));
        $pagina = min($pagina, $totalPaginas);
        $offset = ($pagina - 1) * $porPagina;

        $posts = $blogModel->getAllPublicados($porPagina, $offset);

        $this->render('public/blog/index', [
            'meta' => [
                'title'       => 'Blog — ' . SITE_NAME,
                'description' => 'Noticias, guías y artículos sobre turismo, cultura y gastronomía en Purranque.',
            ],
            'posts'           => $posts,
            'pagina'          => $pagina,
            'totalPaginas'    => $totalPaginas,
            'total'           => $total,
        ]);
    }

    public function show(string $slug): void
    {
        $blogModel = new BlogPost($this->db);
        $post = $blogModel->getBySlugPublico($slug);

        if (!$post) {
            http_response_code(404);
            $this->render('public/404', [
                'meta' => ['title' => 'Artículo no encontrado — ' . SITE_NAME],
            ]);
            return;
        }

        $blogModel->registrarVista((int)$post['id']);
        $relacionados = $blogModel->getRelacionados((int)$post['id'], $post['categoria_id'] ? (int)$post['categoria_id'] : null);

        $postImg = !empty($post['imagen_portada'])
            ? SITE_URL . '/uploads/' . $post['imagen_portada']
            : null;

        $this->render('public/blog/show', [
            'meta' => [
                'title'       => $post['titulo'] . ' — ' . SITE_NAME,
                'description' => mb_strimwidth($post['extracto'] ?? $post['titulo'], 0, 160, '...'),
                'url'         => SITE_URL . '/blog/' . $post['slug'],
                'type'        => 'article',
                'image'       => $postImg,
            ],
            'post'            => $post,
            'relacionados'    => $relacionados,
        ]);
    }
}
