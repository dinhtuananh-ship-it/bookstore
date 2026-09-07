<section class="books-layout">
    <aside class="filter-sidebar">
        <h3>Bộ lọc</h3>

        <form method="get" action="<?= url('/sach') ?>" id="filter-form">
            <input type="hidden" name="keyword" value="<?= e($filters['keyword'] ?? '') ?>">

            <div class="filter-group">
                <label>Tác giả</label>
                <select name="author" onchange="document.getElementById('filter-form').submit()">
                    <option value="">Tất cả</option>
                    <?php foreach ($authors as $a): ?>
                        <option value="<?= e($a['author']) ?>" <?= (($filters['author'] ?? '') === $a['author']) ? 'selected' : '' ?>>
                            <?= e($a['author']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="filter-group">
                <label>Nhà xuất bản</label>
                <select name="publisher" onchange="document.getElementById('filter-form').submit()">
                    <option value="">Tất cả</option>
                    <?php foreach ($publishers as $p): ?>
                        <option value="<?= e($p['publisher']) ?>" <?= (($filters['publisher'] ?? '') === $p['publisher']) ? 'selected' : '' ?>>
                            <?= e($p['publisher']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="filter-group">
                <label>Giá tối thiểu (₫)</label>
                <input type="number" name="price_min" min="0" step="1000" value="<?= e($filters['price_min'] ?? '') ?>">
            </div>

            <div class="filter-group">
                <label>Giá tối đa (₫)</label>
                <input type="number" name="price_max" min="0" step="1000" value="<?= e($filters['price_max'] ?? '') ?>">
            </div>

            <button type="submit" class="btn btn-block">Áp dụng lọc</button>
        </form>
    </aside>

    <div class="books-main">
        <div class="books-toolbar">
            <h1><?= e($pageTitle) ?></h1>
            <span class="books-count"><?= (int) $count ?> sản phẩm</span>
            <form method="get" action="<?= url('/sach') ?>" class="sort-form">
                <?php foreach ($filters as $k => $v): ?>
                    <?php if ($k === 'sort' || is_array($v) || $v === ''): continue; endif; ?>
                    <input type="hidden" name="<?= e($k) ?>" value="<?= e((string) $v) ?>">
                <?php endforeach; ?>
                <select name="sort" onchange="this.form.submit()">
                    <option value="newest" <?= (($filters['sort'] ?? '') === 'newest') ? 'selected' : '' ?>>Mới nhất</option>
                    <option value="best_selling" <?= (($filters['sort'] ?? '') === 'best_selling') ? 'selected' : '' ?>>Bán chạy nhất</option>
                    <option value="price_asc" <?= (($filters['sort'] ?? '') === 'price_asc') ? 'selected' : '' ?>>Giá thấp → cao</option>
                    <option value="price_desc" <?= (($filters['sort'] ?? '') === 'price_desc') ? 'selected' : '' ?>>Giá cao → thấp</option>
                    <option value="name_asc" <?= (($filters['sort'] ?? '') === 'name_asc') ? 'selected' : '' ?>>Tên A → Z</option>
                </select>
            </form>
        </div>

        <?php if ($books === []): ?>
            <p class="empty-note">Không tìm thấy sách phù hợp.</p>
        <?php else: ?>
            <div class="book-grid">
                <?php foreach ($books as $book): ?>
                    <?php require APP_PATH . '/views/partials/book_card.php'; ?>
                <?php endforeach; ?>
            </div>

            <?php
            $query = $filters;
            unset($query['category_ids']);
            if (isset($category)) {
                $query = ['slug' => $category['slug'], 'sort' => $filters['sort']];
            }
            if (!isset($category)) {
                unset($query['category_ids']);
            }
            $base = isset($category) ? '/danh-muc' : '/sach';
            echo paginationLinks($page, $pages, $query, $base);
            ?>
        <?php endif; ?>
    </div>
</section>