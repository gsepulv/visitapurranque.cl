<?php

class FichaController extends Controller
{
    public function show(string $slug): void
    {
        $fichaModel  = new Ficha($this->db);
        $resenaModel = new Resena($this->db);

        $ficha = $fichaModel->getBySlugPublico($slug);
        if (!$ficha) {
            http_response_code(404);
            $this->render('public/404', ['pageTitle' => 'No encontrado — ' . SITE_NAME]);
            return;
        }

        $rating       = $resenaModel->getPromedioByFicha($ficha['id']);
        $resenas      = $resenaModel->getAprobadasByFicha($ficha['id']);
        $relacionadas = $fichaModel->getRelacionadas($ficha['id'], $ficha['categoria_id'], 4);

        // Incrementar vistas
        $this->registrarVista($ficha['id']);

        $this->render('public/fichas/show', [
            'pageTitle'       => e($ficha['nombre']) . ' — ' . SITE_NAME,
            'pageDescription' => $ficha['descripcion_corta'] ?? $ficha['nombre'] . ' en Purranque',
            'ficha'           => $ficha,
            'rating'          => $rating,
            'resenas'         => $resenas,
            'relacionadas'    => $relacionadas,
        ]);
    }

    public function resena(string $slug): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/atractivo/' . $slug);
            return;
        }

        $fichaModel = new Ficha($this->db);
        $ficha = $fichaModel->getBySlugPublico($slug);
        if (!$ficha) {
            http_response_code(404);
            $this->render('public/404', ['pageTitle' => 'No encontrado — ' . SITE_NAME]);
            return;
        }

        // Validar CSRF
        $token = $_POST['_csrf'] ?? '';
        if ($token !== ($_SESSION['csrf_token'] ?? '')) {
            $this->redirect('/atractivo/' . $slug, ['error' => 'Token invalido. Intenta de nuevo.']);
            return;
        }

        // Validar campos
        $nombre     = trim($_POST['nombre'] ?? '');
        $email      = trim($_POST['email'] ?? '');
        $rating     = (int)($_POST['rating'] ?? 0);
        $comentario = trim($_POST['comentario'] ?? '');

        if ($nombre === '' || $rating < 1 || $rating > 5 || $comentario === '') {
            $this->redirect('/atractivo/' . $slug, ['error' => 'Completa todos los campos obligatorios.']);
            return;
        }

        $resenaModel = new Resena($this->db);
        $resenaModel->create([
            'ficha_id'         => $ficha['id'],
            'nombre'           => $nombre,
            'email'            => $email,
            'rating'           => $rating,
            'tipo_experiencia' => $_POST['tipo_experiencia'] ?? 'otro',
            'comentario'       => $comentario,
            'estado'           => 'pendiente',
        ]);

        $this->redirect('/atractivo/' . $slug, [
            'success' => 'Gracias por tu resena. Sera publicada despues de revision.'
        ]);
    }

    private function registrarVista(int $fichaId): void
    {
        try {
            $this->db->prepare(
                "UPDATE fichas SET vistas = vistas + 1 WHERE id = ?"
            )->execute([$fichaId]);
        } catch (\Throwable $e) {
            // Silenciar errores de estadísticas
        }
    }
}
