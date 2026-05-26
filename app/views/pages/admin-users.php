<section class="admin-heading">
    <div>
        <p class="eyebrow">Access management</p>
        <h1>Users</h1>
        <p>Manage admin, editor, and contractor accounts. Permission sets are inherited from roles.</p>
    </div>
    <?php partial('admin-nav', ['active' => 'users']); ?>
</section>

<?php if (!empty($success)): ?><div class="notice success"><?= e($success); ?></div><?php endif; ?>
<?php if (!empty($error)): ?><div class="notice error"><?= e($error); ?></div><?php endif; ?>

<section class="panel">
    <div class="button-row admin-top-actions">
        <a class="button primary" href="<?= e(url('/admin/users/create')); ?>">Add user</a>
    </div>
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Permissions</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $account): ?>
                    <tr>
                        <td><strong><?= e($account['name']); ?></strong></td>
                        <td><?= e($account['email']); ?></td>
                        <td><?= e($account['role_name']); ?></td>
                        <td>
                            <?php foreach ($account['permissions'] as $permission): ?>
                                <span class="tag"><?= e($permission); ?></span>
                            <?php endforeach; ?>
                        </td>
                        <td>
                            <div class="table-actions">
                                <a class="button" href="<?= e(url('/admin/users/' . $account['id'] . '/edit')); ?>">Edit</a>
                                <?php if ((int) ($user['id'] ?? 0) !== (int) $account['id']): ?>
                                    <form method="post" action="<?= e(url('/admin/users/' . $account['id'] . '/delete')); ?>" onsubmit="return confirm('Delete this user?');">
                                        <input type="hidden" name="_csrf" value="<?= e(csrf_token()); ?>">
                                        <button class="button danger" type="submit">Delete</button>
                                    </form>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <?php if (count($users) === 0): ?>
                    <tr><td colspan="5">No users yet.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</section>
