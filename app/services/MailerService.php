<?php

require_once APP_PATH . '/libs/PHPMailer/src/Exception.php';
require_once APP_PATH . '/libs/PHPMailer/src/PHPMailer.php';
require_once APP_PATH . '/libs/PHPMailer/src/SMTP.php';

use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\PHPMailer;

class MailerService
{
    private string $fromEmail;
    private string $fromName;

    public function __construct()
    {
        $this->fromEmail = MAIL_FROM_EMAIL;
        $this->fromName = MAIL_FROM_NAME;
    }

    public function sendOTP(string $toEmail, string $otpCode): bool
    {
        $subject = 'Tu código OTP de acceso';
        $message = $this->buildOtpMessage($otpCode);

        try {
            $mailer = new PHPMailer(true);
            $mailer->CharSet = 'UTF-8';
            $mailer->isSMTP();
            $mailer->Host = MAIL_HOST;
            $mailer->Port = MAIL_PORT;
            $mailer->SMTPAuth = MAIL_AUTH;

            if ($mailer->SMTPAuth) {
                $mailer->Username = MAIL_USERNAME;
                $mailer->Password = MAIL_PASSWORD;
            }

            if (MAIL_ENCRYPTION !== '') {
                $mailer->SMTPSecure = MAIL_ENCRYPTION; // 'tls' o 'ssl'
            }

            $mailer->setFrom($this->fromEmail, $this->fromName);
            $mailer->addAddress($toEmail);
            $mailer->addReplyTo($this->fromEmail, $this->fromName);
            $mailer->isHTML(true);
            $mailer->Subject = $subject;
            $mailer->Body = $message;
            $mailer->AltBody = "Tu código OTP es: {$otpCode}. Este código expira en 5 minutos.";

            return $mailer->send();
        } catch (Exception $exception) {
            error_log('Error al enviar OTP: ' . $exception->getMessage());
            return false;
        }
    }

    private function buildOtpMessage(string $otpCode): string
    {
        $otpCode = htmlspecialchars($otpCode, ENT_QUOTES, 'UTF-8');

        return "
            <h2>Verificación de inicio de sesión</h2>
            <p>Tu código OTP es:</p>
            <p style=\"font-size: 24px; font-weight: bold; letter-spacing: 2px;\">{$otpCode}</p>
            <p>Este código expira en 5 minutos.</p>
        ";
    }
}
