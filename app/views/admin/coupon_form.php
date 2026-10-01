<section>
    <div class="admin-head">
        <h1><?= !empty($isEdit) ? 'Sửa mã: ' . e($coupon['code'] ?? '') : 'Tạo mã khuyến mãi mới' ?></h1>
        <a class="btn btn-sm" href="<?= url('/admin/khuyen-mai') ?>">← Về danh sách</a>
    </div>

    <p class="muted">
        Điền 4 ô là xong: <strong>Mã + Loại + Giá trị + Trạng thái</strong>.
        Còn lại để 0 hoặc trống nghĩa là không giới hạn.
        Ví dụ: mã GIAM10 loại % giá trị 10 đơn tối thiểu 100000.
    </p>

    <?php if (!empty($errors)): ?>
        <div class="alert alert-error">
            <?= e(implode(' ', array_values((array) $errors))) ?>
        </div>
    <?php endif; ?>

    <?php
    $c = is_array($coupon ?? null) ? $coupon : [];
    $toLocal = function ($v) {
        $v = trim((string) ($v ?? ''));
        if ($v === '') return '';
        $v = str_replace(' ', 'T', $v);
        return substr($v, 0, 16);
    };
    ?>

    <form method="post" action="<?= !empty($isEdit) ? url('/admin/khuyen-mai/sua?id=' . (int) ($c['id'] ?? 0)) : url('/admin/khuyen-mai/them') ?>" class="admin-form" style="max-width:640px;">
        <?= csrfField() ?>
        <?php if (!empty($isEdit)): ?>
            <input type="hidden" name="id" value="<?= (int) ($c['id'] ?? 0) ?>">
        <?php endif; ?>

        <div class="form-group">
            <label>Mã giảm giá *</label>
            <input type="text" name="code" required maxlength="30" style="text-transform:uppercase;"
                   placeholder="VD: SALE50" value="<?= e(strtoupper((string) ($c['code'] ?? ''))) ?>">
            <?php if (!empty($errors['code'])): ?><small class="error"><?= e($errors['code']) ?></small><?php endif; ?>
            <small class="muted">3-30 ký tự in hoa, số, _ -. Tự đổi thành in hoa.</small>
        </div>

        <div class="form-grid" style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
            <div class="form-group">
                <label>Loại giảm</label>
                <select name="type" id="coupon-type">
                    <option value="percent" <?= ($c['type'] ?? 'percent') === 'percent' ? 'selected' : '' ?>>Phần trăm (%)</option>
                    <option value="fixed" <?= ($c['type'] ?? '') === 'fixed' ? 'selected' : '' ?>>Tiền mặt (₫)</option>
                </select>
            </div>
            <div class="form-group">
                <label>Giá trị giảm *</label>
                <input type="number" name="value" id="coupon-value" required min="1" onwheel="this.blur()"
                       placeholder="10 cho 10% hoặc 50000"
                       value="<?= e((string) ($c['value'] ?? '')) ?>">
                <?php if (!empty($errors['value'])): ?><small class="error"><?= e($errors['value']) ?></small><?php endif; ?>
                <small class="muted" id="coupon-value-hint">Nếu % thì nhập 1-100. Nếu tiền mặt thì từ 1000.</small>
            </div>
        </div>

        <div class="form-grid" style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
            <div class="form-group">
                <label>Đơn tối thiểu (₫)</label>
                <input type="number" name="min_order" min="0" step="1000" onwheel="this.blur()" placeholder="0 = không giới hạn"
                       value="<?= e((string) ($c['min_order'] ?? 0)) ?>">
                <?php if (!empty($errors['min_order'])): ?><small class="error"><?= e($errors['min_order']) ?></small><?php endif; ?>
            </div>
            <div class="form-group">
                <label>Lượt dùng tối đa</label>
                <input type="number" name="max_uses" min="0" step="1" onwheel="this.blur()" placeholder="0 = không giới hạn"
                       value="<?= e((string) ($c['max_uses'] ?? 0)) ?>">
                <?php if (!empty($errors['max_uses'])): ?><small class="error"><?= e($errors['max_uses']) ?></small><?php endif; ?>
                <?php if (!empty($c['used_count'])): ?>
                    <small class="muted">Đã dùng: <?= (int) $c['used_count'] ?> lần.</small>
                <?php endif; ?>
            </div>
        </div>

        <div class="form-grid" style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
            <div class="form-group">
                <label>Bắt đầu (trống = ngay)</label>
                <input type="datetime-local" name="start_date" value="<?= e($toLocal($c['start_date'] ?? '')) ?>">
                <?php if (!empty($errors['start_date'])): ?><small class="error"><?= e($errors['start_date']) ?></small><?php endif; ?>
            </div>
            <div class="form-group">
                <label>Kết thúc (trống = vĩnh viễn)</label>
                <input type="datetime-local" name="end_date" value="<?= e($toLocal($c['end_date'] ?? '')) ?>">
                <?php if (!empty($errors['end_date'])): ?><small class="error"><?= e($errors['end_date']) ?></small><?php endif; ?>
            </div>
        </div>

        <div class="form-group">
            <label>Trạng thái</label>
            <select name="status">
                <option value="1" <?= !isset($c['status']) || (int) $c['status'] === 1 ? 'selected' : '' ?>>Đang bật - khách dùng được</option>
                <option value="0" <?= isset($c['status']) && (int) $c['status'] === 0 ? 'selected' : '' ?>>Tắt - giữ lại không cho dùng</option>
            </select>
        </div>

        <div style="display:flex;gap:8px;margin-top:12px;">
            <button type="submit" class="btn btn-primary"><?= !empty($isEdit) ? 'Lưu thay đổi' : 'Tạo mã' ?></button>
            <a class="btn" href="<?= url('/admin/khuyen-mai') ?>">Hủy</a>
        </div>
    </form>
</section>

<script>
(function () {
    var type = document.getElementById('coupon-type');
    var val = document.getElementById('coupon-value');
    var hint = document.getElementById('coupon-value-hint');
    if (!type || !val) return;
    function sync() {
        if (type.value === 'percent') {
            val.setAttribute('max', '100');
            val.setAttribute('step', '1');
            if (hint) hint.textContent = 'Nhập 1-100. Ví dụ 10 là giảm 10%.';
        } else {
            val.removeAttribute('max');
            val.setAttribute('step', '1000');
            if (hint) hint.textContent = 'Nhập số tiền. Ví dụ 50000 là giảm 50.000đ.';
        }
    }
    type.addEventListener('change', sync);
    sync();
})();
</script>
