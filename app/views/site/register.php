<section class="auth-wrap">
    <div class="auth-box">
        <h1>✨ Đăng ký tài khoản</h1>

        <form method="post" action="<?= url('/dang-ky') ?>" novalidate>
            <?= csrfField() ?>

            <div class="form-group">
                <label for="name">Họ tên *</label>
                <input type="text" id="name" name="name" value="<?= e($old['name']) ?>" required>
                <?php if (isset($errors['name'])): ?>
                    <span class="field-error"><?= e($errors['name']) ?></span>
                <?php endif; ?>
            </div>

            <div class="form-group">
                <label for="email">Email *</label>
                <input type="email" id="email" name="email" value="<?= e($old['email']) ?>" required>
                <?php if (isset($errors['email'])): ?>
                    <span class="field-error"><?= e($errors['email']) ?></span>
                <?php endif; ?>
            </div>

            <div class="form-group">
                <label for="phone">Số điện thoại</label>
                <input type="tel" id="phone" name="phone" value="<?= e($old['phone']) ?>">
                <?php if (isset($errors['phone'])): ?>
                    <span class="field-error"><?= e($errors['phone']) ?></span>
                <?php endif; ?>
            </div>

            <div class="form-group">
                <label for="password">Mật khẩu *</label>
                <input type="password" id="password" name="password" required>
                <?php if (isset($errors['password'])): ?>
                    <span class="field-error"><?= e($errors['password']) ?></span>
                <?php endif; ?>
            </div>

            <div class="form-group">
                <label for="confirm_password">Xác nhận mật khẩu *</label>
                <input type="password" id="confirm_password" name="confirm_password" required>
                <?php if (isset($errors['confirm_password'])): ?>
                    <span class="field-error"><?= e($errors['confirm_password']) ?></span>
                <?php endif; ?>
            </div>

            <button type="submit" class="btn btn-block">Đăng ký</button>
        </form>

        <p class="auth-alt">Đã có tài khoản? <a href="<?= url('/dang-nhap') ?>">Đăng nhập</a></p>
    </div>
</section>