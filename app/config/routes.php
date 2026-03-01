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
