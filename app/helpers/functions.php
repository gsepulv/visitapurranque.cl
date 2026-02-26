<?php

/**
 * Escapar output HTML (prevenir XSS)
 */
function e(?string $value): string
{
    return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');
}

/**
 * Obtener texto editable desde BD
 */
function texto(string $clave, string $default = ''): string
{
    return $default;
}

/**
 * Generar slug URL-friendly
 */
function slugify(string $text): string
{
    return '';
}

/**
 * Generar o recuperar token CSRF
 */
function csrf_token(): string
{
    return '';
}
