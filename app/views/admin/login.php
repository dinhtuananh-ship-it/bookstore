<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($pageTitle ?? 'Quản trị') ?> | <?= e(APP_NAME) ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&family=Baloo+2:wght@500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= asset('css/style.css') ?>">
    <script src="<?= asset('js/main.js') ?>"></script>
</head>
<body class="admin-login-body">
    <div class="admin-login-wrap">
        <div class="admin-login-box">
            <h1><span class="lock-icon">🔐</span> Kim Đồng Admin</h1>
            <p class="admin-login-sub">Đăng nhập khu vực quản trị bán sách</p>

            <?php if (isset($errors['general'])): ?>
                <div class="alert alert-error"><?= e($errors['general']) ?></div>
            <?php endif; ?>

            <form method="post" action="<?= url('/admin/dang-nhap') ?>" novalidate>
                <?= csrfField() ?>

                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" value="<?= e($oldEmail) ?>" required>
                </div>

                <div class="form-group">
                    <label for="password">Mật khẩu</label>
                    <input type="password" id="password" name="password" required>
                </div>

                <button type="submit" class="btn btn-block">Đăng nhập hệ thống</button>
            </form>

            <p class="auth-alt"><a href="<?= url('/') ?>">← Về trang chủ</a></p>
        </div>
    </div>
</body>
</html>