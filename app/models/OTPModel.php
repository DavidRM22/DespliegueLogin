<?php

require_once MODEL_PATH . '/database.php';

class OTPModel
{
    public function generate(string $userId): string
    {
        $db = Database::connectOrFail();

        // Invalida OTPs anteriores no usados
        $stmt = $db->prepare("UPDATE otp_codes SET used_at = NOW() WHERE user_id = :user_id AND used_at IS NULL");
        $stmt->execute([':user_id' => $userId]);

        $code = (string)random_int(100000, 999999);
        $codeHash = password_hash($code, PASSWORD_DEFAULT);

        $sql = "INSERT INTO otp_codes (user_id, code_hash, expires_at, created_at, ip, user_agent)
                VALUES (:user_id, :code_hash, NOW() + INTERVAL '5 minutes', NOW(), :ip, :user_agent)";
        $stmt = $db->prepare($sql);
        $stmt->execute([
            ':user_id' => $userId,
            ':code_hash' => $codeHash,
            ':ip' => $_SERVER['REMOTE_ADDR'] ?? null,
            ':user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? null,
        ]);

        return $code;
    }

    public function verifyByEmail(string $email, string $code): bool
    {
        $db = Database::connectOrFail();

        $sql = "SELECT oc.id, oc.code_hash, oc.expires_at, oc.used_at
                FROM otp_codes oc
                INNER JOIN users u ON u.id = oc.user_id
                WHERE u.email = :email
                  AND oc.used_at IS NULL
                ORDER BY oc.created_at DESC
                LIMIT 1";
        $stmt = $db->prepare($sql);
        $stmt->execute([':email' => $email]);
        $row = $stmt->fetch();

        if (!$row) {
            return false;
        }

        // expirado
        $expiresAt = strtotime($row['expires_at']);
        if ($expiresAt !== false && time() > $expiresAt) {
            // marca como usado para no reutilizarlo
            $upd = $db->prepare("UPDATE otp_codes SET used_at = NOW() WHERE id = :id");
            $upd->execute([':id' => $row['id']]);
            return false;
        }

        $valid = password_verify((string)$code, (string)$row['code_hash']);

        if ($valid) {
            $upd = $db->prepare("UPDATE otp_codes SET used_at = NOW() WHERE id = :id");
            $upd->execute([':id' => $row['id']]);
            return true;
        }

        $upd = $db->prepare("UPDATE otp_codes SET attempts = attempts + 1 WHERE id = :id");
        $upd->execute([':id' => $row['id']]);
        return false;
    }
}
