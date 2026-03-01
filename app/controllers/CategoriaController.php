<?php

class CategoriaController extends Controller
{
    public function index(): void
    {
        $categoriaModel = new Categoria($this->db);

        $this->render('public/categorias/index', [
            'pageTitle'       => 'Categorias — ' . SITE_NAME,
            'pageDescription' => 'Explora todas las categorias de atractivos turisticos de Purranque.',
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
            $this->render('public/404', ['pageTitle' => 'No encontrado — ' . SITE_NAME]);
            return;
        }

        $page     = max(1, (int)($_GET['page'] ?? 1));
        $perPage  = 12;
        $offset   = ($page - 1) * $perPage;
        $total    = $fichaModel->countByCategoria($categoria['id']);
        $totalPages = max(1, (int)ceil($total / $perPage));

        $this->render('public/categorias/show', [
            'pageTitle'       => e($categoria['nombre']) . ' — ' . SITE_NAME,
            'pageDescription' => 'Atractivos turisticos en la categoria ' . $categoria['nombre'] . ' en Purranque.',
            'categoria'       => $categoria,
            'fichas'          => $fichaModel->getByCategoria($categoria['id'], $perPage, $offset),
            'page'            => $page,
            'totalPages'      => $totalPages,
            'total'           => $total,
        ]);
    }
}
