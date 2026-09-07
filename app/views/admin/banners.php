<section>
    <div class="admin-head">
        <h1>Banner quảng cáo (<?= count($banners) ?>)</h1>
        <a class="btn btn-sm btn-primary" href="#add-form">+ Thêm banner</a>
    </div>

    <p class="table-sub" style="margin-bottom: 12px;">
        Banner có thứ tự (sort order) nhỏ nhất và đang hoạt động sẽ hiển thị trên trang chủ.
    </p>

    <div class="admin-columns">
        <div class="admin-column-main">
            <table class="admin-table">
                <thead>
                <tr>
                    <th>ID</th>
                    <th>Ảnh</th>
                    <th>Tiêu đề</th>
                    <th>Link</th>
                    <th>Thứ tự</th>
                    <th>Trạng thái</th>
                    <th>Thao tác</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($banners as $banner): ?>
                    <tr>
                        <td><?= (int) $banner['id'] ?></td>
                        <td>
                            <?php if (coverUrl($banner['image'] ?? null) !== ''): ?>
                                <img class="table-thumb" src="<?= e(coverUrl($banner['image'] ?? null)) ?>" alt="">
                            <?php else: ?>
                                <span class="table-sub">—</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <strong><?= e($banner['title']) ?></strong>
                            <?php if (!empty($banner['subtitle'])): ?>
                                <div class="table-sub"><?= e($banner['subtitle']) ?></div>
                            <?php endif; ?>
                        </td>
                        <td><?= e($banner['link'] ?? '—') ?></td>
                        <td><?= (int) $banner['sort_order'] ?></td>
                        <td>
                            <span class="badge badge-<?= (int) $banner['status'] === 1 ? 'success' : 'muted' ?>">
                                <?= (int) $banner['status'] === 1 ? 'Hoạt động' : 'Tắt' ?>
                            </span>
                        </td>
                        <td class="table-actions">
                            <a class="btn btn-xs" href="#edit-<?= (int) $banner['id'] ?>">Sửa</a>
                            <form method="post" action="<?= url('/admin/banner/xoa') ?>" class="inline-form">
                                <input type="hidden" name="csrf_token" value="<?= e(csrfToken()) ?>">
                                <input type="hidden" name="id" value="<?= (int) $banner['id'] ?>">
                                <button type="submit" class="btn btn-xs btn-danger" onclick="return confirm('Xóa banner này?')">Xóa</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <?php if ($banners === []): ?>
                    <tr><td colspan="7" class="table-empty">Chưa có banner nào.</td></tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>

        <div class="admin-column-side">
            <?php foreach ($banners as $banner): ?>
                <div id="edit-<?= (int) $banner['id'] ?>" class="panel panel-collapsible">
                    <h3 class="panel-title">Sửa: <?= e($banner['title']) ?></h3>
                    <form method="post" action="<?= url('/admin/banner/sua') ?>" class="admin-form" enctype="multipart/form-data">
                        <input type="hidden" name="csrf_token" value="<?= e(csrfToken()) ?>">
                        <input type="hidden" name="id" value="<?= (int) $banner['id'] ?>">
                        <div class="form-group">
                            <label>Tiêu đề *</label>
                            <input type="text" name="title" value="<?= e($banner['title']) ?>" required>
                        </div>
                        <div class="form-group">
                            <label>Phụ đề</label>
                            <input type="text" name="subtitle" value="<?= e($banner['subtitle'] ?? '') ?>">
                        </div>
                        <div class="form-group">
                            <label>Link (vd: /sach hoặc https://...)</label>
                            <input type="text" name="link" value="<?= e($banner['link'] ?? '') ?>">
                        </div>
                        <div class="form-group">
                            <label>Thứ tự (nhỏ = hiển thị trước)</label>
                            <input type="number" name="sort_order" min="0" value="<?= (int) $banner['sort_order'] ?>">
                        </div>
                        <div class="form-group">
                            <label>Trạng thái</label>
                            <select name="status">
                                <option value="1" <?= (int) $banner['status'] === 1 ? 'selected' : '' ?>>Hoạt động</option>
                                <option value="0" <?= (int) $banner['status'] === 0 ? 'selected' : '' ?>>Tắt</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Ảnh banner (bỏ trống = giữ ảnh cũ)</label>
                            <input type="file" name="image" accept="image/*">
                            <input type="text" name="image_url" placeholder="Hoặc dán URL ảnh (https://...)" value="">
                        </div>
                        <button type="submit" class="btn btn-primary btn-sm">Lưu</button>
                    </form>
                </div>
            <?php endforeach; ?>

            <div id="add-form" class="panel panel-collapsible">
                <h3 class="panel-title">+ Thêm banner mới</h3>
                <form method="post" action="<?= url('/admin/banner/them') ?>" class="admin-form" enctype="multipart/form-data">
                    <input type="hidden" name="csrf_token" value="<?= e(csrfToken()) ?>">
                    <div class="form-group">
                        <label>Tiêu đề *</label>
                        <input type="text" name="title" required placeholder="Ví dụ: Khuyến mãi sách Kinh tế - giảm 30%">
                    </div>
                    <div class="form-group">
                        <label>Phụ đề</label>
                        <input type="text" name="subtitle" placeholder="Ví dụ: Áp dụng đến hết tháng">
                    </div>
                    <div class="form-group">
                        <label>Link (vd: /sach hoặc https://...)</label>
                        <input type="text" name="link" placeholder="/sach">
                    </div>
                    <div class="form-group">
                        <label>Thứ tự (nhỏ = hiển thị trước)</label>
                        <input type="number" name="sort_order" min="0" value="0">
                    </div>
                    <div class="form-group">
                        <label>Trạng thái</label>
                        <select name="status">
                            <option value="1" selected>Hoạt động</option>
                            <option value="0">Tắt</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Ảnh banner</label>
                        <input type="file" name="image" accept="image/*">
                        <input type="text" name="image_url" placeholder="Hoặc dán URL ảnh (https://...)" value="">
                    </div>
                    <button type="submit" class="btn btn-primary btn-sm">Thêm banner</button>
                </form>
            </div>
        </div>
    </div>
</section>