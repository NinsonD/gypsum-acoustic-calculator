<?php

declare(strict_types=1);

final class Database
{
    private static ?PDO $connection = null;

    public static function connection(array $config): PDO
    {
        if (self::$connection instanceof PDO) {
            return self::$connection;
        }

        $db = $config['db'] ?? [];

        if (empty($db['name']) || empty($db['user'])) {
            throw new RuntimeException('Database credentials are not configured.');
        }

        $dsn = sprintf(
            'mysql:host=%s;dbname=%s;charset=%s',
            $db['host'] ?? 'localhost',
            $db['name'],
            $db['charset'] ?? 'utf8mb4'
        );

        self::$connection = new PDO($dsn, $db['user'], $db['pass'] ?? '', [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ]);

        return self::$connection;
    }
}
