<?php
declare(strict_types=1);

define('APP_ROOT', dirname(__DIR__));
define('APP_PATH', APP_ROOT . '/app');
define('BASE_PATH', str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'])));

if (BASE_PATH === '/' ) {
    define('BASE_URL', '');
} else {
    define('BASE_URL', rtrim(BASE_PATH, '/'));
}

define('DB_HOST', 'localhost');
define('DB_PORT', '3306');
define('DB_NAME', 'bookstore');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_CHARSET', 'utf8mb4');

define('APP_NAME', 'Kim Đồng Bookstore');
define('APP_DEBUG', true);
define('ITEMS_PER_PAGE', 12);
define('SHIPPING_FEE', 30000);
define('FREE_SHIPPING_MIN', 500000);
define('AUTO_APPROVE_REVIEWS', true);
define('LOG_PATH', APP_ROOT . '/storage/logs/app.log');

if (!is_dir(dirname(LOG_PATH))) {
    mkdir(dirname(LOG_PATH), 0777, true);
}

session_set_cookie_params([
    'httponly' => true,
    'samesite' => 'Lax',
    'path'     => '/',
]);

session_start();

function writeLog(string $level, string $message): void
{
    $line = '[' . date('Y-m-d H:i:s') . '] ' . strtoupper($level) . ': ' . $message . PHP_EOL;
    @file_put_contents(LOG_PATH, $line, FILE_APPEND | LOCK_EX);
}

set_error_handler(function (int $errno, string $errstr, string $errfile, int $errline): bool {
    if (!(error_reporting() & $errno)) {
        return false;
    }
    writeLog('error', "{$errstr} in {$errfile}:{$errline}");
    return false;
});

set_exception_handler(function (Throwable $e): void {
    writeLog('exception', get_class($e) . ': ' . $e->getMessage() . ' in ' . $e->getFile() . ':' . $e->getLine());
});

spl_autoload_register(function (string $class): void {
    $class = ltrim($class, '\\');
    $relative = str_replace('\\', '/', $class);

    $candidates = [
        APP_PATH . '/core/' . $relative . '.php',
        APP_PATH . '/models/' . $relative . '.php',
        APP_PATH . '/controllers/' . $relative . '.php',
    ];

    foreach ($candidates as $file) {
        if (is_file($file)) {
            require_once $file;
            return;
        }
    }
});

require_once APP_PATH . '/helpers/functions.php';
