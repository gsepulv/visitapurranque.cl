<?php

class EventoController extends Controller
{
    public function index(): void
    {
        $eventoModel = new Evento($this->db);

        $tiempo = $_GET['t'] ?? 'proximos';
        if (!in_array($tiempo, ['todos', 'proximos', 'pasados'])) {
            $tiempo = 'proximos';
        }

        $pagina = max(1, (int)($_GET['p'] ?? 1));
        $porPagina = 12;
        $total = $eventoModel->countPublicos($tiempo);
        $totalPaginas = max(1, (int)ceil($total / $porPagina));
        $pagina = min($pagina, $totalPaginas);
        $offset = ($pagina - 1) * $porPagina;

        $eventos = $eventoModel->getAllPublicos($tiempo, $porPagina, $offset);

        $this->render('public/eventos/index', [
            'pageTitle'       => 'Eventos y Actividades — ' . SITE_NAME,
            'pageDescription' => 'Descubre los eventos, fiestas y actividades turisticas en Purranque y alrededores.',
            'eventos'         => $eventos,
            'tiempo'          => $tiempo,
            'pagina'          => $pagina,
            'totalPaginas'    => $totalPaginas,
            'total'           => $total,
        ]);
    }

    public function show(string $slug): void
    {
        $eventoModel = new Evento($this->db);
        $evento = $eventoModel->getBySlugPublico($slug);

        if (!$evento) {
            http_response_code(404);
            $this->render('public/404', [
                'pageTitle' => 'Evento no encontrado — ' . SITE_NAME,
            ]);
            return;
        }

        $this->render('public/eventos/show', [
            'pageTitle'       => e($evento['titulo']) . ' — ' . SITE_NAME,
            'pageDescription' => mb_strimwidth($evento['descripcion_corta'] ?? $evento['titulo'], 0, 160, '...'),
            'evento'          => $evento,
        ]);
    }
}
