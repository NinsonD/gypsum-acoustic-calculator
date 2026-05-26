<section class="admin-heading">
    <div>
        <p class="eyebrow">BOQ management</p>
        <h1>BOQ estimates</h1>
        <p>Latest calculator estimates saved to MySQL.</p>
    </div>
    <?php partial('admin-nav', ['active' => 'boqs']); ?>
</section>

<section class="panel">
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Reference</th>
                    <th>Project</th>
                    <th>Calculator</th>
                    <th>Area</th>
                    <th>Items</th>
                    <th>Date</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($boqs as $boq): ?>
                    <?php
                    $payload = json_decode((string) $boq['calculations_json'], true);
                    $items = is_array($payload['items'] ?? null) ? $payload['items'] : [];
                    ?>
                    <tr>
                        <td><?= e($boq['reference'] ?? ('BOQ-' . $boq['id'])); ?></td>
                        <td><strong><?= e($boq['project_name']); ?></strong></td>
                        <td><?= e($boq['calculator_type']); ?></td>
                        <td><?= e($boq['area']); ?></td>
                        <td><?= e(count($items)); ?></td>
                        <td><?= e($boq['created_at']); ?></td>
                    </tr>
                <?php endforeach; ?>
                <?php if (count($boqs) === 0): ?>
                    <tr><td colspan="6">No BOQ estimates saved yet.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</section>
