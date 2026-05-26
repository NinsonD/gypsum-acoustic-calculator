<?php

declare(strict_types=1);

if (PHP_SAPI !== 'cli') {
    http_response_code(403);
    echo "This script must be run from the command line.\n";
    exit(1);
}

$config = require dirname(__DIR__) . '/app/bootstrap.php';
$content = new ContentRepository();
$pdo = Database::connection($config);

$brandStmt = $pdo->prepare(
    'INSERT INTO brands (name, slug, description)
     VALUES (:name, :slug, :description)
     ON DUPLICATE KEY UPDATE name = VALUES(name), description = VALUES(description)'
);

foreach ($content->brands() as $brand) {
    $brandStmt->execute([
        'name' => $brand['name'],
        'slug' => $brand['slug'],
        'description' => $brand['description'],
    ]);
}

$categoryStmt = $pdo->prepare(
    'INSERT INTO categories (name, slug)
     VALUES (:name, :slug)
     ON DUPLICATE KEY UPDATE name = VALUES(name)'
);

$categoryNames = [];
foreach ($content->products() as $product) {
    $categoryNames[$product['category']] = slugify($product['category']);
}

foreach ($categoryNames as $name => $slug) {
    $categoryStmt->execute(['name' => $name, 'slug' => $slug]);
}

$brandIds = [];
$categoryIds = [];

foreach ($pdo->query('SELECT id, slug FROM brands') as $brand) {
    $brandIds[$brand['slug']] = (int) $brand['id'];
}

foreach ($pdo->query('SELECT id, slug FROM categories') as $category) {
    $categoryIds[$category['slug']] = (int) $category['id'];
}

$productStmt = $pdo->prepare(
    'INSERT INTO products
        (brand_id, category_id, name, slug, short_description, description, specifications, nrc_value, stc_value, fire_rating, is_active)
     VALUES
        (:brand_id, :category_id, :name, :slug, :short_description, :description, :specifications, :nrc_value, :stc_value, :fire_rating, 1)
     ON DUPLICATE KEY UPDATE
        brand_id = VALUES(brand_id),
        category_id = VALUES(category_id),
        name = VALUES(name),
        short_description = VALUES(short_description),
        description = VALUES(description),
        specifications = VALUES(specifications),
        nrc_value = VALUES(nrc_value),
        stc_value = VALUES(stc_value),
        fire_rating = VALUES(fire_rating),
        is_active = 1'
);

foreach ($content->products() as $product) {
    $categorySlug = slugify($product['category']);
    $productStmt->execute([
        'brand_id' => $brandIds[$product['brand_id']] ?? null,
        'category_id' => $categoryIds[$categorySlug] ?? null,
        'name' => $product['name'],
        'slug' => $product['slug'],
        'short_description' => $product['summary'],
        'description' => $product['description'],
        'specifications' => json_encode($product['specs'], JSON_UNESCAPED_SLASHES),
        'nrc_value' => $product['nrc'],
        'stc_value' => $product['stc'],
        'fire_rating' => $product['fire_rating'],
    ]);
}

echo "Catalog seed complete: " . count($content->products()) . " products.\n";
