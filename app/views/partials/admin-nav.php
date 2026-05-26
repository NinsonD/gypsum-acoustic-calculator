<div class="admin-actions">
    <a class="button <?= ($active ?? '') === 'dashboard' ? 'primary' : ''; ?>" href="<?= e(url('/admin')); ?>">Dashboard</a>
    <a class="button <?= ($active ?? '') === 'inquiries' ? 'primary' : ''; ?>" href="<?= e(url('/admin/inquiries')); ?>">Inquiries</a>
    <a class="button <?= ($active ?? '') === 'boqs' ? 'primary' : ''; ?>" href="<?= e(url('/admin/boqs')); ?>">BOQs</a>
    <form method="post" action="<?= e(url('/admin/logout')); ?>">
        <input type="hidden" name="_csrf" value="<?= e(csrf_token()); ?>">
        <button class="button" type="submit">Logout</button>
    </form>
</div>
