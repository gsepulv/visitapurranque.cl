<?php

class CategoriaController extends Controller
{
    public function index(): void
    {
        $categoriaModel = new Categoria($this->db);

        $this->render('public/categorias/index', [
            'meta' => [
                'title'       => 'Categorías — ' . SITE_NAME,
                'description' => 'Explora todas las categorías de atractivos turísticos de Purranque.',
            ],
            'categorias'      => $categoriaModel->getAllConContador(),
        ]);
    }

    public function show(string $slug): void
    {
        $categoriaModel = new Categoria($this->db);
        $fichaModel     = new Ficha($this->db);

        $categoria = $categoriaModel->getBySlugPublico($slug);
        if (!$categoria) {
            http_response_code(404);
            $this->render('public/404', ['meta' => ['title' => 'No encontrado — ' . SITE_NAME]]);
            return;
        }

        $page     = max(1, (int)($_GET['page'] ?? 1));
        $perPage  = 12;
        $offset   = ($page - 1) * $perPage;
        $total    = $fichaModel->countByCategoria($categoria['id']);
        $totalPages = max(1, (int)ceil($total / $perPage));

        $this->render('public/categorias/show', [
            'meta' => [
                'title'       => $categoria['nombre'] . ' — ' . SITE_NAME,
                'description' => 'Atractivos turísticos en la categoría ' . $categoria['nombre'] . ' en Purranque.',
                'url'         => SITE_URL . '/categoria/' . $categoria['slug'],
            ],
            'categoria'       => $categoria,
            'fichas'          => $fichaModel->getByCategoria($categoria['id'], $perPage, $offset),
            'page'            => $page,
            'totalPages'      => $totalPages,
            'total'           => $total,
        ]);
    }
}
