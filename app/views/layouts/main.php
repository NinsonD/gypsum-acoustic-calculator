<?php
$pageTitle = $title ?? $config['name'];
$pageDescription = $description ?? 'Gypsum, drywall, acoustic, and BOQ engineering platform.';
$currentPath = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
$basePath = base_path();

if ($basePath !== '' && str_starts_with($currentPath, $basePath)) {
    $currentPath = substr($currentPath, strlen($basePath)) ?: '/';
}

$currentPath = $currentPath === '/' ? '/' : rtrim($currentPath, '/');
$pageCanonical = $canonical ?? url($currentPath);
$pageRobots = $robots ?? (str_starts_with($currentPath, '/admin') ? 'noindex,nofollow' : 'index,follow');
$pageOgImage = $og_image ?? asset('images/og-default.svg');
$pageOgType = $og_type ?? (is_array($schema ?? null) && (($schema['@type'] ?? '') === 'Article') ? 'article' : 'website');
$adminUser = class_exists('Auth') ? Auth::user($config) : null;
$siteSchema = [
    '@context' => 'https://schema.org',
    '@type' => 'WebSite',
    'name' => $config['name'],
    'url' => $config['url'],
];
$organizationSchema = [
    '@context' => 'https://schema.org',
    '@type' => 'LocalBusiness',
    'name' => $config['name'],
    'url' => $config['url'],
    'description' => $pageDescription,
];
$nav = [
    '/calculators' => 'Calculators',
    '/products' => 'Products',
    '/blog' => 'Blog',
    '/installation' => 'Installation',
    '/gallery' => 'Gallery',
    '/knowledge' => 'Knowledge',
    '/downloads' => 'Downloads',
    '/contact' => 'Contact',
    '/admin' => 'Admin',
];
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e($pageTitle); ?> | <?= e($config['name']); ?></title>
    <meta name="description" content="<?= e($pageDescription); ?>">
    <meta name="robots" content="<?= e($pageRobots); ?>">
    <link rel="canonical" href="<?= e($pageCanonical); ?>">
    <meta property="og:site_name" content="<?= e($config['name']); ?>">
    <meta property="og:title" content="<?= e($pageTitle); ?>">
    <meta property="og:description" content="<?= e($pageDescription); ?>">
    <meta property="og:type" content="<?= e($pageOgType); ?>">
    <meta property="og:url" content="<?= e($pageCanonical); ?>">
    <meta property="og:image" content="<?= e($pageOgImage); ?>">
    <meta name="twitter:card" content="summary_large_image">
    <link rel="stylesheet" href="<?= e(asset('css/app.css')); ?>">
    <script type="application/ld+json"><?= json_encode($siteSchema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE); ?></script>
    <script type="application/ld+json"><?= json_encode($organizationSchema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE); ?></script>
    <?php if (!empty($schema)): ?>
        <script type="application/ld+json"><?= json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE); ?></script>
    <?php endif; ?>
</head>
<body data-base-path="<?= e(base_path()); ?>">
    <header class="site-header">
        <a class="brand" href="<?= e(url('/')); ?>" aria-label="Home">
            <img class="brand-logo" src="<?= e(asset('images/logo/main-logo.png')); ?>" alt="Al Namariq Building Materials">
            <span class="brand-copy">
                <strong><?= e($config['name']); ?></strong>
                <small>Building materials</small>
            </span>
        </a>
        <nav class="nav" aria-label="Primary navigation">
            <?php foreach ($nav as $path => $label): ?>
                <a class="<?= $currentPath === $path || ($path === '/admin' && str_starts_with($currentPath, '/admin')) ? 'active' : ''; ?>" href="<?= e(url($path)); ?>"><?= e($label); ?></a>
            <?php endforeach; ?>
            <?php if ($adminUser): ?>
                <span class="nav-user"><?= e($adminUser['name']); ?></span>
            <?php endif; ?>
        </nav>
    </header>

    <main>
        <?= $content; ?>
    </main>

    <footer class="site-footer">
        <div>
            <strong><?= e($config['name']); ?></strong>
            <p>cPanel-ready PHP/MySQL platform for gypsum ceilings, drywall partitions, acoustic systems, BOQ tools, and contractor inquiries.</p>
        </div>
        <div class="footer-links">
            <a href="<?= e(url('/calculators')); ?>">Material calculators</a>
            <a href="<?= e(url('/products')); ?>">Product catalog</a>
            <a href="<?= e(url('/blog')); ?>">Blog</a>
            <a href="<?= e(url('/gallery')); ?>">Gallery</a>
            <a href="<?= e(url('/admin')); ?>">Admin structure</a>
        </div>
    </footer>

    <script src="<?= e(asset('js/app.js')); ?>" defer></script>
</body>
</html>
