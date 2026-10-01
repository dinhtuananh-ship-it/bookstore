<section>
    <div class="admin-head">
        <h1>Khuyến mãi (<?= count($coupons) ?>)</h1>
        <a class="btn btn-sm btn-primary" href="<?= url('/admin/khuyen-mai/them') ?>">+ Tạo mã mới</a>
    </div>

    <p class="muted" style="margin:8px 0;">
        Mẹo: bấm <strong>Sửa</strong> để mở trang riêng dễ nhìn. Bấm <strong>Bật/Tắt</strong> để ẩn mã đã dùng thay vì xóa.
        Nhập <strong>0</strong> ở lượt dùng nghĩa là không giới hạn.
    </p>

    <form method="get" action="<?= url('/admin/khuyen-mai') ?>" class="admin-filter" style="display:flex;gap:8px;margin-bottom:12px;">
        <input type="text" name="keyword" placeholder="Tìm mã... VD: GIAM10" value="<?= e($keyword ?? '') ?>">
        <select name="status">
            <option value="">Tất cả trạng thái</option>
            <option value="1" <?= ($statusFilter ?? '') === '1' ? 'selected' : '' ?>>Đang hoạt động</option>
            <option value="0" <?= ($statusFilter ?? '') === '0' ? 'selected' : '' ?>>Hết hiệu lực / Tắt</option>
        </select>
        <button class="btn btn-sm" type="submit">Lọc</button>
        <?php if (!empty($keyword) || ($statusFilter ?? '') !== ''): ?>
            <a class="btn btn-sm" href="<?= url('/admin/khuyen-mai') ?>">Xóa lọc</a>
        <?php endif; ?>
    </form>

    <div class="table-responsive">
        <table class="admin-table">
            <thead>
            <tr>
                <th>ID</th>
                <th>Mã</th>
                <th>Giảm</th>
                <th>Đơn tối thiểu</th>
                <th>Lượt dùng</th>
                <th>Hiệu lực</th>
                <th>Trạng thái</th>
                <th>Thao tác</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($coupons as $coupon): ?>
                <?php
                $now = time();
                $start = !empty($coupon['start_date']) ? strtotime((string) $coupon['start_date']) : null;
                $end = !empty($coupon['end_date']) ? strtotime((string) $coupon['end_date']) : null;
                $maxUses = (int) ($coupon['max_uses'] ?? 0);
                // Trạng thái nút bật tắt chỉ theo cột status để đồng bộ với form tạo sửa
                $isOn = (int) ($coupon['status'] ?? 0) === 1;
                // Hiệu lực thực tế còn phụ thuộc ngày và lượt dùng
                $inDate = ($start === null || $start === false || $start <= $now)
                    && ($end === null || $end === false || $end >= $now);
                $hasTurn = ($maxUses === 0 || (int) $coupon['used_count'] < $maxUses);
                $canUse = $isOn && $inDate && $hasTurn;
                ?>
                <tr>
                    <td><?= (int) $coupon['id'] ?></td>
                    <td>
                        <strong class="coupon-code"><?= e($coupon['code']) ?></strong><br>
                        <small class="muted"><?= $coupon['type'] === 'percent' ? 'Theo %' : 'Tiền mặt' ?></small>
                    </td>
                    <td>
                        <?= e(couponLabel($coupon)) ?>
                    </td>
                    <td><?= (float) $coupon['min_order'] > 0 ? formatPrice((float) $coupon['min_order']) : 'Không giới hạn' ?></td>
                    <td><?= (int) $coupon['used_count'] ?><?= $maxUses > 0 ? ' / ' . $maxUses : ' / ∞' ?></td>
                    <td>
                        <?= !empty($coupon['start_date']) ? e(date('d/m/Y H:i', strtotime((string) $coupon['start_date']))) : 'Ngay lập tức' ?>
                        <br>→ <?= !empty($coupon['end_date']) ? e(date('d/m/Y H:i', strtotime((string) $coupon['end_date']))) : 'Vĩnh viễn' ?>
                    </td>
                    <td>
                        <span class="badge badge-<?= $isOn ? 'success' : 'muted' ?>"><?= $isOn ? 'Đang bật' : 'Đang tắt' ?></span><br>
                        <small class="muted"><?= $canUse ? 'Khách dùng được' : (!$inDate ? 'Chưa tới hạn hoặc quá hạn' : (!$hasTurn ? 'Hết lượt dùng' : 'Đang tắt nên khách chưa dùng được')) ?></small>
                    </td>
                    <td class="table-actions" style="white-space:nowrap;">
                        <a class="btn btn-xs" href="<?= url('/admin/khuyen-mai/sua?id=' . (int) $coupon['id']) ?>">Sửa</a>
                        <a class="btn btn-xs" href="<?= url('/admin/khuyen-mai/doi-trang-thai?id=' . (int) $coupon['id']) ?>"
                           onclick="return confirm('<?= $isOn ? 'Tắt' : 'Bật' ?> mã <?= e($coupon['code']) ?>?')">
                            <?= $isOn ? 'Tắt' : 'Bật' ?>
                        </a>
                        <form method="post" action="<?= url('/admin/khuyen-mai/xoa') ?>" class="inline-form">
                            <input type="hidden" name="csrf_token" value="<?= e(csrfToken()) ?>">
                            <input type="hidden" name="id" value="<?= (int) $coupon['id'] ?>">
                            <button type="submit" class="btn btn-xs btn-danger" onclick="return confirm('Xóa mã <?= e($coupon['code']) ?>? Nếu mã đã dùng, nên Tắt thay vì Xóa.')">Xóa</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
            <?php if ($coupons === []): ?>
                <tr><td colspan="8" class="table-empty">Chưa có mã nào. Bấm Tạo mã mới.</td></tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</section>
