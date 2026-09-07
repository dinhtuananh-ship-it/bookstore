<h1>🛒 Giỏ hàng</h1>

<?php if ($rows === []): ?>
    <div class="empty-state">
        <span class="empty-icon">🛒</span>
        <p>Giỏ hàng của bạn đang trống.</p>
        <a class="btn" href="<?= url('/sach') ?>">Tiếp tục mua sắm</a>
    </div>
<?php else: ?>
    <div class="cart-layout">
        <div class="cart-items">
            <?php foreach ($rows as $i => $row): ?>
                <div class="cart-item">
                    <a class="cart-item-cover" href="<?= url('/sach/chi-tiet?id=' . (int) $row['book']['id']) ?>">
                        <?php if (bookCover($row['book']) !== ''): ?>
                            <img src="<?= e(bookCover($row['book'])) ?>" alt="<?= e($row['book']['title']) ?>">
                        <?php else: ?>
                            <span class="book-cover-placeholder">📖</span>
                        <?php endif; ?>
                    </a>
                    <div class="cart-item-info">
                        <a class="cart-item-title" href="<?= url('/sach/chi-tiet?id=' . (int) $row['book']['id']) ?>">
                            <?= e($row['book']['title']) ?>
                            <?php if ((int) $row['volume'] > 1): ?>
                                <span class="cart-item-tap">- Tập <?= (int) $row['volume'] ?></span>
                            <?php endif; ?>
                        </a>
                        <p class="cart-item-meta"><?= e($row['book']['author'] ?? '') ?></p>
                        <p class="cart-item-price"><?= e(formatPrice($row['price'])) ?></p>
                        <p class="cart-item-stock">Còn <?= (int) $row['book']['stock'] ?> cuốn</p>
                    </div>
                    <form class="cart-qty-form" method="post" action="<?= url('/gio-hang/cap-nhat') ?>">
                        <?= csrfField() ?>
                        <input type="hidden" name="book_id" value="<?= (int) $row['book']['id'] ?>">
                        <input type="hidden" name="volume" value="<?= (int) $row['volume'] ?>">
                        <span class="quantity-stepper">
                            <button type="button" class="qty-btn minus" aria-label="Giảm">−</button>
                            <input class="qty-input" type="number" name="quantity" value="<?= (int) $row['quantity'] ?>"
                                   min="1" max="<?= max(1, (int) $row['book']['stock']) ?>">
                            <button type="button" class="qty-btn plus" aria-label="Tăng">+</button>
                        </span>
                        <button type="submit" class="btn btn-sm">Cập nhật</button>
                    </form>
                    <div class="cart-item-total"><?= e(formatPrice($row['line_total'])) ?></div>
                    <form method="post" action="<?= url('/gio-hang/xoa') ?>" onsubmit="return confirm('Xóa sản phẩm này?')">
                        <?= csrfField() ?>
                        <input type="hidden" name="book_id" value="<?= (int) $row['book']['id'] ?>">
                        <input type="hidden" name="volume" value="<?= (int) $row['volume'] ?>">
                        <button type="submit" class="btn-remove" title="Xóa sản phẩm">✕</button>
                    </form>
                </div>
            <?php endforeach; ?>
        </div>

        <aside class="cart-summary">
            <h3>Tóm tắt đơn hàng</h3>
            <div class="summary-row"><span>Tạm tính</span><span><?= e(formatPrice($subtotal)) ?></span></div>
            <div class="summary-row total"><span>Tổng cộng</span><span><?= e(formatPrice($subtotal)) ?></span></div>
            <a class="btn btn-block" href="<?= url('/thanh-toan') ?>">🚀 Tiến hành đặt hàng</a>
            <a class="btn btn-ghost btn-block" href="<?= url('/sach') ?>">Tiếp tục mua sắm</a>
        </aside>
    </div>
<?php endif; ?>