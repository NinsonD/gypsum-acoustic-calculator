<?php
$isEdit = !empty($post['id']);
?>
<section class="admin-heading">
    <div>
        <p class="eyebrow">Content management</p>
        <h1><?= $isEdit ? 'Edit blog post' : 'Add blog post'; ?></h1>
        <p>Write concise SEO articles with proper meta title, meta description, and publish status.</p>
    </div>
    <?php partial('admin-nav', ['active' => 'blogs']); ?>
</section>

<?php if (!empty($error)): ?><div class="notice error"><?= e($error); ?></div><?php endif; ?>

<form class="panel admin-product-form" method="post" enctype="multipart/form-data" action="<?= e(url('/admin/blogs/save')); ?>">
    <input type="hidden" name="_csrf" value="<?= e(csrf_token()); ?>">
    <input type="hidden" name="id" value="<?= e($post['id'] ?? ''); ?>">

    <div class="form-grid">
        <label>
            <span>Title</span>
            <input name="title" value="<?= e($post['title'] ?? ''); ?>" required>
        </label>
        <label>
            <span>Slug</span>
            <input name="slug" value="<?= e($post['slug'] ?? ''); ?>" placeholder="auto-generated when empty">
        </label>
        <label>
            <span>Status</span>
            <select name="status">
                <option value="draft" <?= ($post['status'] ?? 'draft') === 'draft' ? 'selected' : ''; ?>>Draft</option>
                <option value="published" <?= ($post['status'] ?? '') === 'published' ? 'selected' : ''; ?>>Published</option>
            </select>
        </label>
        <label>
            <span>Author ID</span>
            <input name="author_id" value="<?= e($post['author_id'] ?? ''); ?>" placeholder="leave empty to use current user">
        </label>
    </div>

    <div class="form-grid">
        <label>
            <span>Meta title</span>
            <input name="meta_title" value="<?= e($post['meta_title'] ?? ''); ?>">
        </label>
        <label>
            <span>Meta description</span>
            <input name="meta_description" value="<?= e($post['meta_description'] ?? ''); ?>">
        </label>
    </div>

    <label>
        <span>Feature image</span>
        <input type="file" name="image_upload" accept=".jpg,.jpeg,.png,.webp,.gif">
    </label>

    <p class="muted">
        <?php if (!empty($post['image'])): ?>
            Current image: <?= e($post['image']); ?>
        <?php else: ?>
            No image uploaded yet.
        <?php endif; ?>
    </p>

    <label>
        <span>Content</span>
        <textarea name="content" rows="14" placeholder="Write the article content here"><?= e($post['content'] ?? ''); ?></textarea>
    </label>

    <div class="button-row">
        <button class="button primary" type="submit">Save post</button>
        <a class="button" href="<?= e(url('/admin/blogs')); ?>">Cancel</a>
    </div>
</form>
