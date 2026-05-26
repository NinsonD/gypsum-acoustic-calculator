<?php

declare(strict_types=1);

if (PHP_SAPI !== 'cli') {
    http_response_code(403);
    echo "This script must be run from the command line.\n";
    exit(1);
}

$config = require dirname(__DIR__) . '/app/bootstrap.php';
$pdo = Database::connection($config);

$items = [
    [
        'title' => 'Acoustic Ceiling Installation',
        'slug' => 'acoustic-ceiling-installation',
        'summary' => 'Reference image for acoustic ceiling layout and finished grid work.',
        'image' => 'assets/images/og-default.svg',
        'project_name' => 'Reference Project',
        'category' => 'Acoustic',
        'sort_order' => 1,
    ],
    [
        'title' => 'Drywall Partition Frame',
        'slug' => 'drywall-partition-frame',
        'summary' => 'Reference image for stud framing, board fixing, and service routing.',
        'image' => 'assets/images/og-default.svg',
        'project_name' => 'Reference Project',
        'category' => 'Partition',
        'sort_order' => 2,
    ],
];

$stmt = $pdo->prepare(
    'INSERT INTO gallery_items (title, slug, summary, image, project_name, category, sort_order, is_public)
     VALUES (:title, :slug, :summary, :image, :project_name, :category, :sort_order, 1)
     ON DUPLICATE KEY UPDATE
        title = VALUES(title),
        summary = VALUES(summary),
        image = VALUES(image),
        project_name = VALUES(project_name),
        category = VALUES(category),
        sort_order = VALUES(sort_order),
        is_public = 1'
);

foreach ($items as $item) {
    $stmt->execute($item);
}

echo "Gallery seed complete: " . count($items) . " items.\n";
