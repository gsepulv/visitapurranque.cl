<?php
/**
 * Página En Construcción — visitapurranque.cl
 * Standalone (sin header/footer del layout)
 */
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Visita Purranque — Próximamente</title>
    <meta name="description" content="La guía turística más completa de Purranque. Naturaleza, cultura y tradiciones en la Región de Los Lagos, Chile.">
    <meta property="og:title" content="Visita Purranque — Próximamente">
    <meta property="og:description" content="La guía turística más completa de Purranque. Naturaleza, cultura y tradiciones en la Región de Los Lagos, Chile.">
    <meta property="og:type" content="website">
    <meta property="og:url" content="https://visitapurranque.cl">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,700;1,400&family=Outfit:wght@300;400;500;600&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { margin: 0; padding: 0; box-sizing: border-box; }

        :root {
            --verde-bosque: #0f3d1e;
            --verde-medio: #1a5632;
            --verde-claro: #2d8a56;
            --azul-lago: #0c7bb3;
            --azul-cielo: #7ec8e3;
            --dorado: #c9a84c;
            --crema: #f5f0e8;
            --blanco: #ffffff;
            --gris: #94a3b8;
        }

        html { scroll-behavior: smooth; }

        body {
            font-family: 'Outfit', sans-serif;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            background: var(--verde-bosque);
            color: var(--crema);
            overflow-x: hidden;
        }

        /* ═══ FONDO ATMOSFÉRICO ═══ */
        .atmosphere {
            position: fixed;
            inset: 0;
            z-index: 0;
            background:
                radial-gradient(ellipse 80% 50% at 20% 80%, rgba(26,86,50,0.6) 0%, transparent 70%),
                radial-gradient(ellipse 60% 40% at 80% 20%, rgba(12,123,179,0.3) 0%, transparent 60%),
                radial-gradient(ellipse 100% 60% at 50% 100%, rgba(15,61,30,0.8) 0%, transparent 50%),
                linear-gradient(175deg, #0a2614 0%, #0f3d1e 30%, #122f1e 60%, #0c2a3d 100%);
        }

        .atmosphere::before {
            content: '';
            position: absolute;
            inset: 0;
            background: url("data:image/svg+xml,%3Csvg viewBox='0 0 256 256' xmlns='http://www.w3.org/2000/svg'%3E%3Cfilter id='n'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='0.9' numOctaves='4' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23n)' opacity='0.03'/%3E%3C/svg%3E");
            opacity: 0.4;
        }

        /* ═══ NIEBLA ANIMADA ═══ */
        .mist {
            position: fixed;
            bottom: 0;
            left: -10%;
            width: 120%;
            height: 35%;
            z-index: 1;
            opacity: 0.15;
            background:
                radial-gradient(ellipse 70% 100% at 30% 100%, rgba(255,255,255,0.3) 0%, transparent 70%),
                radial-gradient(ellipse 50% 80% at 70% 100%, rgba(126,200,227,0.2) 0%, transparent 60%);
            animation: mistFloat 20s ease-in-out infinite;
        }

        @keyframes mistFloat {
            0%, 100% { transform: translateX(0); opacity: 0.15; }
            50% { transform: translateX(3%); opacity: 0.22; }
        }

        /* ═══ CONTENIDO PRINCIPAL ═══ */
        .page {
            position: relative;
            z-index: 2;
            flex: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 2rem 1.5rem;
            min-height: 100vh;
            text-align: center;
        }

        /* ═══ BADGE SUPERIOR ═══ */
        .badge {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.5rem 1.2rem;
            border: 1px solid rgba(201,168,76,0.3);
            border-radius: 50px;
            font-size: 0.75rem;
            font-weight: 500;
            letter-spacing: 0.15em;
            text-transform: uppercase;
            color: var(--dorado);
            background: rgba(201,168,76,0.08);
            backdrop-filter: blur(10px);
            margin-bottom: 2.5rem;
            animation: fadeDown 1s ease-out;
        }

        .badge::before {
            content: '';
            width: 6px;
            height: 6px;
            border-radius: 50%;
            background: var(--dorado);
            animation: pulse 2s ease-in-out infinite;
        }

        @keyframes pulse {
            0%, 100% { opacity: 1; transform: scale(1); }
            50% { opacity: 0.4; transform: scale(0.8); }
        }

        /* ═══ MONTAÑAS SVG ═══ */
        .mountains {
            width: 120px;
            height: auto;
            margin-bottom: 2rem;
            opacity: 0.6;
            animation: fadeDown 1.2s ease-out;
        }

        /* ═══ TÍTULO ═══ */
        .title {
            font-family: 'Playfair Display', serif;
            font-size: clamp(2.8rem, 7vw, 5rem);
            font-weight: 700;
            line-height: 1.05;
            color: var(--blanco);
            margin-bottom: 0.3rem;
            animation: fadeUp 1s ease-out 0.2s both;
        }

        .title span {
            display: block;
            font-size: clamp(1rem, 2.5vw, 1.3rem);
            font-family: 'Outfit', sans-serif;
            font-weight: 300;
            letter-spacing: 0.25em;
            text-transform: uppercase;
            color: var(--azul-cielo);
            margin-bottom: 0.5rem;
        }

        /* ═══ LÍNEA DECORATIVA ═══ */
        .divider {
            width: 60px;
            height: 2px;
            background: linear-gradient(90deg, transparent, var(--dorado), transparent);
            margin: 2rem auto;
            animation: fadeUp 1s ease-out 0.4s both;
        }

        /* ═══ DESCRIPCIÓN ═══ */
        .description {
            max-width: 520px;
            font-size: 1.05rem;
            font-weight: 300;
            line-height: 1.7;
            color: rgba(245,240,232,0.7);
            margin-bottom: 2.5rem;
            animation: fadeUp 1s ease-out 0.5s both;
        }

        .description strong {
            color: var(--crema);
            font-weight: 500;
        }

        /* ═══ BARRA DE PROGRESO ═══ */
        .progress-container {
            width: 100%;
            max-width: 320px;
            margin-bottom: 2.5rem;
            animation: fadeUp 1s ease-out 0.6s both;
        }

        .progress-label {
            display: flex;
            justify-content: space-between;
            font-size: 0.7rem;
            font-weight: 500;
            letter-spacing: 0.12em;
            text-transform: uppercase;
            color: var(--gris);
            margin-bottom: 0.6rem;
        }

        .progress-bar {
            height: 3px;
            border-radius: 3px;
            background: rgba(255,255,255,0.08);
            overflow: hidden;
        }

        .progress-fill {
            height: 100%;
            width: 8%;
            border-radius: 3px;
            background: linear-gradient(90deg, var(--verde-claro), var(--azul-lago));
            position: relative;
            animation: progressGrow 2s ease-out 1s both;
        }

        .progress-fill::after {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(90deg, transparent 0%, rgba(255,255,255,0.4) 50%, transparent 100%);
            animation: shimmer 2.5s ease-in-out infinite;
        }

        @keyframes progressGrow {
            from { width: 0%; }
            to { width: 8%; }
        }

        @keyframes shimmer {
            0% { transform: translateX(-100%); }
            100% { transform: translateX(300%); }
        }

        /* ═══ FEATURES ═══ */
        .features {
            display: flex;
            gap: 2rem;
            margin-bottom: 3rem;
            animation: fadeUp 1s ease-out 0.7s both;
        }

        .feature {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 0.5rem;
        }

        .feature-icon {
            width: 40px;
            height: 40px;
            border-radius: 10px;
            background: rgba(255,255,255,0.05);
            border: 1px solid rgba(255,255,255,0.08);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.1rem;
        }

        .feature-text {
            font-size: 0.7rem;
            font-weight: 400;
            color: var(--gris);
            letter-spacing: 0.05em;
        }

        /* ═══ CONTACTO ═══ */
        .contact {
            animation: fadeUp 1s ease-out 0.8s both;
        }

        .contact a {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            color: rgba(245,240,232,0.5);
            text-decoration: none;
            font-size: 0.85rem;
            font-weight: 400;
            transition: color 0.3s;
            padding: 0.5rem 0;
        }

        .contact a:hover {
            color: var(--dorado);
        }

        .contact-divider {
            color: rgba(255,255,255,0.15);
            margin: 0 0.8rem;
        }

        /* ═══ FOOTER ═══ */
        .page-footer {
            position: relative;
            z-index: 2;
            text-align: center;
            padding: 1.5rem;
            font-size: 0.7rem;
            color: rgba(245,240,232,0.25);
            letter-spacing: 0.05em;
        }

        .page-footer a {
            color: rgba(245,240,232,0.35);
            text-decoration: none;
            transition: color 0.3s;
        }

        .page-footer a:hover {
            color: var(--dorado);
        }

        /* ═══ ANIMACIONES ═══ */
        @keyframes fadeUp {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        @keyframes fadeDown {
            from { opacity: 0; transform: translateY(-15px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* ═══ RESPONSIVE ═══ */
        @media (max-width: 640px) {
            .page { padding: 2rem 1.2rem; }
            .features { gap: 1.2rem; }
            .feature-text { font-size: 0.65rem; }
            .contact a { font-size: 0.78rem; }
            .contact-divider { margin: 0 0.4rem; }
            .mountains { width: 90px; }
        }

        @media (max-width: 380px) {
            .features { flex-wrap: wrap; justify-content: center; }
        }
    </style>
</head>
<body>

<div class="atmosphere"></div>
<div class="mist"></div>

<main class="page">

    <div class="badge">En desarrollo</div>

    <svg class="mountains" viewBox="0 0 120 60" fill="none" xmlns="http://www.w3.org/2000/svg">
        <path d="M0 60 L25 18 L40 35 L55 8 L70 35 L85 20 L120 60Z" fill="rgba(255,255,255,0.06)" stroke="rgba(255,255,255,0.15)" stroke-width="0.5"/>
        <path d="M10 60 L45 25 L60 38 L75 15 L110 60Z" fill="rgba(255,255,255,0.03)" stroke="rgba(255,255,255,0.08)" stroke-width="0.5"/>
        <circle cx="95" cy="12" r="6" fill="rgba(201,168,76,0.15)" stroke="rgba(201,168,76,0.3)" stroke-width="0.5"/>
    </svg>

    <h1 class="title">
        <span>Guía del Visitante</span>
        Visita Purranque
    </h1>

    <div class="divider"></div>

    <p class="description">
        Estamos creando la <strong>guía turística más completa</strong> de Purranque.
        Naturaleza, cultura y tradiciones de la <strong>Región de Los Lagos</strong>, Chile.
    </p>

    <div class="progress-container">
        <div class="progress-label">
            <span>Progreso</span>
            <span>2026</span>
        </div>
        <div class="progress-bar">
            <div class="progress-fill"></div>
        </div>
    </div>

    <div class="features">
        <div class="feature">
            <div class="feature-icon">🏔</div>
            <span class="feature-text">Atractivos</span>
        </div>
        <div class="feature">
            <div class="feature-icon">🗺</div>
            <span class="feature-text">Mapa</span>
        </div>
        <div class="feature">
            <div class="feature-icon">🎭</div>
            <span class="feature-text">Eventos</span>
        </div>
        <div class="feature">
            <div class="feature-icon">📝</div>
            <span class="feature-text">Blog</span>
        </div>
    </div>

    <div class="contact">
        <a href="mailto:contacto@purranque.info">contacto@purranque.info</a>
        <span class="contact-divider">·</span>
        <a href="https://purranque.info" target="_blank">PurranQUE.INFO</a>
    </div>

</main>

<footer class="page-footer">
    © 2026 Visita Purranque — Un proyecto de <a href="https://purranque.info" target="_blank">PurranQUE.INFO</a>
</footer>

</body>
</html>