<?php

class HomeController extends Controller
{
    public function index(): void
    {
        $categoriaModel = new Categoria($this->db);
        $fichaModel     = new Ficha($this->db);
        $eventoModel    = new Evento($this->db);
        $blogModel      = new BlogPost($this->db);

        // Próximo evento para countdown del hero
        $proximoEvento = $this->db->query(
            "SELECT id, titulo, slug, fecha_inicio, lugar
             FROM eventos
             WHERE fecha_inicio > NOW() AND activo = 1 AND eliminado = 0
             ORDER BY fecha_inicio ASC
             LIMIT 1"
        )->fetch() ?: null;

        $this->render('public/home', [
            'pageTitle'       => 'Descubre Purranque — Guia turistica',
            'pageDescription' => 'Explora los atractivos naturales, culturales y gastronomicos de Purranque, Region de Los Lagos, Chile.',
            'categorias'      => $categoriaModel->getAllConContador(),
            'destacados'      => $fichaModel->getDestacados(6),
            'proximoEvento'   => $proximoEvento,
            'eventos'         => $eventoModel->getProximos(3),
            'posts'           => $blogModel->getPublicados(3),
        ]);
    }
}
