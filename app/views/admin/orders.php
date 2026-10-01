<section>
    <div class="admin-head">
        <h1>Đơn hàng (<?= number_format($count) ?>)</h1>
    </div>

    <form class="filter-bar" method="get" action="<?= url('/admin/don-hang') ?>">
        <input type="text" name="keyword" placeholder="Mã đơn, tên, email khách..." value="<?= e($filters['keyword'] ?? '') ?>">
        <select name="status">
            <option value="">Tất cả trạng thái</option>
            <?php foreach (['pending' => 'Chờ xử lý', 'processing' => 'Đang xử lý', 'shipping' => 'Đang giao', 'completed' => 'Hoàn thành', 'cancelled' => 'Đã hủy'] as $value => $label): ?>
                <option value="<?= e($value) ?>" <?= ($filters['status'] ?? '') === $value ? 'selected' : '' ?>><?= e($label) ?></option>
            <?php endforeach; ?>
        </select>
        <input type="date" name="from" value="<?= e($filters['from'] ?? '') ?>">
        <input type="date" name="to" value="<?= e($filters['to'] ?? '') ?>">
        <button type="submit" class="btn btn-sm">Lọc</button>
        <a class="btn btn-sm" href="<?= url('/admin/don-hang') ?>">Bỏ lọc</a>
    </form>

    <div class="table-responsive">
    <table class="admin-table">
        <thead>
        <tr>
            <th>Mã đơn</th>
            <th>Khách hàng</th>
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
                <td>
                    <?= e($order['user_name']) ?>
                    <div class="table-sub"><?= e($order['user_email']) ?></div>
                </td>
                <td><?= formatPrice($order['total']) ?></td>
                <td><?= $order['payment_method'] === 'vnpay' ? 'VNPay' : 'COD' ?></td>
                <td><span class="badge badge-<?= e(orderStatusClass($order['status'])) ?>"><?= e(orderStatusLabel($order['status'])) ?></span></td>
                <td><?= e(date('d/m/Y H:i', strtotime((string) $order['created_at']))) ?></td>
                <td><a class="btn btn-xs" href="<?= url('/admin/don-hang/chi-tiet?id=' . (int) $order['id']) ?>">Chi tiết</a></td>
            </tr>
        <?php endforeach; ?>
        <?php if ($orders === []): ?>
            <tr><td colspan="7" class="table-empty">Không tìm thấy đơn hàng nào.</td></tr>
        <?php endif; ?>
        </tbody>
    </table>
    </div>

    <?php if ($pages > 1): ?>
        <?= paginationLinks($page, $pages, array_filter($filters, static fn ($v) => $v !== ''), '/admin/don-hang') ?>
    <?php endif; ?>
</section>