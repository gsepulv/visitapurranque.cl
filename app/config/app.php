<?php
/**
 * Configuracion global — visitapurranque.cl
 */

// Deteccion de entorno
$isProduction = isset($_SERVER['HTTP_HOST'])
    ? str_contains($_SERVER['HTTP_HOST'], 'visitapurranque.cl')
      && !str_contains($_SERVER['HTTP_HOST'], '.test')
    : is_dir('/home/purranque');

define('APP_ENV',   $isProduction ? 'production' : 'development');
define('APP_DEBUG', APP_ENV === 'development');

// Sitio
define('SITE_NAME',        'Visita Purranque');
define('SITE_DESCRIPTION', 'Guia turistica de Purranque — Naturaleza, cultura y tradiciones en la Region de Los Lagos, Chile.');
define('SITE_URL', $isProduction
    ? 'https://visitapurranque.cl'
    : 'http://visitapurranque.cl.test'
);

// Version
define('APP_VERSION', '0.1.0');

// Zona horaria
date_default_timezone_set('America/Santiago');

// Session
define('SESSION_NAME',     'visita_sess');
define('SESSION_LIFETIME', 7200);

// Uploads
define('UPLOAD_MAX_SIZE',      5 * 1024 * 1024);
define('UPLOAD_ALLOWED_TYPES', ['image/jpeg', 'image/png', 'image/webp']);
define('UPLOAD_PATH',          BASE_PATH . '/public/uploads');

// Ciudad
define('CITY_LAT',  -40.91305);
define('CITY_LNG',  -73.15913);
define('CITY_NAME', 'Purranque');
define('CITY_ZOOM', 14);

// Paginacion
define('PER_PAGE',       12);
define('ADMIN_PER_PAGE', 20);

// Logs
define('LOG_PATH', BASE_PATH . '/storage/logs');
