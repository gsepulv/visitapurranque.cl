<?php

class BuscadorController extends Controller
{
    public function index(): void
    {
        $this->render('public/placeholder', [
            'pageTitle'   => 'Buscar — ' . SITE_NAME,
            'sectionName' => 'Buscador',
        ]);
    }
}
