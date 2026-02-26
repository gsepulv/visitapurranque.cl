<?php

class CategoriaController extends Controller
{
    public function index(): void
    {
        $this->render('public/placeholder', [
            'pageTitle'   => 'Categorias — ' . SITE_NAME,
            'sectionName' => 'Categorias',
        ]);
    }

    public function show(string $slug): void
    {
        $this->render('public/placeholder', [
            'pageTitle'   => ucfirst($slug) . ' — ' . SITE_NAME,
            'sectionName' => 'Categoria: ' . $slug,
        ]);
    }
}
