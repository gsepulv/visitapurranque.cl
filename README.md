# visitapurranque.cl

**Guía del Visitante de Purranque** — Plataforma digital de turismo para la comuna de Purranque, Región de Los Lagos, Chile.

Un proyecto de [PurranQUE.INFO](https://purranque.info)

## Stack

- PHP 8.x + MySQL/MariaDB
- HTML5, CSS3, JavaScript vanilla
- MVC propio (mismo patrón de regalospurranque.cl)
- Cloudflare Turnstile (CAPTCHA)
- Google Drive API (backups)
- PWA (Service Worker)

## Desarrollo local

```bash
# Requisitos: Laragon con PHP 8.x y MySQL
# Symlink ya creado en C:\laragon\www\visitapurranque.cl

# URL local:
http://visitapurranque.cl.test

# BD local: visitapurranque (root, sin password)
```

## Deploy

```bash
# En el VPS:
cd /home/purranque/visitapurranque.cl
git pull origin main
```

## Estructura

```
visitapurranque.cl/
├── public/          # Document root (index.php, assets, uploads)
├── app/
│   ├── config/      # Configuración (database.php, app.php)
│   ├── controllers/ # Controladores
│   ├── models/      # Modelos
│   ├── views/       # Vistas (layouts, public, admin)
│   ├── services/    # Servicios (email, backup, etc.)
│   ├── middleware/   # Auth, CSRF, rate limiting
│   └── helpers/     # Funciones helper globales
├── cron/            # Scripts cron (backups, reportes)
├── database/        # Migraciones y seeders SQL
└── storage/         # Logs, cache, backups temporales
```

## Estado

🚧 En desarrollo — Meta BETA: Agosto 2026
