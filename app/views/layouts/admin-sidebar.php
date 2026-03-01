<?php
/**
 * Admin Sidebar — visitapurranque.cl
 * Variables: $usuario, $sidebarCounts
 */
$counts = $sidebarCounts ?? [];

$adminNav = [
    ['url' => '/admin/dashboard',       'icon' => '&#128202;', 'label' => 'Dashboard',       'count' => null],
    ['url' => '/admin/fichas',          'icon' => '&#128205;', 'label' => 'Fichas',          'count' => $counts['fichas'] ?? null],
    ['url' => '/admin/categorias',      'icon' => '&#128194;', 'label' => 'Categorías',      'count' => $counts['categorias'] ?? null],
    ['url' => '/admin/eventos',         'icon' => '&#128197;', 'label' => 'Eventos',         'count' => null],
    ['url' => '/admin/blog',            'icon' => '&#128221;', 'label' => 'Blog',            'count' => null],
    ['url' => '/admin/resenas',         'icon' => '&#11088;',  'label' => 'Reseñas',         'count' => $counts['resenas'] ?? null],
    ['url' => '/admin/estadisticas',    'icon' => '&#128200;', 'label' => 'Estadísticas',    'count' => null],
    ['url' => '/admin/mensajes',        'icon' => '&#128231;', 'label' => 'Mensajes',        'count' => $counts['mensajes'] ?? null],
    ['url' => '/admin/banners',         'icon' => '&#127912;', 'label' => 'Banners',         'count' => null],
    ['url' => '/admin/planes',          'icon' => '&#128176;', 'label' => 'Planes',          'count' => null],
    ['url' => '/admin/cambios',         'icon' => '&#128260;', 'label' => 'Cambios',         'count' => $counts['cambios'] ?? null],
    ['url' => '/admin/seo',             'icon' => '&#128269;', 'label' => 'SEO',             'count' => null],
    ['url' => '/admin/apariencia',      'icon' => '&#127912;', 'label' => 'Apariencia',      'count' => null],
    ['url' => '/admin/textos',          'icon' => '&#128196;', 'label' => 'Textos',          'count' => null],
    ['url' => '/admin/paginas',         'icon' => '&#128195;', 'label' => 'Páginas',         'count' => null],
    ['url' => '/admin/usuarios',        'icon' => '&#128101;', 'label' => 'Usuarios',        'count' => null],
    ['url' => '/admin/configuracion',   'icon' => '&#9881;',   'label' => 'Configuración',   'count' => null],
];
?>

<aside class="admin-sidebar" id="adminSidebar">
    <div class="sidebar-logo">
        <a href="<?= url('/admin/dashboard') ?>">
            <span class="sidebar-logo-icon">&#9968;</span>
            <span class="sidebar-logo-text">VP Admin</span>
        </a>
    </div>

    <nav class="sidebar-nav">
        <?php foreach ($adminNav as $item): ?>
        <a href="<?= url($item['url']) ?>"
           class="sidebar-link<?= is_active($item['url']) ? ' active' : '' ?>">
            <span class="sidebar-icon"><?= $item['icon'] ?></span>
            <span class="sidebar-label"><?= e($item['label']) ?></span>
            <?php if ($item['count'] !== null && (int)$item['count'] > 0): ?>
                <span class="sidebar-badge"><?= (int)$item['count'] ?></span>
            <?php endif; ?>
        </a>
        <?php endforeach; ?>
    </nav>

    <div class="sidebar-bottom">
        <a href="<?= url('/') ?>" target="_blank" class="sidebar-link sidebar-link--site">
            <span class="sidebar-icon">&#8599;</span>
            <span class="sidebar-label">Ver sitio</span>
        </a>
        <div class="sidebar-user">
            <div class="sidebar-user-info">
                <span class="sidebar-user-name"><?= e($usuario['nombre'] ?? '') ?></span>
                <span class="sidebar-user-role"><?= e($usuario['rol_nombre'] ?? '') ?></span>
            </div>
            <a href="<?= url('/admin/logout') ?>" class="sidebar-logout" title="Cerrar sesión">&#10148;</a>
        </div>
    </div>
</aside>

<div class="admin-content">
    <?php if (!empty($flash['success'])): ?>
        <div class="admin-flash admin-flash--success" data-autohide><?= e($flash['success']) ?></div>
    <?php endif; ?>
    <?php if (!empty($flash['error'])): ?>
        <div class="admin-flash admin-flash--error"><?= e($flash['error']) ?></div>
    <?php endif; ?>
