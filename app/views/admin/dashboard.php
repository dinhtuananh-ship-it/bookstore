<section>
    <div class="stat-grid">
        <div class="stat-card reveal">
            <span class="stat-icon">📖</span>
            <div class="stat-value"><span class="counter" data-target="<?= (int) $bookCount ?>"></span></div>
            <div class="stat-label">Tổng số sách</div>
        </div>
        <div class="stat-card reveal reveal-delay-1">
            <span class="stat-icon">🧾</span>
            <div class="stat-value"><span class="counter" data-target="<?= (int) $stats['count'] ?>"></span></div>
            <div class="stat-label">Đơn hàng</div>
        </div>
        <div class="stat-card reveal reveal-delay-2">
            <span class="stat-icon">💰</span>
            <div class="stat-value" style="font-size:20px;"><span class="counter" data-target="<?= (float) $stats['revenue'] ?>" data-prefix="" data-suffix=" ₫"></span></div>
            <div class="stat-label">Doanh thu</div>
        </div>
        <div class="stat-card reveal reveal-delay-3">
            <span class="stat-icon">👥</span>
            <div class="stat-value"><span class="counter" data-target="<?= (int) $memberCount ?>"></span></div>
            <div class="stat-label">Khách hàng</div>
        </div>
    </div>

    <h2 class="section-title">🗂️ Đơn hàng theo trạng thái</h2>
    <div class="stat-grid">
        <?php
        $statusMeta = [
            'pending'      => ['Chờ xử lý', '⏳', 'badge-pending'],
            'paid'         => ['Đã thanh toán', '💳', 'badge-pending'],
            'processing'   => ['Đang xử lý', '⚙️', 'badge-processing'],
            'shipping'     => ['Đang giao', '🚚', 'badge-shipping'],
            'completed'    => ['Hoàn thành', '✅', 'badge-completed'],
            'cancelled'    => ['Đã hủy', '❌', 'badge-cancelled'],
        ];
        ?>
        <?php foreach ($statusMeta as $status => [$label, $icon, $badgeCls]): ?>
            <div class="stat-card reveal">
                <span class="stat-icon"><?= $icon ?></span>
                <div class="stat-value stat-value-sm"><span class="counter" data-target="<?= (int) ($stats['byStatus'][$status] ?? 0) ?>"></span></div>
                <div class="stat-label"><?= e($label) ?></div>
            </div>
        <?php endforeach; ?>
    </div>

    <h2 class="section-title">🕐 Đơn hàng gần đây</h2>
    <div class="table-responsive">
        <table class="admin-table">
            <thead>
            <tr>
                <th>Mã đơn</th>
                <th>Khách hàng</th>
                <th>Tổng tiền</th>
                <th>Trạng thái</th>
                <th>Ngày đặt</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($stats['recent'] as $order): ?>
                <tr>
                    <td><strong><?= e($order['code']) ?></strong></td>
                    <td><?= e($order['user_name']) ?></td>
                    <td><?= formatPrice($order['total']) ?></td>
                    <td><span class="badge badge-<?= e(orderStatusClass($order['status'])) ?>"><?= e(orderStatusLabel($order['status'])) ?></span></td>
                    <td><?= e(date('d/m/Y H:i', strtotime((string) $order['created_at']))) ?></td>
                </tr>
            <?php endforeach; ?>
            <?php if ($stats['recent'] === []): ?>
                <tr><td colspan="5" class="table-empty">Chưa có đơn hàng nào.</td></tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</section>