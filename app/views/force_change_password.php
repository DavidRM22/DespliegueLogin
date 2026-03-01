<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cambio de contraseña obligatorio</title>
    <link rel="stylesheet" href="<?= asset('estilos.css') ?>">
    <script src="<?= asset('theme.js') ?>" defer></script>
</head>
<body>
<button class="theme-toggle" type="button" data-theme-toggle aria-label="Cambiar tema"></button>
<div class="page-shell">
    <section class="panel">
        <h1>🔒 Cambio de Contraseña Obligatorio</h1>
        <p class="subtitle">Por seguridad, debes cambiar tu contraseña temporal antes de continuar.</p>

        <div class="password-info-box">
            <strong>ℹ️ Tu cuenta fue creada con una contraseña temporal.</strong>
            <p>Por favor, establece una contraseña nueva y segura.</p>
        </div>

        <?php if (!empty($_SESSION['password_change_error'])): ?>
            <div class="alert alert-error"><?= htmlspecialchars($_SESSION['password_change_error']) ?></div>
            <?php unset($_SESSION['password_change_error']); ?>
        <?php endif; ?>

        <form class="form-grid" method="POST" action="<?= route('auth', 'updateMandatoryPassword') ?>" id="mandatory-password-form" novalidate>
            <div class="field">
                <label for="email">Correo Electrónico</label>
                <input id="email" type="email" value="<?= htmlspecialchars($_SESSION['user_id'] ?? '') ?>" readonly>
            </div>

            <div class="field">
                <label for="new_password">Nueva Contraseña</label>
                <input id="new_password" type="password" name="new_password" required>
            </div>

            <div class="password-strength">
                <div class="password-strength__label">Requisitos de contraseña:</div>
                <ul class="requirements" id="password-requirements">
                    <li data-rule="length">Al menos 8 caracteres</li>
                    <li data-rule="lower">Una letra minúscula</li>
                    <li data-rule="upper">Una letra mayúscula</li>
                    <li data-rule="number">Un número</li>
                    <li data-rule="special">Un carácter especial (!@#$%^&*)</li>
                </ul>
            </div>

            <div class="field">
                <label for="confirm_password">Confirmar Contraseña</label>
                <input id="confirm_password" type="password" name="confirm_password" required>
            </div>

            <button class="btn-primary" id="submit-password-change" type="submit" disabled>Cambiar Contraseña</button>
        </form>
    </section>
</div>

<script>
(function () {
    const passwordInput = document.getElementById('new_password');
    const confirmInput = document.getElementById('confirm_password');
    const submitButton = document.getElementById('submit-password-change');
    const requirementItems = document.querySelectorAll('#password-requirements li');

    const validators = {
        length: (value) => value.length >= 8,
        lower: (value) => /[a-z]/.test(value),
        upper: (value) => /[A-Z]/.test(value),
        number: (value) => /[0-9]/.test(value),
        special: (value) => /[^A-Za-z0-9]/.test(value),
    };

    function validate() {
        const value = passwordInput.value;
        let allValid = true;

        requirementItems.forEach((item) => {
            const rule = item.dataset.rule;
            const passed = validators[rule](value);
            item.classList.toggle('is-valid', passed);
            if (!passed) {
                allValid = false;
            }
        });

        const confirmMatches = value !== '' && value === confirmInput.value;
        submitButton.disabled = !(allValid && confirmMatches);
    }

    passwordInput.addEventListener('input', validate);
    confirmInput.addEventListener('input', validate);
    validate();
})();
</script>
</body>
</html>
