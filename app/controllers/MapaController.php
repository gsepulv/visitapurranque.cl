<?php

class MapaController extends Controller
{
    public function index(): void
    {
        $fichaModel = new Ficha($this->db);

        $this->render('public/mapa', [
            'pageTitle'       => 'Mapa interactivo — ' . SITE_NAME,
            'pageDescription' => 'Explora todos los atractivos turisticos de Purranque en un mapa interactivo.',
            'fichas'          => $fichaModel->getAllParaMapa(),
        ]);
    }
}
