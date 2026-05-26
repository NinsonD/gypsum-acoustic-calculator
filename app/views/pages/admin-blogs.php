<section class="admin-heading">
    <div>
        <p class="eyebrow">Content management</p>
        <h1>Blogs</h1>
        <p>Manage SEO articles and technical guides for gypsum, drywall, and acoustic topics.</p>
    </div>
    <?php partial('admin-nav', ['active' => 'blogs']); ?>
</section>

<?php if (!empty($success)): ?><div class="notice success"><?= e($success); ?></div><?php endif; ?>
<?php if (!empty($error)): ?><div class="notice error"><?= e($error); ?></div><?php endif; ?>

<section class="panel">
    <div class="button-row admin-top-actions">
        <a class="button primary" href="<?= e(url('/admin/blogs/create')); ?>">Add post</a>
    </div>
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Title</th>
                    <th>Author</th>
                    <th>Status</th>
                    <th>Published</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($posts as $post): ?>
                    <tr>
                        <td>
                            <strong><?= e($post['title']); ?></strong><br>
                            <small><?= e($post['slug']); ?></small>
                        </td>
                        <td><?= e($post['author_name']); ?></td>
                        <td><?= e($post['status']); ?></td>
                        <td><?= e($post['published_at'] ?: '-'); ?></td>
                        <td>
                            <div class="table-actions">
                                <a class="button" href="<?= e(url('/admin/blogs/' . $post['id'] . '/edit')); ?>">Edit</a>
                                <a class="button" href="<?= e(url('/blog/' . $post['slug'])); ?>" target="_blank" rel="noreferrer">View</a>
                                <form method="post" action="<?= e(url('/admin/blogs/' . $post['id'] . '/delete')); ?>" onsubmit="return confirm('Delete this blog post?');">
                                    <input type="hidden" name="_csrf" value="<?= e(csrf_token()); ?>">
                                    <button class="button danger" type="submit">Delete</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <?php if (count($posts) === 0): ?>
                    <tr><td colspan="5">No blog posts yet.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</section>
