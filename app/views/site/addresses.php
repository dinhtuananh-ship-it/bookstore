<div class="addr-layout">
    <div class="addr-list">
        <h1>Sổ địa chỉ</h1>

        <?php if ($addresses === []): ?>
            <p class="empty-note">Bạn chưa có địa chỉ nào.</p>
        <?php else: ?>
            <?php foreach ($addresses as $addr): ?>
                <div class="addr-card">
                    <div class="addr-info">
                        <strong><?= e($addr['full_name']) ?> (<?= e($addr['phone']) ?>)</strong>
                        <?php if ((int) $addr['is_default'] === 1): ?>
                            <span class="addr-default">Mặc định</span>
                        <?php endif; ?>
                        <p><?= e($addr['detail']) ?>, <?= e($addr['ward']) ?>, <?= e($addr['district']) ?>, <?= e($addr['province']) ?></p>
                    </div>
                    <div class="addr-actions">
                        <?php if ((int) $addr['is_default'] !== 1): ?>
                            <form method="post" action="<?= url('/dia-chi/mac-dinh') ?>">
                                <?= csrfField() ?>
                                <input type="hidden" name="id" value="<?= (int) $addr['id'] ?>">
                                <button type="submit" class="btn btn-sm btn-ghost">Đặt mặc định</button>
                            </form>
                        <?php endif; ?>
                        <a class="btn btn-sm btn-ghost" href="<?= url('/dia-chi?edit=' . (int) $addr['id']) ?>">Sửa</a>
                        <form method="post" action="<?= url('/dia-chi/xoa') ?>" onsubmit="return confirm('Xóa địa chỉ này?')">
                            <?= csrfField() ?>
                            <input type="hidden" name="id" value="<?= (int) $addr['id'] ?>">
                            <button type="submit" class="btn btn-sm btn-danger">Xóa</button>
                        </form>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <section class="checkout-section addr-form">
        <h2><?= $editing ? 'Sửa địa chỉ' : 'Thêm địa chỉ mới' ?></h2>
        <form method="post" action="<?= $editing ? url('/dia-chi/sua') : url('/dia-chi/them') ?>" novalidate>
            <?= csrfField() ?>
            <?php if ($editing): ?>
                <input type="hidden" name="id" value="<?= (int) $editing['id'] ?>">
            <?php endif; ?>

            <div class="form-row">
                <div class="form-group">
                    <label>Họ tên *</label>
                    <input type="text" name="full_name" value="<?= e($editing['full_name'] ?? '') ?>" required>
                </div>
                <div class="form-group">
                    <label>Số điện thoại *</label>
                    <input type="tel" name="phone" value="<?= e($editing['phone'] ?? '') ?>" required>
                </div>
            </div>
            <div class="form-group">
                <label>Tỉnh/Thành *</label>
                <input type="text" name="province" value="<?= e($editing['province'] ?? '') ?>" required>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label>Quận/Huyện *</label>
                    <input type="text" name="district" value="<?= e($editing['district'] ?? '') ?>" required>
                </div>
                <div class="form-group">
                    <label>Phường/Xã *</label>
                    <input type="text" name="ward" value="<?= e($editing['ward'] ?? '') ?>" required>
                </div>
            </div>
            <div class="form-group">
                <label>Địa chỉ chi tiết *</label>
                <input type="text" name="detail" value="<?= e($editing['detail'] ?? '') ?>" required>
            </div>

            <button type="submit" class="btn"><?= $editing ? 'Cập nhật' : 'Thêm địa chỉ' ?></button>
            <?php if ($editing): ?>
                <a class="btn btn-ghost" href="<?= url('/dia-chi') ?>">Hủy</a>
            <?php endif; ?>
        </form>
    </section>
</div>