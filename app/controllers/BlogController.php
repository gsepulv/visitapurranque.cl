<?php

class BlogController extends Controller
{
    public function index(): void
    {
        $this->render('public/placeholder', [
            'pageTitle'   => 'Blog — ' . SITE_NAME,
            'sectionName' => 'Blog',
        ]);
    }

    public function show(string $slug): void
    {
        $this->render('public/placeholder', [
            'pageTitle'   => ucfirst($slug) . ' — ' . SITE_NAME,
            'sectionName' => 'Articulo: ' . $slug,
        ]);
    }
}
