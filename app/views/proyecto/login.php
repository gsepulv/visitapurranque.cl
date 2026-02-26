<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Proyecto — <?= SITE_NAME ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600&display=swap" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Outfit', sans-serif;
            background: #0f1117;
            color: #e2e8f0;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .login-card {
            background: #1a1d27;
            border: 1px solid #2a2e3a;
            border-radius: 16px;
            padding: 48px 40px;
            width: 100%;
            max-width: 400px;
            margin: 20px;
        }
        .login-card h1 {
            font-size: 1.5rem;
            font-weight: 600;
            margin-bottom: 8px;
            text-align: center;
        }
        .login-card p {
            color: #94a3b8;
            font-size: 0.875rem;
            text-align: center;
            margin-bottom: 32px;
        }
        .error {
            background: rgba(239, 68, 68, 0.1);
            border: 1px solid rgba(239, 68, 68, 0.3);
            color: #fca5a5;
            padding: 10px 16px;
            border-radius: 8px;
            font-size: 0.875rem;
            margin-bottom: 20px;
            text-align: center;
        }
        label {
            display: block;
            font-size: 0.8rem;
            color: #94a3b8;
            margin-bottom: 8px;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }
        input[type="password"] {
            width: 100%;
            padding: 12px 16px;
            background: #0f1117;
            border: 1px solid #2a2e3a;
            border-radius: 8px;
            color: #e2e8f0;
            font-family: 'Outfit', sans-serif;
            font-size: 1rem;
            outline: none;
            transition: border-color 0.2s;
        }
        input[type="password"]:focus {
            border-color: #3b82f6;
        }
        button {
            width: 100%;
            margin-top: 24px;
            padding: 12px;
            background: #3b82f6;
            color: #fff;
            border: none;
            border-radius: 8px;
            font-family: 'Outfit', sans-serif;
            font-size: 1rem;
            font-weight: 500;
            cursor: pointer;
            transition: background 0.2s;
        }
        button:hover { background: #2563eb; }
    </style>
</head>
<body>
    <form class="login-card" method="POST" action="<?= url('/proyecto/login') ?>">
        <h1>Proyecto</h1>
        <p>visitapurranque.cl — Dashboard de seguimiento</p>
        <?php if (!empty($error)): ?>
            <div class="error"><?= e($error) ?></div>
        <?php endif; ?>
        <label for="password">Password</label>
        <input type="password" id="password" name="password" autofocus required>
        <button type="submit">Entrar</button>
    </form>
</body>
</html>
