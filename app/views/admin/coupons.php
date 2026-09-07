<section>
    <div class="admin-head">
        <h1>Khuyến mãi (<?= count($coupons) ?>)</h1>
        <a class="btn btn-sm btn-primary" href="#add-form">+ Tạo mã mới</a>
    </div>

    <div class="admin-columns">
        <div class="admin-column-main">
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
                    $active = (int) $coupon['status'] === 1
                        && ($start === null || $start <= $now)
                        && ($end === null || $end >= $now)
                        && ((int) $coupon['max_uses'] === 0 || (int) $coupon['used_count'] < (int) $coupon['max_uses']);
                    ?>
                    <tr>
                        <td><?= (int) $coupon['id'] ?></td>
                        <td><strong class="coupon-code"><?= e($coupon['code']) ?></strong></td>
                        <td>
                            <?= $coupon['type'] === 'percent' ? e((string) (float) $coupon['value']) . '%' : formatPrice((float) $coupon['value']) ?>
                        </td>
                        <td><?= formatPrice((float) $coupon['min_order']) ?></td>
                        <td><?= (int) $coupon['used_count'] ?><?= (int) $coupon['max_uses'] > 0 ? ' / ' . (int) $coupon['max_uses'] : ' / ∞' ?></td>
                        <td>
                            <?= !empty($coupon['start_date']) ? e(date('d/m/Y', strtotime((string) $coupon['start_date']))) : '—' ?>
                            → <?= !empty($coupon['end_date']) ? e(date('d/m/Y', strtotime((string) $coupon['end_date']))) : '∞' ?>
                        </td>
                        <td>
                            <span class="badge badge-<?= $active ? 'success' : 'muted' ?>"><?= $active ? 'Hoạt động' : 'Hết hiệu lực' ?></span>
                        </td>
                        <td class="table-actions">
                            <a class="btn btn-xs" href="#edit-<?= (int) $coupon['id'] ?>">Sửa</a>
                            <form method="post" action="<?= url('/admin/khuyen-mai/xoa') ?>" class="inline-form">
                                <input type="hidden" name="csrf_token" value="<?= e(csrfToken()) ?>">
                                <input type="hidden" name="id" value="<?= (int) $coupon['id'] ?>">
                                <button type="submit" class="btn btn-xs btn-danger" onclick="return confirm('Xóa mã này?')">Xóa</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <?php if ($coupons === []): ?>
                    <tr><td colspan="8" class="table-empty">Chưa có mã khuyến mãi nào.</td></tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>

        <div class="admin-column-side">
            <?php foreach ($coupons as $coupon): ?>
                <div id="edit-<?= (int) $coupon['id'] ?>" class="panel panel-collapsible">
                    <h3 class="panel-title">Sửa: <?= e($coupon['code']) ?></h3>
                    <form method="post" action="<?= url('/admin/khuyen-mai/sua') ?>" class="admin-form">
                        <input type="hidden" name="csrf_token" value="<?= e(csrfToken()) ?>">
                        <input type="hidden" name="id" value="<?= (int) $coupon['id'] ?>">
                        <?php include __DIR__ . '/partials/coupon_form_fields.php'; ?>
                        <button type="submit" class="btn btn-primary btn-sm">Lưu</button>
                    </form>
                </div>
            <?php endforeach; ?>

            <div id="add-form" class="panel panel-collapsible">
                <h3 class="panel-title">+ Tạo mã khuyến mãi</h3>
                <form method="post" action="<?= url('/admin/khuyen-mai/them') ?>" class="admin-form">
                    <input type="hidden" name="csrf_token" value="<?= e(csrfToken()) ?>">
                    <?php $coupon = null; include __DIR__ . '/partials/coupon_form_fields.php'; ?>
                    <button type="submit" class="btn btn-primary btn-sm">Tạo mã</button>
                </form>
            </div>
        </div>
    </div>
</section>