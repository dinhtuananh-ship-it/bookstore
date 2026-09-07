<h1>🧾 Thanh toán</h1>

<div class="checkout-layout reveal">
    <div class="checkout-form">
        <form method="post" action="<?= url('/thanh-toan') ?>" id="checkout-form" novalidate>
            <?= csrfField() ?>

            <section class="checkout-section">
                <h2>📍 1. Địa chỉ giao hàng</h2>

                <?php if (isset($errors['address'])): ?>
                    <div class="alert alert-error"><?= e($errors['address']) ?></div>
                <?php endif; ?>
                <?php if (isset($errors['stock'])): ?>
                    <div class="alert alert-error"><?= e($errors['stock']) ?></div>
                <?php endif; ?>

                <?php if ($addresses !== []): ?>
                    <div class="address-list">
                        <?php foreach ($addresses as $i => $addr): ?>
                            <label class="address-item">
                                <input type="radio" name="address_mode" value="saved" checked>
                                <input type="radio" name="address_id" value="<?= (int) $addr['id'] ?>" checked>
                                <span>
                                    <strong><?= e($addr['full_name']) ?> (<?= e($addr['phone']) ?>)</strong><br>
                                    <?= e($addr['detail']) ?>, <?= e($addr['ward']) ?>, <?= e($addr['district']) ?>, <?= e($addr['province']) ?>
                                    <?php if ((int) $addr['is_default'] === 1): ?> <em>(Mặc định)</em><?php endif; ?>
                                </span>
                            </label>
                        <?php endforeach; ?>
                    </div>
                    <label class="address-item">
                        <input type="radio" name="address_mode" value="new">
                        <span>➕ Giao đến địa chỉ khác</span>
                    </label>
                <?php endif; ?>

                <div class="new-address-box">
                    <div class="form-group">
                        <label>Họ tên người nhận *</label>
                        <input type="text" name="full_name" value="<?= e(currentUser()['name'] ?? '') ?>">
                    </div>
                    <div class="form-group">
                        <label>Số điện thoại *</label>
                        <input type="tel" name="phone">
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label>Tỉnh/Thành *</label>
                            <input type="text" name="province">
                        </div>
                        <div class="form-group">
                            <label>Quận/Huyện *</label>
                            <input type="text" name="district">
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label>Phường/Xã *</label>
                            <input type="text" name="ward">
                        </div>
                        <div class="form-group">
                            <label>Địa chỉ chi tiết *</label>
                            <input type="text" name="detail">
                        </div>
                    </div>
                </div>
            </section>

            <section class="checkout-section">
                <h2>💳 2. Phương thức thanh toán</h2>
                <label class="payment-item">
                    <input type="radio" name="payment_method" value="cod" checked>
                    <span>💵 Thanh toán khi nhận hàng (COD)</span>
                </label>
                <label class="payment-item">
                    <input type="radio" name="payment_method" value="vnpay">
                    <span>🏦 Thanh toán qua cổng VNPay (demo)</span>
                </label>
            </section>

            <section class="checkout-section">
                <h2>🏷️ 3. Mã giảm giá</h2>
                <div class="coupon-row">
                    <input type="text" name="coupon_code" placeholder="Nhập mã (vd: GIAM10)"
                           value="<?= e($couponCode) ?>">
                    <button type="submit" class="btn" name="apply_coupon" value="1">Áp dụng</button>
                </div>
                <?php if (isset($errors['coupon'])): ?>
                    <div class="alert alert-error"><?= e($errors['coupon']) ?></div>
                <?php endif; ?>
                <?php if ($coupon): ?>
                    <div class="alert alert-success">Đã áp dụng mã <strong><?= e($coupon['code']) ?></strong></div>
                <?php endif; ?>
            </section>

            <section class="checkout-section">
                <h2>📝 4. Ghi chú</h2>
                <textarea name="note" rows="3" placeholder="Ghi chú cho người bán (nếu có)"></textarea>
            </section>

            <button type="submit" class="btn btn-big btn-block">Xác nhận đặt hàng</button>
        </form>
    </div>

    <aside class="cart-summary">
        <h3>Đơn hàng của bạn</h3>
        <?php foreach ($rows as $row): ?>
            <div class="summary-line">
                <span><?= e($row['book']['title']) ?><?= (int) $row['volume'] > 1 ? ' - Tập ' . (int) $row['volume'] : '' ?> × <?= (int) $row['quantity'] ?></span>
                <span><?= e(formatPrice($row['line_total'])) ?></span>
            </div>
        <?php endforeach; ?>
        <div class="summary-row"><span>Tạm tính</span><span><?= e(formatPrice($subtotal)) ?></span></div>
        <?php if ($discount > 0): ?>
            <div class="summary-row discount"><span>Giảm giá</span><span>-<?= e(formatPrice($discount)) ?></span></div>
        <?php endif; ?>
        <div class="summary-row">
            <span>Phí vận chuyển</span>
            <span><?= $shippingFee > 0 ? e(formatPrice($shippingFee)) : 'Miễn phí' ?></span>
        </div>
        <div class="summary-row total"><span>Tổng cộng</span><span><?= e(formatPrice($total)) ?></span></div>
        <?php if ($shippingFee > 0): ?>
            <p class="summary-note">Đơn từ <?= e(formatPrice(FREE_SHIPPING_MIN)) ?> được miễn phí vận chuyển.</p>
        <?php endif; ?>
    </aside>
</div>