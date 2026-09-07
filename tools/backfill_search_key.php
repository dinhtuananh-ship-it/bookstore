<?php
declare(strict_types=1);

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';

$pdo = Database::getConnection();
$rows = $pdo->query('SELECT id, title, author, publisher, isbn FROM books')->fetchAll(PDO::FETCH_ASSOC);

$stmt = $pdo->prepare('UPDATE books SET search_key = ? WHERE id = ?');
$updated = 0;
foreach ($rows as $row) {
    $key = normalizeSearchKey(
        ($row['title'] ?? '') . ' ' . ($row['author'] ?? '') . ' ' . ($row['publisher'] ?? '') . ' ' . ($row['isbn'] ?? '')
    );
    $stmt->execute([$key, (int) $row['id']]);
    $updated++;
}

echo "Backfill xong: {$updated} sach\n";