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
            $this->redirect('/atractivo/' . $slug, ['error' => 'Token inválido. Intenta de nuevo.']);
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
            'success' => 'Gracias por tu reseña. Será publicada después de revisión.'
        ]);
    }

    public function compartir(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->json(['error' => 'Método no permitido'], 405);
            return;
        }

        $input = json_decode(file_get_contents('php://input'), true);
        $tipo       = $input['tipo'] ?? '';
        $registroId = (int)($input['registro_id'] ?? 0);
        $redSocial  = $input['red_social'] ?? '';

        $tiposValidos = ['ficha', 'evento', 'blog_post'];
        $redesValidas = ['facebook', 'whatsapp', 'copiar'];

        if (!in_array($tipo, $tiposValidos) || $registroId < 1 || !in_array($redSocial, $redesValidas)) {
            $this->json(['error' => 'Datos inválidos'], 400);
            return;
        }

        try {
            $this->db->prepare(
                "INSERT INTO compartidos (tipo, registro_id, red_social, ip) VALUES (?, ?, ?, ?)"
            )->execute([$tipo, $registroId, $redSocial, $_SERVER['REMOTE_ADDR'] ?? null]);

            // Incrementar contador en la tabla correspondiente
            $tablas = ['ficha' => 'fichas', 'evento' => 'eventos', 'blog_post' => 'blog_posts'];
            if (isset($tablas[$tipo])) {
                $this->db->prepare(
                    "UPDATE {$tablas[$tipo]} SET compartidos = compartidos + 1 WHERE id = ?"
                )->execute([$registroId]);
            }

            $this->json(['ok' => true]);
        } catch (\Throwable $e) {
            $this->json(['error' => 'Error interno'], 500);
        }
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
