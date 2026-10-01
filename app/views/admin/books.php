<section>
    <div class="admin-head">
        <h1>Sách (<?= number_format($count) ?>)</h1>
        <div class="admin-head-actions">
            <a class="btn btn-sm" href="<?= url('/admin/sach/xuat?csrf_token=' . csrfToken()) ?>">Xuất CSV</a>
            <a class="btn btn-sm btn-primary" href="<?= url('/admin/sach/them') ?>">+ Thêm sách</a>
        </div>
    </div>

    <form class="filter-bar" method="get" action="<?= url('/admin/sach') ?>">
        <input type="text" name="keyword" placeholder="Tìm theo tên, tác giả, ISBN..." value="<?= e($filters['keyword'] ?? '') ?>">
        <select name="category_id">
            <option value="">Tất cả danh mục</option>
            <?php foreach ($categories as $cat): ?>
                <option value="<?= (int) $cat['id'] ?>" <?= (int) ($filters['category_id'] ?? 0) === (int) $cat['id'] ? 'selected' : '' ?>>
                    <?= e((int) $cat['parent_id'] === 0 ? $cat['name'] : '— ' . $cat['name']) ?>
                </option>
            <?php endforeach; ?>
        </select>
        <select name="status">
            <option value="">Tất cả trạng thái</option>
            <option value="1" <?= ($filters['status'] ?? '') === '1' ? 'selected' : '' ?>>Hiển thị</option>
            <option value="0" <?= ($filters['status'] ?? '') === '0' ? 'selected' : '' ?>>Ẩn</option>
        </select>
        <button type="submit" class="btn btn-sm">Lọc</button>
        <a class="btn btn-sm" href="<?= url('/admin/sach') ?>">Bỏ lọc</a>
    </form>

    <div class="table-responsive">
    <table class="admin-table">
        <thead>
        <tr>
            <th>ID</th>
            <th>Ảnh</th>
            <th>Tên sách</th>
            <th>Danh mục</th>
            <th>Giá</th>
            <th>Tồn kho</th>
            <th>Tập</th>
            <th>Đã bán</th>
            <th>Trạng thái</th>
            <th>Thao tác</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($books as $book): ?>
            <tr class="<?= (int) $book['status'] === 0 ? 'row-muted' : '' ?>">
                <td><?= (int) $book['id'] ?></td>
                <td>
                    <?php if (!empty($book['cover_image'])): ?>
                        <img class="table-thumb" src="<?= e(coverUrl($book['cover_image'] ?? null)) ?>" alt="">
                    <?php endif; ?>
                </td>
                <td>
                    <strong><?= e($book['title']) ?></strong>
                    <div class="table-sub"><?= e($book['author']) ?> • <?= e($book['isbn'] ?? '—') ?></div>
                </td>
                <td><?= e($book['category_name']) ?></td>
                <td>
                    <?= formatPrice($book['sale_price'] ?? $book['price']) ?>
                    <?php if (!empty($book['sale_price'])): ?>
                        <div class="table-sub price-line-through"><?= formatPrice($book['price']) ?></div>
                    <?php endif; ?>
                </td>
                <td><?= (int) $book['stock'] ?></td>
                <td><?= (int) $book['volumes'] > 1 ? (int) $book['volumes'] . ' tập' : '—' ?></td>
                <td><?= (int) $book['sold_count'] ?></td>
                <td>
                    <span class="badge badge-<?= (int) $book['status'] === 1 ? 'success' : 'muted' ?>">
                        <?= (int) $book['status'] === 1 ? 'Hiển thị' : 'Ẩn' ?>
                    </span>
                </td>
                <td class="table-actions">
                    <a class="btn btn-xs" href="<?= url('/admin/sach/sua?id=' . (int) $book['id']) ?>">Sửa</a>
                    <form method="post" action="<?= url('/admin/sach/xoa') ?>" class="inline-form">
                        <input type="hidden" name="csrf_token" value="<?= e(csrfToken()) ?>">
                        <input type="hidden" name="id" value="<?= (int) $book['id'] ?>">
                        <button type="submit" class="btn btn-xs btn-warning" onclick="return confirm('<?= (int) $book['status'] === 1 ? 'Ẩn sách này khỏi trang công khai?' : 'Hiện sách trở lại?' ?>')">
                            <?= (int) $book['status'] === 1 ? 'Ẩn' : 'Hiện' ?>
                        </button>
                    </form>
                    <form method="post" action="<?= url('/admin/sach/xoa-han') ?>" class="inline-form"
                          onsubmit="return confirm('Xóa hẳn sách này khỏi hệ thống? Hành động không thể hoàn tác!')">
                        <input type="hidden" name="csrf_token" value="<?= e(csrfToken()) ?>">
                        <input type="hidden" name="id" value="<?= (int) $book['id'] ?>">
                        <button type="submit" class="btn btn-xs btn-danger">Xóa hẳn</button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
        <?php if ($books === []): ?>
            <tr><td colspan="9" class="table-empty">Không tìm thấy sách nào.</td></tr>
        <?php endif; ?>
        </tbody>
    </table>
    </div>

    <?php if ($pages > 1): ?>
        <?= paginationLinks($page, $pages, array_filter($filters, static fn ($v) => $v !== '' && $v !== null && $v !== 0), '/admin/sach') ?>
    <?php endif; ?>
</section>
