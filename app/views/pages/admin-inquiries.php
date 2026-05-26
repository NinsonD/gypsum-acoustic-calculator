<section class="admin-heading">
    <div>
        <p class="eyebrow">Lead management</p>
        <h1>Inquiries</h1>
        <p>Latest contact form submissions saved to MySQL.</p>
    </div>
    <?php partial('admin-nav', ['active' => 'inquiries']); ?>
</section>

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
                        <td><?= e($inquiry['status']); ?></td>
                        <td><?= e($inquiry['created_at']); ?></td>
                    </tr>
                <?php endforeach; ?>
                <?php if (count($inquiries) === 0): ?>
                    <tr><td colspan="7">No inquiries saved yet.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</section>
