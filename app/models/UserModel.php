<?php

require_once MODEL_PATH . '/database.php';

class UserModel
{
    public function create(array $data): ?array
    {
        $db = Database::connectOrFail();

        $rawPassword = (string)($data['password'] ?? '');
        if ($rawPassword === '') {
            throw new InvalidArgumentException('Password requerido para crear usuario.');
        }

        $record = [
            'name' => isset($data['full_name']) ? $data['full_name'] : ($data['name'] ?? ''),
            'email' => $data['email'] ?? '',
            'phone' => $data['phone'] ?? null,
            'type' => $data['type'] ?? null,
            'department' => $data['department'] ?? null,
            'position' => $data['position'] ?? null,
            'hired_at' => $data['hired_at'] !== '' ? ($data['hired_at'] ?? null) : null,
            'status' => $data['status'] ?? null,
            'photo_url' => $data['photo_url'] ?? null,
            'password_hash' => password_hash($rawPassword, PASSWORD_DEFAULT),
            'must_change_password' => (bool)($data['must_change_password'] ?? false),
        ];

        $sql = "INSERT INTO users
                (name, email, phone, type, department, position, hired_at, status, photo_url, password_hash, must_change_password, created_at)
                VALUES
                (:name, :email, :phone, :type, :department, :position, :hired_at, :status, :photo_url, :password_hash, :must_change_password, NOW())
                RETURNING id, name, email, phone, type, department, position, hired_at, status, photo_url,
                          password_hash, must_change_password, TO_CHAR(created_at, 'YYYY-MM-DD HH24:MI:SS') AS created_at";

        $stmt = $db->prepare($sql);
        $stmt->execute([
            ':name' => $record['name'],
            ':email' => $record['email'],
            ':phone' => $record['phone'],
            ':type' => $record['type'],
            ':department' => $record['department'],
            ':position' => $record['position'],
            ':hired_at' => $record['hired_at'],
            ':status' => $record['status'],
            ':photo_url' => $record['photo_url'],
            ':password_hash' => $record['password_hash'],
            ':must_change_password' => $record['must_change_password'],
        ]);

        return $stmt->fetch() ?: null;
    }

    public function findByEmail(string $email): ?array
    {
        $db = Database::connectOrFail();

        $sql = "SELECT id, name, email, phone, type, department, position, hired_at, status, photo_url,
                       password_hash, must_change_password, TO_CHAR(created_at, 'YYYY-MM-DD HH24:MI:SS') AS created_at
                FROM users
                WHERE email = :email
                LIMIT 1";
        $stmt = $db->prepare($sql);
        $stmt->execute([':email' => $email]);
        $row = $stmt->fetch();

        return $row ?: null;
    }

    public function findById(string $id): ?array
    {
        $db = Database::connectOrFail();

        $sql = "SELECT id, name, email, phone, type, department, position, hired_at, status, photo_url,
                       password_hash, must_change_password, TO_CHAR(created_at, 'YYYY-MM-DD HH24:MI:SS') AS created_at
                FROM users
                WHERE id = :id
                LIMIT 1";
        $stmt = $db->prepare($sql);
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch();

        return $row ?: null;
    }

    public function listUsers(string $search = '', string $type = '', string $status = ''): array
    {
        $db = Database::connectOrFail();

        $where = [];
        $params = [];

        if ($search !== '') {
            $where[] = "(name ILIKE :q OR email ILIKE :q OR COALESCE(position, '') ILIKE :q)";
            $params[':q'] = '%' . $search . '%';
        }
        if ($type !== '') {
            $where[] = "type = :type";
            $params[':type'] = $type;
        }
        if ($status !== '') {
            $where[] = "status = :status";
            $params[':status'] = $status;
        }

        $sql = "SELECT id, name, email, phone, type, department, position, hired_at, status, photo_url,
                       must_change_password, TO_CHAR(created_at, 'YYYY-MM-DD HH24:MI:SS') AS created_at
                FROM users";
        if ($where) {
            $sql .= " WHERE " . implode(' AND ', $where);
        }
        $sql .= " ORDER BY created_at DESC";

        $stmt = $db->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetchAll() ?: [];
    }

    public function update(string $id, array $data): bool
    {
        $db = Database::connectOrFail();

        $sql = "UPDATE users
                SET name = :name,
                    email = :email,
                    phone = :phone,
                    type = :type,
                    department = :department,
                    position = :position,
                    hired_at = :hired_at,
                    status = :status
                WHERE id = :id";

        $stmt = $db->prepare($sql);

        return $stmt->execute([
            ':id' => $id,
            ':name' => isset($data['full_name']) ? $data['full_name'] : ($data['name'] ?? ''),
            ':email' => $data['email'] ?? '',
            ':phone' => $data['phone'] ?? null,
            ':type' => $data['type'] ?? null,
            ':department' => $data['department'] ?? null,
            ':position' => $data['position'] ?? null,
            ':hired_at' => ($data['hired_at'] ?? '') !== '' ? ($data['hired_at'] ?? null) : null,
            ':status' => $data['status'] ?? null,
        ]);
    }

    public function updatePasswordById(string $id, string $newPassword): bool
    {
        $db = Database::connectOrFail();

        $sql = "UPDATE users
                SET password_hash = :password_hash,
                    must_change_password = FALSE
                WHERE id = :id";
        $stmt = $db->prepare($sql);
        return $stmt->execute([
            ':id' => $id,
            ':password_hash' => password_hash($newPassword, PASSWORD_DEFAULT),
        ]);
    }

    public function updatePasswordByEmail(string $email, string $newPassword): bool
    {
        $db = Database::connectOrFail();

        $sql = "UPDATE users
                SET password_hash = :password_hash,
                    must_change_password = FALSE
                WHERE email = :email";
        $stmt = $db->prepare($sql);
        return $stmt->execute([
            ':email' => $email,
            ':password_hash' => password_hash($newPassword, PASSWORD_DEFAULT),
        ]);
    }
}
