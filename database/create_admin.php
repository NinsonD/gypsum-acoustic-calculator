<?php

declare(strict_types=1);

if (PHP_SAPI !== 'cli') {
    http_response_code(403);
    echo "This script must be run from the command line.\n";
    exit(1);
}

$config = require dirname(__DIR__) . '/app/bootstrap.php';

$email = $argv[1] ?? (string) env('ADMIN_SEED_EMAIL', '');
$password = $argv[2] ?? (string) env('ADMIN_SEED_PASSWORD', '');
$name = $argv[3] ?? 'Super Admin';

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo "Usage: php database/create_admin.php admin@example.com \"StrongPassword123!\" \"Admin Name\"\n";
    exit(1);
}

if (strlen($password) < 10) {
    echo "Admin password must be at least 10 characters.\n";
    exit(1);
}

$pdo = Database::connection($config);
$pdo->exec("INSERT IGNORE INTO roles (role_name, permissions) VALUES ('Super Admin', JSON_ARRAY('all'))");

$roleStmt = $pdo->prepare("SELECT id FROM roles WHERE role_name = 'Super Admin' LIMIT 1");
$roleStmt->execute();
$roleId = (int) $roleStmt->fetchColumn();

$stmt = $pdo->prepare(
    'INSERT INTO users (role_id, name, email, password_hash)
     VALUES (:role_id, :name, :email, :password_hash)
     ON DUPLICATE KEY UPDATE
        role_id = VALUES(role_id),
        name = VALUES(name),
        password_hash = VALUES(password_hash)'
);

$stmt->execute([
    'role_id' => $roleId,
    'name' => $name,
    'email' => $email,
    'password_hash' => password_hash($password, PASSWORD_DEFAULT),
]);

echo "Admin user ready: {$email}\n";
