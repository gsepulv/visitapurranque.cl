<?php

class MapaController extends Controller
{
    public function index(): void
    {
        $this->render('public/placeholder', [
            'pageTitle'   => 'Mapa — ' . SITE_NAME,
            'sectionName' => 'Mapa Interactivo',
        ]);
    }
}
