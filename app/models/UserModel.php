<?php
declare(strict_types=1);

class UserModel extends Model
{
    public function findByEmail(string $email): ?array
    {
        return $this->fetchOne('SELECT * FROM users WHERE email = ?', [$email]);
    }

    public function findById(int $id): ?array
    {
        return $this->fetchOne('SELECT * FROM users WHERE id = ?', [$id]);
    }

    public function emailExists(string $email): bool
    {
        return $this->fetchOne('SELECT id FROM users WHERE email = ?', [$email]) !== null;
    }

    public function create(array $data): int
    {
        $this->query(
            'INSERT INTO users (name, email, phone, password, role) VALUES (?, ?, ?, ?, ?)',
            [$data['name'], $data['email'], $data['phone'] ?? null, $data['password'], 'member']
        );
        return $this->lastInsertId();
    }

    public function updateProfile(int $id, array $data): void
    {
        $this->query(
            'UPDATE users SET name = ?, phone = ?, avatar = ? WHERE id = ?',
            [$data['name'], $data['phone'] ?? null, $data['avatar'] ?? null, $id]
        );
    }

    public function updatePassword(int $id, string $hash): void
    {
        $this->query('UPDATE users SET password = ? WHERE id = ?', [$hash, $id]);
    }

    public function countMembers(): int
    {
        return (int) ($this->fetchOne(
            'SELECT COUNT(*) AS c FROM users WHERE role = "member"'
        )['c'] ?? 0);
    }

    public function getAllAdmin(array $filters = [], int $page = 1, int $perPage = ITEMS_PER_PAGE): array
    {
        $where = ['u.role = "member"'];
        $params = [];

        $keyword = trim((string) ($filters['keyword'] ?? ''));
        if ($keyword !== '') {
            $where[] = '(u.name LIKE ? OR u.email LIKE ? OR u.phone LIKE ?)';
            $like = '%' . $keyword . '%';
            array_push($params, $like, $like, $like);
        }

        if (isset($filters['status']) && $filters['status'] !== '') {
            $where[] = 'u.status = ?';
            $params[] = (int) $filters['status'];
        }

        $whereSql = 'WHERE ' . implode(' AND ', $where);

        $count = (int) ($this->fetchOne(
            "SELECT COUNT(*) AS c FROM users u {$whereSql}",
            $params
        )['c'] ?? 0);

        $pages = max(1, (int) ceil($count / $perPage));
        $page = min(max(1, $page), $pages);
        $offset = ($page - 1) * $perPage;

        $items = $this->fetchAll(
            "SELECT u.id, u.name, u.email, u.phone, u.status, u.created_at,
                    (SELECT COUNT(*) FROM orders o WHERE o.user_id = u.id AND o.status NOT IN ('cancelled')) AS order_count,
                    (SELECT COALESCE(SUM(o.total), 0) FROM orders o WHERE o.user_id = u.id AND o.status NOT IN ('cancelled')) AS total_spent
             FROM users u
             {$whereSql}
             ORDER BY u.id DESC
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

    public function toggleStatus(int $id): void
    {
        $this->query('UPDATE users SET status = 1 - status WHERE id = ?', [$id]);
    }

    public function getStats(int $userId): array
    {
        $row = $this->fetchOne(
            "SELECT COUNT(*) AS c, COALESCE(SUM(total), 0) AS t, MAX(created_at) AS last_order
             FROM orders
             WHERE user_id = ? AND status NOT IN ('cancelled')",
            [$userId]
        );
        return [
            'order_count' => (int) ($row['c'] ?? 0),
            'total_spent' => (float) ($row['t'] ?? 0),
            'last_order'  => $row['last_order'] ?? null,
        ];
    }
}