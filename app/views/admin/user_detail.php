<section>
    <div class="admin-head">
        <h1>Khách hàng: <?= e($user['name']) ?></h1>
        <a class="btn btn-sm" href="<?= url('/admin/khach-hang') ?>">← Quay lại danh sách</a>
    </div>

    <div class="stat-grid">
        <div class="stat-card">
            <div class="stat-value stat-value-sm"><?= number_format((int) $stats['order_count']) ?></div>
            <div class="stat-label">Đơn đã đặt</div>
        </div>
        <div class="stat-card">
            <div class="stat-value stat-value-sm"><?= formatPrice((float) $stats['total_spent']) ?></div>
            <div class="stat-label">Tổng chi tiêu</div>
        </div>
        <div class="stat-card">
            <div class="stat-value stat-value-sm"><?= $stats['last_order'] ? e(date('d/m/Y', strtotime((string) $stats['last_order']))) : '—' ?></div>
            <div class="stat-label">Đơn gần nhất</div>
        </div>
    </div>

    <div class="panel" style="margin-bottom: 16px;">
        <p><strong>Email:</strong> <?= e($user['email']) ?></p>
        <p><strong>SĐT:</strong> <?= e($user['phone'] ?? '—') ?></p>
        <p>
            <strong>Trạng thái:</strong>
            <span class="badge badge-<?= (int) $user['status'] === 1 ? 'success' : 'muted' ?>"><?= (int) $user['status'] === 1 ? 'Hoạt động' : 'Đã khóa' ?></span>
            <strong style="margin-left: 16px;">Ngày tham gia:</strong> <?= e(date('d/m/Y', strtotime((string) $user['created_at']))) ?>
        </p>
    </div>

    <h2 class="section-title">Lịch sử mua hàng</h2>
    <table class="admin-table">
        <thead>
        <tr>
            <th>Mã đơn</th>
            <th>Tổng tiền</th>
            <th>Thanh toán</th>
            <th>Trạng thái</th>
            <th>Ngày đặt</th>
            <th>Thao tác</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($orders as $order): ?>
            <tr>
                <td><strong><?= e($order['code']) ?></strong></td>
                <td><?= formatPrice($order['total']) ?></td>
                <td><?= $order['payment_method'] === 'vnpay' ? 'VNPay' : 'COD' ?></td>
                <td><span class="badge badge-<?= e(orderStatusClass($order['status'])) ?>"><?= e(orderStatusLabel($order['status'])) ?></span></td>
                <td><?= e(date('d/m/Y H:i', strtotime((string) $order['created_at']))) ?></td>
                <td><a class="btn btn-xs" href="<?= url('/admin/don-hang/chi-tiet?id=' . (int) $order['id']) ?>">Chi tiết</a></td>
            </tr>
        <?php endforeach; ?>
        <?php if ($orders === []): ?>
            <tr><td colspan="6" class="table-empty">Khách hàng chưa có đơn hàng nào.</td></tr>
        <?php endif; ?>
        </tbody>
    </table>
</section>