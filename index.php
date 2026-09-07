<?php
declare(strict_types=1);

header('X-Frame-Options: DENY');
header('X-Content-Type-Options: nosniff');
header('Referrer-Policy: strict-origin-when-cross-origin');

require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/config/database.php';

function renderErrorPage(string $view, int $status): void
{
    http_response_code($status);
    $viewFile = APP_PATH . '/views/errors/' . $view . '.php';
    if (is_file($viewFile)) {
        require $viewFile;
    } else {
        exit('Đã có lỗi xảy ra. Vui lòng thử lại sau.');
    }
    exit;
}

try {
    $router = new Router($_SERVER['REQUEST_URI']);
    $router->dispatch();
} catch (PDOException $e) {
    writeLog('db_error', $e->getMessage() . ' in ' . $e->getFile() . ':' . $e->getLine());
    renderErrorPage('500', 500);
} catch (Throwable $e) {
    writeLog('fatal', get_class($e) . ': ' . $e->getMessage() . ' in ' . $e->getFile() . ':' . $e->getLine());
    renderErrorPage('500', 500);
}