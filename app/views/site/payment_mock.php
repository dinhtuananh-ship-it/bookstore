<div class="success-box">
    <div class="success-icon">🏦</div>
    <h1>Cổng thanh toán demo (VNPay)</h1>
    <p>Đơn hàng: <strong><?= e($order['code']) ?></strong></p>
    <p>Số tiền: <strong><?= e(formatPrice((float) $order['total'])) ?></strong></p>
    <p class="summary-note">Đây là cổng thanh toán mô phỏng cho dự án học tập.
        Trong thực tế, bước này sẽ chuyển hướng sang trang VNPay/Momo và xác minh chữ ký qua webhook.</p>

    <form method="post" action="<?= url('/thanh-toan/online?id=' . (int) $order['id']) ?>">
        <?= csrfField() ?>
        <button type="submit" class="btn btn-big">✔ Xác nhận thanh toán thành công</button>
    </form>
</div>