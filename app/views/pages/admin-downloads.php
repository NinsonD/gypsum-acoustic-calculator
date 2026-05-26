<section class="admin-heading">
    <div>
        <p class="eyebrow">Resource management</p>
        <h1>Downloads</h1>
        <p>Upload BOQ templates, method statements, data sheets, and CAD references.</p>
    </div>
    <?php partial('admin-nav', ['active' => 'downloads']); ?>
</section>

<?php if (!empty($success)): ?><div class="notice success"><?= e($success); ?></div><?php endif; ?>
<?php if (!empty($error)): ?><div class="notice error"><?= e($error); ?></div><?php endif; ?>

<section class="panel">
    <div class="button-row admin-top-actions">
        <a class="button primary" href="<?= e(url('/admin/downloads/create')); ?>">Add download</a>
    </div>
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Title</th>
                    <th>Type</th>
                    <th>Category</th>
                    <th>Public</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($downloads as $download): ?>
                    <tr>
                        <td>
                            <strong><?= e($download['title']); ?></strong><br>
                            <small><?= e($download['slug']); ?></small>
                        </td>
                        <td><?= e($download['file_type']); ?></td>
                        <td><?= e($download['category']); ?></td>
                        <td><?= $download['is_public'] ? 'Yes' : 'No'; ?></td>
                        <td>
                            <div class="table-actions">
                                <a class="button" href="<?= e(url('/admin/downloads/' . $download['id'] . '/edit')); ?>">Edit</a>
                                <a class="button" href="<?= e(url('/' . ltrim((string) $download['file_path'], '/'))); ?>" target="_blank" rel="noreferrer">Open</a>
                                <form method="post" action="<?= e(url('/admin/downloads/' . $download['id'] . '/delete')); ?>" onsubmit="return confirm('Delete this download?');">
                                    <input type="hidden" name="_csrf" value="<?= e(csrf_token()); ?>">
                                    <button class="button danger" type="submit">Delete</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <?php if (count($downloads) === 0): ?>
                    <tr><td colspan="5">No downloads yet.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</section>
