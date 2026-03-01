<?php
/**
 * Definicion de rutas — visitapurranque.cl
 * Formato: [metodo, URI, 'Controller@metodo']
 * IMPORTANTE: /{slug} debe ser la ULTIMA ruta (catch-all)
 */

return [
    // Pagina principal
    ['GET', '/',                  'HomeController@index'],

    // Categorias
    ['GET', '/categorias',        'CategoriaController@index'],
    ['GET', '/categoria/{slug}',  'CategoriaController@show'],

    // Atractivos turisticos
    ['GET', '/atractivo/{slug}',  'FichaController@show'],

    // Mapa interactivo
    ['GET', '/mapa',              'MapaController@index'],

    // Eventos
    ['GET', '/eventos',           'EventoController@index'],

    // Blog
    ['GET', '/blog',              'BlogController@index'],
    ['GET', '/blog/{slug}',       'BlogController@show'],

    // Contacto
    ['GET', '/contacto',          'ContactoController@index'],

    // Buscador
    ['GET', '/buscar',            'BuscadorController@index'],

    // FAQ
    ['GET', '/faq',               'FaqController@index'],

    // Admin
    ['GET',  '/admin',                     'AdminController@index'],
    ['GET',  '/admin/login',              'AdminController@loginForm'],
    ['POST', '/admin/login',              'AdminController@login'],
    ['GET',  '/admin/logout',             'AdminController@logout'],
    ['GET',  '/admin/dashboard',          'AdminController@dashboard'],

    // Admin — Categorías
    ['GET',  '/admin/categorias',                              'AdminCategoriaController@index'],
    ['GET',  '/admin/categorias/crear',                        'AdminCategoriaController@create'],
    ['POST', '/admin/categorias/crear',                        'AdminCategoriaController@store'],
    ['GET',  '/admin/categorias/{id}/editar',                  'AdminCategoriaController@edit'],
    ['POST', '/admin/categorias/{id}/editar',                  'AdminCategoriaController@update'],
    ['POST', '/admin/categorias/{id}/eliminar',                'AdminCategoriaController@delete'],
    ['POST', '/admin/categorias/{id}/toggle',                  'AdminCategoriaController@toggle'],
    ['POST', '/admin/categorias/{id}/subcategorias/crear',     'AdminCategoriaController@storeSub'],
    ['POST', '/admin/categorias/{catId}/subcategorias/{subId}/eliminar', 'AdminCategoriaController@deleteSub'],

    // Admin — Eventos
    ['GET',  '/admin/eventos',                'AdminEventoController@index'],
    ['GET',  '/admin/eventos/crear',          'AdminEventoController@create'],
    ['POST', '/admin/eventos/crear',          'AdminEventoController@store'],
    ['GET',  '/admin/eventos/{id}/editar',    'AdminEventoController@edit'],
    ['POST', '/admin/eventos/{id}/editar',    'AdminEventoController@update'],
    ['POST', '/admin/eventos/{id}/eliminar',  'AdminEventoController@delete'],
    ['POST', '/admin/eventos/{id}/toggle',    'AdminEventoController@toggle'],

    // Admin — Mensajes (Contacto)
    ['GET',  '/admin/mensajes',                    'AdminContactoController@index'],
    ['GET',  '/admin/mensajes/{id}',               'AdminContactoController@show'],
    ['POST', '/admin/mensajes/{id}/responder',     'AdminContactoController@responder'],
    ['POST', '/admin/mensajes/{id}/toggle-leido',  'AdminContactoController@toggleLeido'],
    ['POST', '/admin/mensajes/{id}/eliminar',      'AdminContactoController@delete'],

    // Admin — Planes
    ['GET',  '/admin/planes',                       'AdminPlanController@index'],
    ['GET',  '/admin/planes/crear',                 'AdminPlanController@create'],
    ['POST', '/admin/planes/crear',                 'AdminPlanController@store'],
    ['GET',  '/admin/planes/{id}/editar',           'AdminPlanController@edit'],
    ['POST', '/admin/planes/{id}/editar',           'AdminPlanController@update'],
    ['POST', '/admin/planes/{id}/eliminar',         'AdminPlanController@deletePlan'],
    ['POST', '/admin/planes/{id}/toggle',           'AdminPlanController@togglePlan'],

    // Admin — Suscripciones
    ['GET',  '/admin/suscripciones',                'AdminPlanController@suscripciones'],
    ['GET',  '/admin/suscripciones/crear',          'AdminPlanController@createSuscripcion'],
    ['POST', '/admin/suscripciones/crear',          'AdminPlanController@storeSuscripcion'],
    ['GET',  '/admin/suscripciones/{id}/editar',    'AdminPlanController@editSuscripcion'],
    ['POST', '/admin/suscripciones/{id}/editar',    'AdminPlanController@updateSuscripcion'],
    ['POST', '/admin/suscripciones/{id}/eliminar',  'AdminPlanController@deleteSuscripcion'],

    // Admin — Banners
    ['GET',  '/admin/banners',                    'AdminBannerController@index'],
    ['GET',  '/admin/banners/crear',              'AdminBannerController@create'],
    ['POST', '/admin/banners/crear',              'AdminBannerController@store'],
    ['GET',  '/admin/banners/{id}/editar',        'AdminBannerController@edit'],
    ['POST', '/admin/banners/{id}/editar',        'AdminBannerController@update'],
    ['POST', '/admin/banners/{id}/eliminar',      'AdminBannerController@delete'],
    ['POST', '/admin/banners/{id}/toggle',        'AdminBannerController@toggle'],
    ['POST', '/admin/banners/{id}/reset-stats',   'AdminBannerController@resetStats'],

    // Admin — Reseñas
    ['GET',  '/admin/resenas',                'AdminResenaController@index'],
    ['GET',  '/admin/resenas/{id}',           'AdminResenaController@show'],
    ['POST', '/admin/resenas/{id}/estado',    'AdminResenaController@cambiarEstado'],
    ['POST', '/admin/resenas/{id}/responder', 'AdminResenaController@responder'],
    ['POST', '/admin/resenas/{id}/eliminar',  'AdminResenaController@delete'],

    // Admin — Blog
    ['GET',  '/admin/blog',                'AdminBlogController@index'],
    ['GET',  '/admin/blog/crear',          'AdminBlogController@create'],
    ['POST', '/admin/blog/crear',          'AdminBlogController@store'],
    ['GET',  '/admin/blog/{id}/editar',    'AdminBlogController@edit'],
    ['POST', '/admin/blog/{id}/editar',    'AdminBlogController@update'],
    ['POST', '/admin/blog/{id}/eliminar',  'AdminBlogController@delete'],

    // Admin — Fichas
    ['GET',  '/admin/fichas',                'AdminFichaController@index'],
    ['GET',  '/admin/fichas/crear',          'AdminFichaController@create'],
    ['POST', '/admin/fichas/crear',          'AdminFichaController@store'],
    ['GET',  '/admin/fichas/{id}/editar',    'AdminFichaController@edit'],
    ['POST', '/admin/fichas/{id}/editar',    'AdminFichaController@update'],
    ['POST', '/admin/fichas/{id}/eliminar',  'AdminFichaController@delete'],
    ['POST', '/admin/fichas/{id}/toggle',    'AdminFichaController@toggle'],

    // Dashboard proyecto
    ['GET',  '/proyecto',                  'ProyectoController@index'],
    ['POST', '/proyecto/login',            'ProyectoController@login'],
    ['GET',  '/proyecto/logout',           'ProyectoController@logout'],
    ['POST', '/proyecto/api/tarea-toggle', 'ProyectoController@tareaToggle'],
    ['GET',  '/proyecto/api/stats',        'ProyectoController@apiStats'],

    // Paginas estaticas (terminos, privacidad, etc.) — DEBE SER LA ULTIMA
    ['GET', '/{slug}',            'LegalController@show'],
];
