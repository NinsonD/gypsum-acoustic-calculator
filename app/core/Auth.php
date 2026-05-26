<?php

declare(strict_types=1);

final class Auth
{
    public static function user(array $config): ?array
    {
        $userId = $_SESSION['user_id'] ?? null;

        if (!is_numeric($userId)) {
            return null;
        }

        try {
            $pdo = Database::connection($config);
            $stmt = $pdo->prepare(
                'SELECT users.id, users.name, users.email, roles.role_name, roles.permissions
                 FROM users
                 INNER JOIN roles ON roles.id = users.role_id
                 WHERE users.id = :id
                 LIMIT 1'
            );
            $stmt->execute(['id' => (int) $userId]);
            $user = $stmt->fetch();

            return is_array($user) ? $user : null;
        } catch (Throwable) {
            return null;
        }
    }

    public static function check(array $config): bool
    {
        return self::user($config) !== null;
    }

    public static function attempt(array $config, string $email, string $password): bool
    {
        try {
            $pdo = Database::connection($config);
            $stmt = $pdo->prepare(
                'SELECT users.id, users.password_hash, roles.role_name
                 FROM users
                 INNER JOIN roles ON roles.id = users.role_id
                 WHERE users.email = :email
                 LIMIT 1'
            );
            $stmt->execute(['email' => $email]);
            $user = $stmt->fetch();

            if (!is_array($user) || !password_verify($password, (string) $user['password_hash'])) {
                return false;
            }

            if (!in_array($user['role_name'], ['Super Admin', 'Admin', 'Editor', 'Contractor'], true)) {
                return false;
            }

            session_regenerate_id(true);
            $_SESSION['user_id'] = (int) $user['id'];

            return true;
        } catch (Throwable) {
            return false;
        }
    }

    public static function logout(): void
    {
        unset($_SESSION['user_id']);
        session_regenerate_id(true);
    }

    public static function requireAdmin(array $config): void
    {
        if (!self::check($config)) {
            redirect_to('/admin/login');
        }
    }

    public static function permissions(array $config): array
    {
        $user = self::user($config);

        if ($user === null) {
            return [];
        }

        $decoded = json_decode((string) ($user['permissions'] ?? '[]'), true);
        return is_array($decoded) ? $decoded : [];
    }

    public static function hasPermission(array $config, string $permission): bool
    {
        $permissions = self::permissions($config);

        return in_array('all', $permissions, true) || in_array($permission, $permissions, true);
    }

    public static function requirePermission(array $config, string $permission): void
    {
        if (!self::check($config) || !self::hasPermission($config, $permission)) {
            http_response_code(403);
            echo 'Forbidden';
            exit;
        }
    }
}
