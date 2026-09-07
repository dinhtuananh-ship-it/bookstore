<section>
    <div class="admin-head">
        <h1>Bình luận / Đánh giá (<?= number_format($count) ?>)</h1>
    </div>

    <form class="filter-bar" method="get" action="<?= url('/admin/binh-luan') ?>">
        <input type="text" name="keyword" placeholder="Tìm theo sách, khách hàng, nội dung..." value="<?= e($filters['keyword'] ?? '') ?>">
        <select name="status">
            <option value="">Tất cả trạng thái</option>
            <option value="1" <?= ($filters['status'] ?? '') === '1' ? 'selected' : '' ?>>Hiển thị</option>
            <option value="0" <?= ($filters['status'] ?? '') === '0' ? 'selected' : '' ?>>Ẩn</option>
        </select>
        <button type="submit" class="btn btn-sm">Lọc</button>
        <a class="btn btn-sm" href="<?= url('/admin/binh-luan') ?>">Bỏ lọc</a>
    </form>

    <table class="admin-table">
        <thead>
        <tr>
            <th>ID</th>
            <th>Sách</th>
            <th>Khách hàng</th>
            <th>Sao</th>
            <th>Nội dung</th>
            <th>Ngày</th>
            <th>Trạng thái</th>
            <th>Thao tác</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($reviews as $review): ?>
            <tr class="<?= (int) $review['status'] === 0 ? 'row-muted' : '' ?>">
                <td><?= (int) $review['id'] ?></td>
                <td><?= e($review['book_title']) ?></td>
                <td>
                    <?= e($review['user_name']) ?>
                    <div class="table-sub"><?= e($review['user_email']) ?></div>
                </td>
                <td><?= stars((int) $review['rating']) ?></td>
                <td><?= e($review['comment'] ?? '—') ?></td>
                <td><?= e(date('d/m/Y', strtotime((string) $review['created_at']))) ?></td>
                <td>
                    <span class="badge badge-<?= (int) $review['status'] === 1 ? 'success' : 'muted' ?>">
                        <?= (int) $review['status'] === 1 ? 'Hiển thị' : 'Ẩn' ?>
                    </span>
                </td>
                <td class="table-actions">
                    <form method="post" action="<?= url('/admin/binh-luan/duyet') ?>" class="inline-form">
                        <input type="hidden" name="csrf_token" value="<?= e(csrfToken()) ?>">
                        <input type="hidden" name="id" value="<?= (int) $review['id'] ?>">
                        <button type="submit" class="btn btn-xs <?= (int) $review['status'] === 1 ? 'btn-warning' : 'btn-success' ?>">
                            <?= (int) $review['status'] === 1 ? 'Ẩn' : 'Hiển thị' ?>
                        </button>
                    </form>
                    <form method="post" action="<?= url('/admin/binh-luan/xoa') ?>" class="inline-form">
                        <input type="hidden" name="csrf_token" value="<?= e(csrfToken()) ?>">
                        <input type="hidden" name="id" value="<?= (int) $review['id'] ?>">
                        <button type="submit" class="btn btn-xs btn-danger" onclick="return confirm('Xóa bình luận này?')">Xóa</button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
        <?php if ($reviews === []): ?>
            <tr><td colspan="8" class="table-empty">Chưa có bình luận nào.</td></tr>
        <?php endif; ?>
        </tbody>
    </table>

    <?php if ($pages > 1): ?>
        <?= paginationLinks($page, $pages, array_filter($filters, static fn ($v) => $v !== ''), '/admin/binh-luan') ?>
    <?php endif; ?>
</section>