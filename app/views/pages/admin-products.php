<section class="admin-heading">
    <div>
        <p class="eyebrow">Catalog management</p>
        <h1>Products</h1>
        <p>Manage gypsum ceiling, drywall partition, acoustic ceiling, and soundproofing catalog records.</p>
    </div>
    <?php partial('admin-nav', ['active' => 'products']); ?>
</section>

<?php if (!empty($success)): ?><div class="notice success"><?= e($success); ?></div><?php endif; ?>
<?php if (!empty($error)): ?><div class="notice error"><?= e($error); ?></div><?php endif; ?>

<section class="panel">
    <div class="button-row admin-top-actions">
        <a class="button primary" href="<?= e(url('/admin/products/create')); ?>">Add product</a>
        <a class="button" href="<?= e(url('/admin/brands')); ?>">Manage brands</a>
    </div>
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Brand</th>
                    <th>Category</th>
                    <th>NRC/STC</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($products as $product): ?>
                    <tr>
                        <td>
                            <strong><?= e($product['name']); ?></strong><br>
                            <small><?= e($product['slug']); ?></small>
                        </td>
                        <td><?= e($product['brand']['name']); ?></td>
                        <td><?= e($product['category']); ?></td>
                        <td><?= e($product['nrc']); ?><br><?= e($product['stc']); ?></td>
                        <td><?= $product['is_active'] ? 'Active' : 'Inactive'; ?></td>
                        <td>
                            <div class="table-actions">
                                <a class="button" href="<?= e(url('/admin/products/' . $product['id'] . '/edit')); ?>">Edit</a>
                                <a class="button" href="<?= e(url('/products/' . $product['slug'])); ?>" target="_blank" rel="noreferrer">View</a>
                                <form method="post" action="<?= e(url('/admin/products/' . $product['id'] . '/delete')); ?>" onsubmit="return confirm('Delete this product?');">
                                    <input type="hidden" name="_csrf" value="<?= e(csrf_token()); ?>">
                                    <button class="button danger" type="submit">Delete</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <?php if (count($products) === 0): ?><tr><td colspan="6">No products yet.</td></tr><?php endif; ?>
            </tbody>
        </table>
    </div>
</section>
