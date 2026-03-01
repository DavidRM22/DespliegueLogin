<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="<?= asset('estilos.css') ?>">
    <script src="<?= asset('theme.js') ?>" defer></script>
</head>
<body>
<button class="theme-toggle" type="button" data-theme-toggle aria-label="Cambiar tema"></button>
<div class="page-shell">
    <section class="panel">
        <h1>Bienvenido de vuelta</h1>
        <p class="subtitle">Ingresa tus credenciales para continuar.</p>

        <?php if (!empty($_SESSION['login_error'])): ?>
            <div class="alert alert-error"><?= htmlspecialchars($_SESSION['login_error']) ?></div>
            <?php unset($_SESSION['login_error']); ?>
        <?php endif; ?>

        <?php if (!empty($_SESSION['password_change_success'])): ?>
            <div class="alert alert-success"><?= htmlspecialchars($_SESSION['password_change_success']) ?></div>
            <?php unset($_SESSION['password_change_success']); ?>
        <?php endif; ?>

        <form class="form-grid" method="POST" action="<?= route('auth', 'doLogin') ?>">
            <div class="field">
                <label for="email">Correo electrónico</label>
                <input id="email" type="email" name="email" placeholder="usuario@empresa.com" required>
            </div>

            <div class="field">
                <label for="password">Contraseña</label>
                <input id="password" type="password" name="password" placeholder="••••••••" required>
            </div>

            <button class="btn-primary" type="submit">Iniciar sesión</button>
        </form>

    </section>
</div>
</body>
</html>
