<?php
declare(strict_types=1);

class CategoryModel extends Model
{
    private static ?array $cache = null;

    private function cached(): array
    {
        if (self::$cache === null) {
            self::$cache = $this->fetchAll(
                'SELECT * FROM categories WHERE status = 1 ORDER BY id'
            );
        }
        return self::$cache;
    }

    public static function flushCache(): void
    {
        self::$cache = null;
    }

    public function getAll(): array
    {
        return $this->cached();
    }

    public function findBySlug(string $slug): ?array
    {
        foreach ($this->cached() as $cat) {
            if ($cat['slug'] === $slug) {
                return $cat;
            }
        }
        return null;
    }

    public function findById(int $id): ?array
    {
        foreach ($this->cached() as $cat) {
            if ((int) $cat['id'] === $id) {
                return $cat;
            }
        }
        return null;
    }

    public function getTopLevel(): array
    {
        return array_values(array_filter(
            $this->cached(),
            fn($cat) => ($cat['parent_id'] ?? null) === null
        ));
    }

    public function getChildren(int $parentId): array
    {
        return array_values(array_filter(
            $this->cached(),
            fn($cat) => (int) ($cat['parent_id'] ?? 0) === $parentId
        ));
    }

    public function getAllAdmin(): array
    {
        return $this->fetchAll(
            'SELECT c.*, COUNT(b.id) AS book_count
             FROM categories c
             LEFT JOIN books b ON b.category_id = c.id
             GROUP BY c.id
             ORDER BY c.parent_id, c.id'
        );
    }

    public function slugExists(string $slug, ?int $excludeId = null): bool
    {
        if ($excludeId) {
            $row = $this->fetchOne('SELECT id FROM categories WHERE slug = ? AND id != ?', [$slug, $excludeId]);
        } else {
            $row = $this->fetchOne('SELECT id FROM categories WHERE slug = ?', [$slug]);
        }
        return $row !== null;
    }

    public function countChildren(int $id): int
    {
        return (int) ($this->fetchOne('SELECT COUNT(*) AS c FROM categories WHERE parent_id = ?', [$id])['c'] ?? 0);
    }

    public function countBooks(int $id): int
    {
        return (int) ($this->fetchOne('SELECT COUNT(*) AS c FROM books WHERE category_id = ?', [$id])['c'] ?? 0);
    }

    public function create(array $data): int
    {
        $parentId = (int) ($data['parent_id'] ?? 0) > 0 ? (int) $data['parent_id'] : null;
        $this->query(
            'INSERT INTO categories (parent_id, name, slug, status) VALUES (?, ?, ?, ?)',
            [$parentId, $data['name'], $data['slug'], $data['status']]
        );
        self::flushCache();
        return $this->lastInsertId();
    }

    public function update(int $id, array $data): void
    {
        $parentId = (int) ($data['parent_id'] ?? 0) > 0 ? (int) $data['parent_id'] : null;
        $this->query(
            'UPDATE categories SET parent_id = ?, name = ?, slug = ?, status = ? WHERE id = ?',
            [$parentId, $data['name'], $data['slug'], $data['status'], $id]
        );
        self::flushCache();
    }

    public function delete(int $id): void
    {
        $this->query('DELETE FROM categories WHERE id = ?', [$id]);
        self::flushCache();
    }
}