<section>
    <div class="admin-head">
        <h1>Danh mục (<?= count($categories) ?>)</h1>
        <button type="button" class="btn btn-sm btn-primary toggle-btn" data-toggle-target="add-panel" data-toggle-group="cat-forms">+ Thêm danh mục</button>
    </div>

    <div id="add-panel" class="collapsible form-card">
        <h3>+ Thêm danh mục mới</h3>
        <form method="post" action="<?= url('/admin/danh-muc/them') ?>" class="admin-form">
            <input type="hidden" name="csrf_token" value="<?= e(csrfToken()) ?>">
            <div class="form-grid">
                <div class="form-group">
                    <label>Tên danh mục *</label>
                    <input type="text" name="name" required placeholder="Ví dụ: Văn học nước ngoài">
                </div>
                <div class="form-group">
                    <label>Slug (để trống = tự tạo)</label>
                    <input type="text" name="slug" placeholder="Ví dụ: van-hoc-nuoc-ngoai">
                </div>
                <div class="form-group">
                    <label>Danh mục cha</label>
                    <select name="parent_id">
                        <option value="0">— Không có (cấp cao nhất) —</option>
                        <?php foreach ($categories as $option): ?>
                            <option value="<?= (int) $option['id'] ?>"><?= e($option['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label>Trạng thái</label>
                    <select name="status">
                        <option value="1">Hoạt động</option>
                        <option value="0">Tắt</option>
                    </select>
                </div>
            </div>
            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Thêm danh mục</button>
                <button type="button" class="btn" data-toggle-target="add-panel">Hủy</button>
            </div>
        </form>
    </div>

    <div class="table-responsive">
        <table class="admin-table">
            <thead>
            <tr>
                <th>ID</th>
                <th>Tên danh mục</th>
                <th>Slug</th>
                <th>Sách</th>
                <th>Trạng thái</th>
                <th>Thao tác</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($tree as $cat): ?>
                <tr>
                    <td><?= (int) $cat['id'] ?></td>
                    <td><strong><?= e($cat['name']) ?></strong></td>
                    <td><?= e($cat['slug']) ?></td>
                    <td><?= number_format((int) $cat['book_count']) ?></td>
                    <td>
                        <span class="badge badge-<?= (int) $cat['status'] === 1 ? 'success' : 'muted' ?>">
                            <?= (int) $cat['status'] === 1 ? 'Hoạt động' : 'Tắt' ?>
                        </span>
                    </td>
                    <td class="table-actions">
                        <button type="button" class="btn btn-xs toggle-btn" data-toggle-target="edit-<?= (int) $cat['id'] ?>" data-toggle-group="cat-forms">Sửa</button>
                        <form method="post" action="<?= url('/admin/danh-muc/xoa') ?>" class="inline-form">
                            <input type="hidden" name="csrf_token" value="<?= e(csrfToken()) ?>">
                            <input type="hidden" name="id" value="<?= (int) $cat['id'] ?>">
                            <button type="submit" class="btn btn-xs btn-danger" onclick="return confirm('Xóa danh mục này?')">Xóa</button>
                        </form>
                    </td>
                </tr>
                <?php foreach ($cat['children'] as $child): ?>
                    <tr class="row-child">
                        <td><?= (int) $child['id'] ?></td>
                        <td>↳ <strong><?= e($child['name']) ?></strong></td>
                        <td><?= e($child['slug']) ?></td>
                        <td><?= number_format((int) $child['book_count']) ?></td>
                        <td>
                            <span class="badge badge-<?= (int) $child['status'] === 1 ? 'success' : 'muted' ?>">
                                <?= (int) $child['status'] === 1 ? 'Hoạt động' : 'Tắt' ?>
                            </span>
                        </td>
                        <td class="table-actions">
                            <button type="button" class="btn btn-xs toggle-btn" data-toggle-target="edit-<?= (int) $child['id'] ?>" data-toggle-group="cat-forms">Sửa</button>
                            <form method="post" action="<?= url('/admin/danh-muc/xoa') ?>" class="inline-form">
                                <input type="hidden" name="csrf_token" value="<?= e(csrfToken()) ?>">
                                <input type="hidden" name="id" value="<?= (int) $child['id'] ?>">
                                <button type="submit" class="btn btn-xs btn-danger" onclick="return confirm('Xóa danh mục này?')">Xóa</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endforeach; ?>
            <?php if ($tree === []): ?>
                <tr><td colspan="6" class="table-empty">Chưa có danh mục nào.</td></tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>

    <?php foreach ($categories as $cat): ?>
    <div id="edit-<?= (int) $cat['id'] ?>" class="collapsible form-card" style="margin-top: 16px;">
        <h3>Sửa danh mục: <?= e($cat['name']) ?></h3>
        <form method="post" action="<?= url('/admin/danh-muc/sua') ?>" class="admin-form">
            <input type="hidden" name="csrf_token" value="<?= e(csrfToken()) ?>">
            <input type="hidden" name="id" value="<?= (int) $cat['id'] ?>">
            <div class="form-grid">
                <div class="form-group">
                    <label>Tên danh mục *</label>
                    <input type="text" name="name" value="<?= e($cat['name']) ?>" required>
                </div>
                <div class="form-group">
                    <label>Slug (để trống = tự tạo)</label>
                    <input type="text" name="slug" value="<?= e($cat['slug']) ?>">
                </div>
                <div class="form-group">
                    <label>Danh mục cha</label>
                    <select name="parent_id">
                        <option value="0">— Không có (cấp cao nhất) —</option>
                        <?php foreach ($categories as $option): ?>
                            <?php if ((int) $option['id'] === (int) $cat['id']): continue; endif; ?>
                            <option value="<?= (int) $option['id'] ?>" <?= (int) $cat['parent_id'] === (int) $option['id'] ? 'selected' : '' ?>>
                                <?= e($option['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label>Trạng thái</label>
                    <select name="status">
                        <option value="1" <?= (int) $cat['status'] === 1 ? 'selected' : '' ?>>Hoạt động</option>
                        <option value="0" <?= (int) $cat['status'] === 0 ? 'selected' : '' ?>>Tắt</option>
                    </select>
                </div>
            </div>
            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Lưu thay đổi</button>
                <button type="button" class="btn" data-toggle-target="edit-<?= (int) $cat['id'] ?>">Hủy</button>
            </div>
        </form>
    </div>
    <?php endforeach; ?>
</section>
