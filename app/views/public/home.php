<!DOCTYPE html>
<html lang="es-CL">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Visita Purranque — Guia del Visitante de Purranque</title>
    <meta name="description" content="La guia turistica mas completa de Purranque, Region de Los Lagos, Chile. Naturaleza, cultura, gastronomia y tradiciones del sur de Chile.">
    <meta name="robots" content="index, follow">

    <!-- Open Graph -->
    <meta property="og:type" content="website">
    <meta property="og:title" content="Visita Purranque — Guia del Visitante">
    <meta property="og:description" content="La guia turistica mas completa de Purranque, Region de Los Lagos, Chile.">
    <meta property="og:url" content="https://visitapurranque.cl">
    <meta property="og:site_name" content="Visita Purranque">
    <meta property="og:locale" content="es_CL">

    <!-- Favicon -->
    <link rel="icon" href="/assets/img/favicon.ico" type="image/x-icon">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Serif+Display&family=Outfit:wght@300;400;500;600&display=swap" rel="stylesheet">

    <style>
        *, *::before, *::after {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        :root {
            --green: #1a5632;
            --green-light: #22c55e;
            --blue: #0ea5e9;
            --blue-dark: #0369a1;
        }

        html, body {
            height: 100%;
        }

        body {
            font-family: 'Outfit', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            background: linear-gradient(145deg, var(--green) 0%, #0f4a2a 40%, var(--blue-dark) 75%, var(--blue) 100%);
            color: #fff;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            overflow-x: hidden;
            position: relative;
        }

        /* Textura sutil de fondo */
        body::before {
            content: '';
            position: fixed;
            inset: 0;
            background:
                radial-gradient(ellipse at 20% 50%, rgba(34, 197, 94, .08) 0%, transparent 60%),
                radial-gradient(ellipse at 80% 20%, rgba(14, 165, 233, .1) 0%, transparent 50%),
                radial-gradient(ellipse at 50% 100%, rgba(0, 0, 0, .15) 0%, transparent 60%);
            pointer-events: none;
            z-index: 0;
        }

        .page {
            position: relative;
            z-index: 1;
            width: 100%;
            max-width: 680px;
            padding: 40px 24px;
            text-align: center;
        }

        /* Icono principal */
        .hero-icon {
            font-size: 4rem;
            line-height: 1;
            margin-bottom: 16px;
            display: block;
            animation: float 4s ease-in-out infinite;
        }

        @keyframes float {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-8px); }
        }

        /* Logo / Titulo */
        .site-title {
            font-family: 'DM Serif Display', Georgia, serif;
            font-size: 3rem;
            font-weight: 400;
            line-height: 1.1;
            color: #fff;
            margin-bottom: 8px;
            letter-spacing: -0.5px;
        }

        .site-subtitle {
            font-size: 1.05rem;
            font-weight: 300;
            color: rgba(255, 255, 255, .7);
            letter-spacing: 2px;
            text-transform: uppercase;
            margin-bottom: 40px;
        }

        /* Separador */
        .divider {
            width: 48px;
            height: 3px;
            background: var(--green-light);
            border-radius: 2px;
            margin: 0 auto 40px;
            opacity: .6;
        }

        /* Texto principal */
        .main-text {
            font-family: 'DM Serif Display', Georgia, serif;
            font-size: 1.5rem;
            font-weight: 400;
            line-height: 1.4;
            color: #fff;
            margin-bottom: 12px;
        }

        .secondary-text {
            font-size: 0.95rem;
            font-weight: 300;
            color: rgba(255, 255, 255, .65);
            line-height: 1.6;
            max-width: 480px;
            margin: 0 auto 40px;
        }

        /* Barra de progreso */
        .progress-wrap {
            margin-bottom: 12px;
        }

        .progress-bar {
            width: 100%;
            max-width: 360px;
            height: 6px;
            background: rgba(255, 255, 255, .12);
            border-radius: 3px;
            margin: 0 auto;
            overflow: hidden;
            position: relative;
        }

        .progress-fill {
            height: 100%;
            width: 30%;
            border-radius: 3px;
            background: linear-gradient(90deg, var(--green-light), #4ade80);
            position: relative;
            animation: pulse-glow 2.5s ease-in-out infinite;
        }

        .progress-fill::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,.3), transparent);
            animation: shimmer 2s ease-in-out infinite;
        }

        @keyframes pulse-glow {
            0%, 100% { opacity: .85; }
            50% { opacity: 1; }
        }

        @keyframes shimmer {
            0% { transform: translateX(-100%); }
            100% { transform: translateX(200%); }
        }

        .progress-label {
            font-size: 0.78rem;
            color: rgba(255, 255, 255, .4);
            margin-top: 10px;
            letter-spacing: 1px;
            text-transform: uppercase;
        }

        /* Proximamente badge */
        .coming-badge {
            display: inline-block;
            margin: 36px auto 40px;
            padding: 8px 24px;
            border: 1.5px solid rgba(255, 255, 255, .2);
            border-radius: 50px;
            font-size: 0.88rem;
            font-weight: 500;
            color: rgba(255, 255, 255, .8);
            letter-spacing: 1.5px;
            text-transform: uppercase;
            backdrop-filter: blur(4px);
            background: rgba(255, 255, 255, .04);
        }

        /* Links de contacto */
        .contact-links {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 10px;
        }

        .contact-link {
            color: rgba(255, 255, 255, .55);
            text-decoration: none;
            font-size: 0.88rem;
            font-weight: 400;
            transition: color .2s;
        }

        .contact-link:hover {
            color: #fff;
        }

        .contact-link--email {
            color: rgba(255, 255, 255, .75);
            font-weight: 500;
            padding: 6px 16px;
            border-radius: 6px;
            background: rgba(255, 255, 255, .06);
            transition: background .2s, color .2s;
        }

        .contact-link--email:hover {
            background: rgba(255, 255, 255, .12);
            color: #fff;
        }

        .contact-sep {
            width: 4px;
            height: 4px;
            border-radius: 50%;
            background: rgba(255, 255, 255, .2);
        }

        /* Footer */
        .page-footer {
            margin-top: 48px;
            font-size: 0.75rem;
            color: rgba(255, 255, 255, .25);
        }

        /* ── Responsive ── */
        @media (max-width: 480px) {
            .page {
                padding: 32px 20px;
            }

            .hero-icon {
                font-size: 3rem;
            }

            .site-title {
                font-size: 2.2rem;
            }

            .site-subtitle {
                font-size: 0.88rem;
                letter-spacing: 1.5px;
                margin-bottom: 32px;
            }

            .main-text {
                font-size: 1.25rem;
            }

            .secondary-text {
                font-size: 0.88rem;
                margin-bottom: 32px;
            }

            .coming-badge {
                margin: 28px auto 32px;
                font-size: 0.8rem;
            }
        }

        @media (min-width: 768px) {
            .site-title {
                font-size: 3.5rem;
            }

            .main-text {
                font-size: 1.65rem;
            }

            .contact-links {
                flex-direction: row;
                justify-content: center;
                gap: 16px;
            }
        }
    </style>
</head>
<body>

<main class="page">
    <span class="hero-icon" aria-hidden="true">&#9968;</span>

    <h1 class="site-title">Visita Purranque</h1>
    <p class="site-subtitle">Guia del Visitante de Purranque</p>

    <div class="divider"></div>

    <p class="main-text">Estamos construyendo algo increible para ti</p>
    <p class="secondary-text">
        La guia turistica mas completa de Purranque, Region de Los Lagos, Chile.
        Naturaleza, cultura, gastronomia y tradiciones del sur de Chile.
    </p>

    <div class="progress-wrap">
        <div class="progress-bar">
            <div class="progress-fill"></div>
        </div>
        <p class="progress-label">En desarrollo</p>
    </div>

    <span class="coming-badge">Proximamente &mdash; 2026</span>

    <div class="contact-links">
        <a href="mailto:contacto@purranque.info" class="contact-link contact-link--email">
            contacto@purranque.info
        </a>
        <span class="contact-sep"></span>
        <a href="https://purranque.info" target="_blank" rel="noopener" class="contact-link">
            PurranQUE.INFO
        </a>
    </div>

    <p class="page-footer">&copy; 2026 Visita Purranque</p>
</main>

</body>
</html>
