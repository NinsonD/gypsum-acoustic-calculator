<?php
$isEdit = !empty($product['id']);
?>
<section class="admin-heading">
    <div>
        <p class="eyebrow">Catalog management</p>
        <h1><?= $isEdit ? 'Edit product' : 'Add product'; ?></h1>
        <p>Fill the technical fields shown on the public catalog and product detail pages.</p>
    </div>
    <?php partial('admin-nav', ['active' => 'products']); ?>
</section>

<?php if (!empty($error)): ?><div class="notice error"><?= e($error); ?></div><?php endif; ?>

<form class="panel admin-product-form" method="post" enctype="multipart/form-data" action="<?= e(url('/admin/products/save')); ?>">
    <input type="hidden" name="_csrf" value="<?= e(csrf_token()); ?>">
    <input type="hidden" name="id" value="<?= e($product['id'] ?? ''); ?>">

    <div class="form-grid">
        <label>
            <span>Name</span>
            <input name="name" value="<?= e($product['name'] ?? ''); ?>" required>
        </label>
        <label>
            <span>Slug</span>
            <input name="slug" value="<?= e($product['slug'] ?? ''); ?>" placeholder="auto-generated when empty">
        </label>
        <label>
            <span>Brand</span>
            <select name="brand_id">
                <option value="">No brand</option>
                <?php foreach ($brands as $brand): ?>
                    <option value="<?= e($brand['id']); ?>" <?= (int) ($product['brand_id'] ?? 0) === (int) $brand['id'] ? 'selected' : ''; ?>>
                        <?= e($brand['name']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </label>
        <label>
            <span>Category</span>
            <select name="category_id">
                <option value="">No category</option>
                <?php foreach ($categories as $category): ?>
                    <option value="<?= e($category['id']); ?>" <?= (int) ($product['category_id'] ?? 0) === (int) $category['id'] ? 'selected' : ''; ?>>
                        <?= e($category['name']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </label>
    </div>

    <label>
        <span>Short description</span>
        <input name="short_description" value="<?= e($product['short_description'] ?? ''); ?>" maxlength="255">
    </label>

    <label>
        <span>Full description</span>
        <textarea name="description" rows="6"><?= e($product['description'] ?? ''); ?></textarea>
    </label>

    <div class="form-grid">
        <label>
            <span>NRC value</span>
            <input name="nrc_value" value="<?= e($product['nrc_value'] ?? ''); ?>" placeholder="Typical 0.60 - 0.85">
        </label>
        <label>
            <span>STC value</span>
            <input name="stc_value" value="<?= e($product['stc_value'] ?? ''); ?>" placeholder="Assembly dependent">
        </label>
        <label>
            <span>Fire rating</span>
            <input name="fire_rating" value="<?= e($product['fire_rating'] ?? ''); ?>" placeholder="Verify tested assembly">
        </label>
        <label>
            <span>Image path</span>
            <input name="image" value="<?= e($product['image'] ?? ''); ?>" placeholder="/uploads/product.jpg">
        </label>
    </div>

    <label>
        <span>Upload image</span>
        <input type="file" name="image_upload" accept=".jpg,.jpeg,.png,.webp,.gif">
    </label>

    <label>
        <span>Specifications</span>
        <textarea name="specs_text" rows="8" placeholder="Board size: 1200 x 2400 mm&#10;Furring spacing: 400 mm"><?= e($product['specs_text'] ?? ''); ?></textarea>
    </label>

    <label class="check-row">
        <input type="checkbox" name="is_active" value="1" <?= !isset($product['is_active']) || (int) $product['is_active'] === 1 ? 'checked' : ''; ?>>
        <span>Active on public catalog</span>
    </label>

    <div class="button-row">
        <button class="button primary" type="submit">Save product</button>
        <a class="button" href="<?= e(url('/admin/products')); ?>">Cancel</a>
    </div>
</form>
