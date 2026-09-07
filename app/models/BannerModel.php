<?php
declare(strict_types=1);

class BannerModel extends Model
{
    public function getAll(): array
    {
        return $this->fetchAll('SELECT * FROM banners ORDER BY sort_order ASC, id ASC');
    }

    public function getActive(): array
    {
        return $this->fetchAll(
            'SELECT * FROM banners WHERE status = 1 ORDER BY sort_order ASC, id ASC'
        );
    }

    public function findById(int $id): ?array
    {
        return $this->fetchOne('SELECT * FROM banners WHERE id = ?', [$id]);
    }

    public function create(array $data): int
    {
        $this->query(
            'INSERT INTO banners (title, subtitle, image, link, sort_order, status) VALUES (?, ?, ?, ?, ?, ?)',
            [
                $data['title'],
                $data['subtitle'] ?? null,
                $data['image'] ?? null,
                $data['link'] ?? null,
                (int) $data['sort_order'],
                (int) $data['status'],
            ]
        );
        return $this->lastInsertId();
    }

    public function update(int $id, array $data): void
    {
        $this->query(
            'UPDATE banners SET title = ?, subtitle = ?, image = ?, link = ?, sort_order = ?, status = ? WHERE id = ?',
            [
                $data['title'],
                $data['subtitle'] ?? null,
                $data['image'] ?? null,
                $data['link'] ?? null,
                (int) $data['sort_order'],
                (int) $data['status'],
                $id,
            ]
        );
    }

    public function delete(int $id): void
    {
        $this->query('DELETE FROM banners WHERE id = ?', [$id]);
    }
}