<?php
$pageTitle = $title ?? $config['name'];
$pageDescription = $description ?? 'Gypsum, drywall, acoustic, and BOQ engineering platform.';
$currentPath = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
$nav = [
    '/calculators' => 'Calculators',
    '/products' => 'Products',
    '/installation' => 'Installation',
    '/knowledge' => 'Knowledge',
    '/downloads' => 'Downloads',
    '/contact' => 'Contact',
];
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e($pageTitle); ?> | <?= e($config['name']); ?></title>
    <meta name="description" content="<?= e($pageDescription); ?>">
    <link rel="stylesheet" href="<?= e(asset('css/app.css')); ?>">
</head>
<body data-base-path="<?= e(base_path()); ?>">
    <header class="site-header">
        <a class="brand" href="<?= e(url('/')); ?>" aria-label="Home">
            <span class="brand-mark">GA</span>
            <span>
                <strong>Gypsum & Acoustic</strong>
                <small>Engineering systems</small>
            </span>
        </a>
        <nav class="nav" aria-label="Primary navigation">
            <?php foreach ($nav as $path => $label): ?>
                <a class="<?= $currentPath === $path ? 'active' : ''; ?>" href="<?= e(url($path)); ?>"><?= e($label); ?></a>
            <?php endforeach; ?>
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
            <a href="<?= e(url('/admin')); ?>">Admin structure</a>
        </div>
    </footer>

    <script src="<?= e(asset('js/app.js')); ?>" defer></script>
</body>
</html>
