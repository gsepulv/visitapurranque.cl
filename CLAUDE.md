# CLAUDE.md — Contexto del proyecto visitapurranque.cl

## Qué es este proyecto
Plataforma digital de turismo para Purranque, Región de Los Lagos, Chile.
Guía del Visitante con fichas de atractivos, mapa interactivo, eventos,
blog editorial, sistema de reseñas y panel admin con 35 módulos.

## Proyecto padre
PurranQUE.INFO (purranque.info) — ecosistema digital para Purranque.
Sitio hermano: regalospurranque.cl (directorio de comercios).

## Stack técnico
- PHP 8.x (sin frameworks, MVC propio)
- MySQL 8.x / MariaDB
- HTML5, CSS3, JavaScript vanilla (sin jQuery, sin React)
- Apache con mod_rewrite
- Cloudflare Turnstile para CAPTCHA
- Google Drive API para backups automáticos

## Entorno de desarrollo
- Carpeta: C:\Proyectos\visitapurranque.cl
- Laragon: http://visitapurranque.cl.test
- BD local: visitapurranque (root, sin pass)
- BD producción: visitapurranque_visita (en VPS cPanel)

## Estructura de archivos
```
public/              → Document root (index.php es el entry point)
public/assets/css/   → CSS del sitio
public/assets/js/    → JavaScript del sitio
public/assets/img/   → Imágenes estáticas
public/uploads/      → Uploads de usuarios (no va a git)
app/config/          → database.php, app.php (database.php no va a git)
app/controllers/     → Un controller por módulo
app/models/          → Un model por tabla principal
app/views/           → Organizadas en: layouts/, public/, admin/, proyecto/
app/services/        → EmailService, BackupService, etc.
app/middleware/       → AuthMiddleware, CsrfMiddleware, RateLimiter
app/helpers/         → functions.php (e, texto, slugify, csrf_token, etc.)
cron/                → Scripts cron (backups, reportes proyecto)
database/            → schema.sql, seeders.sql, migraciones
storage/logs/        → Logs de errores y cron
storage/cache/       → Cache de vistas o datos
storage/backups/     → Backups temporales antes de subir a Drive
```

## Convenciones de código
- Nombres de tablas: snake_case, plural (fichas, categorias, blog_posts)
- Nombres de archivos: kebab-case o snake_case
- Controllers: PascalCase (FichaController.php)
- Models: PascalCase singular (Ficha.php)
- Vistas: kebab-case (ficha-individual.php)
- CSS classes: kebab-case (card-atractivo, hero-section)
- Indentación: 4 espacios
- Encoding: UTF-8 siempre
- Todos los textos del sitio via helper texto('clave') → tabla textos_editables

## Base de datos
- 48 tablas total (42 sitio + 6 seguimiento proyecto)
- Soft delete en fichas, eventos, blog_posts (columna eliminado + eliminado_at)
- Audit log en tabla audit_log para todas las acciones admin
- Todas las tablas con created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
- Foreign keys con ON DELETE CASCADE o SET NULL según corresponda

## Seguridad
- Passwords: bcrypt (password_hash con PASSWORD_DEFAULT)
- Sessions: session_regenerate_id en login, HttpOnly + Secure + SameSite
- CSRF: token en todos los formularios POST
- Rate limiting: tabla login_intentos (5 intentos / 15 min)
- Prepared statements (PDO) siempre, nunca concatenar SQL
- htmlspecialchars() via helper e() para todo output
- Headers: X-Content-Type-Options, X-Frame-Options, CSP

## Categorías turísticas (10)
🌊 Playas y Costa | 🌲 Naturaleza y Senderos | 🏛 Patrimonio e Historia
🍽 Gastronomía | 🎭 Cultura y Tradiciones | 🏨 Alojamiento
🚌 Transporte | 🎵 Eventos y Fiestas | 🐦 Fauna y Avistamiento
🛍 Servicios al Visitante

## Categorías del blog (10)
📰 Noticias Locales | 🏔 Turismo y Naturaleza | 🎭 Cultura y Tradiciones
🍽 Gastronomía | 📋 Guías Prácticas | 📜 Historia de Purranque
💼 Emprendimiento Local | 🌿 Comunidad Huilliche | ⚽ Deportes
💭 Opinión

## Deploy
```bash
# En VPS:
cd /home/purranque/visitapurranque.cl
git pull origin main
```

## Notas importantes
- El sitio debe funcionar offline (PWA) para la costa sin señal
- Zona horaria: America/Santiago
- Moneda: CLP (pesos chilenos)
- Correo admin: contacto@purranque.info
- El admin password por defecto es admin123 — CAMBIAR en producción
