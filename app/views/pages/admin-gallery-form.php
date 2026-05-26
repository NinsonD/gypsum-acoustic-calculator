<?php
$isEdit = !empty($item['id']);
?>
<section class="admin-heading">
    <div>
        <p class="eyebrow">Media management</p>
        <h1><?= $isEdit ? 'Edit gallery item' : 'Add gallery item'; ?></h1>
        <p>Upload a project image with a short project label and summary.</p>
    </div>
    <?php partial('admin-nav', ['active' => 'gallery']); ?>
</section>

<?php if (!empty($error)): ?><div class="notice error"><?= e($error); ?></div><?php endif; ?>

<form class="panel admin-product-form" method="post" enctype="multipart/form-data" action="<?= e(url('/admin/gallery/save')); ?>">
    <input type="hidden" name="_csrf" value="<?= e(csrf_token()); ?>">
    <input type="hidden" name="id" value="<?= e($item['id'] ?? ''); ?>">

    <div class="form-grid">
        <label>
            <span>Title</span>
            <input name="title" value="<?= e($item['title'] ?? ''); ?>" required>
        </label>
        <label>
            <span>Slug</span>
            <input name="slug" value="<?= e($item['slug'] ?? ''); ?>" placeholder="auto-generated when empty">
        </label>
        <label>
            <span>Project name</span>
            <input name="project_name" value="<?= e($item['project_name'] ?? ''); ?>">
        </label>
        <label>
            <span>Category</span>
            <input name="category" value="<?= e($item['category'] ?? ''); ?>" placeholder="Ceiling, Partition, Acoustic">
        </label>
        <label>
            <span>Sort order</span>
            <input type="number" name="sort_order" value="<?= e($item['sort_order'] ?? 0); ?>">
        </label>
        <label class="check-row">
            <input type="checkbox" name="is_public" value="1" <?= !isset($item['is_public']) || (int) $item['is_public'] === 1 ? 'checked' : ''; ?>>
            <span>Public</span>
        </label>
    </div>

    <label>
        <span>Summary</span>
        <textarea name="summary" rows="4"><?= e($item['summary'] ?? ''); ?></textarea>
    </label>

    <label>
        <span>Image upload</span>
        <input type="file" name="image_upload" accept=".jpg,.jpeg,.png,.webp,.gif">
    </label>

    <p class="muted">
        <?php if (!empty($item['image'])): ?>
            Current image: <?= e($item['image']); ?>
        <?php else: ?>
            No image uploaded yet.
        <?php endif; ?>
    </p>

    <div class="button-row">
        <button class="button primary" type="submit">Save item</button>
        <a class="button" href="<?= e(url('/admin/gallery')); ?>">Cancel</a>
    </div>
</form>
