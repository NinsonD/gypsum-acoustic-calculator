<?php

declare(strict_types=1);

final class UserRepository
{
    public function __construct(private array $config)
    {
    }

    public function users(): array
    {
        $stmt = $this->pdo()->query(
            'SELECT users.*, roles.role_name, roles.permissions
             FROM users
             INNER JOIN roles ON roles.id = users.role_id
             ORDER BY users.created_at DESC, users.name ASC'
        );

        return array_map([$this, 'mapUser'], $stmt->fetchAll());
    }

    public function userById(int $id): ?array
    {
        $stmt = $this->pdo()->prepare(
            'SELECT users.*, roles.role_name, roles.permissions
             FROM users
             INNER JOIN roles ON roles.id = users.role_id
             WHERE users.id = :id
             LIMIT 1'
        );
        $stmt->execute(['id' => $id]);
        $user = $stmt->fetch();

        return is_array($user) ? $this->mapUser($user) : null;
    }

    public function roles(): array
    {
        $stmt = $this->pdo()->query('SELECT * FROM roles ORDER BY role_name ASC');
        return array_map([$this, 'mapRole'], $stmt->fetchAll());
    }

    public function roleById(int $id): ?array
    {
        $stmt = $this->pdo()->prepare('SELECT * FROM roles WHERE id = :id LIMIT 1');
        $stmt->execute(['id' => $id]);
        $role = $stmt->fetch();

        return is_array($role) ? $this->mapRole($role) : null;
    }

    public function saveUser(array $data): void
    {
        $id = (int) ($data['id'] ?? 0);
        $roleId = $this->roleId($data['role_id'] ?? null);
        $name = trim((string) ($data['name'] ?? ''));
        $email = trim((string) ($data['email'] ?? ''));
        $password = (string) ($data['password'] ?? '');

        if ($roleId === null) {
            throw new InvalidArgumentException('Role is required.');
        }

        if ($this->roleById($roleId) === null) {
            throw new InvalidArgumentException('Invalid role selected.');
        }

        if ($name === '') {
            throw new InvalidArgumentException('Name is required.');
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new InvalidArgumentException('Valid email is required.');
        }

        $payload = [
            'role_id' => $roleId,
            'name' => $name,
            'email' => $email,
        ];

        if ($id > 0) {
            $existing = $this->userById($id);
            if ($existing === null) {
                throw new InvalidArgumentException('User not found.');
            }

            $payload['id'] = $id;
            $payload['password_hash'] = $existing['password_hash'];
            if ($password !== '') {
                if (strlen($password) < 10) {
                    throw new InvalidArgumentException('Password must be at least 10 characters.');
                }
                $payload['password_hash'] = password_hash($password, PASSWORD_DEFAULT);
            }

            $stmt = $this->pdo()->prepare(
                'UPDATE users
                 SET role_id = :role_id, name = :name, email = :email, password_hash = :password_hash
                 WHERE id = :id'
            );
            $stmt->execute($payload);
            return;
        }

        if (strlen($password) < 10) {
            throw new InvalidArgumentException('Password must be at least 10 characters.');
        }

        $payload['password_hash'] = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $this->pdo()->prepare(
            'INSERT INTO users (role_id, name, email, password_hash)
             VALUES (:role_id, :name, :email, :password_hash)'
        );
        $stmt->execute($payload);
    }

    public function deleteUser(int $id): void
    {
        $stmt = $this->pdo()->prepare('DELETE FROM users WHERE id = :id');
        $stmt->execute(['id' => $id]);
    }

    private function pdo(): PDO
    {
        return Database::connection($this->config);
    }

    private function roleId(mixed $value): ?int
    {
        return is_numeric($value) && (int) $value > 0 ? (int) $value : null;
    }

    private function mapRole(array $role): array
    {
        return [
            'id' => (int) $role['id'],
            'role_name' => $role['role_name'],
            'permissions' => $this->decodePermissions($role['permissions'] ?? null),
        ];
    }

    private function mapUser(array $user): array
    {
        return [
            'id' => (int) $user['id'],
            'role_id' => (int) $user['role_id'],
            'name' => $user['name'],
            'email' => $user['email'],
            'password_hash' => $user['password_hash'],
            'role_name' => $user['role_name'] ?? 'Public User',
            'permissions' => $this->decodePermissions($user['permissions'] ?? null),
            'created_at' => $user['created_at'] ?? '',
            'updated_at' => $user['updated_at'] ?? '',
        ];
    }

    private function decodePermissions(mixed $value): array
    {
        $decoded = json_decode((string) $value, true);
        return is_array($decoded) ? $decoded : [];
    }
}
