<?php
/**
 * Funciones helper globales — visitapurranque.cl
 */

// ── Escapado y seguridad ───────────────────────────────

function e(?string $value): string
{
    return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');
}

function csrf_token(): string
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function csrf_field(): string
{
    return '<input type="hidden" name="_csrf" value="' . csrf_token() . '">';
}

// ── Texto editable desde BD ────────────────────────────

function texto(string $clave, string $default = ''): string
{
    static $cache = [];

    if (isset($cache[$clave])) {
        return $cache[$clave];
    }

    try {
        global $pdo;
        if (!$pdo) return $default;

        $stmt = $pdo->prepare('SELECT valor FROM textos_editables WHERE clave = ? AND activo = 1 LIMIT 1');
        $stmt->execute([$clave]);
        $row = $stmt->fetch();
        $cache[$clave] = $row ? $row['valor'] : $default;
    } catch (Throwable $e) {
        $cache[$clave] = $default;
    }

    return $cache[$clave];
}

// ── Configuracion desde BD ─────────────────────────────

function config(string $clave, string $default = ''): string
{
    static $cache = [];

    if (isset($cache[$clave])) {
        return $cache[$clave];
    }

    try {
        global $pdo;
        if (!$pdo) return $default;

        $stmt = $pdo->prepare('SELECT valor FROM configuracion WHERE clave = ? LIMIT 1');
        $stmt->execute([$clave]);
        $row = $stmt->fetch();
        $cache[$clave] = $row ? $row['valor'] : $default;
    } catch (Throwable $e) {
        $cache[$clave] = $default;
    }

    return $cache[$clave];
}

// ── URLs ───────────────────────────────────────────────

function url(string $path = ''): string
{
    if (str_starts_with($path, 'http')) {
        return $path;
    }
    return rtrim(SITE_URL, '/') . '/' . ltrim($path, '/');
}

function asset(string $path): string
{
    return url('assets/' . ltrim($path, '/'));
}

// ── Navegacion ─────────────────────────────────────────

function is_active(string $path): string
{
    $current = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
    // Strip /public prefix para comparacion en desarrollo
    $current = preg_replace('#^/public#', '', $current);
    $current = $current ?: '/';

    if ($path === '/') {
        return $current === '/' ? 'active' : '';
    }

    return str_starts_with($current, $path) ? 'active' : '';
}

// ── Slug ───────────────────────────────────────────────

function slugify(string $text): string
{
    $text = mb_strtolower($text, 'UTF-8');
    $search  = ['á','é','í','ó','ú','ñ','ü'];
    $replace = ['a','e','i','o','u','n','u'];
    $text = str_replace($search, $replace, $text);
    $text = preg_replace('/[^a-z0-9\-]/', '-', $text);
    $text = preg_replace('/-+/', '-', $text);
    return trim($text, '-');
}

// ── Debug ──────────────────────────────────────────────

function dd(mixed ...$vars): void
{
    if (!defined('APP_DEBUG') || !APP_DEBUG) return;

    echo '<pre style="background:#1e293b;color:#f8fafc;padding:20px;margin:10px;border-radius:8px;font-size:13px;overflow:auto;">';
    foreach ($vars as $var) {
        var_dump($var);
        echo "\n";
    }
    echo '</pre>';
    die();
}

// ── Fecha ──────────────────────────────────────────────

function formatDate(?string $date, string $format = 'd/m/Y'): string
{
    if (!$date) return '';

    $ts = strtotime($date);
    return $ts ? date($format, $ts) : $date;
}
