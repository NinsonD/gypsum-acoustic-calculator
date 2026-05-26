<section class="page-heading">
    <p class="eyebrow">Product systems</p>
    <h1>Gypsum, drywall, and acoustic catalog</h1>
    <p>Seed data for manufacturer pages, technical specifications, NRC/STC values, fire ratings, and downloadable resource links.</p>
</section>

<section class="card-grid three">
    <?php foreach ($products as $product): ?>
        <article class="card">
            <p class="tag"><?= e($product['category']); ?></p>
            <h2><?= e($product['name']); ?></h2>
            <p class="muted"><?= e($product['brand']['name'] ?? 'Manufacturer'); ?></p>
            <p><?= e($product['summary']); ?></p>
            <a href="<?= e(url('/products/' . $product['slug'])); ?>">View product</a>
        </article>
    <?php endforeach; ?>
</section>
