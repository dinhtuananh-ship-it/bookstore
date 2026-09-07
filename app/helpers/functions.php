<?php
declare(strict_types=1);

function e(mixed $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

function redirect(string $path): void
{
    header('Location: ' . url($path));
    exit;
}

function url(string $path = ''): string
{
    if ($path !== '' && $path[0] !== '/') {
        $path = '/' . $path;
    }
    return BASE_URL . $path;
}

function asset(string $path): string
{
    return url('/public/' . ltrim($path, '/'));
}

function baseController(): string
{
    return $_SESSION['base_controller'] ?? 'site';
}

function setBaseController(string $controller): void
{
    $_SESSION['base_controller'] = $controller;
}

function render(string $view, array $data = []): void
{
    extract($data, EXTR_SKIP);
    $viewFile = APP_PATH . '/views/' . $view . '.php';

    if (!is_file($viewFile)) {
        throw new RuntimeException("View not found: {$view}");
    }

    $layout = (string) ($GLOBALS['__layout'] ?? 'layout/main');
    $layoutFile = APP_PATH . '/views/' . $layout . '.php';

    $GLOBALS['__view_data'] = $data;

    if ($layout === '' || !is_file($layoutFile)) {
        unset($GLOBALS['__view_data']);
        require $viewFile;
        return;
    }

    $GLOBALS['__view_file'] = $viewFile;
    require $layoutFile;
    unset($GLOBALS['__view_data']);
}

function setLayout(string $layout): void
{
    $GLOBALS['__layout'] = $layout;
}

function layoutContent(): void
{
    if (!empty($GLOBALS['__view_data'])) {
        extract($GLOBALS['__view_data'], EXTR_SKIP);
    }
    $viewFile = $GLOBALS['__view_file'] ?? null;
    if ($viewFile && is_file($viewFile)) {
        require $viewFile;
    }
}

function sessionSet(string $key, mixed $value): void
{
    $_SESSION[$key] = $value;
}

function sessionGet(string $key, mixed $default = null): mixed
{
    return $_SESSION[$key] ?? $default;
}

function currentUser(): ?array
{
    return sessionGet('user');
}

function requireLogin(): void
{
    if (!currentUser()) {
        sessionFlash('error', 'Vui lòng đăng nhập để tiếp tục.');
        redirect('/dang-nhap');
    }
}

function requireAdmin(): void
{
    $user = currentUser();
    if (!$user) {
        redirect('/admin/dang-nhap');
    }
    if (($user['role'] ?? '') !== 'admin') {
        http_response_code(403);
        die('403 - Bạn không có quyền truy cập khu vực quản trị');
    }
}

function sessionFlash(string $key, ?string $message = null): ?string
{
    if ($message !== null) {
        $_SESSION['flash'][$key] = $message;
        return null;
    }

    $value = $_SESSION['flash'][$key] ?? null;
    unset($_SESSION['flash'][$key]);
    return $value;
}

function isPost(): bool
{
    return $_SERVER['REQUEST_METHOD'] === 'POST';
}

function csrfToken(): string
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function csrfField(): string
{
    return '<input type="hidden" name="csrf_token" value="' . csrfToken() . '">';
}

function csrfCheck(?string $token = null): void
{
    if ($token === null) {
        $token = (string) ($_POST['csrf_token'] ?? '');
    }
    if (!hash_equals(csrfToken(), $token)) {
        header('HTTP/1.1 419 Page Expired', true, 419);
        die('Phiên làm việc đã hết hạn. Vui lòng quay lại và thử lại.');
    }
}

function validateCsrf(): bool
{
    $token = $_POST['csrf_token'] ?? '';
    return !empty($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], (string) $token);
}

function bookPrice(array $book): float
{
    return (float) ($book['sale_price'] !== null && (float) $book['sale_price'] > 0
        ? $book['sale_price']
        : $book['price']);
}

function formatPrice(float $price): string
{
    return number_format($price, 0, ',', '.') . ' ₫';
}

function coverUrl(?string $cover): string
{
    $cover = trim((string) $cover);
    if ($cover === '') {
        return '';
    }
    if (preg_match('#^https?://#i', $cover)) {
        return $cover;
    }
    return asset($cover);
}

function bookCover(array $book): string
{
    return coverUrl($book['cover_image'] ?? null);
}

function cartItems(): array
{
    $cart = (array) sessionGet('cart', []);
    $items = [];
    foreach ($cart as $bookId => $qty) {
        $items[] = ['book_id' => (int) $bookId, 'qty' => (int) $qty];
    }
    return $items;
}

function cartCount(): int
{
    return array_sum(sessionGet('cart', []));
}

function orderStatusLabel(string $status): string
{
    return match ($status) {
        'pending'    => 'Chờ thanh toán',
        'paid'       => 'Đã thanh toán',
        'processing' => 'Đang xử lý',
        'shipping'   => 'Đang giao',
        'completed'  => 'Đã giao',
        'cancelled'  => 'Đã hủy',
        default      => $status,
    };
}

function orderStatusClass(string $status): string
{
    return match ($status) {
        'pending'    => 'st-pending',
        'paid'       => 'st-paid',
        'processing' => 'st-processing',
        'shipping'   => 'st-shipping',
        'completed'  => 'st-completed',
        'cancelled'  => 'st-cancelled',
        default      => '',
    };
}

function canCancelOrder(array $order): bool
{
    return in_array($order['status'], ['pending', 'paid', 'processing'], true);
}

function stars(int $rating): string
{
    $html = '<span class="stars">';
    for ($i = 1; $i <= 5; $i++) {
        $html .= $i <= $rating ? '★' : '☆';
    }
    $html .= '</span>';
    return $html;
}

function slugify(string $text): string
{
    $text = normalizeSearchKey($text);
    $text = preg_replace('/[^a-z0-9]+/', '-', $text);
    return trim((string) $text, '-');
}

function normalizeSearchKey(string $text): string
{
    $map = [
        'à'=>'a','á'=>'a','ả'=>'a','ã'=>'a','ạ'=>'a','ă'=>'a','ằ'=>'a','ắ'=>'a','ẳ'=>'a','ẵ'=>'a','ặ'=>'a','â'=>'a','ầ'=>'a','ấ'=>'a','ẩ'=>'a','ẫ'=>'a','ậ'=>'a',
        'đ'=>'d','è'=>'e','é'=>'e','ẻ'=>'e','ẽ'=>'e','ẹ'=>'e','ê'=>'e','ề'=>'e','ế'=>'e','ể'=>'e','ễ'=>'e','ệ'=>'e',
        'ì'=>'i','í'=>'i','ỉ'=>'i','ĩ'=>'i','ị'=>'i','ò'=>'o','ó'=>'o','ỏ'=>'o','õ'=>'o','ọ'=>'o','ô'=>'o','ồ'=>'o','ố'=>'o','ổ'=>'o','ỗ'=>'o','ộ'=>'o','ơ'=>'o','ờ'=>'o','ớ'=>'o','ở'=>'o','ỡ'=>'o','ợ'=>'o',
        'ù'=>'u','ú'=>'u','ủ'=>'u','ũ'=>'u','ụ'=>'u','ư'=>'u','ừ'=>'u','ứ'=>'u','ử'=>'u','ữ'=>'u','ự'=>'u',
        'ỳ'=>'y','ý'=>'y','ỷ'=>'y','ỹ'=>'y','ỵ'=>'y',
        'À'=>'a','Á'=>'a','Ả'=>'a','Ã'=>'a','Ạ'=>'a','Ă'=>'a','Ằ'=>'a','Ắ'=>'a','Ẳ'=>'a','Ẵ'=>'a','Ặ'=>'a','Â'=>'a','Ầ'=>'a','Ấ'=>'a','Ẩ'=>'a','Ẫ'=>'a','Ậ'=>'a',
        'Đ'=>'d','È'=>'e','É'=>'e','Ẻ'=>'e','Ẽ'=>'e','Ẹ'=>'e','Ê'=>'e','Ề'=>'e','Ế'=>'e','Ể'=>'e','Ễ'=>'e','Ệ'=>'e',
        'Ì'=>'i','Í'=>'i','Ỉ'=>'i','Ĩ'=>'i','Ị'=>'i','Ò'=>'o','Ó'=>'o','Ỏ'=>'o','Õ'=>'o','Ọ'=>'o','Ô'=>'o','Ồ'=>'o','Ố'=>'o','Ổ'=>'o','Ỗ'=>'o','Ộ'=>'o','Ơ'=>'o','Ờ'=>'o','Ớ'=>'o','Ở'=>'o','Ỡ'=>'o','Ợ'=>'o',
        'Ù'=>'u','Ú'=>'u','Ủ'=>'u','Ũ'=>'u','Ụ'=>'u','Ư'=>'u','Ừ'=>'u','Ứ'=>'u','Ử'=>'u','Ữ'=>'u','Ự'=>'u',
        'Ỳ'=>'y','Ý'=>'y','Ỷ'=>'y','Ỹ'=>'y','Ỵ'=>'y',
    ];

    $text = strtr($text, $map);
    $text = strtolower($text);
    $text = preg_replace('/\s+/', ' ', $text);
    return trim((string) $text);
}

function uploadImage(string $field, string $dir, int $maxBytes = 2 * 1024 * 1024): ?string
{
    if (empty($_FILES[$field]['name'])) {
        return null;
    }

    $file = $_FILES[$field];
    if ($file['error'] !== UPLOAD_ERR_OK) {
        return null;
    }

    $allowed = [
        'image/jpeg' => 'jpg',
        'image/png'  => 'png',
        'image/webp' => 'webp',
        'image/gif'  => 'gif',
    ];

    $mime = mime_content_type((string) $file['tmp_name']) ?: $file['type'];
    if (!isset($allowed[$mime])) {
        return 'INVALID_TYPE';
    }
    if ((int) $file['size'] > $maxBytes) {
        return 'INVALID_SIZE';
    }

    $fullDir = APP_ROOT . '/public/' . trim($dir, '/');
    if (!is_dir($fullDir)) {
        mkdir($fullDir, 0777, true);
    }

    $name = bin2hex(random_bytes(8)) . '.' . $allowed[$mime];
    $fullPath = $fullDir . '/' . $name;
    if (!move_uploaded_file((string) $file['tmp_name'], $fullPath)) {
        return null;
    }

    compressImage($fullPath, $mime, 900);

    return trim($dir, '/') . '/' . $name;
}

function compressImage(string $path, string $mime, int $maxWidth = 900): void
{
    if (!extension_loaded('gd')) {
        return;
    }

    $info = @getimagesize($path);
    if ($info === false) {
        return;
    }

    $src = match ($info[2]) {
        IMAGETYPE_JPEG => @imagecreatefromjpeg($path),
        IMAGETYPE_PNG  => @imagecreatefrompng($path),
        IMAGETYPE_WEBP => @imagecreatefromwebp($path),
        IMAGETYPE_GIF  => @imagecreatefromgif($path),
        default        => false,
    };

    if (!$src) {
        return;
    }

    $w = imagesx($src);
    $h = imagesy($src);

    if ($w > $maxWidth) {
        $nh = (int) round($h * $maxWidth / $w);
        $dst = imagecreatetruecolor($maxWidth, $nh);
        imagecopyresampled($dst, $src, 0, 0, 0, 0, $maxWidth, $nh, $w, $h);
        imagedestroy($src);
        $src = $dst;
    }

    switch ($info[2]) {
        case IMAGETYPE_JPEG:
            imagejpeg($src, $path, 82);
            break;
        case IMAGETYPE_PNG:
            imagepng($src, $path, 6);
            break;
        case IMAGETYPE_WEBP:
            imagewebp($src, $path, 82);
            break;
        case IMAGETYPE_GIF:
            imagegif($src, $path);
            break;
    }

    imagedestroy($src);
}

function sendMail(string $to, string $subject, string $body): bool
{
    try {
        $headers = "MIME-Version: 1.0\r\n";
        $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
        $headers .= "From: " . APP_NAME . " <no-reply@localhost>\r\n";
        return mail($to, '=?UTF-8?B?' . base64_encode($subject) . '?=', $body, $headers);
    } catch (Throwable $e) {
        error_log('Mail error: ' . $e->getMessage());
        return false;
    }
}

function paginationLinks(int $page, int $pages, array $query = [], string $base = ''): string
{
    if ($pages <= 1) {
        return '';
    }

    $buildUrl = function (int $p) use ($query, $base) {
        $q = $query;
        $q['page'] = $p;
        return url($base) . '?' . http_build_query($q);
    };

    $html = '<nav class="pagination">';
    if ($page > 1) {
        $html .= '<a href="' . e($buildUrl($page - 1)) . '">‹ Trước</a>';
    }
    for ($i = 1; $i <= $pages; $i++) {
        $cls = $i === $page ? ' class="active"' : '';
        $html .= '<a' . $cls . ' href="' . e($buildUrl($i)) . '">' . $i . '</a>';
    }
    if ($page < $pages) {
        $html .= '<a href="' . e($buildUrl($page + 1)) . '">Sau ›</a>';
    }
    $html .= '</nav>';

    return $html;
}
