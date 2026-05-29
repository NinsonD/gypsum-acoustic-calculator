<section class="page-heading">
    <p class="eyebrow"><?= e($product['brand']['name'] ?? 'System'); ?></p>
    <h1><?= e($product['name']); ?></h1>
    <p><?= e($product['description']); ?></p>
</section>

<section class="detail-grid">
    <article class="panel">
        <?php if (!empty($product['image'])): ?>
            <img class="media-card lg" src="<?= e(url('/' . ltrim((string) $product['image'], '/'))); ?>" alt="<?= e($product['name']); ?>">
        <?php endif; ?>
        <div class="spec-row"><strong>Category</strong><span><?= e($product['category']); ?></span></div>
        <div class="spec-row"><strong>NRC</strong><span><?= e($product['nrc']); ?></span></div>
        <div class="spec-row"><strong>STC</strong><span><?= e($product['stc']); ?></span></div>
        <div class="spec-row"><strong>Fire rating</strong><span><?= e($product['fire_rating']); ?></span></div>
    </article>

    <article class="panel">
        <h2>Technical specifications</h2>
        <?php foreach ($product['specs'] as $label => $value): ?>
            <div class="spec-row"><strong><?= e($label); ?></strong><span><?= e($value); ?></span></div>
        <?php endforeach; ?>
        <div class="button-row" style="margin-top: 18px;">
            <a class="button primary" href="https://namariqgroup.ae/" target="_blank" rel="noreferrer">View on ecommerce site</a>
            <a class="button" href="https://namariqgroup.com/" target="_blank" rel="noreferrer">Company website</a>
        </div>
    </article>
</section>
