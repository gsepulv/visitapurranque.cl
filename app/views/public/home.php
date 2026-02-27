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
    <meta property="og:description" content="Descubre Purranque: volcanes, bosques nativos, ríos y la mejor gastronomía del sur de Chile.">
    <meta property="og:type" content="website">
    <meta property="og:url" content="https://visitapurranque.cl">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,300;0,400;0,600;1,300;1,400&family=Figtree:wght@300;400;500;600&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { margin: 0; padding: 0; box-sizing: border-box; }

        :root {
            --bosque: #0b1f0e;
            --musgo: #1c3a20;
            --helecho: #3a7d44;
            --agua: #4da8c4;
            --niebla: #c8d5d0;
            --leche: #f7f4ef;
            --ceniza: #8a9490;
            --madera: #b5875a;
            --fuego: #d4763a;
        }

        html { font-size: 16px; }

        body {
            font-family: 'Figtree', sans-serif;
            min-height: 100vh;
            background: var(--bosque);
            color: var(--leche);
            overflow-x: hidden;
        }

        .scene {
            min-height: 100vh;
            display: grid;
            grid-template-rows: 1fr auto;
            position: relative;
        }

        .sky {
            position: fixed;
            inset: 0;
            z-index: 0;
            background: linear-gradient(
                180deg,
                #0b1a2e 0%,
                #122840 15%,
                #1a3f5c 30%,
                #2d6b5e 55%,
                #1c3a20 75%,
                #0b1f0e 100%
            );
        }

        .sky::before {
            content: '';
            position: absolute;
            inset: 0;
            background-image:
                radial-gradient(1px 1px at 10% 8%, rgba(255,255,255,0.8) 0%, transparent 100%),
                radial-gradient(1px 1px at 25% 15%, rgba(255,255,255,0.5) 0%, transparent 100%),
                radial-gradient(1.5px 1.5px at 45% 5%, rgba(255,255,255,0.9) 0%, transparent 100%),
                radial-gradient(1px 1px at 60% 12%, rgba(255,255,255,0.6) 0%, transparent 100%),
                radial-gradient(1px 1px at 75% 3%, rgba(255,255,255,0.7) 0%, transparent 100%),
                radial-gradient(1.5px 1.5px at 88% 10%, rgba(255,255,255,0.8) 0%, transparent 100%),
                radial-gradient(1px 1px at 15% 20%, rgba(255,255,255,0.4) 0%, transparent 100%),
                radial-gradient(1px 1px at 35% 18%, rgba(255,255,255,0.5) 0%, transparent 100%),
                radial-gradient(1px 1px at 55% 22%, rgba(255,255,255,0.6) 0%, transparent 100%),
                radial-gradient(1px 1px at 70% 7%, rgba(255,255,255,0.4) 0%, transparent 100%),
                radial-gradient(1px 1px at 92% 18%, rgba(255,255,255,0.5) 0%, transparent 100%),
                radial-gradient(1px 1px at 5% 25%, rgba(255,255,255,0.3) 0%, transparent 100%);
            animation: twinkle 4s ease-in-out infinite alternate;
        }

        @keyframes twinkle {
            0% { opacity: 0.6; }
            100% { opacity: 1; }
        }

        .volcano {
            position: fixed;
            bottom: 18%;
            left: 50%;
            transform: translateX(-50%);
            z-index: 1;
        }

        .volcano svg {
            width: min(800px, 100vw);
            height: auto;
            display: block;
        }

        .forest {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            z-index: 2;
            height: 25%;
        }

        .forest svg {
            width: 100%;
            height: 100%;
            display: block;
        }

        .fog {
            position: fixed;
            bottom: 15%;
            left: -5%;
            right: -5%;
            height: 20%;
            z-index: 3;
            background: linear-gradient(
                180deg,
                transparent 0%,
                rgba(200,213,208,0.04) 30%,
                rgba(200,213,208,0.08) 60%,
                rgba(200,213,208,0.03) 100%
            );
            animation: fogDrift 25s ease-in-out infinite;
        }

        @keyframes fogDrift {
            0%, 100% { transform: translateX(0) scaleY(1); opacity: 1; }
            33% { transform: translateX(2%) scaleY(1.1); opacity: 0.8; }
            66% { transform: translateX(-1.5%) scaleY(0.95); opacity: 0.9; }
        }

        .content {
            position: relative;
            z-index: 10;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            padding: 3rem 1.5rem 6rem;
            text-align: center;
        }

        .tag {
            font-size: 0.65rem;
            font-weight: 500;
            letter-spacing: 0.3em;
            text-transform: uppercase;
            color: var(--agua);
            margin-bottom: 3rem;
            position: relative;
            padding: 0 2rem;
            animation: appear 1.2s ease-out;
        }

        .tag::before, .tag::after {
            content: '';
            position: absolute;
            top: 50%;
            width: 30px;
            height: 1px;
            background: var(--agua);
            opacity: 0.3;
        }
        .tag::before { left: -15px; }
        .tag::after { right: -15px; }

        .icon {
            margin-bottom: 2rem;
            animation: appear 1.2s ease-out 0.15s both;
        }

        .icon svg {
            width: 48px;
            height: 48px;
        }

        .heading {
            font-family: 'Cormorant Garamond', serif;
            font-weight: 300;
            font-size: clamp(3rem, 8vw, 6rem);
            line-height: 0.95;
            color: var(--leche);
            margin-bottom: 0.4rem;
            animation: appear 1.2s ease-out 0.3s both;
        }

        .heading em {
            font-style: italic;
            color: var(--agua);
            font-weight: 300;
        }

        .subheading {
            font-family: 'Cormorant Garamond', serif;
            font-weight: 300;
            font-style: italic;
            font-size: clamp(1.1rem, 2.5vw, 1.5rem);
            color: var(--ceniza);
            margin-bottom: 3rem;
            animation: appear 1.2s ease-out 0.45s both;
        }

        .line {
            width: 1px;
            height: 50px;
            background: linear-gradient(180deg, var(--helecho), transparent);
            margin: 0 auto 2.5rem;
            animation: growDown 1.5s ease-out 0.6s both;
        }

        @keyframes growDown {
            from { height: 0; opacity: 0; }
            to { height: 50px; opacity: 1; }
        }

        .text {
            max-width: 440px;
            font-size: 0.95rem;
            font-weight: 300;
            line-height: 1.8;
            color: rgba(247,244,239,0.55);
            margin-bottom: 3rem;
            animation: appear 1.2s ease-out 0.7s both;
        }

        .text strong {
            font-weight: 500;
            color: rgba(247,244,239,0.8);
        }

        .pills {
            display: flex;
            gap: 0.6rem;
            flex-wrap: wrap;
            justify-content: center;
            margin-bottom: 3rem;
            animation: appear 1.2s ease-out 0.85s both;
        }

        .pill {
            padding: 0.45rem 1rem;
            border-radius: 100px;
            font-size: 0.72rem;
            font-weight: 500;
            letter-spacing: 0.06em;
            background: rgba(255,255,255,0.04);
            border: 1px solid rgba(255,255,255,0.08);
            color: var(--ceniza);
            transition: all 0.4s;
        }

        .pill:hover {
            background: rgba(77,168,196,0.1);
            border-color: rgba(77,168,196,0.25);
            color: var(--agua);
        }

        .pill-icon {
            margin-right: 0.3rem;
        }

        .progress {
            width: 200px;
            margin-bottom: 3rem;
            animation: appear 1.2s ease-out 1s both;
        }

        .progress-track {
            height: 2px;
            background: rgba(255,255,255,0.06);
            border-radius: 2px;
            overflow: hidden;
        }

        .progress-bar {
            height: 100%;
            width: 0%;
            background: linear-gradient(90deg, var(--helecho), var(--agua));
            border-radius: 2px;
            animation: fillBar 2.5s ease-out 1.5s forwards;
        }

        @keyframes fillBar {
            to { width: 10%; }
        }

        .progress-text {
            display: flex;
            justify-content: space-between;
            margin-top: 0.5rem;
            font-size: 0.6rem;
            letter-spacing: 0.15em;
            text-transform: uppercase;
            color: rgba(247,244,239,0.25);
        }

        .links {
            display: flex;
            align-items: center;
            gap: 1.5rem;
            animation: appear 1.2s ease-out 1.1s both;
        }

        .links a {
            font-size: 0.78rem;
            font-weight: 400;
            color: rgba(247,244,239,0.35);
            text-decoration: none;
            letter-spacing: 0.02em;
            transition: color 0.3s;
        }

        .links a:hover { color: var(--madera); }

        .links-dot {
            width: 3px;
            height: 3px;
            border-radius: 50%;
            background: rgba(247,244,239,0.15);
        }

        .foot {
            position: relative;
            z-index: 10;
            text-align: center;
            padding: 1.5rem;
            font-size: 0.65rem;
            letter-spacing: 0.08em;
            color: rgba(247,244,239,0.15);
        }

        .foot a {
            color: rgba(247,244,239,0.22);
            text-decoration: none;
            transition: color 0.3s;
        }

        .foot a:hover { color: var(--madera); }

        @keyframes appear {
            from { opacity: 0; transform: translateY(15px); }
            to { opacity: 1; transform: translateY(0); }
        }

        @media (max-width: 640px) {
            .content { padding: 2rem 1.2rem 5rem; }
            .tag { margin-bottom: 2rem; }
            .line { height: 35px; margin-bottom: 2rem; }
            .pills { gap: 0.4rem; }
            .pill { padding: 0.4rem 0.8rem; font-size: 0.68rem; }
            .links { flex-direction: column; gap: 0.8rem; }
            .links-dot { display: none; }
        }
    </style>
</head>
<body>
<!-- Deploy test v1 - 27 feb 2026 -->

<div class="scene">

    <div class="sky"></div>

    <div class="volcano">
        <svg viewBox="0 0 800 200" fill="none" preserveAspectRatio="xMidYMax meet">
            <path d="M250 200 L370 45 C375 38 380 35 385 32 C390 28 395 26 400 25 C405 26 410 28 415 32 C420 35 425 38 430 45 L550 200Z"
                  fill="rgba(28,58,32,0.5)" stroke="rgba(200,213,208,0.08)" stroke-width="0.5"/>
            <path d="M370 45 C375 38 380 35 385 32 C390 28 395 26 400 25 C405 26 410 28 415 32 C420 35 425 38 430 45 L420 65 C410 55 390 55 380 65Z"
                  fill="rgba(200,213,208,0.12)"/>
            <path d="M0 200 L120 110 L200 140 L280 95 L350 160 L370 145 L250 200Z"
                  fill="rgba(28,58,32,0.3)" stroke="rgba(200,213,208,0.04)" stroke-width="0.5"/>
            <path d="M550 200 L450 155 L500 120 L600 100 L700 130 L800 200Z"
                  fill="rgba(28,58,32,0.3)" stroke="rgba(200,213,208,0.04)" stroke-width="0.5"/>
            <rect x="100" y="185" width="600" height="15" rx="2"
                  fill="rgba(77,168,196,0.04)"/>
        </svg>
    </div>

    <div class="forest">
        <svg viewBox="0 0 1200 200" preserveAspectRatio="none" fill="none">
            <path d="M0 60 L20 30 L35 50 L50 15 L65 45 L80 20 L100 55 L115 25 L130 50 L150 10 L165 40 L180 22 L200 55 L215 18 L235 48 L250 25 L270 52 L285 12 L300 42 L320 28 L340 55 L355 15 L375 45 L390 20 L410 50 L425 8 L445 38 L460 25 L480 55 L495 18 L515 48 L530 30 L550 52 L565 12 L585 42 L600 22 L620 55 L635 15 L655 45 L670 28 L690 50 L705 10 L725 40 L740 25 L760 55 L775 18 L795 48 L810 30 L830 52 L845 12 L865 42 L880 22 L900 55 L920 20 L940 48 L955 8 L975 38 L990 28 L1010 52 L1025 15 L1045 45 L1060 25 L1080 55 L1095 12 L1115 42 L1130 30 L1150 50 L1170 18 L1190 45 L1200 35 L1200 200 L0 200Z"
                  fill="rgba(11,31,14,0.95)"/>
        </svg>
    </div>

    <div class="fog"></div>

    <main class="content">

        <div class="tag">Región de Los Lagos · Chile</div>

        <div class="icon">
            <svg viewBox="0 0 48 48" fill="none" stroke="rgba(77,168,196,0.5)" stroke-width="1" stroke-linecap="round">
                <path d="M24 6 L24 4"/>
                <path d="M24 6 L14 28 L34 28Z" fill="rgba(58,125,68,0.15)"/>
                <path d="M20 28 L10 42 L38 42 L28 28" fill="rgba(58,125,68,0.1)"/>
                <path d="M22 16 L24 6 L26 16" stroke="rgba(200,213,208,0.3)"/>
                <line x1="6" y1="42" x2="42" y2="42" stroke="rgba(77,168,196,0.2)"/>
            </svg>
        </div>

        <h1 class="heading">Visita <em>Purranque</em></h1>
        <p class="subheading">Guía del visitante</p>

        <div class="line"></div>

        <p class="text">
            Estamos creando la <strong>guía turística más completa</strong> de Purranque.
            Volcanes, bosques milenarios, ríos cristalinos y la rica <strong>tradición gastronómica</strong> del sur de Chile, todo en un solo lugar.
        </p>

        <div class="pills">
            <span class="pill"><span class="pill-icon">🌋</span>Volcanes</span>
            <span class="pill"><span class="pill-icon">🌿</span>Bosques nativos</span>
            <span class="pill"><span class="pill-icon">🐂</span>Tradiciones</span>
            <span class="pill"><span class="pill-icon">🍲</span>Gastronomía</span>
            <span class="pill"><span class="pill-icon">🗺</span>Rutas</span>
        </div>

        <div class="progress">
            <div class="progress-track">
                <div class="progress-bar"></div>
            </div>
            <div class="progress-text">
                <span>En desarrollo</span>
                <span>2026</span>
            </div>
        </div>

        <div class="links">
            <a href="mailto:contacto@purranque.info">contacto@purranque.info</a>
            <div class="links-dot"></div>
            <a href="https://purranque.info" target="_blank">PurranQUE.INFO</a>
        </div>

    </main>

    <footer class="foot">
        © 2026 Visita Purranque — Un proyecto de <a href="https://purranque.info" target="_blank">PurranQUE.INFO</a>
    </footer>

</div>

</body>
</html>