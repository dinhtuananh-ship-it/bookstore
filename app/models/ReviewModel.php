<?php
declare(strict_types=1);

class ReviewModel extends Model
{
    public function getSummary(int $bookId): array
    {
        $row = $this->fetchOne(
            'SELECT COUNT(*) AS cnt, COALESCE(AVG(rating), 0) AS avg_rating
             FROM reviews WHERE book_id = ? AND status = 1',
            [$bookId]
        );

        $perStar = [];
        foreach ($this->fetchAll(
            'SELECT rating, COUNT(*) AS c FROM reviews WHERE book_id = ? AND status = 1 GROUP BY rating',
            [$bookId]
        ) as $r) {
            $perStar[(int) $r['rating']] = (int) $r['c'];
        }

        return [
            'count'    => (int) ($row['cnt'] ?? 0),
            'avg'      => round((float) ($row['avg_rating'] ?? 0), 1),
            'per_star' => $perStar,
        ];
    }

    public function getForBook(int $bookId, int $page = 1, int $perPage = 5): array
    {
        $count = (int) ($this->fetchOne(
            'SELECT COUNT(*) AS c FROM reviews WHERE book_id = ? AND status = 1',
            [$bookId]
        )['c'] ?? 0);

        $pages = max(1, (int) ceil($count / $perPage));
        $page = min(max(1, $page), $pages);
        $offset = ($page - 1) * $perPage;

        $items = $this->fetchAll(
            'SELECT r.*, u.name AS user_name, u.avatar AS user_avatar
             FROM reviews r
             JOIN users u ON u.id = r.user_id
             WHERE r.book_id = ? AND r.status = 1
             ORDER BY r.id DESC
             LIMIT ? OFFSET ?',
            [$bookId, $perPage, $offset]
        );

        return [
            'items' => $items,
            'count' => $count,
            'page'  => $page,
            'pages' => $pages,
        ];
    }

    public function hasPurchased(int $userId, int $bookId): bool
    {
        return $this->fetchOne(
            'SELECT oi.id FROM order_items oi
             JOIN orders o ON o.id = oi.order_id
             WHERE o.user_id = ? AND oi.book_id = ? AND o.status = "completed"
             LIMIT 1',
            [$userId, $bookId]
        ) !== null;
    }

    public function getByUserAndBook(int $userId, int $bookId): ?array
    {
        return $this->fetchOne(
            'SELECT * FROM reviews WHERE user_id = ? AND book_id = ?',
            [$userId, $bookId]
        );
    }

    public function findById(int $id): ?array
    {
        return $this->fetchOne('SELECT * FROM reviews WHERE id = ?', [$id]);
    }

    public function create(int $userId, int $bookId, int $rating, string $comment, int $status = 1): int
    {
        $this->query(
            'INSERT INTO reviews (user_id, book_id, rating, comment, status) VALUES (?, ?, ?, ?, ?)',
            [$userId, $bookId, $rating, $comment, $status]
        );
        return $this->lastInsertId();
    }

    public function update(int $id, int $rating, string $comment): void
    {
        $this->query('UPDATE reviews SET rating = ?, comment = ? WHERE id = ?', [$rating, $comment, $id]);
    }

    public function delete(int $id, int $userId): void
    {
        $this->query('DELETE FROM reviews WHERE id = ? AND user_id = ?', [$id, $userId]);
    }

    public function getAllAdmin(array $filters = [], int $page = 1, int $perPage = 10): array
    {
        $where = [];
        $params = [];

        if (isset($filters['status']) && $filters['status'] !== '') {
            $where[] = 'r.status = ?';
            $params[] = (int) $filters['status'];
        }

        $keyword = trim((string) ($filters['keyword'] ?? ''));
        if ($keyword !== '') {
            $where[] = '(b.title LIKE ? OR u.name LIKE ? OR r.comment LIKE ?)';
            $like = '%' . $keyword . '%';
            array_push($params, $like, $like, $like);
        }

        $whereSql = $where === [] ? '' : 'WHERE ' . implode(' AND ', $where);

        $count = (int) ($this->fetchOne(
            "SELECT COUNT(*) AS c FROM reviews r JOIN books b ON b.id = r.book_id JOIN users u ON u.id = r.user_id {$whereSql}",
            $params
        )['c'] ?? 0);

        $pages = max(1, (int) ceil($count / $perPage));
        $page = min(max(1, $page), $pages);
        $offset = ($page - 1) * $perPage;

        $items = $this->fetchAll(
            "SELECT r.*, b.title AS book_title, u.name AS user_name, u.email AS user_email
             FROM reviews r
             JOIN books b ON b.id = r.book_id
             JOIN users u ON u.id = r.user_id
             {$whereSql}
             ORDER BY r.id DESC
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

    public function setStatus(int $id, int $status): void
    {
        $this->query('UPDATE reviews SET status = ? WHERE id = ?', [$status, $id]);
    }

    public function deleteById(int $id): void
    {
        $this->query('DELETE FROM reviews WHERE id = ?', [$id]);
    }
}