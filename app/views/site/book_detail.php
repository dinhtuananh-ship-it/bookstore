<nav class="breadcrumb">
    <a href="<?= url('/') ?>">Trang chủ</a>
    <span>›</span>
    <a href="<?= url('/danh-muc?slug=' . urlencode($book['category_slug'])) ?>"><?= e($book['category_name']) ?></a>
    <span>›</span>
    <span><?= e($book['title']) ?></span>
</nav>

<section class="book-detail">
    <div class="book-detail-cover">
        <?php if (bookCover($book) !== ''): ?>
            <img src="<?= e(bookCover($book)) ?>" alt="<?= e($book['title']) ?>" loading="lazy">
        <?php else: ?>
            <div class="book-cover-placeholder large">📖</div>
        <?php endif; ?>
    </div>

    <div class="book-detail-info">
        <h1><?= e($book['title']) ?></h1>
        <p class="detail-meta">
            Tác giả: <strong><?= e($book['author'] ?? 'Đang cập nhật') ?></strong> |
            NXB: <strong><?= e($book['publisher'] ?? 'Đang cập nhật') ?></strong>
        </p>
        <p class="detail-meta">ISBN: <?= e($book['isbn'] ?? '-') ?> | Đã bán: <?= (int) $book['sold_count'] ?>
            <?php if ((int) $book['volumes'] > 1): ?> | Bộ sách <?= (int) $book['volumes'] ?> tập<?php endif; ?>
        </p>

        <div class="detail-price">
            <?php if ((float) $book['sale_price'] > 0 && (float) $book['sale_price'] < (float) $book['price']): ?>
                <span class="price-sale big"><?= e(formatPrice((float) $book['sale_price'])) ?></span>
                <span class="price-old big"><?= e(formatPrice((float) $book['price'])) ?></span>
            <?php else: ?>
                <span class="price-sale big"><?= e(formatPrice((float) $book['price'])) ?></span>
            <?php endif; ?>
        </div>

        <p class="detail-stock">
            <?php if ((int) $book['stock'] > 0): ?>
                ✅ Còn hàng (<?= (int) $book['stock'] ?> cuốn)
            <?php else: ?>
                ⛔ Hết hàng
            <?php endif; ?>
        </p>

        <form method="post" action="<?= url('/gio-hang/them') ?>">
            <?= csrfField() ?>
            <input type="hidden" name="book_id" value="<?= (int) $book['id'] ?>">
            <?php if ((int) $book['volumes'] > 1): ?>
                <label class="qty-label" for="volume">Chọn tập:</label>
                <select class="qty-input" id="volume" name="volume">
                    <?php for ($i = 1; $i <= (int) $book['volumes']; $i++): ?>
                        <option value="<?= $i ?>">Tập <?= $i ?></option>
                    <?php endfor; ?>
                </select>
            <?php else: ?>
                <input type="hidden" name="volume" value="1">
            <?php endif; ?>
            <label class="qty-label" for="qty">Số lượng:</label>
            <span class="quantity-stepper">
                <button type="button" class="qty-btn minus" aria-label="Giảm">−</button>
                <input class="qty-input" type="number" id="qty" name="quantity" value="1" min="1"
                       max="<?= max(1, (int) $book['stock']) ?>">
                <button type="button" class="qty-btn plus" aria-label="Tăng">+</button>
            </span>
            <button type="submit" class="btn btn-big" <?= ((int) $book['stock'] <= 0) ? 'disabled' : '' ?>>
                🛒 Thêm vào giỏ hàng
            </button>
        </form>
    </div>
</section>

<section class="section-block">
    <h2>Mô tả sách</h2>
    <div class="book-description">
        <?= nl2br(e($book['description'] ?? 'Chưa có mô tả.')) ?>
    </div>
</section>

<section class="section-block">
    <h2>Đánh giá sản phẩm</h2>
    <div class="reviews-layout">
        <aside class="review-summary">
            <div class="review-avg"><?= e(number_format($reviewSummary['avg'], 1, '.', ',')) ?><small>/5</small></div>
            <div><?= stars((int) round($reviewSummary['avg'])) ?></div>
            <p><?= (int) $reviewCount ?> đánh giá</p>
            <div class="star-bars">
                <?php for ($s = 5; $s >= 1; $s--): ?>
                    <?php $cnt = $reviewSummary['per_star'][$s] ?? 0; ?>
                    <?php $pct = $reviewCount > 0 ? round($cnt / $reviewCount * 100) : 0; ?>
                    <div class="star-bar">
                        <span><?= $s ?>★</span>
                        <div class="star-bar-track"><div class="star-bar-fill" style="--w: <?= (int) $pct ?>%"></div></div>
                        <span class="star-bar-count"><?= (int) $cnt ?></span>
                    </div>
                <?php endfor; ?>
            </div>
        </aside>

        <div class="review-main">
            <?php if ($user = sessionGet('user')): ?>
                <?php if ($editingReview): ?>
                    <div class="review-form-box">
                        <h3>Sửa đánh giá của bạn</h3>
                        <form method="post" action="<?= url('/danh-gia/sua') ?>">
                            <?= csrfField() ?>
                            <input type="hidden" name="id" value="<?= (int) $userReview['id'] ?>">
                            <div class="star-input">
                                <?php for ($s = 5; $s >= 1; $s--): ?>
                                    <label><input type="radio" name="rating" value="<?= $s ?>" <?= ((int) $userReview['rating'] === $s) ? 'checked' : '' ?>> <?= $s ?>★</label>
                                <?php endfor; ?>
                            </div>
                            <textarea name="comment" rows="3" required><?= e($userReview['comment']) ?></textarea>
                            <button type="submit" class="btn">Lưu đánh giá</button>
                            <a class="btn btn-ghost" href="<?= url('/sach/chi-tiet?id=' . (int) $book['id']) ?>">Hủy</a>
                        </form>
                    </div>
                <?php elseif ($canReview): ?>
                    <div class="review-form-box">
                        <h3>Đánh giá sản phẩm</h3>
                        <form method="post" action="<?= url('/danh-gia') ?>">
                            <?= csrfField() ?>
                            <input type="hidden" name="book_id" value="<?= (int) $book['id'] ?>">
                            <div class="star-input">
                                <?php for ($s = 5; $s >= 1; $s--): ?>
                                    <label><input type="radio" name="rating" value="<?= $s ?>"> <?= $s ?>★</label>
                                <?php endfor; ?>
                            </div>
                            <textarea name="comment" rows="3" placeholder="Chia sẻ cảm nhận của bạn về cuốn sách..." required></textarea>
                            <button type="submit" class="btn">Gửi đánh giá</button>
                        </form>
                    </div>
                <?php elseif ($userReview): ?>
                    <div class="review-form-box">
                        <h3>Đánh giá của bạn</h3>
                        <div class="review-item">
                            <div class="review-head">
                                <strong><?= e($userReview['user_name'] ?? $user['name']) ?></strong>
                                <?= stars((int) $userReview['rating']) ?>
                                <span class="review-date"><?= e(date('d/m/Y', strtotime($userReview['created_at']))) ?></span>
                            </div>
                            <p><?= nl2br(e($userReview['comment'])) ?></p>
                            <div class="review-actions">
                                <a class="btn btn-sm btn-ghost" href="<?= url('/sach/chi-tiet?id=' . (int) $book['id'] . '&edit=1') ?>">Sửa</a>
                                <form method="post" action="<?= url('/danh-gia/xoa') ?>" onsubmit="return confirm('Xóa đánh giá này?')">
                                    <?= csrfField() ?>
                                    <input type="hidden" name="id" value="<?= (int) $userReview['id'] ?>">
                                    <button type="submit" class="btn btn-sm btn-danger">Xóa</button>
                                </form>
                            </div>
                        </div>
                    </div>
                <?php else: ?>
                    <p class="empty-note">Bạn cần mua và nhận hàng sách này để có thể đánh giá.</p>
                <?php endif; ?>
            <?php else: ?>
                <p class="empty-note"><a href="<?= url('/dang-nhap') ?>">Đăng nhập</a> để đánh giá sản phẩm.</p>
            <?php endif; ?>

            <div class="review-list">
                <?php if ($reviews === []): ?>
                    <p class="empty-note">Chưa có đánh giá nào.</p>
                <?php else: ?>
                    <?php foreach ($reviews as $review): ?>
                        <div class="review-item">
                            <div class="review-head">
                                <div class="review-avatar">
                                    <?php if (!empty($review['user_avatar'])): ?>
                                        <img src="<?= asset($review['user_avatar']) ?>" alt="avatar">
                                    <?php else: ?>
                                        <span><?= e(mb_substr($review['user_name'], 0, 1)) ?></span>
                                    <?php endif; ?>
                                </div>
                                <div>
                                    <strong><?= e($review['user_name']) ?></strong>
                                    <?= stars((int) $review['rating']) ?>
                                    <span class="review-date"><?= e(date('d/m/Y H:i', strtotime($review['created_at']))) ?></span>
                                </div>
                            </div>
                            <p><?= nl2br(e($review['comment'])) ?></p>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>

            <?php if ($reviewPages > 1): ?>
                <?= paginationLinks($reviewPage, $reviewPages, ['id' => (int) $book['id']], '/sach/chi-tiet') ?>
            <?php endif; ?>
        </div>
    </div>
</section>

<?php if ($related !== []): ?>
    <section class="section-block">
        <div class="section-head">
            <h2>Sách liên quan</h2>
        </div>
        <div class="book-grid">
            <?php foreach ($related as $book): ?>
                <?php require APP_PATH . '/views/partials/book_card.php'; ?>
            <?php endforeach; ?>
        </div>
    </section>
<?php endif; ?>