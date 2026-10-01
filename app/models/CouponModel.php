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
        if ((int) ($coupon['status'] ?? 0) !== 1) {
            return false;
        }
        if ($subtotal < (float) ($coupon['min_order'] ?? 0)) {
            return false;
        }
        // max_uses = 0 nghĩa là không giới hạn lượt dùng
        $maxUses = (int) ($coupon['max_uses'] ?? 0);
        if ($maxUses > 0 && (int) $coupon['used_count'] >= $maxUses) {
            return false;
        }
        return true;
    }

    // Cách tính dễ nhớ: nhập 50 nghĩa là giảm 50%, nhập 10 nghĩa là giảm 10%.
    // Ví dụ đơn 200.000đ áp mã 50% thì giảm 200.000 * 50 / 100 = 100.000đ.
    public function calculateDiscount(?array $coupon, float $subtotal): float
    {
        if (!$coupon) {
            return 0.0;
        }
        if (($coupon['type'] ?? 'percent') === 'percent') {
            $percent = max(0, min(100, (float) $coupon['value']));
            $discount = $subtotal * $percent / 100;
        } else {
            $discount = max(0, (float) $coupon['value']);
        }
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

    public function findById(int $id): ?array
    {
        return $this->fetchOne('SELECT * FROM coupons WHERE id = ?', [$id]);
    }

    public function setStatus(int $id, int $status): void
    {
        $this->query('UPDATE coupons SET status = ? WHERE id = ?', [$status === 1 ? 1 : 0, $id]);
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