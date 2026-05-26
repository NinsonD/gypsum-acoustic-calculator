<section class="page-heading">
    <p class="eyebrow">Method statements</p>
    <h1>Installation tutorials</h1>
    <p>Installation sequences for gypsum ceilings, drywall partitions, and acoustic treatment systems.</p>
</section>

<section class="tutorial-list">
    <?php foreach ($tutorials as $tutorial): ?>
        <article class="panel">
            <h2><?= e($tutorial['title']); ?></h2>
            <div class="step-grid">
                <?php foreach ($tutorial['steps'] as $index => $step): ?>
                    <div class="step">
                        <span><?= $index + 1; ?></span>
                        <strong><?= e($step); ?></strong>
                    </div>
                <?php endforeach; ?>
            </div>
        </article>
    <?php endforeach; ?>
</section>
