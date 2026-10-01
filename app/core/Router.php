<?php
declare(strict_types=1);

class Router
{
    private const DEFAULT_CONTROLLER = 'SiteController';
    private const DEFAULT_ACTION = 'index';

    private const ROUTES = [
        ''                            => ['SiteController', 'index'],
        'dang-ky'                     => ['AuthController', 'register'],
        'dang-nhap'                   => ['AuthController', 'login'],
        'dang-xuat'                   => ['AuthController', 'logout'],
        'sach'                        => ['BookController', 'search'],
        'sach/chi-tiet'               => ['BookController', 'detail'],
        'sach/tac-gia'                => ['BookController', 'byAuthor'],
        'sach/nxb'                    => ['BookController', 'byPublisher'],
        'danh-muc'                    => ['CategoryController', 'show'],
        'gio-hang'                    => ['CartController', 'index'],
        'gio-hang/them'               => ['CartController', 'add'],
        'gio-hang/cap-nhat'           => ['CartController', 'update'],
        'gio-hang/xoa'                => ['CartController', 'remove'],
        'thanh-toan'                  => ['OrderController', 'checkout'],
        'thanh-toan/thanh-cong'       => ['OrderController', 'success'],
        'thanh-toan/online'           => ['OrderController', 'onlinePayment'],
        'don-hang'                    => ['OrderController', 'history'],
        'don-hang/chi-tiet'           => ['OrderController', 'detail'],
        'don-hang/huy'                => ['OrderController', 'cancel'],
        'ho-so'                       => ['ProfileController', 'index'],
        'ho-so/mat-khau'              => ['ProfileController', 'changePassword'],
        'dia-chi'                     => ['AddressController', 'index'],
        'dia-chi/them'                => ['AddressController', 'store'],
        'dia-chi/sua'                 => ['AddressController', 'update'],
        'dia-chi/xoa'                 => ['AddressController', 'delete'],
        'dia-chi/mac-dinh'            => ['AddressController', 'setDefault'],
        'danh-gia'                    => ['ReviewController', 'store'],
        'danh-gia/sua'                => ['ReviewController', 'update'],
        'danh-gia/xoa'                => ['ReviewController', 'delete'],
        'admin'                       => ['Admin\DashboardController', 'index'],
        'admin/dang-nhap'             => ['Admin\AuthController', 'login'],
        'admin/dang-xuat'             => ['Admin\AuthController', 'logout'],
        'admin/sach'                  => ['Admin\BookController', 'index'],
        'admin/sach/them'             => ['Admin\BookController', 'create'],
        'admin/sach/sua'              => ['Admin\BookController', 'update'],
        'admin/sach/xoa'              => ['Admin\BookController', 'delete'],
        'admin/sach/xoa-han'          => ['Admin\BookController', 'deletePermanently'],
        'admin/sach/xuat'             => ['Admin\BookController', 'export'],
        'admin/danh-muc'              => ['Admin\CategoryController', 'index'],
        'admin/danh-muc/them'         => ['Admin\CategoryController', 'create'],
        'admin/danh-muc/sua'          => ['Admin\CategoryController', 'update'],
        'admin/danh-muc/xoa'          => ['Admin\CategoryController', 'delete'],
        'admin/banner'                => ['Admin\BannerController', 'index'],
        'admin/banner/them'           => ['Admin\BannerController', 'create'],
        'admin/banner/sua'            => ['Admin\BannerController', 'update'],
        'admin/banner/xoa'            => ['Admin\BannerController', 'delete'],
        'admin/don-hang'              => ['Admin\OrderController', 'index'],
        'admin/don-hang/chi-tiet'     => ['Admin\OrderController', 'detail'],
        'admin/don-hang/trang-thai'   => ['Admin\OrderController', 'status'],
        'admin/don-hang/huy'          => ['Admin\OrderController', 'cancel'],
        'admin/khach-hang'            => ['Admin\UserController', 'index'],
        'admin/khach-hang/chi-tiet'   => ['Admin\UserController', 'detail'],
        'admin/khach-hang/khoa'       => ['Admin\UserController', 'toggle'],
        'admin/bao-cao'               => ['Admin\ReportController', 'index'],
        'admin/khuyen-mai'            => ['Admin\CouponController', 'index'],
        'admin/khuyen-mai/them'       => ['Admin\CouponController', 'create'],
        'admin/khuyen-mai/sua'        => ['Admin\CouponController', 'update'],
        'admin/khuyen-mai/xoa'        => ['Admin\CouponController', 'delete'],
        'admin/khuyen-mai/doi-trang-thai' => ['Admin\CouponController', 'toggle'],
        'admin/binh-luan'             => ['Admin\ReviewController', 'index'],
        'admin/binh-luan/duyet'       => ['Admin\ReviewController', 'toggle'],
        'admin/binh-luan/xoa'         => ['Admin\ReviewController', 'delete'],
    ];

    private string $controller;
    private string $action;
    private array $params = [];

    public function __construct(string $uri)
    {
        $this->parse($uri);
    }

    public function dispatch(): void
    {
        $controllerClass = $this->controller;
        $controllerFile = APP_PATH . '/controllers/' . $controllerClass . '.php';

        if (!is_file($controllerFile)) {
            $this->notFound("Controller not found: {$controllerClass}");
        }

        require_once $controllerFile;

        if (!class_exists($controllerClass)) {
            $this->notFound("Class not found: {$controllerClass}");
        }

        $controller = new $controllerClass();

        if (!method_exists($controller, $this->action)) {
            $this->notFound("Action not found: {$this->controller}@{$this->action}");
        }

        if (method_exists($controller, 'beforeAction')) {
            $controller->beforeAction($this->action);
        }

        call_user_func_array([$controller, $this->action], $this->params);
    }

    private function parse(string $uri): void
    {
        $path = parse_url($uri, PHP_URL_PATH) ?: '/';
        $path = str_replace(BASE_URL, '', $path);
        $path = trim($path, '/');

        if (isset(self::ROUTES[$path])) {
            [$this->controller, $this->action] = self::ROUTES[$path];
            setBaseController(strpos($this->controller, 'Admin\\') === 0 ? 'admin' : 'site');
            return;
        }

        $segments = array_values(array_filter(explode('/', $path), fn($s) => $s !== ''));

        $admin = false;
        if (($segments[0] ?? '') === 'admin') {
            $admin = true;
            array_shift($segments);
        }

        if ($segments === []) {
            $this->controller = $admin ? 'Admin\DashboardController' : self::DEFAULT_CONTROLLER;
            $this->action = self::DEFAULT_ACTION;
            setBaseController($admin ? 'admin' : 'site');
            return;
        }

        if (!$admin && ($segments[0] ?? '') === 'danh-muc') {
            $this->controller = 'CategoryController';
            $this->action = 'show';
            $this->params = array_slice($segments, 1);
            setBaseController('site');
            return;
        }

        $controllerName = str_replace(' ', '', ucwords(str_replace(['-', '_'], ' ', ucfirst($segments[0]))));
        $this->controller = ($admin ? 'Admin\\' : '') . $controllerName . 'Controller';
        $this->action = str_replace(['-', '_'], '', strtolower($segments[1] ?? self::DEFAULT_ACTION));
        $this->params = array_slice($segments, 2);
        setBaseController($admin ? 'admin' : 'site');
    }

    private function notFound(string $message): void
    {
        writeLog('404', $message . ' - URI: ' . ($_SERVER['REQUEST_URI'] ?? ''));
        http_response_code(404);
        $viewFile = APP_PATH . '/views/errors/404.php';
        if (is_file($viewFile)) {
            require $viewFile;
        } else {
            exit('404 - Trang không tồn tại');
        }
        exit;
    }
}