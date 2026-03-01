<?php

require_once MODEL_PATH . '/database.php';

class AuditModel
{
    public function log(string $event, string $email, string $details = '', ?string $userId = null): void
    {
        $db = Database::connectOrFail();

        $sql = "INSERT INTO audit_logs (event, email, user_id, ip, user_agent, created_at, details)
                VALUES (:event, :email, :user_id, :ip, :user_agent, NOW(), :details)";

        $stmt = $db->prepare($sql);
        $stmt->execute([
            ':event' => $event,
            ':email' => $email,
            ':user_id' => $userId,
            ':ip' => $_SERVER['REMOTE_ADDR'] ?? null,
            ':user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? null,
            ':details' => $details,
        ]);
    }

    public function getRecentLogs(int $limit = 500): array
    {
        $db = Database::connectOrFail();

        $limit = max(1, min(2000, (int)$limit));
        $sql = "SELECT event, email, ip, TO_CHAR(created_at, 'YYYY-MM-DD HH24:MI:SS') AS created_at, details
                FROM audit_logs
                ORDER BY created_at DESC
                LIMIT {$limit}";

        return $db->query($sql)->fetchAll() ?: [];
    }
}
