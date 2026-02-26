<?php
/**
 * Header layout — visitapurranque.cl
 * Variables disponibles: $pageTitle, $pageDescription, $csrf, $flash
 */

// Menu items desde BD
$menuItems = [];
try {
    global $pdo;
    $stmt = $pdo->prepare(
        "SELECT titulo, url, icono, target
         FROM menu_items
         WHERE ubicacion = 'principal' AND activo = 1
         ORDER BY orden ASC"
    );
    $stmt->execute();
    $menuItems = $stmt->fetchAll();
} catch (Throwable $e) {
    // Sin menu si BD no disponible
}
?>
<!DOCTYPE html>
<html lang="es-CL">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($pageTitle ?? SITE_NAME) ?></title>
    <meta name="description" content="<?= e($pageDescription ?? SITE_DESCRIPTION) ?>">

    <!-- Open Graph -->
    <meta property="og:title" content="<?= e($pageTitle ?? SITE_NAME) ?>">
    <meta property="og:description" content="<?= e($pageDescription ?? SITE_DESCRIPTION) ?>">
    <meta property="og:type" content="website">
    <meta property="og:locale" content="es_CL">

    <!-- Favicon -->
    <link rel="icon" href="<?= asset('img/favicon.ico') ?>" type="image/x-icon">

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Serif+Display&family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- CSS -->
    <link rel="stylesheet" href="<?= asset('css/main.css?v=' . APP_VERSION) ?>">
</head>
<body>
    <!-- Skip to content (accesibilidad) -->
    <a href="#main-content" class="sr-only sr-only-focusable">Saltar al contenido</a>

    <header class="site-header" id="siteHeader">
        <div class="container header-inner">
            <a href="<?= url('/') ?>" class="logo">
                <span class="logo-icon">&#9968;</span>
                <span class="logo-text"><?= e(SITE_NAME) ?></span>
            </a>

            <button class="hamburger" id="hamburger" aria-label="Abrir menu" aria-expanded="false">
                <span></span>
                <span></span>
                <span></span>
            </button>

            <nav class="main-nav" id="mainNav" aria-label="Navegacion principal">
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
