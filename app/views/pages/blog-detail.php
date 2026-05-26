<section class="page-heading">
    <p class="eyebrow">Blog article</p>
    <h1><?= e($post['title']); ?></h1>
    <p class="muted">By <?= e($post['author_name']); ?><?php if (!empty($post['published_at'])): ?> · <?= e($post['published_at']); ?><?php endif; ?></p>
</section>

<section class="detail-grid">
    <article class="panel">
        <?php if (!empty($post['image'])): ?>
            <img src="<?= e(url('/' . ltrim((string) $post['image'], '/'))); ?>" alt="<?= e($post['title']); ?>" style="width:100%;height:260px;object-fit:cover;border-radius:8px;margin-bottom:16px;">
        <?php endif; ?>
        <?php foreach (preg_split("/\r\n\r\n|\n\n|\r\r/", trim((string) $post['content'])) ?: [] as $paragraph): ?>
            <p><?= e(trim($paragraph)); ?></p>
        <?php endforeach; ?>
    </article>

    <article class="panel">
        <h2>SEO details</h2>
        <div class="spec-row"><strong>Slug</strong><span><?= e($post['slug']); ?></span></div>
        <div class="spec-row"><strong>Meta title</strong><span><?= e($post['meta_title'] ?: $post['title']); ?></span></div>
        <div class="spec-row"><strong>Meta description</strong><span><?= e($post['meta_description'] ?: $post['excerpt']); ?></span></div>
        <div class="spec-row"><strong>Status</strong><span><?= e($post['status']); ?></span></div>
    </article>
</section>
