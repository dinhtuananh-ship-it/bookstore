<section class="auth-wrap">
    <div class="auth-box">
        <h1>🔐 Đăng nhập</h1>

        <?php if (isset($errors['general'])): ?>
            <div class="alert alert-error"><?= e($errors['general']) ?></div>
        <?php endif; ?>

        <form method="post" action="<?= url('/dang-nhap') ?>" novalidate>
            <?= csrfField() ?>

            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" value="<?= e($oldEmail) ?>" required>
            </div>

            <div class="form-group">
                <label for="password">Mật khẩu</label>
                <input type="password" id="password" name="password" required>
            </div>

            <button type="submit" class="btn btn-block">Đăng nhập</button>
        </form>

        <p class="auth-alt">Chưa có tài khoản? <a href="<?= url('/dang-ky') ?>">Đăng ký ngay</a></p>
    </div>
</section>