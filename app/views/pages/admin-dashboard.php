<section class="admin-heading">
    <div>
        <p class="eyebrow">Admin dashboard</p>
        <h1>Welcome, <?= e($user['name'] ?? 'Admin'); ?></h1>
        <p>Manage saved inquiries, BOQ estimates, and the next content modules.</p>
    </div>
    <?php partial('admin-nav', ['active' => 'dashboard']); ?>
</section>

<section class="summary-grid">
    <div class="summary-item"><span>Inquiries</span><strong><?= e($stats['inquiries'] ?? 0); ?></strong><small>saved leads</small></div>
    <div class="summary-item"><span>BOQs</span><strong><?= e($stats['boqs'] ?? 0); ?></strong><small>saved estimates</small></div>
    <div class="summary-item"><span>Products</span><strong><?= e($stats['products'] ?? 0); ?></strong><small>database records</small></div>
    <div class="summary-item"><span>Downloads</span><strong><?= e($stats['downloads'] ?? 0); ?></strong><small>resource records</small></div>
</section>

<section class="card-grid four">
    <article class="card"><h2>Products</h2><p>Manage brands, categories, specs, images, NRC, STC, and fire ratings.</p><a href="<?= e(url('/admin/products')); ?>">Open products</a></article>
    <article class="card"><h2>Blogs</h2><p>Next SEO module for gypsum ceiling UAE, drywall Dubai, and acoustic content pages.</p></article>
    <article class="card"><h2>Downloads</h2><p>Next file module for datasheets, BOQ templates, CAD files, and method statements.</p></article>
    <article class="card"><h2>Users</h2><p>Next user module for admin, editor, contractor, and permission management.</p></article>
</section>
