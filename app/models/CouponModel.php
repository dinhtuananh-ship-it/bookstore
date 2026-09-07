<?php
declare(strict_types=1);

class CouponModel extends Model
{
    public function findByCode(string $code): ?array
    {
        return $this->fetchOne(
            'SELECT * FROM coupons
             WHERE code = ? AND status = 1
               AND (start_date IS NULL OR start_date <= NOW())
               AND (end_date IS NULL OR end_date >= NOW())',
            [$code]
        );
    }

    public function isValid(?array $coupon, float $subtotal): bool
    {
        if (!$coupon) {
            return false;
        }
        if ($subtotal < (float) $coupon['min_order']) {
            return false;
        }
        if ((int) $coupon['used_count'] >= (int) $coupon['max_uses']) {
            return false;
        }
        return true;
    }

    public function calculateDiscount(?array $coupon, float $subtotal): float
    {
        if (!$coupon) {
            return 0.0;
        }
        $discount = $coupon['type'] === 'percent'
            ? $subtotal * (float) $coupon['value'] / 100
            : (float) $coupon['value'];
        return min($discount, $subtotal);
    }

    public function incrementUsed(int $couponId): void
    {
        $this->query('UPDATE coupons SET used_count = used_count + 1 WHERE id = ?', [$couponId]);
    }

    public function getAll(): array
    {
        return $this->fetchAll('SELECT * FROM coupons ORDER BY id DESC');
    }

    public function codeExists(string $code, ?int $excludeId = null): bool
    {
        if ($excludeId) {
            $row = $this->fetchOne('SELECT id FROM coupons WHERE code = ? AND id != ?', [$code, $excludeId]);
        } else {
            $row = $this->fetchOne('SELECT id FROM coupons WHERE code = ?', [$code]);
        }
        return $row !== null;
    }

    public function create(array $data): int
    {
        $this->query(
            'INSERT INTO coupons (code, type, value, min_order, max_uses, used_count, start_date, end_date, status)
             VALUES (?, ?, ?, ?, ?, 0, ?, ?, ?)',
            [
                $data['code'],
                $data['type'],
                $data['value'],
                $data['min_order'],
                $data['max_uses'],
                $data['start_date'],
                $data['end_date'],
                $data['status'],
            ]
        );
        return $this->lastInsertId();
    }

    public function update(int $id, array $data): void
    {
        $this->query(
            'UPDATE coupons SET code = ?, type = ?, value = ?, min_order = ?, max_uses = ?,
             start_date = ?, end_date = ?, status = ? WHERE id = ?',
            [
                $data['code'],
                $data['type'],
                $data['value'],
                $data['min_order'],
                $data['max_uses'],
                $data['start_date'],
                $data['end_date'],
                $data['status'],
                $id,
            ]
        );
    }

    public function delete(int $id): void
    {
        $this->query('DELETE FROM coupons WHERE id = ?', [$id]);
    }
}