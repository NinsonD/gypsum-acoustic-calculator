<?php

declare(strict_types=1);

if (PHP_SAPI !== 'cli') {
    fwrite(STDERR, "Run from CLI.\n");
    exit(1);
}

$backupFile = $argv[1] ?? '';
if ($backupFile === '' || !is_file($backupFile)) {
    fwrite(STDERR, "Usage: php scripts/restore.php /path/to/database.json\n");
    exit(1);
}

$config = require dirname(__DIR__) . '/app/bootstrap.php';
$pdo = Database::connection($config);
$snapshot = json_decode((string) file_get_contents($backupFile), true);

if (!is_array($snapshot) || !isset($snapshot['tables']) || !is_array($snapshot['tables'])) {
    fwrite(STDERR, "Invalid backup file.\n");
    exit(1);
}

$order = ['boq_estimations', 'inquiries', 'downloads', 'gallery_items', 'blog_posts', 'products', 'services', 'categories', 'brands', 'users', 'roles'];
$pdo->exec('SET FOREIGN_KEY_CHECKS=0');

foreach ($order as $table) {
    try {
        $pdo->exec('TRUNCATE TABLE ' . $table);
    } catch (Throwable) {
        // ignore missing tables during partial restores
    }
}

foreach (array_reverse($order) as $table) {
    $rows = $snapshot['tables'][$table] ?? [];
    if (!is_array($rows) || $rows === [] || isset($rows['error'])) {
        continue;
    }

    foreach ($rows as $row) {
        if (!is_array($row)) {
            continue;
        }

        $columns = array_keys($row);
        $placeholders = array_map(static fn ($column) => ':' . $column, $columns);
        $stmt = $pdo->prepare(
            'INSERT INTO ' . $table . ' (' . implode(',', $columns) . ') VALUES (' . implode(',', $placeholders) . ')'
        );
        $stmt->execute($row);
    }
}

$pdo->exec('SET FOREIGN_KEY_CHECKS=1');
echo "Restore completed from {$backupFile}\n";
