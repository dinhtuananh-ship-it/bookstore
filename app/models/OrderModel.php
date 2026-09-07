<?php
declare(strict_types=1);

class OrderModel extends Model
{
    public function create(array $data): int
    {
        $this->db->beginTransaction();

        try {
            $this->query(
                'INSERT INTO orders (user_id, code, address_id, coupon_id, subtotal, discount, shipping_fee, total, payment_method, status, note)
                 VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)',
                [
                    $data['user_id'],
                    '',
                    $data['address_id'],
                    $data['coupon_id'],
                    $data['subtotal'],
                    $data['discount'],
                    $data['shipping_fee'],
                    $data['total'],
                    $data['payment_method'],
                    $data['status'],
                    $data['note'] ?? null,
                ]
            );

            $orderId = $this->lastInsertId();
            $code = 'BK' . date('Ymd') . '-' . str_pad((string) $orderId, 4, '0', STR_PAD_LEFT);
            $this->query('UPDATE orders SET code = ? WHERE id = ?', [$code, $orderId]);

            foreach ($data['items'] as $item) {
                $this->query(
                    'INSERT INTO order_items (order_id, book_id, volume, quantity, price) VALUES (?, ?, ?, ?, ?)',
                    [$orderId, $item['book_id'], (int) ($item['volume'] ?? 1), $item['quantity'], $item['price']]
                );
                $this->query(
                    'UPDATE books SET stock = stock - ?, sold_count = sold_count + ? WHERE id = ?',
                    [$item['quantity'], $item['quantity'], $item['book_id']]
                );
            }

            if (!empty($data['coupon_id'])) {
                (new CouponModel())->incrementUsed((int) $data['coupon_id']);
            }

            $this->db->commit();
            return $orderId;
        } catch (Throwable $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    public function getById(int $orderId): ?array
    {
        return $this->fetchOne(
            'SELECT o.*, a.full_name, a.phone, a.province, a.district, a.ward, a.detail AS address_detail
             FROM orders o
             LEFT JOIN addresses a ON a.id = o.address_id
             WHERE o.id = ?',
            [$orderId]
        );
    }

    public function getItems(int $orderId): array
    {
        return $this->fetchAll(
            'SELECT oi.*, b.title, b.author, b.cover_image
             FROM order_items oi
             JOIN books b ON b.id = oi.book_id
             WHERE oi.order_id = ?',
            [$orderId]
        );
    }

    public function updateStatus(int $orderId, string $status): void
    {
        $this->query('UPDATE orders SET status = ? WHERE id = ?', [$status, $orderId]);
    }

    public function recordPayment(int $orderId, string $method, string $transactionId, float $amount): void
    {
        $this->query(
            'INSERT INTO payments (order_id, method, transaction_id, amount, status) VALUES (?, ?, ?, ?, "success")',
            [$orderId, $method, $transactionId, $amount]
        );
        $this->query('UPDATE orders SET status = "paid" WHERE id = ?', [$orderId]);
    }

    public function getByUser(int $userId, int $page = 1, int $perPage = 10): array
    {
        $count = (int) ($this->fetchOne(
            'SELECT COUNT(*) AS c FROM orders WHERE user_id = ?',
            [$userId]
        )['c'] ?? 0);

        $pages = max(1, (int) ceil($count / $perPage));
        $page = min(max(1, $page), $pages);
        $offset = ($page - 1) * $perPage;

        $items = $this->fetchAll(
            'SELECT * FROM orders WHERE user_id = ? ORDER BY id DESC LIMIT ? OFFSET ?',
            [$userId, $perPage, $offset]
        );

        return [
            'items' => $items,
            'count' => $count,
            'page'  => $page,
            'pages' => $pages,
        ];
    }

    public function recordStatus(int $orderId, string $status, ?string $note = null, string $changedBy = 'system'): void
    {
        $this->query(
            'INSERT INTO order_status_history (order_id, status, note, changed_by) VALUES (?, ?, ?, ?)',
            [$orderId, $status, $note, $changedBy]
        );
    }

    public function getStatusHistory(int $orderId): array
    {
        return $this->fetchAll(
            'SELECT * FROM order_status_history WHERE order_id = ? ORDER BY id DESC',
            [$orderId]
        );
    }

    public function cancel(int $orderId, string $note = 'Khách hàng hủy đơn', string $changedBy = 'member'): void
    {
        $this->db->beginTransaction();

        try {
            foreach ($this->getItems($orderId) as $item) {
                $this->query(
                    'UPDATE books SET stock = stock + ?, sold_count = GREATEST(sold_count - ?, 0) WHERE id = ?',
                    [(int) $item['quantity'], (int) $item['quantity'], (int) $item['book_id']]
                );
            }

            $this->query(
                'UPDATE payments SET status = "failed" WHERE order_id = ? AND status = "success"',
                [$orderId]
            );
            $this->query('UPDATE orders SET status = "cancelled" WHERE id = ?', [$orderId]);
            $this->recordStatus($orderId, 'cancelled', $note, $changedBy);

            $this->db->commit();
        } catch (Throwable $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    public function getAllAdmin(array $filters = [], int $page = 1, int $perPage = ITEMS_PER_PAGE): array
    {
        $where = [];
        $params = [];

        if (isset($filters['status']) && $filters['status'] !== '') {
            $where[] = 'o.status = ?';
            $params[] = (string) $filters['status'];
        }

        $keyword = trim((string) ($filters['keyword'] ?? ''));
        if ($keyword !== '') {
            $where[] = '(o.code LIKE ? OR u.name LIKE ? OR u.email LIKE ?)';
            $like = '%' . $keyword . '%';
            array_push($params, $like, $like, $like);
        }

        if (!empty($filters['from'])) {
            $where[] = 'DATE(o.created_at) >= ?';
            $params[] = (string) $filters['from'];
        }
        if (!empty($filters['to'])) {
            $where[] = 'DATE(o.created_at) <= ?';
            $params[] = (string) $filters['to'];
        }

        $whereSql = $where === [] ? '' : 'WHERE ' . implode(' AND ', $where);

        $count = (int) ($this->fetchOne(
            "SELECT COUNT(*) AS c FROM orders o JOIN users u ON u.id = o.user_id {$whereSql}",
            $params
        )['c'] ?? 0);

        $pages = max(1, (int) ceil($count / $perPage));
        $page = min(max(1, $page), $pages);
        $offset = ($page - 1) * $perPage;

        $items = $this->fetchAll(
            "SELECT o.*, u.name AS user_name, u.email AS user_email
             FROM orders o
             JOIN users u ON u.id = o.user_id
             {$whereSql}
             ORDER BY o.id DESC
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

    public function reportSummary(string $from, string $to): array
    {
        $row = $this->fetchOne(
            "SELECT COUNT(*) AS c, COALESCE(SUM(total), 0) AS t
             FROM orders
             WHERE status NOT IN ('cancelled') AND DATE(created_at) BETWEEN ? AND ?",
            [$from, $to]
        );

        $count = (int) ($row['c'] ?? 0);
        $revenue = (float) ($row['t'] ?? 0);

        return [
            'count'   => $count,
            'revenue' => $revenue,
            'avg'     => $count > 0 ? $revenue / $count : 0.0,
        ];
    }

    public function revenueByDay(string $from, string $to): array
    {
        return $this->fetchAll(
            "SELECT DATE(created_at) AS day, COUNT(*) AS c, COALESCE(SUM(total), 0) AS t
             FROM orders
             WHERE status NOT IN ('cancelled') AND DATE(created_at) BETWEEN ? AND ?
             GROUP BY DATE(created_at)
             ORDER BY day",
            [$from, $to]
        );
    }

    public function statusBreakdown(string $from, string $to): array
    {
        $rows = $this->fetchAll(
            'SELECT status, COUNT(*) AS c FROM orders WHERE DATE(created_at) BETWEEN ? AND ? GROUP BY status',
            [$from, $to]
        );
        $map = [];
        foreach ($rows as $row) {
            $map[$row['status']] = (int) $row['c'];
        }
        return $map;
    }

    public function topBooks(string $from, string $to, int $limit = 5): array
    {
        return $this->fetchAll(
            "SELECT b.title, SUM(oi.quantity) AS qty, SUM(oi.price * oi.quantity) AS revenue
             FROM order_items oi
             JOIN orders o ON o.id = oi.order_id
             JOIN books b ON b.id = oi.book_id
             WHERE o.status NOT IN ('cancelled') AND DATE(o.created_at) BETWEEN ? AND ?
             GROUP BY oi.book_id, b.title
             ORDER BY qty DESC, revenue DESC
             LIMIT ?",
            [$from, $to, $limit]
        );
    }

    public function topCustomers(string $from, string $to, int $limit = 5): array
    {
        return $this->fetchAll(
            "SELECT u.id, u.name, u.email, COUNT(o.id) AS orders, COALESCE(SUM(o.total), 0) AS total
             FROM users u
             JOIN orders o ON o.user_id = u.id
             WHERE o.status NOT IN ('cancelled') AND DATE(o.created_at) BETWEEN ? AND ?
             GROUP BY u.id, u.name, u.email
             ORDER BY total DESC
             LIMIT ?",
            [$from, $to, $limit]
        );
    }

    public function stats(): array
    {
        $revenue = (float) ($this->fetchOne(
            "SELECT COALESCE(SUM(total), 0) AS t FROM orders WHERE status NOT IN ('cancelled')"
        )['t'] ?? 0);

        $count = (int) ($this->fetchOne('SELECT COUNT(*) AS c FROM orders')['c'] ?? 0);

        $byStatus = [];
        foreach ($this->fetchAll('SELECT status, COUNT(*) AS c FROM orders GROUP BY status') as $row) {
            $byStatus[$row['status']] = (int) $row['c'];
        }

        $recent = $this->fetchAll(
            'SELECT o.*, u.name AS user_name
             FROM orders o
             JOIN users u ON u.id = o.user_id
             ORDER BY o.id DESC
             LIMIT 5'
        );

        return [
            'revenue'  => $revenue,
            'count'    => $count,
            'byStatus' => $byStatus,
            'recent'   => $recent,
        ];
    }
}