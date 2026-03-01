<?php

class ContactoController extends Controller
{
    public function index(): void
    {
        $this->render('public/contacto', [
            'pageTitle'       => 'Contacto — ' . SITE_NAME,
            'pageDescription' => 'Contactanos para consultas sobre turismo en Purranque, sugerencias o para agregar tu negocio al directorio.',
        ]);
    }

    public function send(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/contacto');
            return;
        }

        // CSRF
        if (!isset($_POST['_token']) || $_POST['_token'] !== ($_SESSION['_token'] ?? '')) {
            $_SESSION['flash_error'] = 'Token de seguridad invalido. Intenta de nuevo.';
            $this->redirect('/contacto');
            return;
        }

        $nombre  = trim($_POST['nombre'] ?? '');
        $email   = trim($_POST['email'] ?? '');
        $telefono = trim($_POST['telefono'] ?? '');
        $asunto  = trim($_POST['asunto'] ?? '');
        $mensaje = trim($_POST['mensaje'] ?? '');

        // Validacion
        $errores = [];
        if (mb_strlen($nombre) < 2 || mb_strlen($nombre) > 100) $errores[] = 'El nombre es obligatorio (2-100 caracteres).';
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errores[] = 'Ingresa un email valido.';
        if (mb_strlen($mensaje) < 10 || mb_strlen($mensaje) > 2000) $errores[] = 'El mensaje debe tener entre 10 y 2000 caracteres.';

        if (!empty($errores)) {
            $_SESSION['flash_error'] = implode(' ', $errores);
            $this->redirect('/contacto');
            return;
        }

        $model = new ContactoMensaje($this->db);
        $model->crear([
            'nombre'  => $nombre,
            'email'   => $email,
            'telefono' => $telefono,
            'asunto'  => $asunto,
            'mensaje' => $mensaje,
            'ip'      => $_SERVER['REMOTE_ADDR'] ?? null,
        ]);

        $_SESSION['flash_success'] = 'Mensaje enviado correctamente. Te responderemos pronto.';
        $this->redirect('/contacto');
    }
}
