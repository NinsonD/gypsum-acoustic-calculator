<section class="page-heading">
    <p class="eyebrow">Project gallery</p>
    <h1>Gallery</h1>
    <p>Installed gypsum ceilings, drywall partitions, acoustic ceilings, and technical reference projects.</p>
</section>

<section class="card-grid three">
    <?php foreach ($items as $item): ?>
        <article class="card">
            <img src="<?= e($item['image_url']); ?>" alt="<?= e($item['title']); ?>" style="width:100%;height:200px;object-fit:cover;border-radius:8px;margin-bottom:12px;">
            <p class="tag"><?= e($item['category'] ?: 'Project'); ?></p>
            <h2><?= e($item['title']); ?></h2>
            <p class="muted"><?= e($item['project_name'] ?: 'Reference project'); ?></p>
            <p><?= e($item['summary']); ?></p>
        </article>
    <?php endforeach; ?>
    <?php if (count($items) === 0): ?>
        <article class="card"><h2>No gallery items yet</h2><p>Publish your first project image from the admin gallery module.</p></article>
    <?php endif; ?>
</section>
