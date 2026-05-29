<?php $config = $config ?? app_config(); ?>
<?php
$groups = [
    [
        'label' => 'Overview',
        'items' => [
            ['key' => 'dashboard', 'label' => 'Dashboard', 'href' => '/admin'],
            ['key' => 'inquiries', 'label' => 'Inquiries', 'href' => '/admin/inquiries', 'permission' => 'leads'],
            ['key' => 'boqs', 'label' => 'BOQs', 'href' => '/admin/boqs', 'permission' => 'leads'],
        ],
    ],
    [
        'label' => 'Catalog',
        'items' => [
            ['key' => 'products', 'label' => 'Products', 'href' => '/admin/products', 'permission' => 'products'],
            ['key' => 'brands', 'label' => 'Brands', 'href' => '/admin/brands', 'permission' => 'products'],
            ['key' => 'categories', 'label' => 'Categories', 'href' => '/admin/categories', 'permission' => 'products'],
        ],
    ],
    [
        'label' => 'Content',
        'items' => [
            ['key' => 'blogs', 'label' => 'Blogs', 'href' => '/admin/blogs', 'permission' => 'blog'],
            ['key' => 'downloads', 'label' => 'Downloads', 'href' => '/admin/downloads', 'permission' => 'downloads'],
            ['key' => 'gallery', 'label' => 'Gallery', 'href' => '/admin/gallery', 'permission' => 'gallery'],
        ],
    ],
    [
        'label' => 'System',
        'items' => [
            ['key' => 'users', 'label' => 'Users', 'href' => '/admin/users', 'permission' => 'users'],
        ],
    ],
];
?>
<nav class="admin-actions admin-actions-dropdown" aria-label="Admin sections">
    <?php foreach ($groups as $group): ?>
        <?php
            $visibleItems = array_filter($group['items'], static function (array $item) use ($config): bool {
                return !isset($item['permission']) || Auth::hasPermission($config, $item['permission']);
            });

            if ($visibleItems === []) {
                continue;
            }

            $groupActive = false;
            foreach ($visibleItems as $item) {
                if (($active ?? '') === $item['key']) {
                    $groupActive = true;
                    break;
                }
            }
        ?>
        <details class="admin-action-group" <?= $groupActive ? 'open' : ''; ?>>
            <summary class="admin-action-label <?= $groupActive ? 'active' : ''; ?>"><?= e($group['label']); ?></summary>
            <div class="admin-action-grid">
                <?php foreach ($visibleItems as $item): ?>
                    <a class="button <?= ($active ?? '') === $item['key'] ? 'primary' : ''; ?>" href="<?= e(url($item['href'])); ?>"><?= e($item['label']); ?></a>
                <?php endforeach; ?>
                <?php if ($group['label'] === 'System'): ?>
                    <form method="post" action="<?= e(url('/admin/logout')); ?>" class="admin-logout">
                        <input type="hidden" name="_csrf" value="<?= e(csrf_token()); ?>">
                        <button class="button" type="submit">Logout</button>
                    </form>
                <?php endif; ?>
            </div>
        </details>
    <?php endforeach; ?>
</nav>
