<?php
/**
 * Admin Header — visitapurranque.cl
 * Variables: $pageTitle, $usuario, $csrf, $flash
 */
?>
<!DOCTYPE html>
<html lang="es-CL">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($pageTitle ?? 'Admin') ?> — <?= e(SITE_NAME) ?></title>
    <meta name="robots" content="noindex, nofollow">
    <link rel="icon" href="<?= asset('img/favicon.ico') ?>" type="image/x-icon">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Serif+Display&family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= asset('css/admin.css?v=' . APP_VERSION) ?>">
</head>
<body class="admin-body">

    <!-- Top bar mobile -->
    <header class="admin-topbar">
        <button class="admin-hamburger" id="adminHamburger" aria-label="Abrir menú">
            <span></span><span></span><span></span>
        </button>
        <span class="admin-topbar-title">VP Admin</span>
        <a href="<?= url('/') ?>" target="_blank" class="admin-topbar-site" title="Ver sitio">&#8599;</a>
    </header>

    <!-- Overlay mobile -->
    <div class="admin-overlay" id="adminOverlay"></div>
