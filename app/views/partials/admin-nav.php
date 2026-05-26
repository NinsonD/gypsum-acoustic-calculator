<div class="admin-actions">
    <?php $config = $config ?? app_config(); ?>
    <a class="button <?= ($active ?? '') === 'dashboard' ? 'primary' : ''; ?>" href="<?= e(url('/admin')); ?>">Dashboard</a>
    <?php if (Auth::hasPermission($config, 'leads')): ?>
        <a class="button <?= ($active ?? '') === 'inquiries' ? 'primary' : ''; ?>" href="<?= e(url('/admin/inquiries')); ?>">Inquiries</a>
        <a class="button <?= ($active ?? '') === 'boqs' ? 'primary' : ''; ?>" href="<?= e(url('/admin/boqs')); ?>">BOQs</a>
    <?php endif; ?>
    <?php if (Auth::hasPermission($config, 'products')): ?>
        <a class="button <?= ($active ?? '') === 'products' ? 'primary' : ''; ?>" href="<?= e(url('/admin/products')); ?>">Products</a>
        <a class="button <?= ($active ?? '') === 'brands' ? 'primary' : ''; ?>" href="<?= e(url('/admin/brands')); ?>">Brands</a>
        <a class="button <?= ($active ?? '') === 'categories' ? 'primary' : ''; ?>" href="<?= e(url('/admin/categories')); ?>">Categories</a>
    <?php endif; ?>
    <?php if (Auth::hasPermission($config, 'blog')): ?>
        <a class="button <?= ($active ?? '') === 'blogs' ? 'primary' : ''; ?>" href="<?= e(url('/admin/blogs')); ?>">Blogs</a>
    <?php endif; ?>
    <?php if (Auth::hasPermission($config, 'downloads')): ?>
        <a class="button <?= ($active ?? '') === 'downloads' ? 'primary' : ''; ?>" href="<?= e(url('/admin/downloads')); ?>">Downloads</a>
    <?php endif; ?>
    <?php if (Auth::hasPermission($config, 'gallery')): ?>
        <a class="button <?= ($active ?? '') === 'gallery' ? 'primary' : ''; ?>" href="<?= e(url('/admin/gallery')); ?>">Gallery</a>
    <?php endif; ?>
    <?php if (Auth::hasPermission($config, 'users')): ?>
        <a class="button <?= ($active ?? '') === 'users' ? 'primary' : ''; ?>" href="<?= e(url('/admin/users')); ?>">Users</a>
    <?php endif; ?>
    <form method="post" action="<?= e(url('/admin/logout')); ?>">
        <input type="hidden" name="_csrf" value="<?= e(csrf_token()); ?>">
        <button class="button" type="submit">Logout</button>
    </form>
</div>
