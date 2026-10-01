<section>
    <div class="admin-head">
        <h1><?= $book === null ? 'Thêm sách mới' : 'Sửa sách' ?></h1>
        <a class="btn btn-sm" href="<?= url('/admin/sach') ?>">← Quay lại danh sách</a>
    </div>

    <form method="post" enctype="multipart/form-data" class="admin-form" action="<?= $book === null ? url('/admin/sach/them') : url('/admin/sach/sua?id=' . (int) $book['id']) ?>">
        <input type="hidden" name="csrf_token" value="<?= e(csrfToken()) ?>">
        <?php if ($book !== null): ?>
            <input type="hidden" name="id" value="<?= (int) $book['id'] ?>">
        <?php endif; ?>

        <div class="form-grid">
            <div class="form-group form-group-full">
                <label>Tên sách *</label>
                <input type="text" name="title" required value="<?= e($book['title'] ?? '') ?>" placeholder="Ví dụ: Đắc Nhân Tâm">
            </div>

            <div class="form-group">
                <label>Danh mục *</label>
                <select name="category_id" required>
                    <option value="">— Chọn danh mục —</option>
                    <?php foreach ($categories as $cat): ?>
                        <option value="<?= (int) $cat['id'] ?>" <?= (int) ($book['category_id'] ?? 0) === (int) $cat['id'] ? 'selected' : '' ?>>
                            <?= e((int) $cat['parent_id'] === 0 ? $cat['name'] : '— ' . $cat['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label>Tác giả</label>
                <input type="text" name="author" value="<?= e($book['author'] ?? '') ?>" placeholder="Ví dụ: Dale Carnegie">
            </div>

            <div class="form-group">
                <label>Nhà xuất bản</label>
                <input type="text" name="publisher" value="<?= e($book['publisher'] ?? '') ?>" placeholder="Ví dụ: NXB Trẻ">
            </div>

            <div class="form-group">
                <label>ISBN</label>
                <input type="text" name="isbn" value="<?= e($book['isbn'] ?? '') ?>" placeholder="Mã ISBN (không trùng)">
            </div>

            <div class="form-group">
                <label>Giá bán (₫) *</label>
                <input type="number" name="price" min="0" step="1000" required onwheel="this.blur()" value="<?= e($book['price'] ?? '') ?>">
            </div>

            <div class="form-group">
                <label>Giá khuyến mãi (₫)</label>
                <input type="number" name="sale_price" min="0" step="1000" onwheel="this.blur()" value="<?= e($book['sale_price'] ?? '') ?>">
            </div>

            <div class="form-group">
                <label>Tồn kho *</label>
                <input type="number" name="stock" min="0" required onwheel="this.blur()" value="<?= e($book['stock'] ?? '') ?>">
            </div>

            <div class="form-group">
                <label>Số tập</label>
                <input type="number" name="volumes" min="0" max="100" onwheel="this.blur()" value="<?= e((int) ($book['volumes'] ?? 0)) ?>">
                <p class="table-sub">0 = sách 1 tập. Nhập 2, 3... nếu sách có nhiều tập (vd: Kính vạn hoa 54 tập)</p>
            </div>

            <div class="form-group">
                <label>Trạng thái</label>
                <select name="status">
                    <option value="1" <?= !isset($book['status']) || (int) $book['status'] === 1 ? 'selected' : '' ?>>Hiển thị</option>
                    <option value="0" <?= isset($book['status']) && (int) $book['status'] === 0 ? 'selected' : '' ?>>Ẩn</option>
                </select>
            </div>

            <?php $coverRaw = (string) ($book['cover_image'] ?? ''); ?>
            <div class="form-group form-group-full">
                <label>Ảnh bìa</label>
                <?php if (coverUrl($coverRaw) !== ''): ?>
                    <div class="form-thumb"><img src="<?= e(coverUrl($coverRaw)) ?>" alt=""></div>
                <?php endif; ?>
                <input type="file" name="cover_image" accept="image/jpeg,image/png,image/webp,image/gif">
                <div class="paste-zone" id="pasteZone">📋 Bấm vào đây rồi nhấn <b>Ctrl+V</b> để dán ảnh bìa từ clipboard</div>
                <p class="table-sub">Tải file lên (jpg/png/webp/gif, tối đa 2MB) <b>hoặc</b> dán URL ảnh bìa bên dưới:</p>
                <input type="text" name="cover_url" placeholder="https://example.com/images/bia-sach.jpg"
                       value="<?= e(preg_match('#^https?://#i', $coverRaw) ? $coverRaw : '') ?>">
            </div>

            <div class="form-group form-group-full">
                <label>Mô tả</label>
                <textarea name="description" rows="5" placeholder="Tóm tắt nội dung sách..."><?= e($book['description'] ?? '') ?></textarea>
            </div>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary"><?= $book === null ? 'Thêm sách' : 'Lưu thay đổi' ?></button>
            <a class="btn" href="<?= url('/admin/sach') ?>">Hủy</a>
        </div>
    </form>
</section>

<script>
(function () {
    var zone = document.getElementById('pasteZone');
    var input = document.querySelector('input[name="cover_image"]');
    if (!zone || !input) { return; }

    zone.addEventListener('paste', function (e) {
        var items = ((e.clipboardData || window.clipboardData) || {}).items || [];
        for (var i = 0; i < items.length; i++) {
            var item = items[i];
            if (item.type && item.type.indexOf('image') === 0) {
                var file = item.getAsFile();
                if (!file) { continue; }
                var dt = new DataTransfer();
                dt.items.add(file);
                input.files = dt.files;
                zone.classList.add('pasted');
                zone.textContent = '✅ Đã dán ảnh: ' + file.name + ' (' + Math.round(file.size / 1024) + ' KB) - nhấn Lưu để tải lên';
                e.preventDefault();
                return;
            }
        }
    });

    input.addEventListener('change', function () {
        if (input.files.length) {
            zone.classList.add('pasted');
            zone.textContent = '✅ Đã chọn file: ' + input.files[0].name + ' - nhấn Lưu để tải lên';
        } else {
            zone.classList.remove('pasted');
            zone.textContent = '📋 Bấm vào đây rồi nhấn Ctrl+V để dán ảnh bìa từ clipboard';
        }
    });
})();
</script>
