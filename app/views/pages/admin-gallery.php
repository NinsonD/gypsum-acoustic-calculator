<section class="admin-heading">
    <div>
        <p class="eyebrow">Media management</p>
        <h1>Gallery</h1>
        <p>Manage project photos and reference images for the public gallery.</p>
    </div>
    <?php partial('admin-nav', ['active' => 'gallery']); ?>
</section>

<?php if (!empty($success)): ?><div class="notice success"><?= e($success); ?></div><?php endif; ?>
<?php if (!empty($error)): ?><div class="notice error"><?= e($error); ?></div><?php endif; ?>

<section class="panel">
    <div class="button-row admin-top-actions">
        <a class="button primary" href="<?= e(url('/admin/gallery/create')); ?>">Add gallery item</a>
    </div>
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Title</th>
                    <th>Project</th>
                    <th>Category</th>
                    <th>Public</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($items as $item): ?>
                    <tr>
                        <td>
                            <strong><?= e($item['title']); ?></strong><br>
                            <small><?= e($item['slug']); ?></small>
                        </td>
                        <td><?= e($item['project_name']); ?></td>
                        <td><?= e($item['category']); ?></td>
                        <td><?= $item['is_public'] ? 'Yes' : 'No'; ?></td>
                        <td>
                            <div class="table-actions">
                                <a class="button" href="<?= e(url('/admin/gallery/' . $item['id'] . '/edit')); ?>">Edit</a>
                                <a class="button" href="<?= e(url('/gallery')); ?>" target="_blank" rel="noreferrer">Open</a>
                                <form method="post" action="<?= e(url('/admin/gallery/' . $item['id'] . '/delete')); ?>" onsubmit="return confirm('Delete this gallery item?');">
                                    <input type="hidden" name="_csrf" value="<?= e(csrf_token()); ?>">
                                    <button class="button danger" type="submit">Delete</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <?php if (count($items) === 0): ?>
                    <tr><td colspan="5">No gallery items yet.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</section>
