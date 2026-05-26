<section class="page-heading">
    <p class="eyebrow">Resources</p>
    <h1>Download center</h1>
    <p>Downloadable BOQ templates, method statements, acoustic guides, CAD details, and inspection checklists.</p>
</section>

<section class="card-grid three">
    <?php foreach ($downloads as $resource): ?>
        <article class="card">
            <p class="tag"><?= e($resource['type'] ?? 'FILE'); ?></p>
            <h2><?= e($resource['title']); ?></h2>
            <p><?= e($resource['summary']); ?></p>
            <?php if (!empty($resource['download_url'])): ?>
                <a class="button" href="<?= e($resource['download_url']); ?>">Download</a>
            <?php else: ?>
                <button class="button" type="button" disabled>Pending</button>
            <?php endif; ?>
        </article>
    <?php endforeach; ?>
</section>
