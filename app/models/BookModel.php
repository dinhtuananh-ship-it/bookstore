<?php
declare(strict_types=1);

class BookModel extends Model
{
    public function search(array $filters = [], int $page = 1, int $perPage = ITEMS_PER_PAGE): array
    {
        $where = ['status = 1'];
        $params = [];

        $keyword = trim((string) ($filters['keyword'] ?? ''));
        if ($keyword !== '') {
            $where[] = '(title LIKE ? OR author LIKE ? OR publisher LIKE ? OR isbn LIKE ? OR search_key LIKE ?)';
            $like = '%' . $keyword . '%';
            array_push($params, $like, $like, $like, $like);
            $params[] = '%' . normalizeSearchKey($keyword) . '%';
        }

        if (!empty($filters['category_ids'])) {
            $ids = array_map('intval', (array) $filters['category_ids']);
            $placeholders = implode(',', array_fill(0, count($ids), '?'));
            $where[] = "category_id IN ({$placeholders})";
            array_push($params, ...$ids);
        }

        $author = trim((string) ($filters['author'] ?? ''));
        if ($author !== '') {
            $where[] = 'author = ?';
            $params[] = $author;
        }

        $publisher = trim((string) ($filters['publisher'] ?? ''));
        if ($publisher !== '') {
            $where[] = 'publisher = ?';
            $params[] = $publisher;
        }

        $priceMin = (float) ($filters['price_min'] ?? 0);
        if ($priceMin > 0) {
            $where[] = '(COALESCE(sale_price, price) >= ?)';
            $params[] = $priceMin;
        }

        $priceMax = (float) ($filters['price_max'] ?? 0);
        if ($priceMax > 0) {
            $where[] = '(COALESCE(sale_price, price) <= ?)';
            $params[] = $priceMax;
        }

        $sort = (string) ($filters['sort'] ?? 'newest');
        $orderBy = match ($sort) {
            'price_asc'     => 'COALESCE(sale_price, price) ASC',
            'price_desc'    => 'COALESCE(sale_price, price) DESC',
            'best_selling'  => 'sold_count DESC, id DESC',
            'name_asc'      => 'title ASC',
            default         => 'created_at DESC, id DESC',
        };

        $whereSql = implode(' AND ', $where);
        $count = (int) ($this->fetchOne(
            "SELECT COUNT(*) AS c FROM books WHERE {$whereSql}",
            $params
        )['c'] ?? 0);

        $pages = max(1, (int) ceil($count / $perPage));
        $page = min(max(1, $page), $pages);
        $offset = ($page - 1) * $perPage;

        $items = $this->fetchAll(
            "SELECT * FROM books WHERE {$whereSql} ORDER BY {$orderBy} LIMIT {$perPage} OFFSET {$offset}",
            $params
        );

        return [
            'items' => $items,
            'count' => $count,
            'page'  => $page,
            'pages' => $pages,
        ];
    }

    public function findById(int $id): ?array
    {
        return $this->fetchOne(
            'SELECT b.*, c.name AS category_name, c.slug AS category_slug
             FROM books b
             JOIN categories c ON c.id = b.category_id
             WHERE b.id = ? AND b.status = 1',
            [$id]
        );
    }

    public function getNewest(int $limit = 8): array
    {
        return $this->fetchAll(
            'SELECT * FROM books WHERE status = 1 ORDER BY created_at DESC, id DESC LIMIT ?',
            [$limit]
        );
    }

    public function getBestSelling(int $limit = 8): array
    {
        return $this->fetchAll(
            'SELECT * FROM books WHERE status = 1 ORDER BY sold_count DESC, id DESC LIMIT ?',
            [$limit]
        );
    }

    public function getRelated(int $bookId, int $categoryId, int $limit = 4): array
    {
        return $this->fetchAll(
            'SELECT * FROM books
             WHERE status = 1 AND category_id = ? AND id != ?
             ORDER BY sold_count DESC LIMIT ?',
            [$categoryId, $bookId, $limit]
        );
    }

    public function getAuthors(): array
    {
        return $this->fetchAll(
            'SELECT DISTINCT author FROM books WHERE status = 1 AND author IS NOT NULL AND author != "" ORDER BY author'
        );
    }

    public function getPublishers(): array
    {
        return $this->fetchAll(
            'SELECT DISTINCT publisher FROM books WHERE status = 1 AND publisher IS NOT NULL AND publisher != "" ORDER BY publisher'
        );
    }

    public function countAll(): int
    {
        return (int) ($this->fetchOne('SELECT COUNT(*) AS c FROM books')['c'] ?? 0);
    }

    public function findByIsbn(string $isbn, ?int $excludeId = null): ?array
    {
        if ($excludeId) {
            return $this->fetchOne('SELECT id FROM books WHERE isbn = ? AND id != ?', [$isbn, $excludeId]);
        }
        return $this->fetchOne('SELECT id FROM books WHERE isbn = ?', [$isbn]);
    }

    public function getAllAdmin(array $filters = [], int $page = 1, int $perPage = ITEMS_PER_PAGE): array
    {
        $where = [];
        $params = [];

        $keyword = trim((string) ($filters['keyword'] ?? ''));
        if ($keyword !== '') {
            $where[] = '(b.title LIKE ? OR b.author LIKE ? OR b.isbn LIKE ? OR b.search_key LIKE ?)';
            $like = '%' . $keyword . '%';
            array_push($params, $like, $like, $like);
            $params[] = '%' . normalizeSearchKey($keyword) . '%';
        }

        $categoryId = (int) ($filters['category_id'] ?? 0);
        if ($categoryId > 0) {
            $where[] = 'b.category_id = ?';
            $params[] = $categoryId;
        }

        if (isset($filters['status']) && $filters['status'] !== '') {
            $where[] = 'b.status = ?';
            $params[] = (int) $filters['status'];
        }

        $whereSql = $where === [] ? '' : 'WHERE ' . implode(' AND ', $where);

        $count = (int) ($this->fetchOne(
            "SELECT COUNT(*) AS c FROM books b {$whereSql}",
            $params
        )['c'] ?? 0);

        $pages = max(1, (int) ceil($count / $perPage));
        $page = min(max(1, $page), $pages);
        $offset = ($page - 1) * $perPage;

        $items = $this->fetchAll(
            "SELECT b.*, c.name AS category_name
             FROM books b
             JOIN categories c ON c.id = b.category_id
             {$whereSql}
             ORDER BY b.id DESC
             LIMIT {$perPage} OFFSET {$offset}",
            $params
        );

        return [
            'items' => $items,
            'count' => $count,
            'page'  => $page,
            'pages' => $pages,
        ];
    }

    public function create(array $data): int
    {
        $this->query(
            'INSERT INTO books (category_id, title, author, publisher, isbn, price, sale_price, stock, volumes, description, search_key, cover_image, status)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)',
            [
                $data['category_id'],
                $data['title'],
                $data['author'] ?? null,
                $data['publisher'] ?? null,
                $data['isbn'] ?? null,
                $data['price'],
                $data['sale_price'],
                $data['stock'],
                (int) ($data['volumes'] ?? 0),
                $data['description'] ?? null,
                normalizeSearchKey($data['title'] . ' ' . ($data['author'] ?? '') . ' ' . ($data['publisher'] ?? '') . ' ' . ($data['isbn'] ?? '')),
                $data['cover_image'] ?? null,
                $data['status'],
            ]
        );
        return $this->lastInsertId();
    }

    public function update(int $id, array $data): void
    {
        $this->query(
            'UPDATE books SET category_id = ?, title = ?, author = ?, publisher = ?, isbn = ?,
             price = ?, sale_price = ?, stock = ?, volumes = ?, description = ?, search_key = ?, cover_image = ?, status = ?
             WHERE id = ?',
            [
                $data['category_id'],
                $data['title'],
                $data['author'] ?? null,
                $data['publisher'] ?? null,
                $data['isbn'] ?? null,
                $data['price'],
                $data['sale_price'],
                $data['stock'],
                (int) ($data['volumes'] ?? 0),
                $data['description'] ?? null,
                normalizeSearchKey($data['title'] . ' ' . ($data['author'] ?? '') . ' ' . ($data['publisher'] ?? '') . ' ' . ($data['isbn'] ?? '')),
                $data['cover_image'] ?? null,
                $data['status'],
                $id,
            ]
        );
    }

    public function toggleStatus(int $id): void
    {
        $this->query('UPDATE books SET status = 1 - status WHERE id = ?', [$id]);
    }

    public function findRaw(int $id): ?array
    {
        return $this->fetchOne('SELECT * FROM books WHERE id = ?', [$id]);
    }

    public function hasOrderItems(int $id): bool
    {
        $row = $this->fetchOne('SELECT COUNT(*) AS c FROM order_items WHERE book_id = ?', [$id]);
        return (int) ($row['c'] ?? 0) > 0;
    }

    public function delete(int $id): void
    {
        $this->query('DELETE FROM books WHERE id = ?', [$id]);
    }
}