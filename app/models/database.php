<?php

class Database
{
    private static ?PDO $connection = null;

    public static function connect(): ?PDO
    {
        if (self::$connection !== null) {
            return self::$connection;
        }

        $driver = getenv('DB_DRIVER') ?: 'pgsql';

        try {
            if ($driver === 'pgsql') {
                $host = getenv('DB_HOST') ?: '';
                $port = getenv('DB_PORT') ?: '5432';
                $name = getenv('DB_NAME') ?: '';
                $user = getenv('DB_USER') ?: '';
                $pass = getenv('DB_PASS') ?: '';
                $sslmode = getenv('DB_SSLMODE') ?: 'require';

                if ($host === '' || $name === '' || $user === '') {
                    throw new RuntimeException('Faltan variables de entorno de la base de datos (DB_HOST/DB_NAME/DB_USER).');
                }

                $dsn = "pgsql:host={$host};port={$port};dbname={$name};sslmode={$sslmode}";
                self::$connection = new PDO($dsn, $user, $pass, [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    // En Postgres, emulación es más compatible en algunos entornos.
                    PDO::ATTR_EMULATE_PREPARES => filter_var(getenv('DB_EMULATE_PREPARES') ?: 'false', FILTER_VALIDATE_BOOLEAN),
                ]);
            } else {
                // Fallback MySQL (por si quieres correrlo localmente con XAMPP)
                $host = getenv('DB_HOST') ?: 'localhost';
                $port = getenv('DB_PORT') ?: '3306';
                $name = getenv('DB_NAME') ?: 'auditoria';
                $user = getenv('DB_USER') ?: 'root';
                $pass = getenv('DB_PASS') ?: '';

                $dsn = "mysql:host={$host};port={$port};dbname={$name};charset=utf8mb4";
                self::$connection = new PDO($dsn, $user, $pass, [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                ]);
            }

            return self::$connection;
        } catch (Throwable $e) {
            error_log('DB connection error: ' . $e->getMessage());
            return null;
        }
    }

    public static function connectOrFail(): PDO
    {
        $db = self::connect();
        if (!$db) {
            throw new RuntimeException('No se pudo conectar a la base de datos. Revisa variables DB_* y logs.');
        }
        return $db;
    }
}
