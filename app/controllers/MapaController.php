<?php

class MapaController extends Controller
{
    public function index(): void
    {
        $fichaModel = new Ficha($this->db);
        $categoriaModel = new Categoria($this->db);
        $eventoModel = new Evento($this->db);

        $this->render('public/mapa', [
            'pageTitle'       => 'Mapa interactivo — ' . SITE_NAME,
            'pageDescription' => 'Explora todos los atractivos turísticos de Purranque en un mapa interactivo.',
            'fichas'          => $fichaModel->getAllParaMapa(),
            'categorias'      => $categoriaModel->getAllConContador(),
            'eventos'         => $eventoModel->getAllParaMapa(),
        ]);
    }
}
