<!DOCTYPE html>
<html lang="es-CL">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($pageTitle ?? 'Login') ?></title>
    <meta name="robots" content="noindex, nofollow">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Serif+Display&family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'Outfit', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #f1f5f9;
            color: #1f2937;
        }

        .login-card {
            width: 100%;
            max-width: 400px;
            margin: 20px;
            background: #fff;
            border-radius: 16px;
            box-shadow: 0 4px 24px rgba(0,0,0,.08);
            padding: 40px 32px;
        }

        .login-logo {
            text-align: center;
            margin-bottom: 28px;
        }

        .login-logo-icon {
            font-size: 2rem;
            display: block;
            margin-bottom: 4px;
        }

        .login-logo-text {
            font-family: 'DM Serif Display', Georgia, serif;
            font-size: 1.4rem;
            color: #1a5632;
        }

        .login-logo-sub {
            font-size: 0.8rem;
            color: #9ca3af;
            margin-top: 2px;
        }

        .flash {
            padding: 12px 16px;
            border-radius: 8px;
            font-size: 0.85rem;
            font-weight: 500;
            margin-bottom: 20px;
        }

        .flash--error {
            background: #fef2f2;
            color: #991b1b;
            border: 1px solid #fecaca;
        }

        .flash--success {
            background: #ecfdf5;
            color: #065f46;
            border: 1px solid #a7f3d0;
        }

        .form-group {
            margin-bottom: 18px;
        }

        .form-label {
            display: block;
            font-size: 0.85rem;
            font-weight: 500;
            color: #374151;
            margin-bottom: 6px;
        }

        .form-input {
            width: 100%;
            padding: 11px 14px;
            border: 1.5px solid #e5e7eb;
            border-radius: 8px;
            font-family: inherit;
            font-size: 0.95rem;
            color: #1f2937;
            background: #fff;
            transition: border-color .2s, box-shadow .2s;
            outline: none;
        }

        .form-input:focus {
            border-color: #1a5632;
            box-shadow: 0 0 0 3px rgba(26,86,50,.1);
        }

        .form-input::placeholder {
            color: #9ca3af;
        }

        .btn-login {
            width: 100%;
            padding: 12px;
            border: none;
            border-radius: 8px;
            background: #1a5632;
            color: #fff;
            font-family: inherit;
            font-size: 0.95rem;
            font-weight: 600;
            cursor: pointer;
            transition: background .2s;
            margin-top: 4px;
        }

        .btn-login:hover {
            background: #14532d;
        }

        .login-footer {
            text-align: center;
            margin-top: 24px;
        }

        .login-footer a {
            color: #6b7280;
            font-size: 0.82rem;
            text-decoration: none;
            transition: color .2s;
        }

        .login-footer a:hover {
            color: #1a5632;
        }
    </style>
</head>
<body>
    <div class="login-card">
        <div class="login-logo">
            <span class="login-logo-icon">&#9968;</span>
            <span class="login-logo-text">Visita Purranque</span>
            <p class="login-logo-sub">Panel de Administración</p>
        </div>

        <?php if (!empty($flash['error'])): ?>
            <div class="flash flash--error"><?= e($flash['error']) ?></div>
        <?php endif; ?>
        <?php if (!empty($flash['success'])): ?>
            <div class="flash flash--success"><?= e($flash['success']) ?></div>
        <?php endif; ?>

        <form method="POST" action="<?= url('/admin/login') ?>">
            <input type="hidden" name="_csrf" value="<?= e($csrf) ?>">

            <div class="form-group">
                <label class="form-label" for="email">Correo electrónico</label>
                <input type="email"
                       id="email"
                       name="email"
                       class="form-input"
                       placeholder="tu@email.com"
                       required
                       autofocus>
            </div>

            <div class="form-group">
                <label class="form-label" for="password">Contraseña</label>
                <input type="password"
                       id="password"
                       name="password"
                       class="form-input"
                       placeholder="&#8226;&#8226;&#8226;&#8226;&#8226;&#8226;&#8226;&#8226;"
                       required>
            </div>

            <button type="submit" class="btn-login">Iniciar sesión</button>
        </form>

        <div class="login-footer">
            <a href="<?= url('/') ?>">&larr; Volver al sitio</a>
        </div>
    </div>
</body>
</html>
