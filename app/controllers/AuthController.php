<?php

require_once MODEL_PATH . '/AuditModel.php';
require_once MODEL_PATH . '/UserModel.php';
require_once MODEL_PATH . '/OTPModel.php';
require_once APP_PATH . '/services/MailerService.php';

class AuthController
{
    private function isPasswordValidForUser(string $plainPassword, ?string $storedHash): bool
    {
        if (!$storedHash) {
            return false;
        }

        // Solo hash (bcrypt/argon) en BD
        return password_verify($plainPassword, $storedHash);
    }

    private function passwordRules(string $password): array
    {
        return [
            'length' => strlen($password) >= 8,
            'lower' => (bool)preg_match('/[a-z]/', $password),
            'upper' => (bool)preg_match('/[A-Z]/', $password),
            'number' => (bool)preg_match('/[0-9]/', $password),
            'special' => (bool)preg_match('/[^A-Za-z0-9]/', $password),
        ];
    }

    public function login()
    {
        require VIEW_PATH . '/login.php';
    }

    public function doLogin()
    {
        $email = trim($_POST['email'] ?? '');
        $password = (string)($_POST['password'] ?? '');

        $userModel = new UserModel();
        $user = $userModel->findByEmail($email);

        if (!$user || !$this->isPasswordValidForUser($password, $user['password_hash'] ?? null)) {
            $_SESSION['login_error'] = 'Credenciales incorrectas.';
            redirect(route('auth', 'login'));
        }

        $otpModel = new OTPModel();
        $otpCode = $otpModel->generate($user['id']);

        $mailer = new MailerService();
        $otpSent = $mailer->sendOTP($email, (string)$otpCode);

        $audit = new AuditModel();
        if (!$otpSent) {
            $_SESSION['login_error'] = 'No se pudo enviar el OTP al correo. Intenta nuevamente.';
            $audit->log('EVENT_FAILED_OTP_DELIVERY', $email, 'Error enviando OTP por correo en login', $user['id']);
            redirect(route('auth', 'login'));
        }

        $_SESSION['otp_email'] = $email;

        $audit->log('EVENT_LOGIN_ATTEMPT', $email, 'Credenciales válidas, OTP enviado por correo', $user['id']);

        redirect(route('auth', 'verify'));
    }

    public function verify()
    {
        require VIEW_PATH . '/verify.php';
    }

    public function verifyOTP()
    {
        $code = (string)($_POST['code'] ?? '');
        $email = $_SESSION['otp_email'] ?? null;

        if (!$email) {
            die("Sesión expirada. Inicie sesión nuevamente.");
        }

        $otpModel = new OTPModel();
        $isValid = $otpModel->verifyByEmail($email, $code);

        $audit = new AuditModel();
        $userModel = new UserModel();
        $user = $userModel->findByEmail($email);
        $userId = $user['id'] ?? null;

        if (!$isValid) {
            echo "❌ Código incorrecto o expirado<br>";
            echo "<a href='" . route('auth', 'verify') . "'>Intentar de nuevo</a>";
            $audit->log('EVENT_FAILED_OTP', $email, 'Código incorrecto o expirado', $userId);
            return;
        }

        $audit->log('EVENT_LOGIN', $email, 'Login exitoso', $userId);

        // Sesión: guarda ID (uuid) y email
        $_SESSION['user_id'] = $userId;
        $_SESSION['user_email'] = $email;
        unset($_SESSION['otp_email']);

        if (!empty($user['must_change_password'])) {
            redirect(route('auth', 'changePasswordRequired'));
        }

        redirect(route('dashboard', 'index'));
    }

    public function changePasswordRequired()
    {
        authRequired();

        $userId = $_SESSION['user_id'];
        $userModel = new UserModel();
        $user = $userModel->findById($userId);

        if (!$user || empty($user['must_change_password'])) {
            redirect(route('dashboard', 'index'));
        }

        require VIEW_PATH . '/force_change_password.php';
    }

    public function updateMandatoryPassword()
    {
        authRequired();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect(route('auth', 'changePasswordRequired'));
        }

        $userId = $_SESSION['user_id'];
        $email = $_SESSION['user_email'] ?? '';

        $newPassword = (string)($_POST['new_password'] ?? '');
        $confirmPassword = (string)($_POST['confirm_password'] ?? '');

        $rules = $this->passwordRules($newPassword);
        $allRulesPassed = !in_array(false, $rules, true);

        if (!$allRulesPassed) {
            $_SESSION['password_change_error'] = 'La nueva contraseña no cumple con todos los requisitos de seguridad.';
            redirect(route('auth', 'changePasswordRequired'));
        }

        if ($newPassword !== $confirmPassword) {
            $_SESSION['password_change_error'] = 'La confirmación de contraseña no coincide.';
            redirect(route('auth', 'changePasswordRequired'));
        }

        $userModel = new UserModel();
        $updated = $userModel->updatePasswordById($userId, $newPassword);

        if (!$updated) {
            $_SESSION['password_change_error'] = 'No se pudo actualizar la contraseña. Intenta nuevamente.';
            redirect(route('auth', 'changePasswordRequired'));
        }

        $audit = new AuditModel();
        $audit->log('EVENT_PASSWORD_CHANGED', $email, 'Cambio obligatorio de contraseña completado', $userId);

        $_SESSION['password_change_success'] = 'Contraseña actualizada correctamente.';

        redirect(route('dashboard', 'index'));
    }
}
