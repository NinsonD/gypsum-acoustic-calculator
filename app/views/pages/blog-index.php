<section class="page-heading">
    <p class="eyebrow">SEO articles</p>
    <h1>Blog</h1>
    <p>Practical gypsum, drywall, acoustic, and soundproofing articles for contractors and consultants in the UAE and GCC.</p>
</section>

<section class="card-grid three">
    <?php foreach ($posts as $post): ?>
        <article class="card">
            <?php if (!empty($post['image'])): ?>
                <img src="<?= e(url('/' . ltrim((string) $post['image'], '/'))); ?>" alt="<?= e($post['title']); ?>" style="width:100%;height:180px;object-fit:cover;border-radius:8px;margin-bottom:12px;">
            <?php endif; ?>
            <p class="tag"><?= e($post['status']); ?></p>
            <h2><?= e($post['title']); ?></h2>
            <p class="muted">By <?= e($post['author_name']); ?><?php if (!empty($post['published_at'])): ?> · <?= e($post['published_at']); ?><?php endif; ?></p>
            <p><?= e($post['excerpt']); ?></p>
            <a href="<?= e($post['download_url']); ?>">Read article</a>
        </article>
    <?php endforeach; ?>
    <?php if (count($posts) === 0): ?>
        <article class="card"><h2>No posts yet</h2><p>Publish your first SEO article from the admin panel.</p></article>
    <?php endif; ?>
</section>
