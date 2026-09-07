<?php
declare(strict_types=1);

class CartModel
{
    private const KEY = 'cart';
    private BookModel $bookModel;

    public function __construct()
    {
        $this->bookModel = new BookModel();
    }

    public function items(): array
    {
        return sessionGet(self::KEY, []);
    }

    public function count(): int
    {
        return array_sum($this->items());
    }

    public function add(int $bookId, int $quantity = 1, int $volume = 1): void
    {
        $items = $this->items();
        $key = $bookId . ':' . max(1, $volume);
        $items[$key] = min((int) ($items[$key] ?? 0) + $quantity, 99);
        sessionSet(self::KEY, $items);
    }

    public function update(string $key, int $quantity): void
    {
        $items = $this->items();
        if ($quantity <= 0) {
            unset($items[$key]);
        } else {
            $items[$key] = min($quantity, 99);
        }
        sessionSet(self::KEY, $items);
    }

    public function remove(string $key): void
    {
        $items = $this->items();
        unset($items[$key]);
        sessionSet(self::KEY, $items);
    }

    public function clear(): void
    {
        sessionSet(self::KEY, []);
    }

    public function details(): array
    {
        $rows = [];
        foreach ($this->items() as $key => $quantity) {
            [$bookId, $volume] = array_pad(explode(':', (string) $key, 2), 2, '1');
            $book = $this->bookModel->findById((int) $bookId);
            if (!$book || (int) $book['status'] !== 1) {
                continue;
            }
            $price = bookPrice($book);
            $rows[] = [
                'book'       => $book,
                'key'        => (string) $key,
                'volume'     => max(1, (int) $volume),
                'quantity'   => (int) $quantity,
                'price'      => $price,
                'line_total' => $price * (int) $quantity,
            ];
        }
        return $rows;
    }

    public function subtotal(): float
    {
        $total = 0.0;
        foreach ($this->details() as $row) {
            $total += $row['line_total'];
        }
        return $total;
    }
}