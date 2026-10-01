<?php
$c = is_array($coupon ?? null) ? $coupon : [];
$toLocal = function ($v) {
    $v = trim((string) ($v ?? ''));
    if ($v === '') return '';
    return substr(str_replace(' ', 'T', $v), 0, 16);
};
?>
<div class="form-group">
    <label>Mã giảm giá *</label>
    <input type="text" name="code" value="<?= e(strtoupper((string) ($c['code'] ?? ''))) ?>" required maxlength="30"
           placeholder="VD: SALE50" style="text-transform: uppercase;">
</div>
<div class="form-group">
    <label>Loại giảm</label>
    <select name="type">
        <option value="percent" <?= ($c['type'] ?? 'percent') === 'percent' ? 'selected' : '' ?>>Phần trăm (%)</option>
        <option value="fixed" <?= ($c['type'] ?? '') === 'fixed' ? 'selected' : '' ?>>Số tiền cố định (₫)</option>
    </select>
</div>
<div class="form-group">
    <label>Giá trị giảm *</label>
    <input type="number" name="value" min="1" step="any" required value="<?= e((string) ($c['value'] ?? '')) ?>"
           placeholder="10 cho 10% hoặc 50000">
</div>
<div class="form-group">
    <label>Đơn tối thiểu (₫, 0 = không giới hạn)</label>
    <input type="number" name="min_order" min="0" step="1000" value="<?= e((string) ($c['min_order'] ?? 0)) ?>">
</div>
<div class="form-group">
    <label>Lượt dùng tối đa (0 = không giới hạn)</label>
    <input type="number" name="max_uses" min="0" step="1" value="<?= e((string) ($c['max_uses'] ?? 0)) ?>">
</div>
<div class="form-group">
    <label>Ngày bắt đầu (trống = ngay lập tức)</label>
    <input type="datetime-local" name="start_date" value="<?= e($toLocal($c['start_date'] ?? '')) ?>">
</div>
<div class="form-group">
    <label>Ngày kết thúc (trống = vĩnh viễn)</label>
    <input type="datetime-local" name="end_date" value="<?= e($toLocal($c['end_date'] ?? '')) ?>">
</div>
<div class="form-group">
    <label>Trạng thái</label>
    <select name="status">
        <option value="1" <?= !isset($c['status']) || (int) $c['status'] === 1 ? 'selected' : '' ?>>Hoạt động</option>
        <option value="0" <?= isset($c['status']) && (int) $c['status'] === 0 ? 'selected' : '' ?>>Tắt</option>
    </select>
</div>
