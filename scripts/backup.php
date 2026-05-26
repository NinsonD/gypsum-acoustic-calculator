<?php

declare(strict_types=1);

if (PHP_SAPI !== 'cli') {
    fwrite(STDERR, "Run from CLI.\n");
    exit(1);
}

$config = require dirname(__DIR__) . '/app/bootstrap.php';
$pdo = Database::connection($config);
$backupDir = STORAGE_PATH . '/backups/' . date('Ymd-His');

if (!is_dir($backupDir) && !mkdir($backupDir, 0775, true) && !is_dir($backupDir)) {
    fwrite(STDERR, "Unable to create backup directory.\n");
    exit(1);
}

$tables = ['roles', 'users', 'brands', 'categories', 'products', 'services', 'blog_posts', 'downloads', 'gallery_items', 'inquiries', 'boq_estimations'];
$snapshot = [
    'created_at' => date('c'),
    'tables' => [],
];

foreach ($tables as $table) {
    try {
        $rows = $pdo->query('SELECT * FROM ' . $table)->fetchAll(PDO::FETCH_ASSOC);
        $snapshot['tables'][$table] = $rows ?: [];
    } catch (Throwable $error) {
        $snapshot['tables'][$table] = ['error' => $error->getMessage()];
    }
}

file_put_contents($backupDir . '/database.json', json_encode($snapshot, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));

$uploadDirs = ['uploads/products', 'uploads/blogs', 'uploads/downloads', 'uploads/gallery'];
foreach ($uploadDirs as $relative) {
    $source = PUBLIC_PATH . '/' . $relative;
    if (is_dir($source)) {
        $target = $backupDir . '/' . str_replace('/', '_', $relative);
        recurse_copy($source, $target);
    }
}

echo "Backup created at {$backupDir}\n";

function recurse_copy(string $source, string $destination): void
{
    if (!is_dir($destination) && !mkdir($destination, 0775, true) && !is_dir($destination)) {
        return;
    }

    $items = scandir($source);
    if ($items === false) {
        return;
    }

    foreach ($items as $item) {
        if ($item === '.' || $item === '..') {
            continue;
        }

        $src = $source . DIRECTORY_SEPARATOR . $item;
        $dst = $destination . DIRECTORY_SEPARATOR . $item;

        if (is_dir($src)) {
            recurse_copy($src, $dst);
        } else {
            copy($src, $dst);
        }
    }
}
