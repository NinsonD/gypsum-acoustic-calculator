<section class="admin-heading">
    <div>
        <p class="eyebrow">Lead management</p>
        <h1>Inquiries</h1>
        <p>Latest contact form submissions saved to MySQL.</p>
    </div>
    <?php partial('admin-nav', ['active' => 'inquiries']); ?>
</section>

<?php if (!empty($success)): ?><div class="notice success"><?= e($success); ?></div><?php endif; ?>
<?php if (!empty($error)): ?><div class="notice error"><?= e($error); ?></div><?php endif; ?>

<section class="panel">
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Reference</th>
                    <th>Customer</th>
                    <th>Contact</th>
                    <th>Type</th>
                    <th>Message</th>
                    <th>Status</th>
                    <th>Update</th>
                    <th>Date</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($inquiries as $inquiry): ?>
                    <tr>
                        <td><?= e($inquiry['reference'] ?? ('LEAD-' . $inquiry['id'])); ?></td>
                        <td><strong><?= e($inquiry['customer_name']); ?></strong></td>
                        <td><?= e($inquiry['email']); ?><br><?= e($inquiry['phone']); ?></td>
                        <td><?= e($inquiry['inquiry_type']); ?></td>
                        <td><?= e($inquiry['message']); ?></td>
                        <td><strong><?= e($inquiry['status']); ?></strong></td>
                        <td>
                            <form method="post" action="<?= e(url('/admin/inquiries/' . $inquiry['id'] . '/status')); ?>" class="status-inline">
                                <input type="hidden" name="_csrf" value="<?= e(csrf_token()); ?>">
                                <select name="status">
                                    <option value="new" <?= $inquiry['status'] === 'new' ? 'selected' : ''; ?>>New</option>
                                    <option value="contacted" <?= $inquiry['status'] === 'contacted' ? 'selected' : ''; ?>>Contacted</option>
                                    <option value="quoted" <?= $inquiry['status'] === 'quoted' ? 'selected' : ''; ?>>Quoted</option>
                                    <option value="closed" <?= $inquiry['status'] === 'closed' ? 'selected' : ''; ?>>Closed</option>
                                </select>
                                <button class="button" type="submit">Save</button>
                            </form>
                        </td>
                        <td><?= e($inquiry['created_at']); ?></td>
                    </tr>
                <?php endforeach; ?>
                <?php if (count($inquiries) === 0): ?>
                    <tr><td colspan="8">No inquiries saved yet.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</section>
