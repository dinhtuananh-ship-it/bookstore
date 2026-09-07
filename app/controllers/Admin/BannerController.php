<?php
declare(strict_types=1);

namespace Admin;

use Controller;
use BannerModel;

class BannerController extends Controller
{
    private BannerModel $bannerModel;

    public function __construct()
    {
        $this->bannerModel = new BannerModel();
    }

    public function beforeAction(string $action): void
    {
        requireAdmin();
    }

    public function index(): void
    {
        setLayout('layout/admin');

        $this->view('admin/banners', [
            'pageTitle' => 'Quản lý banner',
            'banners'   => $this->bannerModel->getAll(),
        ]);
    }

    public function create(): void
    {
        if (!$this->isPost()) {
            redirect('/admin/banner');
        }
        csrfCheck();

        $data = $this->validatedBannerData();
        $image = $this->resolveImage();

        if (isset($image['error'])) {
            sessionFlash('error', $image['error']);
            redirect('/admin/banner');
        }

        $data['image'] = $image['image'];
        $this->bannerModel->create($data);
        sessionFlash('success', 'Thêm banner thành công.');
        redirect('/admin/banner');
    }

    public function update(): void
    {
        if (!$this->isPost()) {
            redirect('/admin/banner');
        }
        csrfCheck();

        $id = (int) ($_POST['id'] ?? 0);
        $banner = $this->bannerModel->findById($id);
        if ($banner === null) {
            sessionFlash('error', 'Banner không tồn tại.');
            redirect('/admin/banner');
        }

        $data = $this->validatedBannerData();
        $image = $this->resolveImage();

        if (isset($image['error'])) {
            sessionFlash('error', $image['error']);
            redirect('/admin/banner');
        }

        $data['image'] = $image['image'] ?? $banner['image'];
        $this->bannerModel->update($id, $data);
        sessionFlash('success', 'Đã lưu banner.');
        redirect('/admin/banner');
    }

    public function delete(): void
    {
        if (!$this->isPost()) {
            redirect('/admin/banner');
        }
        csrfCheck();

        $id = (int) ($_POST['id'] ?? 0);
        $banner = $this->bannerModel->findById($id);

        if ($banner === null) {
            sessionFlash('error', 'Banner không tồn tại.');
        } else {
            $this->bannerModel->delete($id);
            $image = (string) ($banner['image'] ?? '');
            if ($image !== '' && !preg_match('#^https?://#i', $image)) {
                $file = APP_ROOT . '/public/' . ltrim($image, '/');
                if (is_file($file)) {
                    @unlink($file);
                }
            }
            sessionFlash('success', 'Đã xóa banner.');
        }
        redirect('/admin/banner');
    }

    private function validatedBannerData(): array
    {
        $title = trim((string) ($_POST['title'] ?? ''));
        if ($title === '') {
            sessionFlash('error', 'Vui lòng nhập tiêu đề banner.');
            redirect('/admin/banner');
        }

        $link = trim((string) ($_POST['link'] ?? ''));
        if ($link !== '' && !preg_match('#^(/|https?://)#i', $link)) {
            $link = '/' . ltrim($link, '/');
        }

        return [
            'title'      => $title,
            'subtitle'   => trim((string) ($_POST['subtitle'] ?? '')) ?: null,
            'link'       => $link !== '' ? $link : null,
            'sort_order' => max(0, (int) ($_POST['sort_order'] ?? 0)),
            'status'     => (int) ($_POST['status'] ?? 0),
        ];
    }

    private function resolveImage(): array
    {
        if (!empty($_FILES['image']['name'])) {
            $uploaded = uploadImage('image', 'uploads/banners');
            if ($uploaded === 'INVALID_TYPE') {
                return ['error' => 'Ảnh banner phải là JPG, PNG, WEBP hoặc GIF.'];
            }
            if ($uploaded === 'INVALID_SIZE') {
                return ['error' => 'Ảnh banner quá lớn (tối đa 2MB).'];
            }
            if ($uploaded === null) {
                return ['error' => 'Không thể tải ảnh banner lên. Vui lòng thử lại.'];
            }
            return ['image' => $uploaded];
        }

        $urlImage = trim((string) ($_POST['image_url'] ?? ''));
        if ($urlImage !== '') {
            if (!preg_match('#^https?://#i', $urlImage)) {
                return ['error' => 'URL ảnh không hợp lệ (phải bắt đầu bằng http:// hoặc https://).'];
            }
            return ['image' => $urlImage];
        }

        return ['image' => null];
    }
}