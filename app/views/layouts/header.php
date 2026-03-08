<?php
/**
 * Header layout — visitapurranque.cl
 * Variables disponibles: $meta (array), $csrf, $flash
 * $meta keys: title, description, image, url, type
 */

// Construir $meta desde variables legacy o array directo
$meta = $meta ?? [];
if (empty($meta['title']))       $meta['title']       = $pageTitle ?? SITE_NAME;
if (empty($meta['description'])) $meta['description'] = $pageDescription ?? SITE_DESCRIPTION;
if (empty($meta['url']))         $meta['url']         = SITE_URL . strtok($_SERVER['REQUEST_URI'] ?? '/', '?');
if (empty($meta['type']))        $meta['type']        = $ogType ?? 'website';
if (empty($meta['image']))       $meta['image']       = $ogImage ?? SITE_URL . '/assets/img/og-default.jpg';

// Menu items desde BD
$menuItems = [];
try {
    global $pdo;
    $stmt = $pdo->prepare(
        "SELECT titulo, url, icono, target
         FROM menu_items
         WHERE menu = 'principal' AND activo = 1 AND parent_id IS NULL
         ORDER BY orden ASC"
    );
    $stmt->execute();
    $menuItems = $stmt->fetchAll();
} catch (Throwable $e) {
    // Sin menu si BD no disponible
}

// Datos de sesión admin para barra superior
$sessionBar = null;
if (!empty($_SESSION['usuario_id'])) {
    try {
        global $pdo;
        $stmtU = $pdo->prepare(
            "SELECT u.nombre, r.nombre AS rol_nombre
             FROM usuarios u
             JOIN roles r ON r.id = u.rol_id
             WHERE u.id = ? AND u.activo = 1 LIMIT 1"
        );
        $stmtU->execute([$_SESSION['usuario_id']]);
        $sessionBar = $stmtU->fetch();
    } catch (Throwable $e) {
        // Sin barra si falla
    }
}
?>
<!DOCTYPE html>
<html lang="es-CL">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($meta['title']) ?></title>
    <meta name="description" content="<?= e($meta['description']) ?>">

    <!-- Canonical -->
    <link rel="canonical" href="<?= e($meta['url']) ?>">

    <!-- Open Graph -->
    <meta property="og:type" content="<?= e($meta['type']) ?>">
    <meta property="og:title" content="<?= e($meta['title']) ?>">
    <meta property="og:description" content="<?= e($meta['description']) ?>">
    <meta property="og:image" content="<?= e($meta['image']) ?>">
    <meta property="og:url" content="<?= e($meta['url']) ?>">
    <meta property="og:site_name" content="<?= e(SITE_NAME) ?>">
    <meta property="og:locale" content="es_CL">

    <!-- Twitter Card -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="<?= e($meta['title']) ?>">
    <meta name="twitter:description" content="<?= e($meta['description']) ?>">
    <meta name="twitter:image" content="<?= e($meta['image']) ?>">

    <!-- RSS -->
    <link rel="alternate" type="application/rss+xml" title="Blog — <?= e(SITE_NAME) ?>" href="<?= url('/blog/feed') ?>">

    <!-- Favicon -->
    <link rel="icon" href="<?= asset('img/favicon.ico') ?>" type="image/x-icon">

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Serif+Display&family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- CSS -->
    <link rel="stylesheet" href="<?= asset('css/main.css?v=' . APP_VERSION) ?>">
</head>
<body<?= $sessionBar ? ' class="has-session-bar"' : '' ?>>
    <?php if ($sessionBar): ?>
    <div class="admin-session-bar">
        <div class="session-bar-inner">
            <span class="session-bar-info">Sesión activa: <?= e($sessionBar['nombre']) ?> — <?= e($sessionBar['rol_nombre']) ?></span>
            <span class="session-bar-links">
                <a href="<?= url('/admin/dashboard') ?>">Panel admin</a>
                <a href="<?= url('/admin/logout') ?>">Cerrar sesión</a>
            </span>
        </div>
    </div>
    <?php endif; ?>

    <!-- Skip to content (accesibilidad) -->
    <a href="#main-content" class="sr-only sr-only-focusable">Saltar al contenido</a>

    <header class="site-header" id="siteHeader">
        <div class="container header-inner">
            <a href="<?= url('/') ?>" class="logo">
                <span class="logo-icon">&#9968;</span>
                <span class="logo-text"><?= e(SITE_NAME) ?></span>
            </a>

            <button class="hamburger" id="hamburger" aria-label="Abrir menú" aria-expanded="false">
                <span></span>
                <span></span>
                <span></span>
            </button>

            <nav class="main-nav" id="mainNav" aria-label="Navegación principal">
                <ul class="nav-list">
                    <?php foreach ($menuItems as $item): ?>
                    <li>
                        <a href="<?= url($item['url']) ?>"
                           class="<?= is_active($item['url']) ?>"
                           <?= ($item['target'] ?? '') === '_blank' ? 'target="_blank" rel="noopener"' : '' ?>>
                            <?php if (!empty($item['icono'])): ?>
                                <span class="nav-icon"><?= $item['icono'] ?></span>
                            <?php endif; ?>
                            <?= e($item['titulo']) ?>
                        </a>
                    </li>
                    <?php endforeach; ?>
                </ul>
            </nav>
        </div>
    </header>

    <?php if (!empty($flash)): ?>
    <div class="flash-messages container">
        <?php if (!empty($flash['success'])): ?>
            <div class="flash flash--success"><?= e($flash['success']) ?></div>
        <?php endif; ?>
        <?php if (!empty($flash['error'])): ?>
            <div class="flash flash--error"><?= e($flash['error']) ?></div>
        <?php endif; ?>
    </div>
    <?php endif; ?>

    <main id="main-content" role="main">
