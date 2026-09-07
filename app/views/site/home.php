<?php $heroBanner = $banners[0] ?? null; ?>
<section class="hero<?= $heroBanner && !empty($heroBanner['image']) ? ' has-image' : '' ?>"
    <?php if ($heroBanner && !empty($heroBanner['image'])): ?>
        style="background-image: url('<?= e(coverUrl($heroBanner['image'])) ?>')"
    <?php endif; ?>>
    <div class="hero-shapes">
        <span>📖</span>
        <span>🐱</span>
        <span>🚀</span>
        <span>🎈</span>
        <span>⭐</span>
        <span>🦁</span>
    </div>
    <div class="hero-inner">
        <?php if ($heroBanner): ?>
            <span class="hero-eyebrow">📖 Nhà xuất bản Kim Đồng</span>
            <h1><?= e($heroBanner['title']) ?></h1>
            <?php if (!empty($heroBanner['subtitle'])): ?>
                <p class="hero-lead"><?= e($heroBanner['subtitle']) ?></p>
            <?php endif; ?>
            <?php if (!empty($heroBanner['link'])): ?>
                <p><a class="btn btn-big" href="<?= e($heroBanner['link']) ?>">Xem ngay →</a></p>
            <?php endif; ?>
        <?php else: ?>
            <span class="hero-eyebrow">📖 Nhà xuất bản Kim Đồng</span>
            <h1>Chào mừng đến với <?= e(APP_NAME) ?></h1>
            <p class="hero-lead">Hàng nghìn đầu sách hay cho mọi lứa tuổi - giao hàng nhanh toàn quốc</p>
        <?php endif; ?>
        <form class="hero-search" method="get" action="<?= url('/sach') ?>">
            <input type="text" name="keyword" placeholder="Tìm sách, tác giả, NXB..." required>
            <button type="submit" class="btn">Tìm kiếm</button>
        </form>
        <div class="hero-tags">
            <a href="<?= url('/sach?sort=best_selling') ?>">🔥 Bán chạy</a>
            <a href="<?= url('/sach?sort=newest') ?>">🆕 Sách mới</a>
            <a href="<?= url('/sach?category=1') ?>">📚 Văn học</a>
            <a href="<?= url('/sach?category=2') ?>">📖 Kinh tế</a>
        </div>
    </div>
</section>

<section class="section-block reveal">
    <div class="section-head">
        <h2>📚 Sách mới</h2>
        <a class="section-more" href="<?= url('/sach?sort=newest') ?>">Xem tất cả →</a>
    </div>
    <div class="book-grid">
        <?php foreach ($newBooks as $book): ?>
            <?php require APP_PATH . '/views/partials/book_card.php'; ?>
        <?php endforeach; ?>
    </div>
</section>

<section class="section-block reveal">
    <div class="section-head">
        <h2>🔥 Sách bán chạy</h2>
        <a class="section-more" href="<?= url('/sach?sort=best_selling') ?>">Xem tất cả →</a>
    </div>
    <div class="book-grid">
        <?php foreach ($hotBooks as $book): ?>
            <?php require APP_PATH . '/views/partials/book_card.php'; ?>
        <?php endforeach; ?>
    </div>
</section>

<section class="benefits reveal">
    <div class="benefit">
        <span class="benefit-icon">🚚</span>
        <div>
            <strong>Giao hàng nhanh</strong>
            <p>Miễn phí cho đơn từ 500.000đ</p>
        </div>
    </div>
    <div class="benefit">
        <span class="benefit-icon">🔄</span>
        <div>
            <strong>Đổi trả dễ dàng</strong>
            <p>Trong vòng 7 ngày</p>
        </div>
    </div>
    <div class="benefit">
        <span class="benefit-icon">💳</span>
        <div>
            <strong>Thanh toán linh hoạt</strong>
            <p>COD, chuyển khoản, ví điện tử</p>
        </div>
    </div>
    <div class="benefit">
        <span class="benefit-icon">🎧</span>
        <div>
            <strong>Hỗ trợ 24/7</strong>
            <p>Hotline 1900 1234</p>
        </div>
    </div>
</section>