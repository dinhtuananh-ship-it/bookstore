<div class="form-group">
    <label>Mã giảm giá *</label>
    <input type="text" name="code" value="<?= e(strtoupper((string) ($coupon['code'] ?? ''))) ?>" required
           placeholder="VD: SALE50" style="text-transform: uppercase;">
</div>
<div class="form-group">
    <label>Loại giảm</label>
    <select name="type">
        <option value="percent" <?= ($coupon['type'] ?? '') === 'percent' ? 'selected' : '' ?>>Phần trăm (%)</option>
        <option value="fixed" <?= ($coupon['type'] ?? '') === 'fixed' ? 'selected' : '' ?>>Số tiền cố định (₫)</option>
    </select>
</div>
<div class="form-group">
    <label>Giá trị giảm *</label>
    <input type="number" name="value" min="0" step="1000" required value="<?= e($coupon['value'] ?? '') ?>"
           placeholder="VD: 10 (cho 10%) hoặc 50000 (cho 50.000₫)">
</div>
<div class="form-group">
    <label>Đơn tối thiểu (₫, 0 = không giới hạn)</label>
    <input type="number" name="min_order" min="0" step="1000" value="<?= e($coupon['min_order'] ?? '') ?>">
</div>
<div class="form-group">
    <label>Số lượt dùng tối đa (0 = không giới hạn)</label>
    <input type="number" name="max_uses" min="0" value="<?= e($coupon['max_uses'] ?? '') ?>">
</div>
<div class="form-group">
    <label>Ngày bắt đầu (để trống = ngay lập tức)</label>
    <input type="datetime-local" name="start_date" value="<?= e($coupon['start_date'] ?? '') ?>">
</div>
<div class="form-group">
    <label>Ngày kết thúc (để trống = vĩnh viễn)</label>
    <input type="datetime-local" name="end_date" value="<?= e($coupon['end_date'] ?? '') ?>">
</div>
<div class="form-group">
    <label>Trạng thái</label>
    <select name="status">
        <option value="1" <?= !isset($coupon['status']) || (int) $coupon['status'] === 1 ? 'selected' : '' ?>>Hoạt động</option>
        <option value="0" <?= isset($coupon['status']) && (int) $coupon['status'] === 0 ? 'selected' : '' ?>>Tắt</option>
    </select>
</div>