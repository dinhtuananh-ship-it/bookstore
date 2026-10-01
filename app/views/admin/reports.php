<section class="reveal">
    <div class="admin-head">
        <h1>📈 Báo cáo doanh thu</h1>
    </div>

    <form class="filter-bar" method="get" action="<?= url('/admin/bao-cao') ?>">
        <label>Từ ngày</label>
        <input type="date" name="from" value="<?= e($from) ?>">
        <label>Đến ngày</label>
        <input type="date" name="to" value="<?= e($to) ?>">
        <button type="submit" class="btn btn-sm">Xem báo cáo</button>
    </form>

    <div class="stat-grid">
        <div class="stat-card">
            <div class="stat-value"><?= number_format((int) $summary['count']) ?></div>
            <div class="stat-label">Đơn đã giao (completed)</div>
        </div>
        <div class="stat-card">
            <div class="stat-value"><?= formatPrice((float) $summary['revenue']) ?></div>
            <div class="stat-label">Doanh thu (đơn đã giao)</div>
        </div>
        <div class="stat-card">
            <div class="stat-value"><?= formatPrice((float) $summary['avg']) ?></div>
            <div class="stat-label">Giá trị trung bình/đơn</div>
        </div>
    </div>

    <h2 class="section-title">🧾 Đơn theo trạng thái (trong khoảng ngày)</h2>
    <div class="stat-grid">
        <?php
        $statusMeta = [
            'pending' => 'Chờ xử lý',
            'paid' => 'Đã thanh toán',
            'processing' => 'Đang xử lý',
            'shipping' => 'Đang giao',
            'completed' => 'Đã giao',
            'cancelled' => 'Đã hủy',
        ];
        ?>
        <?php foreach ($statusMeta as $st => $label): ?>
            <div class="stat-card">
                <div class="stat-value"><?= number_format((int) ($byStatus[$st] ?? 0)) ?></div>
                <div class="stat-label"><?= e($label) ?></div>
            </div>
        <?php endforeach; ?>
    </div>
    <p class="muted">Mẹo: đổi đơn sang Đã giao thì cột ngày hôm nay tăng thêm một đơn và cộng thêm tiền. Doanh thu theo ngày tính theo ngày duyệt xong, không phải ngày đặt.</p>

    <h2 class="section-title">📈 Doanh thu theo ngày</h2>
    <div class="chart">
        <?php foreach ($byDay as $row): ?>
            <?php $w = $maxDayRevenue > 0 ? (float) $row['t'] / $maxDayRevenue * 100 : 0; ?>
            <div class="chart-row">
                <div class="chart-label"><?= e(date('d/m', strtotime((string) $row['day']))) ?></div>
                <div class="chart-track">
                    <div class="chart-fill" data-width="<?= number_format($w, 2, '.', '') ?>%"
                         style="--w: <?= number_format($w, 2, '.', '') ?>%"></div>
                </div>
                <div class="chart-value"><?= formatPrice((float) $row['t']) ?> (<?= (int) $row['c'] ?> đơn)</div>
            </div>
        <?php endforeach; ?>
        <?php if ($byDay === []): ?>
            <p class="table-empty">Không có dữ liệu trong khoảng thời gian này.</p>
        <?php endif; ?>
    </div>

    <div class="admin-columns">
        <div class="admin-column-main">
            <h2 class="section-title">Top sách bán chạy</h2>
            <div class="table-responsive">
            <table class="admin-table">
                <thead>
                <tr>
                    <th>#</th>
                    <th>Sách</th>
                    <th>SL bán</th>
                    <th>Doanh thu</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($topBooks as $i => $book): ?>
                    <tr>
                        <td><?= $i + 1 ?></td>
                        <td><?= e($book['title']) ?></td>
                        <td><?= (int) $book['qty'] ?></td>
                        <td><?= formatPrice((float) $book['revenue']) ?></td>
                    </tr>
                <?php endforeach; ?>
                <?php if ($topBooks === []): ?>
                    <tr><td colspan="4" class="table-empty">Chưa có dữ liệu.</td></tr>
                <?php endif; ?>
                </tbody>
            </table>
            </div>
        </div>
        <div class="admin-column-main">
            <h2 class="section-title">Top khách hàng</h2>
            <div class="table-responsive">
            <table class="admin-table">
                <thead>
                <tr>
                    <th>#</th>
                    <th>Khách hàng</th>
                    <th>Đơn</th>
                    <th>Chi tiêu</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($topCustomers as $i => $customer): ?>
                    <tr>
                        <td><?= $i + 1 ?></td>
                        <td>
                            <?= e($customer['name']) ?>
                            <div class="table-sub"><?= e($customer['email']) ?></div>
                        </td>
                        <td><?= (int) $customer['orders'] ?></td>
                        <td><?= formatPrice((float) $customer['total']) ?></td>
                    </tr>
                <?php endforeach; ?>
                <?php if ($topCustomers === []): ?>
                    <tr><td colspan="4" class="table-empty">Chưa có dữ liệu.</td></tr>
                <?php endif; ?>
                </tbody>
            </table>
            </div>
        </div>
    </div>
</section>