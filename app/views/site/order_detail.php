<nav class="breadcrumb">
    <a href="<?= url('/don-hang') ?>">Đơn hàng của tôi</a>
    <span>›</span>
    <span><?= e($order['code']) ?></span>
</nav>

<h1>Đơn hàng <?= e($order['code']) ?></h1>
<p class="detail-meta">
    Ngày đặt: <?= e(date('d/m/Y H:i', strtotime($order['created_at']))) ?> |
    Trạng thái: <span class="order-status <?= e(orderStatusClass($order['status'])) ?>"><?= e(orderStatusLabel($order['status'])) ?></span>
</p>

<div class="order-detail-layout">
    <div class="order-detail-main">
        <section class="checkout-section">
            <h2>Sản phẩm</h2>
            <div class="table-wrap">
                <table class="data-table">
                    <thead>
                    <tr><th>Sách</th><th>Giá</th><th>Số lượng</th><th>Thành tiền</th></tr>
                    </thead>
                    <tbody>
                    <?php foreach ($items as $item): ?>
                        <tr>
                            <td><a href="<?= url('/sach/chi-tiet?id=' . (int) $item['book_id']) ?>"><?= e($item['title']) ?><?= (int) $item['volume'] > 1 ? ' - Tập ' . (int) $item['volume'] : '' ?></a></td>
                            <td><?= e(formatPrice((float) $item['price'])) ?></td>
                            <td><?= (int) $item['quantity'] ?></td>
                            <td><strong><?= e(formatPrice((float) $item['price'] * (int) $item['quantity'])) ?></strong></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <div class="summary-box">
                <div class="summary-row"><span>Tạm tính</span><span><?= e(formatPrice((float) $order['subtotal'])) ?></span></div>
                <?php if ((float) $order['discount'] > 0): ?>
                    <div class="summary-row discount"><span>Giảm giá</span><span>-<?= e(formatPrice((float) $order['discount'])) ?></span></div>
                <?php endif; ?>
                <div class="summary-row"><span>Phí vận chuyển</span><span><?= e(formatPrice((float) $order['shipping_fee'])) ?></span></div>
                <div class="summary-row total"><span>Tổng cộng</span><span><?= e(formatPrice((float) $order['total'])) ?></span></div>
            </div>
        </section>

        <section class="checkout-section">
            <h2>Địa chỉ giao hàng</h2>
            <p><strong><?= e($order['full_name']) ?></strong> (<?= e($order['phone']) ?>)</p>
            <p><?= e($order['address_detail'] ?? '') ?>, <?= e($order['ward'] ?? '') ?>, <?= e($order['district'] ?? '') ?>, <?= e($order['province'] ?? '') ?></p>
            <?php if ($order['note']): ?>
                <p class="detail-meta">Ghi chú: <?= e($order['note']) ?></p>
            <?php endif; ?>
        </section>
    </div>

    <aside class="order-detail-side">
        <section class="checkout-section">
            <h2>Lịch sử trạng thái</h2>
            <ul class="status-timeline">
                <?php foreach ($history as $h): ?>
                    <li>
                        <strong><?= e(orderStatusLabel($h['status'])) ?></strong>
                        <span><?= e(date('d/m/Y H:i', strtotime($h['created_at']))) ?></span>
                        <?php if ($h['note']): ?><p><?= e($h['note']) ?></p><?php endif; ?>
                    </li>
                <?php endforeach; ?>
            </ul>
        </section>

        <?php if (canCancelOrder($order)): ?>
            <form method="post" action="<?= url('/don-hang/huy') ?>" onsubmit="return confirm('Bạn chắc chắn muốn hủy đơn hàng này?')">
                <?= csrfField() ?>
                <input type="hidden" name="id" value="<?= (int) $order['id'] ?>">
                <button type="submit" class="btn btn-danger btn-block">Hủy đơn hàng</button>
            </form>
        <?php endif; ?>
    </aside>
</div>