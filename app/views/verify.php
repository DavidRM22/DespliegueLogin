<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verificación OTP</title>
    <link rel="stylesheet" href="<?= asset('estilos.css') ?>">
    <script src="<?= asset('theme.js') ?>" defer></script>
</head>
<body>
<button class="theme-toggle" type="button" data-theme-toggle aria-label="Cambiar tema"></button>
<div class="page-shell">
    <section class="panel">
        <h1>Verificar código OTP</h1>
        <p class="subtitle">Escribe el código de 6 dígitos que enviamos a tu correo.</p>

        <form id="otp-form" class="form-grid" method="POST" action="<?= route('auth', 'verifyOTP') ?>">
            <div class="otp-group" role="group" aria-label="Código OTP de seis dígitos">
                <input class="otp-input" type="text" inputmode="numeric" autocomplete="one-time-code" maxlength="1" pattern="[0-9]" aria-label="Dígito 1" required>
                <input class="otp-input" type="text" inputmode="numeric" maxlength="1" pattern="[0-9]" aria-label="Dígito 2" required>
                <input class="otp-input" type="text" inputmode="numeric" maxlength="1" pattern="[0-9]" aria-label="Dígito 3" required>
                <input class="otp-input" type="text" inputmode="numeric" maxlength="1" pattern="[0-9]" aria-label="Dígito 4" required>
                <input class="otp-input" type="text" inputmode="numeric" maxlength="1" pattern="[0-9]" aria-label="Dígito 5" required>
                <input class="otp-input" type="text" inputmode="numeric" maxlength="1" pattern="[0-9]" aria-label="Dígito 6" required>
            </div>

            <input type="hidden" name="code" id="otp-code">

            <button class="btn-primary" type="submit">Verificar</button>
        </form>

        <div class="links-row">
            <a href="<?= route('auth', 'login') ?>">Volver al inicio de sesión</a>
        </div>
    </section>
</div>

<script>
    const otpForm = document.getElementById('otp-form');
    const otpInputs = Array.from(document.querySelectorAll('.otp-input'));
    const hiddenOtpCode = document.getElementById('otp-code');

    otpInputs.forEach((input, index) => {
        input.addEventListener('input', (event) => {
            const numericValue = event.target.value.replace(/\D/g, '');
            event.target.value = numericValue;

            if (numericValue && index < otpInputs.length - 1) {
                otpInputs[index + 1].focus();
            }
        });

        input.addEventListener('keydown', (event) => {
            if (event.key === 'Backspace' && !input.value && index > 0) {
                otpInputs[index - 1].focus();
            }
        });

        input.addEventListener('paste', (event) => {
            event.preventDefault();
            const pastedData = (event.clipboardData || window.clipboardData).getData('text').replace(/\D/g, '').slice(0, 6);

            pastedData.split('').forEach((digit, digitIndex) => {
                if (otpInputs[digitIndex]) {
                    otpInputs[digitIndex].value = digit;
                }
            });

            const focusIndex = Math.min(pastedData.length, otpInputs.length - 1);
            otpInputs[focusIndex].focus();
        });
    });

    otpForm.addEventListener('submit', (event) => {
        const otpValue = otpInputs.map((input) => input.value).join('');
        hiddenOtpCode.value = otpValue;

        if (otpValue.length !== 6) {
            event.preventDefault();
            otpInputs.find((input) => !input.value)?.focus();
        }
    });
</script>
</body>
</html>
