<section class="page-heading">
    <p class="eyebrow">Product systems</p>
    <h1>Gypsum, drywall, and acoustic catalog</h1>
    <p>Seed data for manufacturer pages, technical specifications, NRC/STC values, fire ratings, and downloadable resource links.</p>
</section>

<section class="card-grid three">
    <?php foreach ($products as $product): ?>
        <article class="card">
            <a class="product-card-link" href="https://namariqgroup.ae/" target="_blank" rel="noreferrer">
                <?php if (!empty($product['image'])): ?>
                    <img class="media-card sm" src="<?= e(url('/' . ltrim((string) $product['image'], '/'))); ?>" alt="<?= e($product['name']); ?>">
                <?php endif; ?>
                <p class="tag"><?= e($product['category']); ?></p>
                <h2><?= e($product['name']); ?></h2>
                <p class="muted"><?= e($product['brand']['name'] ?? 'Manufacturer'); ?></p>
                <p><?= e($product['summary']); ?></p>
                <span class="button linkish">View on ecommerce site</span>
            </a>
        </article>
    <?php endforeach; ?>
</section>
