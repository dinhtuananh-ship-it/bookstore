<section>
    <div class="admin-head">
        <h1>Khách hàng (<?= number_format($count) ?>)</h1>
    </div>

    <form class="filter-bar" method="get" action="<?= url('/admin/khach-hang') ?>">
        <input type="text" name="keyword" placeholder="Tìm theo tên, email, SĐT..." value="<?= e($filters['keyword'] ?? '') ?>">
        <select name="status">
            <option value="">Tất cả trạng thái</option>
            <option value="1" <?= ($filters['status'] ?? '') === '1' ? 'selected' : '' ?>>Hoạt động</option>
            <option value="0" <?= ($filters['status'] ?? '') === '0' ? 'selected' : '' ?>>Đã khóa</option>
        </select>
        <button type="submit" class="btn btn-sm">Lọc</button>
        <a class="btn btn-sm" href="<?= url('/admin/khach-hang') ?>">Bỏ lọc</a>
    </form>

    <div class="table-responsive">
    <table class="admin-table">
        <thead>
        <tr>
            <th>ID</th>
            <th>Khách hàng</th>
            <th>Đơn đã đặt</th>
            <th>Tổng chi tiêu</th>
            <th>Ngày tham gia</th>
            <th>Trạng thái</th>
            <th>Thao tác</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($users as $user): ?>
            <tr class="<?= (int) $user['status'] === 0 ? 'row-muted' : '' ?>">
                <td><?= (int) $user['id'] ?></td>
                <td>
                    <strong><?= e($user['name']) ?></strong>
                    <div class="table-sub"><?= e($user['email']) ?> • <?= e($user['phone'] ?? '—') ?></div>
                </td>
                <td><?= number_format((int) $user['order_count']) ?></td>
                <td><?= formatPrice((float) $user['total_spent']) ?></td>
                <td><?= e(date('d/m/Y', strtotime((string) $user['created_at']))) ?></td>
                <td>
                    <span class="badge badge-<?= (int) $user['status'] === 1 ? 'success' : 'muted' ?>">
                        <?= (int) $user['status'] === 1 ? 'Hoạt động' : 'Đã khóa' ?>
                    </span>
                </td>
                <td class="table-actions">
                    <a class="btn btn-xs" href="<?= url('/admin/khach-hang/chi-tiet?id=' . (int) $user['id']) ?>">Chi tiết</a>
                    <form method="post" action="<?= url('/admin/khach-hang/khoa') ?>" class="inline-form">
                        <input type="hidden" name="csrf_token" value="<?= e(csrfToken()) ?>">
                        <input type="hidden" name="id" value="<?= (int) $user['id'] ?>">
                        <button type="submit" class="btn btn-xs <?= (int) $user['status'] === 1 ? 'btn-warning' : 'btn-success' ?>"
                                onclick="return confirm('<?= (int) $user['status'] === 1 ? 'Khóa tài khoản này?' : 'Mở khóa tài khoản này?' ?>')">
                            <?= (int) $user['status'] === 1 ? 'Khóa' : 'Mở khóa' ?>
                        </button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
        <?php if ($users === []): ?>
            <tr><td colspan="7" class="table-empty">Không tìm thấy khách hàng nào.</td></tr>
        <?php endif; ?>
        </tbody>
    </table>
    </div>

    <?php if ($pages > 1): ?>
        <?= paginationLinks($page, $pages, array_filter($filters, static fn ($v) => $v !== ''), '/admin/khach-hang') ?>
    <?php endif; ?>
</section>