<article class="book-card reveal">
    <a class="book-card-link" href="<?= url('/sach/chi-tiet?id=' . (int) $book['id']) ?>">
        <div class="book-cover">
            <?php if (bookCover($book) !== ''): ?>
                <img src="<?= e(bookCover($book)) ?>" alt="<?= e($book['title']) ?>" loading="lazy">
            <?php else: ?>
                <span class="book-cover-placeholder">📖</span>
            <?php endif; ?>
            <?php if ((float) $book['sale_price'] > 0 && (float) $book['sale_price'] < (float) $book['price']): ?>
                <span class="book-badge">-<?= (int) round((1 - (float) $book['sale_price'] / (float) $book['price']) * 100) ?>%</span>
            <?php endif; ?>
        </div>
        <h3 class="book-title"><?= e($book['title']) ?></h3>
        <p class="book-author"><?= e($book['author'] ?? '') ?></p>
        <div class="book-price">
            <?php if ((float) $book['sale_price'] > 0 && (float) $book['sale_price'] < (float) $book['price']): ?>
                <span class="price-sale"><?= e(formatPrice((float) $book['sale_price'])) ?></span>
                <span class="price-old"><?= e(formatPrice((float) $book['price'])) ?></span>
            <?php else: ?>
                <span class="price-sale"><?= e(formatPrice((float) $book['price'])) ?></span>
            <?php endif; ?>
        </div>
    </a>
    <form class="book-card-cart" method="post" action="<?= url('/gio-hang/them') ?>">
        <input type="hidden" name="csrf_token" value="<?= e(csrfToken()) ?>">
        <input type="hidden" name="book_id" value="<?= (int) $book['id'] ?>">
        <input type="hidden" name="qty" value="1">
        <button type="submit" class="btn-cart-add" title="Thêm vào giỏ hàng">🛒 Thêm vào giỏ</button>
    </form>
</article>