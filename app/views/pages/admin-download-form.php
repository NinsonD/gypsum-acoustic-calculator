<?php
$isEdit = !empty($download['id']);
?>
<section class="admin-heading">
    <div>
        <p class="eyebrow">Resource management</p>
        <h1><?= $isEdit ? 'Edit download' : 'Add download'; ?></h1>
        <p>Upload the actual file once and let the public download page serve it from the repository.</p>
    </div>
    <?php partial('admin-nav', ['active' => 'downloads']); ?>
</section>

<?php if (!empty($error)): ?><div class="notice error"><?= e($error); ?></div><?php endif; ?>

<form class="panel admin-product-form" method="post" enctype="multipart/form-data" action="<?= e(url('/admin/downloads/save')); ?>">
    <input type="hidden" name="_csrf" value="<?= e(csrf_token()); ?>">
    <input type="hidden" name="id" value="<?= e($download['id'] ?? ''); ?>">

    <div class="form-grid">
        <label>
            <span>Title</span>
            <input name="title" value="<?= e($download['title'] ?? ''); ?>" required>
        </label>
        <label>
            <span>Slug</span>
            <input name="slug" value="<?= e($download['slug'] ?? ''); ?>" placeholder="auto-generated when empty">
        </label>
        <label>
            <span>Category</span>
            <input name="category" value="<?= e($download['category'] ?? ''); ?>" placeholder="BOQ, CAD, datasheet">
        </label>
        <label class="check-row">
            <input type="checkbox" name="is_public" value="1" <?= !isset($download['is_public']) || (int) $download['is_public'] === 1 ? 'checked' : ''; ?>>
            <span>Public download</span>
        </label>
    </div>

    <label>
        <span>Upload file</span>
        <input type="file" name="file_upload" accept=".pdf,.xlsx,.xls,.docx,.zip,.dwg,.jpg,.jpeg,.png">
    </label>

    <p class="muted">
        <?php if (!empty($download['file_path'])): ?>
            Current file: <?= e($download['file_path']); ?>
        <?php else: ?>
            No file uploaded yet.
        <?php endif; ?>
    </p>

    <label>
        <span>Stored file type</span>
        <input name="file_type" value="<?= e($download['file_type'] ?? ''); ?>" placeholder="PDF, XLSX, DWG">
    </label>

    <div class="button-row">
        <button class="button primary" type="submit">Save download</button>
        <a class="button" href="<?= e(url('/admin/downloads')); ?>">Cancel</a>
    </div>
</form>
