<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#1c2531">
    <title><?= e($pageTitle ?? 'Quản trị') ?> | <?= e(APP_NAME) ?> Admin</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&family=Baloo+2:wght@500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= asset('css/style.css') ?>">
</head>
<body class="admin-body">
<div class="admin-layout">
    <aside class="admin-sidebar">
        <div class="admin-brand">
            <span class="brand-icon">📚</span>
            Kim Đồng
            <small>Admin Panel</small>
        </div>
        <nav class="admin-nav">
            <a href="<?= url('/admin') ?>"><span class="nav-ico">📊</span> Tổng quan</a>
            <a href="<?= url('/admin/sach') ?>"><span class="nav-ico">📖</span> Sách</a>
            <a href="<?= url('/admin/danh-muc') ?>"><span class="nav-ico">🗂️</span> Danh mục</a>
            <a href="<?= url('/admin/don-hang') ?>"><span class="nav-ico">🧾</span> Đơn hàng</a>
            <a href="<?= url('/admin/khach-hang') ?>"><span class="nav-ico">👥</span> Khách hàng</a>
            <a href="<?= url('/admin/khuyen-mai') ?>"><span class="nav-ico">🏷️</span> Khuyến mãi</a>
            <a href="<?= url('/admin/banner') ?>"><span class="nav-ico">🖼️</span> Banner</a>
            <a href="<?= url('/admin/binh-luan') ?>"><span class="nav-ico">💬</span> Bình luận</a>
            <a href="<?= url('/admin/bao-cao') ?>"><span class="nav-ico">📈</span> Báo cáo</a>
        </nav>
    </aside>
    <div class="admin-main">
        <header class="admin-topbar">
            <span style="display:flex;align-items:center;gap:10px;">
                <button class="sidebar-toggle" aria-label="Mở menu">☰</button>
                <?= e($pageTitle ?? 'Quản trị') ?>
            </span>
            <span class="admin-user">
                <span class="avatar-mini"><?= e(mb_substr((string) (currentUser()['name'] ?? 'A'), 0, 1)) ?></span>
                <?= e(currentUser()['name'] ?? '') ?>
                <a href="<?= url('/admin/dang-xuat') ?>">🚪 Đăng xuất</a>
            </span>
        </header>
        <main class="admin-content">
            <?php if ($flash = sessionFlash('success')): ?>
                <div class="alert alert-success">✅ <?= e($flash) ?></div>
            <?php endif; ?>
            <?php if ($flash = sessionFlash('error')): ?>
                <div class="alert alert-error">⚠️ <?= e($flash) ?></div>
            <?php endif; ?>

            <?php layoutContent(); ?>
        </main>
    </div>
</div>

<script src="<?= asset('js/main.js') ?>"></script>
</body>
</html>