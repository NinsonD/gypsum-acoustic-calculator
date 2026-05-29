<section class="hero">
    <div class="hero-copy">
        <p class="eyebrow">UAE/GCC construction engineering platform</p>
        <h1><?= e($config['name']); ?></h1>
        <p>Material estimators, BOQ generation, installation references, acoustic guidance, product data, and contractor inquiry routing in a clean cPanel-ready PHP structure.</p>
        <div class="button-row">
            <a class="button primary" href="<?= e(url('/calculators')); ?>">Open calculators</a>
            <a class="button" href="<?= e(url('/products')); ?>">Browse products</a>
            <a class="button" href="<?= e(url('/contact')); ?>">Request inquiry</a>
            <a class="button" href="https://namariqgroup.com/" target="_blank" rel="noreferrer">Company website</a>
        </div>
    </div>
    <img class="hero-art" src="<?= e(asset('images/hero/system.jpg')); ?>" alt="Al Namariq gypsum and acoustic engineering systems">
</section>

<?php partial('calculator'); ?>

<section class="section">
    <div class="section-heading">
        <p class="eyebrow">Core modules</p>
        <h2>Built for contractor workflows</h2>
    </div>
    <div class="card-grid four">
        <article class="card"><h3>Calculators</h3><p>Gypsum ceiling, partition, acoustic tile, and room treatment estimates.</p></article>
        <article class="card"><h3>BOQ tools</h3><p>Line-item output ready for MySQL storage, PDF export, and Excel-friendly downloads.</p></article>
        <article class="card"><h3>Knowledge center</h3><p>NRC, STC, reverberation, soundproofing, and installation education.</p></article>
        <article class="card"><h3>Lead generation</h3><p>Contact and inquiry flow for WhatsApp, email, and CRM handoff.</p></article>
    </div>
</section>

<section class="section">
    <div class="section-heading">
        <p class="eyebrow">Product sample</p>
        <h2>Featured catalog</h2>
    </div>
    <div class="card-grid three">
        <?php foreach ($products as $product): ?>
            <article class="card">
                <a class="product-card-link" href="https://namariqgroup.ae/" target="_blank" rel="noreferrer">
                    <?php if (!empty($product['image'])): ?>
                        <img class="media-card sm" src="<?= e(url('/' . ltrim((string) $product['image'], '/'))); ?>" alt="<?= e($product['name']); ?>">
                    <?php endif; ?>
                    <p class="tag"><?= e($product['category']); ?></p>
                    <h3><?= e($product['name']); ?></h3>
                    <p><?= e($product['summary']); ?></p>
                    <span class="button linkish">View on Online Shop</span>
                </a>
            </article>
        <?php endforeach; ?>
    </div>
</section>
