<?php
declare(strict_types=1);

class SiteController extends Controller
{
    private BookModel $bookModel;

    public function __construct()
    {
        $this->bookModel = new BookModel();
    }

    public function index(): void
    {
        $this->view('site/home', [
            'pageTitle'  => 'Trang chủ',
            'newBooks'   => $this->bookModel->getNewest(8),
            'hotBooks'   => $this->bookModel->getBestSelling(8),
            'banners'    => (new BannerModel())->getActive(),
        ]);
    }

    public function notFound(): void
    {
        http_response_code(404);
        $this->view('site/404', ['pageTitle' => 'Không tìm thấy trang']);
    }
}