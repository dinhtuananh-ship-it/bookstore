<?php $heroBanner = $banners[0] ?? null; ?>
<?php $hasSlider = count($banners) > 1; ?>
<section class="hero<?= $heroBanner && !empty($heroBanner['image']) ? ' has-image' : '' ?>"
    <?php if ($heroBanner && !empty($heroBanner['image']) && !$hasSlider): ?>
        style="background-image: url('<?= e(coverUrl($heroBanner['image'])) ?>')"
    <?php endif; ?>
    <?php if ($hasSlider): ?>data-hero-slider<?php endif; ?>>
    <div class="hero-shapes">
        <span>📖</span>
        <span>🐱</span>
        <span>🚀</span>
        <span>🎈</span>
        <span>⭐</span>
        <span>🦁</span>
    </div>
    <div class="hero-inner">
        <?php if ($banners === []): ?>
            <span class="hero-eyebrow">📖 Nhà xuất bản Kim Đồng</span>
            <h1>Chào mừng đến với <?= e(APP_NAME) ?></h1>
            <p class="hero-lead">Hàng nghìn đầu sách hay cho mọi lứa tuổi - giao hàng nhanh toàn quốc</p>
        <?php elseif (!$hasSlider): ?>
            <span class="hero-eyebrow">📖 Nhà xuất bản Kim Đồng</span>
            <h1><?= e($heroBanner['title']) ?></h1>
            <?php if (!empty($heroBanner['subtitle'])): ?>
                <p class="hero-lead"><?= e($heroBanner['subtitle']) ?></p>
            <?php endif; ?>
            <?php if (!empty($heroBanner['link'])): ?>
                <p><a class="btn btn-big" href="<?= e(bannerUrl($heroBanner['link'])) ?>">Xem ngay →</a></p>
            <?php endif; ?>
        <?php else: ?>
            <div class="hero-slides">
                <?php foreach ($banners as $i => $b): ?>
                    <div class="hero-slide<?= $i === 0 ? ' active' : '' ?>"
                        <?= !empty($b['image']) ? 'data-bg="' . e(coverUrl($b['image'])) . '"' : '' ?>>
                        <span class="hero-eyebrow">📖 Nhà xuất bản Kim Đồng (<?= ($i + 1) ?>/<?= count($banners) ?>)</span>
                        <h1><?= e($b['title']) ?></h1>
                        <?php if (!empty($b['subtitle'])): ?>
                            <p class="hero-lead"><?= e($b['subtitle']) ?></p>
                        <?php endif; ?>
                        <?php if (!empty($b['link'])): ?>
                            <p><a class="btn btn-big" href="<?= e(bannerUrl($b['link'])) ?>">Xem ngay →</a></p>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
            <div class="hero-dots">
                <?php foreach ($banners as $i => $b): ?>
                    <button type="button" data-slide="<?= $i ?>" class="<?= $i === 0 ? 'active' : '' ?>" aria-label="Banner <?= ($i + 1) ?>">●</button>
                <?php endforeach; ?>
            </div>
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

<style>
.hero-slides { position: relative; }
.hero-slide { display: none; }
.hero-slide.active { display: block; animation: hero-in .5s ease-out; }
.hero-dots { margin: 10px 0; }
.hero-dots button { background: none; border: none; color: rgba(255,255,255,.5); font-size: 14px; cursor: pointer; padding: 4px; }
.hero-dots button.active { color: #fff; }
.hero[data-hero-slider].has-image, .hero[data-hero-slider] { background-size: cover; background-position: center; transition: background-image .5s; }
</style>

<?php if ($hasSlider): ?>
<script>
(function () {
    var hero = document.querySelector('[data-hero-slider]');
    if (!hero) return;
    var slides = hero.querySelectorAll('.hero-slide');
    var dots = hero.querySelectorAll('.hero-dots button');
    var idx = 0;
    function show(i) {
        idx = (i + slides.length) % slides.length;
        slides.forEach(function (s, k) { s.classList.toggle('active', k === idx); });
        dots.forEach(function (d, k) { d.classList.toggle('active', k === idx); });
        var bg = slides[idx].getAttribute('data-bg');
        if (bg) {
            hero.classList.add('has-image');
            hero.style.backgroundImage = "url('" + bg + "')";
        } else {
            hero.classList.remove('has-image');
            hero.style.backgroundImage = '';
        }
    }
    dots.forEach(function (d) {
        d.addEventListener('click', function () { show(parseInt(d.getAttribute('data-slide'), 10)); });
    });
    // Hiện banner đầu ngay khi tải trang
    show(0);
    // Tự chuyển mỗi 5 giây để thấy rõ ứng dụng của nhiều banner
    setInterval(function () { show(idx + 1); }, 5000);
})();
</script>
<?php endif; ?>

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
