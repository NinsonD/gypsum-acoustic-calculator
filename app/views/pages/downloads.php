<section class="page-heading">
    <p class="eyebrow">Resources</p>
    <h1>Download center</h1>
    <p>Resource placeholders for BOQ templates, method statements, acoustic guides, CAD details, and inspection checklists.</p>
</section>

<section class="card-grid three">
    <?php foreach ($downloads as $resource): ?>
        <article class="card">
            <p class="tag"><?= e($resource['type']); ?></p>
            <h2><?= e($resource['title']); ?></h2>
            <p><?= e($resource['summary']); ?></p>
            <button class="button" type="button">Upload pending</button>
        </article>
    <?php endforeach; ?>
</section>
