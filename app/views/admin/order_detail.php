<section>
    <div class="admin-head">
        <h1>Đơn hàng <?= e($order['code']) ?></h1>
        <a class="btn btn-sm" href="<?= url('/admin/don-hang') ?>">← Quay lại danh sách</a>
    </div>

    <div class="admin-columns">
        <div class="admin-column-main">
            <h2 class="section-title">Sản phẩm</h2>
            <table class="admin-table">
                <thead>
                <tr>
                    <th>Ảnh</th>
                    <th>Sách</th>
                    <th>Đơn giá</th>
                    <th>Số lượng</th>
                    <th>Thành tiền</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($items as $item): ?>
                    <tr>
                        <td>
                            <?php if (coverUrl($item['cover_image'] ?? null) !== ''): ?>
                                <img class="table-thumb" src="<?= e(coverUrl($item['cover_image'] ?? null)) ?>" alt="">
                            <?php endif; ?>
                        </td>
                        <td>
                            <strong><?= e($item['title']) ?></strong>
                            <?php if ((int) $item['volume'] > 1): ?>
                                <div class="table-sub">Tập <?= (int) $item['volume'] ?></div>
                            <?php endif; ?>
                            <div class="table-sub"><?= e($item['author']) ?></div>
                        </td>
                        <td><?= formatPrice($item['price']) ?></td>
                        <td><?= (int) $item['quantity'] ?></td>
                        <td><?= formatPrice((float) $item['price'] * (int) $item['quantity']) ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>

            <div class="summary-box">
                <p>Tạm tính: <strong><?= formatPrice($order['subtotal']) ?></strong></p>
                <?php if ((float) $order['discount'] > 0): ?>
                    <p>Giảm giá: <strong class="text-danger">-<?= formatPrice($order['discount']) ?></strong></p>
                <?php endif; ?>
                <p>Phí vận chuyển: <strong><?= formatPrice($order['shipping_fee']) ?></strong></p>
                <p>Tổng cộng: <strong class="text-primary"><?= formatPrice($order['total']) ?></strong></p>
                <?php if (!empty($order['note'])): ?>
                    <p>Ghi chú: <em><?= e($order['note']) ?></em></p>
                <?php endif; ?>
            </div>

            <h2 class="section-title">Lịch sử trạng thái</h2>
            <ul class="status-timeline">
                <?php foreach ($history as $h): ?>
                    <li>
                        <strong><?= e(orderStatusLabel($h['status'])) ?></strong>
                        <span><?= e(date('d/m/Y H:i', strtotime((string) $h['created_at']))) ?> — <?= e($h['changed_by']) ?></span>
                        <?php if (!empty($h['note'])): ?>
                            <p><?= e($h['note']) ?></p>
                        <?php endif; ?>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>

        <div class="admin-column-side">
            <div class="panel">
                <h3 class="panel-title">Khách hàng</h3>
                <p><strong><?= e($order['full_name'] ?? '—') ?></strong></p>
                <p class="table-sub">SĐT: <?= e($order['phone'] ?? '—') ?></p>
                <p class="table-sub">
                    <?= e(implode(', ', array_filter([$order['address_detail'] ?? '', $order['ward'] ?? '', $order['district'] ?? '', $order['province'] ?? '']))) ?>
                </p>
                <p class="table-sub">Thanh toán: <?= $order['payment_method'] === 'vnpay' ? 'VNPay' : 'COD' ?></p>
            </div>

            <div class="panel">
                <h3 class="panel-title">Trạng thái hiện tại</h3>
                <p><span class="badge badge-<?= e(orderStatusClass($order['status'])) ?>"><?= e(orderStatusLabel($order['status'])) ?></span></p>

                <?php if ($allowedStatuses !== []): ?>
                    <form method="post" action="<?= url('/admin/don-hang/trang-thai') ?>" class="admin-form">
                        <input type="hidden" name="csrf_token" value="<?= e(csrfToken()) ?>">
                        <input type="hidden" name="id" value="<?= (int) $order['id'] ?>">
                        <div class="form-group">
                            <label>Chuyển sang</label>
                            <select name="status">
                                <?php foreach ($allowedStatuses as $s): ?>
                                    <option value="<?= e($s) ?>"><?= e(orderStatusLabel($s)) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary btn-sm">Cập nhật trạng thái</button>
                    </form>
                <?php else: ?>
                    <p class="table-sub">Đơn hàng đã kết thúc ở trạng thái này.</p>
                <?php endif; ?>

                <?php if ($canCancel): ?>
                    <form method="post" action="<?= url('/admin/don-hang/huy') ?>" class="admin-form" style="margin-top: 12px;">
                        <input type="hidden" name="csrf_token" value="<?= e(csrfToken()) ?>">
                        <input type="hidden" name="id" value="<?= (int) $order['id'] ?>">
                        <div class="form-group">
                            <label>Lý do hủy</label>
                            <input type="text" name="note" placeholder="Ghi chú (tùy chọn)">
                        </div>
                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Hủy đơn hàng này? Tồn kho sẽ được hoàn lại.')">Hủy đơn hàng</button>
                    </form>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>