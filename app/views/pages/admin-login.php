<section class="login-shell">
    <form class="panel login-panel" method="post" action="<?= e(url('/admin/login')); ?>">
        <p class="eyebrow">Admin access</p>
        <h1>Login</h1>
        <p>Use an authorized account from the `users` table to access dashboard records.</p>

        <?php if (!empty($error)): ?>
            <div class="notice error"><?= e($error); ?></div>
        <?php endif; ?>

        <input type="hidden" name="_csrf" value="<?= e(csrf_token()); ?>">

        <label>
            <span>Email</span>
            <input type="email" name="email" value="<?= e($email ?? ''); ?>" required autocomplete="username">
        </label>

        <label>
            <span>Password</span>
            <input type="password" name="password" required autocomplete="current-password">
        </label>

        <button class="button primary" type="submit">Login</button>
    </form>
</section>
