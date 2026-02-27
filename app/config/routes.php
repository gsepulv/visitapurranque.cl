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
