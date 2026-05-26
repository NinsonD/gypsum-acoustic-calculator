<section class="hero">
    <div class="hero-copy">
        <p class="eyebrow">UAE/GCC construction engineering platform</p>
        <h1>Gypsum & Acoustic Engineering Systems</h1>
        <p>Material estimators, BOQ previews, installation references, acoustic education, product data, and contractor inquiry routing in a simple cPanel-ready PHP structure.</p>
        <div class="button-row">
            <a class="button primary" href="<?= e(url('/calculators')); ?>">Open calculators</a>
            <a class="button" href="<?= e(url('/products')); ?>">Browse products</a>
        </div>
    </div>
    <div class="technical-visual" aria-label="Ceiling and partition technical visual">
        <div class="slab"></div>
        <div class="hanger h1"></div>
        <div class="hanger h2"></div>
        <div class="hanger h3"></div>
        <div class="main-channel"></div>
        <div class="furring"></div>
        <div class="board"></div>
        <div class="partition">
            <span></span><strong></strong><span></span>
        </div>
    </div>
</section>

<?php partial('calculator'); ?>

<section class="section">
    <div class="section-heading">
        <p class="eyebrow">Core modules</p>
        <h2>Built for contractor workflows</h2>
    </div>
    <div class="card-grid four">
        <article class="card"><h3>Calculators</h3><p>Gypsum ceiling, partition, acoustic tile, and room treatment estimates.</p></article>
        <article class="card"><h3>BOQ tools</h3><p>Line-item output ready for MySQL storage and PDF/Excel export later.</p></article>
        <article class="card"><h3>Knowledge center</h3><p>NRC, STC, reverberation, soundproofing, and installation education.</p></article>
        <article class="card"><h3>Lead generation</h3><p>Contact and inquiry flow for WhatsApp, email, and CRM handoff.</p></article>
    </div>
</section>

<section class="section">
    <div class="section-heading">
        <p class="eyebrow">Product sample</p>
        <h2>Seed catalog</h2>
    </div>
    <div class="card-grid three">
        <?php foreach ($products as $product): ?>
            <article class="card">
                <?php if (!empty($product['image'])): ?>
                    <img src="<?= e(url('/' . ltrim((string) $product['image'], '/'))); ?>" alt="<?= e($product['name']); ?>" style="width:100%;height:180px;object-fit:cover;border-radius:8px;margin-bottom:12px;">
                <?php endif; ?>
                <p class="tag"><?= e($product['category']); ?></p>
                <h3><?= e($product['name']); ?></h3>
                <p><?= e($product['summary']); ?></p>
                <a href="<?= e(url('/products/' . $product['slug'])); ?>">Technical details</a>
            </article>
        <?php endforeach; ?>
    </div>
</section>
