<h1>📦 Đơn hàng của tôi</h1>

<?php if ($orders === []): ?>
    <p class="empty-note">Bạn chưa có đơn hàng nào. <a href="<?= url('/sach') ?>">Mua sách ngay</a></p>
<?php else: ?>
    <div class="table-wrap">
        <table class="data-table">
            <thead>
            <tr>
                <th>Mã đơn</th>
                <th>Ngày đặt</th>
                <th>Tổng tiền</th>
                <th>Thanh toán</th>
                <th>Trạng thái</th>
                <th>Thao tác</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($orders as $order): ?>
                <tr>
                    <td><strong><?= e($order['code']) ?></strong></td>
                    <td><?= e(date('d/m/Y H:i', strtotime($order['created_at']))) ?></td>
                    <td><?= e(formatPrice((float) $order['total'])) ?></td>
                    <td>
                        <?= match ($order['payment_method']) {
                            'cod'   => 'COD',
                            'vnpay' => 'VNPay',
                            'momo'  => 'Momo',
                            default => $order['payment_method'],
                        } ?>
                    </td>
                    <td><span class="order-status <?= e(orderStatusClass($order['status'])) ?><?= $order['status'] === 'shipping' ? ' pulse' : '' ?>"><?= e(orderStatusLabel($order['status'])) ?></span></td>
                    <td><a class="btn btn-sm" href="<?= url('/don-hang/chi-tiet?id=' . (int) $order['id']) ?>">Chi tiết</a></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <?= paginationLinks($page, $pages, [], '/don-hang') ?>
<?php endif; ?>