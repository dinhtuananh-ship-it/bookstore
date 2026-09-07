<?php
$topCategories = (new CategoryModel())->getTopLevel();
$cartTotal = 0;
foreach ((array) cartItems() as $item) { $cartTotal += (int) ($item['qty'] ?? 0); }
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#d81b28">
    <title><?= e($pageTitle ?? APP_NAME) ?> | <?= e(APP_NAME) ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&family=Baloo+2:wght@500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= asset('css/style.css') ?>">
</head>
<body>
<header class="site-header">
    <div class="topbar">
        <div class="container topbar-inner">
            <span class="topbar-welcome">🌟 Chào mừng bạn đến với <?= e(APP_NAME) ?> - Nhà sách trực tuyến</span>
            <span class="topbar-contact">
                <span>📞 1900 1234</span>
                <span>✉ hotro@kimdongbook.vn</span>
            </span>
        </div>
    </div>

    <div class="header-middle">
        <div class="container header-middle-inner">
            <a class="logo" href="<?= url('/') ?>">
                <span class="logo-icon">📚</span>
                <span class="logo-text">Kim Đồng<span> Books</span><i class="logo-dot"></i></span>
            </a>
            <form class="header-search" method="get" action="<?= url('/sach') ?>">
                <input type="text" name="keyword" placeholder="Tìm sách, tác giả, NXB..."
                       value="<?= e($_GET['keyword'] ?? '') ?>">
                <button type="submit" class="search-btn">🔍 Tìm</button>
            </form>
            <div class="header-actions">
                <?php if ($user = sessionGet('user')): ?>
                    <div class="ha-user">
                        <span class="ha-user-icon"><?= e(mb_substr($user['name'], 0, 1)) ?></span>
                        <span class="ha-user-name"><?= e($user['name']) ?></span>
                        <div class="ha-user-menu">
                            <a href="<?= url('/ho-so') ?>">👤 Hồ sơ</a>
                            <a href="<?= url('/don-hang') ?>">📦 Đơn hàng của tôi</a>
                            <a href="<?= url('/dia-chi') ?>">📍 Sổ địa chỉ</a>
                            <?php if (($user['role'] ?? '') === 'admin'): ?>
                                <a href="<?= url('/admin') ?>">🛠 Quản trị</a>
                            <?php endif; ?>
                            <a href="<?= url('/dang-xuat') ?>">🚪 Đăng xuất</a>
                        </div>
                    </div>
                <?php else: ?>
                    <a class="ha-auth" href="<?= url('/dang-nhap') ?>">Đăng nhập</a>
                    <span class="ha-sep">/</span>
                    <a class="ha-auth" href="<?= url('/dang-ky') ?>">Đăng ký</a>
                <?php endif; ?>
                <a class="ha-cart" href="<?= url('/gio-hang') ?>">
                    <span class="ha-cart-icon">🛒</span>
                    <span class="ha-cart-label">Giỏ hàng</span>
                    <?php if ($cartTotal > 0): ?>
                        <span class="cart-badge"><?= (int) $cartTotal ?></span>
                    <?php endif; ?>
                </a>
            </div>
        </div>
    </div>

    <div class="menu-bar">
        <div class="container menu-bar-inner">
            <div class="nav-cat">
                <a class="nav-cat-toggle" href="<?= url('/sach') ?>">
                    <span class="burger"><i></i><i></i><i></i></span> Danh mục sản phẩm
                </a>
                <div class="nav-cat-dropdown">
                    <?php foreach ($topCategories as $cat): ?>
                        <div class="nav-cat-item">
                            <a href="<?= url('/danh-muc/' . urlencode($cat['slug'])) ?>"><?= e($cat['name']) ?> <span class="nav-cat-arrow">›</span></a>
                            <?php $children = (new CategoryModel())->getChildren((int) $cat['id']); ?>
                            <?php if ($children !== []): ?>
                                <div class="nav-cat-sub">
                                    <?php foreach ($children as $child): ?>
                                        <a href="<?= url('/danh-muc/' . urlencode($child['slug'])) ?>"><?= e($child['name']) ?></a>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <nav class="main-nav">
                <a href="<?= url('/') ?>">Trang chủ</a>
                <a href="<?= url('/sach?sort=newest') ?>">Sách mới</a>
                <a href="<?= url('/sach?sort=best_selling') ?>">Bán chạy</a>
                <a href="<?= url('/sach') ?>">Tất cả sách</a>
            </nav>
        </div>
    </div>
</header>

<div class="marquee">
    <div class="marquee-track">
        <span>📚 GIẢM GIÁ ĐẶC BIỆT CÁC TỦA SÁCH THIẾU NHI KIM ĐỒNG</span>
        <span>🚚 Miễn phí vận chuyển đơn từ 500.000₫</span>
        <span>🎁 Tặng bookmark & sticker kèm mỗi đơn hàng</span>
        <span>🔥 Sách mới về mỗi tuần - Cập nhật ngay!</span>
        <span>📞 Hotline hỗ trợ 24/7: 0362045301</span>
        <span>📚 GIẢM GIÁ ĐẶC BIỆT CÁC TỦA SÁCH THIẾU NHI KIM ĐỒNG</span>
        <span>🚚 Miễn phí vận chuyển đơn từ 500.000₫</span>
        <span>🎁 Tặng bookmark & sticker kèm mỗi đơn hàng</span>
        <span>🔥 Sách mới về mỗi tuần - Cập nhật ngay!</span>
        <span>📞 Hotline hỗ trợ 24/7: 0362045301</span>
    </div>
</div>

<main class="site-main">
    <div class="container">
        <?php if ($flash = sessionFlash('success')): ?>
            <div class="alert alert-success">✅ <?= e($flash) ?></div>
        <?php endif; ?>
        <?php if ($flash = sessionFlash('error')): ?>
            <div class="alert alert-error">⚠️ <?= e($flash) ?></div>
        <?php endif; ?>

        <?php layoutContent(); ?>
    </div>
</main>

<footer class="site-footer">
    <div class="container footer-grid">
        <div class="footer-col">
            <h4>Kim Đồng Bookstore</h4>
            <p>Nhà sách trực tuyến của NXB Kim Đồng - chuyên phân phối sách thiếu nhi, truyện tranh, văn học và sách giáo dục cho mọi lứa tuổi.</p>
            <div class="footer-social">
                <a href="#" title="Facebook">📘</a>
                <a href="#" title="Instagram">📸</a>
                <a href="#" title="YouTube">▶</a>
                <a href="#" title="Zalo">💬</a>
            </div>
        </div>
        <div class="footer-col">
            <h4>Hỗ trợ khách hàng</h4>
            <ul>
                <li><a href="<?= url('/sach') ?>">Tìm kiếm sách</a></li>
                <li><a href="<?= url('/sach?sort=newest') ?>">Sách mới phát hành</a></li>
                <li><a href="<?= url('/sach?sort=best_selling') ?>">Sách bán chạy</a></li>
                <li><a href="<?= url('/gio-hang') ?>">Giỏ hàng của tôi</a></li>
            </ul>
        </div>
        <div class="footer-col">
            <h4>Chính sách</h4>
            <ul>
                <li><a href="<?= url('/sach') ?>">Chính sách giao hàng</a></li>
                <li><a href="<?= url('/sach') ?>">Chính sách đổi trả</a></li>
                <li><a href="<?= url('/sach') ?>">Chính sách bảo mật</a></li>
                <li><a href="<?= url('/sach') ?>">Điều khoản sử dụng</a></li>
            </ul>
        </div>
        <div class="footer-col">
            <h4>Liên hệ</h4>
            <ul>
                <li>📞 Hotline: 0362045301</li>
                <li>✉ Email: kimdongsupport@gmail.com</li>
                <li>📍 Phường Việt Hưng, Hà Nội</li>
                <li>🕘 9:00 - 21:00 tất cả các ngày</li>
            </ul>
        </div>
    </div>
    <div class="footer-bottom">
        <div class="container">
            <p>&copy; <?= date('Y') ?> <?= e(APP_NAME) ?> - Nhà xuất bản Kim Đồng. Mọi quyền được bảo lưu.</p>
        </div>
    </div>
</footer>

<button class="to-top" aria-label="Lên đầu trang">↑</button>

<script src="<?= asset('js/main.js') ?>"></script>
</body>
</html>