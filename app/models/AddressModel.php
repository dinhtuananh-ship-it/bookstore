<?php
declare(strict_types=1);

class AddressModel extends Model
{
    public function getByUser(int $userId): array
    {
        return $this->fetchAll(
            'SELECT * FROM addresses WHERE user_id = ? ORDER BY is_default DESC, id DESC',
            [$userId]
        );
    }

    public function findById(int $id): ?array
    {
        return $this->fetchOne('SELECT * FROM addresses WHERE id = ?', [$id]);
    }

    public function create(array $data): int
    {
        $hasAny = $this->fetchOne('SELECT id FROM addresses WHERE user_id = ? LIMIT 1', [$data['user_id']]);

        $this->query(
            'INSERT INTO addresses (user_id, full_name, phone, province, district, ward, detail, is_default)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?)',
            [
                $data['user_id'],
                $data['full_name'],
                $data['phone'],
                $data['province'],
                $data['district'],
                $data['ward'],
                $data['detail'],
                $hasAny ? 0 : 1,
            ]
        );

        return $this->lastInsertId();
    }

    public function update(int $id, array $data): void
    {
        $this->query(
            'UPDATE addresses SET full_name = ?, phone = ?, province = ?, district = ?, ward = ?, detail = ? WHERE id = ?',
            [
                $data['full_name'],
                $data['phone'],
                $data['province'],
                $data['district'],
                $data['ward'],
                $data['detail'],
                $id,
            ]
        );
    }

    public function delete(int $id, int $userId): void
    {
        $this->query('DELETE FROM addresses WHERE id = ? AND user_id = ?', [$id, $userId]);
    }

    public function setDefault(int $id, int $userId): void
    {
        $this->query('UPDATE addresses SET is_default = 0 WHERE user_id = ?', [$userId]);
        $this->query('UPDATE addresses SET is_default = 1 WHERE id = ? AND user_id = ?', [$id, $userId]);
    }
}