<?php
$isEdit = !empty($account['id']);
?>
<section class="admin-heading">
    <div>
        <p class="eyebrow">Access management</p>
        <h1><?= $isEdit ? 'Edit user' : 'Add user'; ?></h1>
        <p>Create accounts for admins, editors, and contractors with role-based permissions.</p>
    </div>
    <?php partial('admin-nav', ['active' => 'users']); ?>
</section>

<?php if (!empty($error)): ?><div class="notice error"><?= e($error); ?></div><?php endif; ?>

<form class="panel admin-product-form" method="post" action="<?= e(url('/admin/users/save')); ?>">
    <input type="hidden" name="_csrf" value="<?= e(csrf_token()); ?>">
    <input type="hidden" name="id" value="<?= e($account['id'] ?? ''); ?>">

    <div class="form-grid">
        <label>
            <span>Name</span>
            <input name="name" value="<?= e($account['name'] ?? ''); ?>" required>
        </label>
        <label>
            <span>Email</span>
            <input type="email" name="email" value="<?= e($account['email'] ?? ''); ?>" required>
        </label>
        <label>
            <span>Role</span>
            <select name="role_id" required>
                <option value="">Select role</option>
                <?php foreach ($roles as $role): ?>
                    <option value="<?= e($role['id']); ?>" <?= (int) ($account['role_id'] ?? 0) === (int) $role['id'] ? 'selected' : ''; ?>>
                        <?= e($role['role_name']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </label>
        <label>
            <span>Password <?= $isEdit ? '(leave blank to keep)' : ''; ?></span>
            <input type="password" name="password" <?= $isEdit ? '' : 'required'; ?> minlength="10" autocomplete="<?= $isEdit ? 'new-password' : 'new-password'; ?>">
        </label>
    </div>

    <div class="button-row">
        <button class="button primary" type="submit">Save user</button>
        <a class="button" href="<?= e(url('/admin/users')); ?>">Cancel</a>
    </div>
</form>
