<?php
/**
 * Front Controller — visitapurranque.cl
 * Unico punto de entrada de la aplicacion
 */

// Base path del proyecto (un nivel arriba de public/)
define('BASE_PATH', dirname(__DIR__));

// Cargar configuracion
require BASE_PATH . '/app/config/app.php';

// Conexion a base de datos ($pdo queda disponible en scope global)
require BASE_PATH . '/app/config/database.php';

// Helpers (usa global $pdo para texto() y config())
require BASE_PATH . '/app/helpers/functions.php';

// Manejo de errores segun entorno
if (APP_DEBUG) {
    error_reporting(E_ALL);
    ini_set('display_errors', '1');
} else {
    error_reporting(0);
    ini_set('display_errors', '0');
    ini_set('log_errors', '1');
    ini_set('error_log', LOG_PATH . '/php_errors.log');
}

// Iniciar sesion
if (session_status() === PHP_SESSION_NONE) {
    session_cache_limiter('');
    session_name(SESSION_NAME);
    session_start([
        'cookie_lifetime' => SESSION_LIFETIME,
        'cookie_httponly'  => true,
        'cookie_secure'   => APP_ENV === 'production',
        'cookie_samesite' => 'Lax',
    ]);
}

// ── Parsear URI ────────────────────────────────────────

$uri = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);

// Quitar prefijo /public en desarrollo (Laragon sirve desde raiz del proyecto)
$uri = preg_replace('#^/public#', '', $uri);

// Normalizar: asegurar que empiece con / y quitar trailing slash (excepto root)
$uri = '/' . trim($uri, '/');
if ($uri !== '/') {
    $uri = rtrim($uri, '/');
}

$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';

// ── Cargar y matchear rutas ────────────────────────────

$routes = require BASE_PATH . '/app/config/routes.php';
$matchedRoute  = null;
$matchedParams = [];

foreach ($routes as $route) {
    [$routeMethod, $routeUri, $routeHandler] = $route;

    // Verificar metodo HTTP
    if ($routeMethod !== $method && !($routeMethod === 'GET' && $method === 'HEAD')) {
        continue;
    }

    // Ruta estatica (sin parametros)
    if (!str_contains($routeUri, '{')) {
        if ($routeUri === $uri) {
            $matchedRoute = $routeHandler;
            break;
        }
        continue;
    }

    // Ruta con parametros: convertir {param} a regex
    $pattern = preg_replace('/\{(\w+)\}/', '(?P<$1>[^/]+)', $routeUri);
    $pattern = '#^' . $pattern . '$#';

    if (preg_match($pattern, $uri, $matches)) {
        $matchedRoute  = $routeHandler;
        $matchedParams = array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);
        break;
    }
}

// ── Despachar ──────────────────────────────────────────

if (!$matchedRoute) {
    http_response_code(404);
    $pageTitle = 'Pagina no encontrada — ' . SITE_NAME;
    require BASE_PATH . '/app/views/layouts/header.php';
    require BASE_PATH . '/app/views/public/404.php';
    require BASE_PATH . '/app/views/layouts/footer.php';
    exit;
}

// Parsear 'ControllerClass@method'
[$controllerName, $actionName] = explode('@', $matchedRoute);
$controllerFile = BASE_PATH . '/app/controllers/' . $controllerName . '.php';

if (!file_exists($controllerFile)) {
    http_response_code(500);
    if (APP_DEBUG) {
        echo "Controller no encontrado: {$controllerFile}";
    }
    exit;
}

require_once BASE_PATH . '/app/controllers/Controller.php';
require_once $controllerFile;

$controller = new $controllerName($pdo);

if (!method_exists($controller, $actionName)) {
    http_response_code(500);
    if (APP_DEBUG) {
        echo "Metodo no encontrado: {$controllerName}@{$actionName}";
    }
    exit;
}

// Llamar al metodo con los parametros de la ruta
call_user_func_array([$controller, $actionName], $matchedParams);
