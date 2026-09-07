<div class="profile-layout reveal">
    <section class="checkout-section">
        <h2>Thông tin cá nhân</h2>
        <form method="post" action="<?= url('/ho-so') ?>" enctype="multipart/form-data" novalidate>
            <?= csrfField() ?>

            <div class="avatar-row">
                <div class="avatar-box">
                    <?php if (!empty(currentUser()['avatar'])): ?>
                        <img src="<?= asset(currentUser()['avatar']) ?>" alt="avatar">
                    <?php else: ?>
                        <span class="avatar-placeholder"><?= e(mb_substr(currentUser()['name'], 0, 1)) ?></span>
                    <?php endif; ?>
                </div>
                <div class="form-group">
                    <label>Ảnh đại diện</label>
                    <input type="file" name="avatar" accept="image/jpeg,image/png,image/webp,image/gif">
                    <?php if (isset($errors['avatar'])): ?>
                        <span class="field-error"><?= e($errors['avatar']) ?></span>
                    <?php endif; ?>
                </div>
            </div>

            <div class="form-group">
                <label>Email (không thể đổi)</label>
                <input type="email" value="<?= e(currentUser()['email']) ?>" disabled>
            </div>

            <div class="form-group">
                <label for="name">Họ tên *</label>
                <input type="text" id="name" name="name" value="<?= e(currentUser()['name']) ?>" required>
                <?php if (isset($errors['name'])): ?>
                    <span class="field-error"><?= e($errors['name']) ?></span>
                <?php endif; ?>
            </div>

            <div class="form-group">
                <label for="phone">Số điện thoại</label>
                <input type="tel" id="phone" name="phone" value="<?= e(currentUser()['phone'] ?? '') ?>">
                <?php if (isset($errors['phone'])): ?>
                    <span class="field-error"><?= e($errors['phone']) ?></span>
                <?php endif; ?>
            </div>

            <button type="submit" class="btn">Lưu thay đổi</button>
        </form>
    </section>

    <section class="checkout-section">
        <h2>Đổi mật khẩu</h2>
        <form method="post" action="<?= url('/ho-so/mat-khau') ?>" novalidate>
            <?= csrfField() ?>

            <div class="form-group">
                <label for="old_password">Mật khẩu hiện tại *</label>
                <input type="password" id="old_password" name="old_password" required>
            </div>

            <div class="form-group">
                <label for="new_password">Mật khẩu mới *</label>
                <input type="password" id="new_password" name="new_password" required>
            </div>

            <div class="form-group">
                <label for="confirm_password">Xác nhận mật khẩu mới *</label>
                <input type="password" id="confirm_password" name="confirm_password" required>
            </div>

            <button type="submit" class="btn">Đổi mật khẩu</button>
        </form>
    </section>
</div>