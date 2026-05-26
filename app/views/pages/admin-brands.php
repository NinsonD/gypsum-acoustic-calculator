<section class="admin-heading">
    <div>
        <p class="eyebrow">Catalog management</p>
        <h1>Brands</h1>
        <p>Add and edit manufacturer/system brands used by the product catalog.</p>
    </div>
    <?php partial('admin-nav', ['active' => 'brands']); ?>
</section>

<?php if (!empty($success)): ?><div class="notice success"><?= e($success); ?></div><?php endif; ?>
<?php if (!empty($error)): ?><div class="notice error"><?= e($error); ?></div><?php endif; ?>

<section class="detail-grid admin-crud-grid">
    <form class="panel" method="post" action="<?= e(url('/admin/brands/save')); ?>">
        <h2><?= $edit ? 'Edit brand' : 'Add brand'; ?></h2>
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
        <label>
            <span>Description</span>
            <textarea name="description" rows="5"><?= e($edit['description'] ?? ''); ?></textarea>
        </label>
        <div class="button-row">
            <button class="button primary" type="submit">Save brand</button>
            <?php if ($edit): ?><a class="button" href="<?= e(url('/admin/brands')); ?>">Cancel</a><?php endif; ?>
        </div>
    </form>

    <div class="panel">
        <h2>Brand list</h2>
        <div class="table-wrap">
            <table>
                <thead><tr><th>Name</th><th>Slug</th><th>Description</th><th>Actions</th></tr></thead>
                <tbody>
                    <?php foreach ($brands as $brand): ?>
                        <tr>
                            <td><strong><?= e($brand['name']); ?></strong></td>
                            <td><?= e($brand['slug']); ?></td>
                            <td><?= e($brand['description']); ?></td>
                            <td>
                                <div class="table-actions">
                                    <a class="button" href="<?= e(url('/admin/brands?edit=' . $brand['id'])); ?>">Edit</a>
                                    <form method="post" action="<?= e(url('/admin/brands/' . $brand['id'] . '/delete')); ?>" onsubmit="return confirm('Delete this brand? Products will keep working without a brand.');">
                                        <input type="hidden" name="_csrf" value="<?= e(csrf_token()); ?>">
                                        <button class="button danger" type="submit">Delete</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    <?php if (count($brands) === 0): ?><tr><td colspan="4">No brands yet.</td></tr><?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</section>
