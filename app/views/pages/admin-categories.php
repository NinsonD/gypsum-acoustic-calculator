<section class="admin-heading">
    <div>
        <p class="eyebrow">Catalog management</p>
        <h1>Categories</h1>
        <p>Manage product categories such as gypsum ceiling, acoustic ceiling, drywall partition, and soundproofing.</p>
    </div>
    <?php partial('admin-nav', ['active' => 'categories']); ?>
</section>

<?php if (!empty($success)): ?><div class="notice success"><?= e($success); ?></div><?php endif; ?>
<?php if (!empty($error)): ?><div class="notice error"><?= e($error); ?></div><?php endif; ?>

<section class="detail-grid admin-crud-grid">
    <form class="panel" method="post" action="<?= e(url('/admin/categories/save')); ?>">
        <h2><?= $edit ? 'Edit category' : 'Add category'; ?></h2>
        <input type="hidden" name="_csrf" value="<?= e(csrf_token()); ?>">
        <input type="hidden" name="id" value="<?= e($edit['id'] ?? ''); ?>">
        <label>
            <span>Name</span>
            <input name="name" value="<?= e($edit['name'] ?? ''); ?>" required>
        </label>
        <label>
            <span>Slug</span>
            <input name="slug" value="<?= e($edit['slug'] ?? ''); ?>" placeholder="auto-generated when empty">
        </label>
        <div class="button-row">
            <button class="button primary" type="submit">Save category</button>
            <?php if ($edit): ?><a class="button" href="<?= e(url('/admin/categories')); ?>">Cancel</a><?php endif; ?>
        </div>
    </form>

    <div class="panel">
        <h2>Category list</h2>
        <div class="table-wrap">
            <table>
                <thead><tr><th>Name</th><th>Slug</th><th>Actions</th></tr></thead>
                <tbody>
                    <?php foreach ($categories as $category): ?>
                        <tr>
                            <td><strong><?= e($category['name']); ?></strong></td>
                            <td><?= e($category['slug']); ?></td>
                            <td>
                                <div class="table-actions">
                                    <a class="button" href="<?= e(url('/admin/categories?edit=' . $category['id'])); ?>">Edit</a>
                                    <form method="post" action="<?= e(url('/admin/categories/' . $category['id'] . '/delete')); ?>" onsubmit="return confirm('Delete this category? Products will keep working without a category.');">
                                        <input type="hidden" name="_csrf" value="<?= e(csrf_token()); ?>">
                                        <button class="button danger" type="submit">Delete</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    <?php if (count($categories) === 0): ?><tr><td colspan="3">No categories yet.</td></tr><?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</section>
