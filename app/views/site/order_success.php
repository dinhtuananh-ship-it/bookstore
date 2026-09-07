<div class="success-box">
    <div class="success-icon">✅</div>
    <h1>Đặt hàng thành công!</h1>
    <p>Mã đơn hàng của bạn: <strong><?= e($order['code']) ?></strong></p>
    <p>Trạng thái: <span class="order-status"><?= e($order['status']) ?></span></p>

    <div class="success-summary">
        <h3>Chi tiết đơn hàng</h3>
        <?php foreach ($items as $item): ?>
            <div class="summary-line">
                <span><?= e($item['title']) ?> × <?= (int) $item['quantity'] ?></span>
                <span><?= e(formatPrice((float) $item['price'] * (int) $item['quantity'])) ?></span>
            </div>
        <?php endforeach; ?>
        <div class="summary-row"><span>Tạm tính</span><span><?= e(formatPrice((float) $order['subtotal'])) ?></span></div>
        <?php if ((float) $order['discount'] > 0): ?>
            <div class="summary-row discount"><span>Giảm giá</span><span>-<?= e(formatPrice((float) $order['discount'])) ?></span></div>
        <?php endif; ?>
        <div class="summary-row"><span>Phí vận chuyển</span><span><?= e(formatPrice((float) $order['shipping_fee'])) ?></span></div>
        <div class="summary-row total"><span>Tổng cộng</span><span><?= e(formatPrice((float) $order['total'])) ?></span></div>
    </div>

    <p class="summary-note">Chúng tôi đã gửi email xác nhận. Bạn có thể theo dõi đơn hàng trong mục
        <a href="<?= url('/don-hang') ?>">Đơn hàng của tôi</a> (sẽ có ở giai đoạn sau).</p>
    <a class="btn" href="<?= url('/') ?>">Về trang chủ</a>
</div>