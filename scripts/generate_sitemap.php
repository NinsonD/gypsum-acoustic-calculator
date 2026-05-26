<?php

declare(strict_types=1);

if (PHP_SAPI !== 'cli') {
    fwrite(STDERR, "Run from CLI.\n");
    exit(1);
}

$config = require dirname(__DIR__) . '/app/bootstrap.php';
$pdo = Database::connection($config);

$urls = [
    ['loc' => url('/'), 'lastmod' => date('Y-m-d')],
    ['loc' => url('/calculators'), 'lastmod' => date('Y-m-d')],
    ['loc' => url('/products'), 'lastmod' => date('Y-m-d')],
    ['loc' => url('/blog'), 'lastmod' => date('Y-m-d')],
    ['loc' => url('/gallery'), 'lastmod' => date('Y-m-d')],
    ['loc' => url('/installation'), 'lastmod' => date('Y-m-d')],
    ['loc' => url('/knowledge'), 'lastmod' => date('Y-m-d')],
    ['loc' => url('/downloads'), 'lastmod' => date('Y-m-d')],
    ['loc' => url('/contact'), 'lastmod' => date('Y-m-d')],
];

foreach ($pdo->query('SELECT slug, updated_at, created_at FROM products WHERE is_active = 1 ORDER BY created_at DESC') as $row) {
    $urls[] = [
        'loc' => url('/products/' . $row['slug']),
        'lastmod' => substr((string) ($row['updated_at'] ?: $row['created_at']), 0, 10),
    ];
}

foreach ($pdo->query('SELECT slug, published_at, created_at FROM blog_posts WHERE status = "published" ORDER BY published_at DESC, created_at DESC') as $row) {
    $urls[] = [
        'loc' => url('/blog/' . $row['slug']),
        'lastmod' => substr((string) ($row['published_at'] ?: $row['created_at']), 0, 10),
    ];
}

foreach ($pdo->query('SELECT slug, created_at FROM gallery_items WHERE is_public = 1 ORDER BY sort_order ASC, created_at DESC') as $row) {
    $urls[] = [
        'loc' => url('/gallery'),
        'lastmod' => substr((string) $row['created_at'], 0, 10),
    ];
    break;
}

$xml = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n<urlset xmlns=\"http://www.sitemaps.org/schemas/sitemap/0.9\">\n";
foreach ($urls as $urlItem) {
    $xml .= "  <url><loc>" . htmlspecialchars($urlItem['loc'], ENT_QUOTES, 'UTF-8') . "</loc><lastmod>" . htmlspecialchars($urlItem['lastmod'], ENT_QUOTES, 'UTF-8') . "</lastmod></url>\n";
}
$xml .= "</urlset>\n";

file_put_contents(PUBLIC_PATH . '/sitemap.xml', $xml);
echo "Sitemap regenerated: " . count($urls) . " URLs.\n";
