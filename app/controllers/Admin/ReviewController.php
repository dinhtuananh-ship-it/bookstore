<?php
declare(strict_types=1);

namespace Admin;

use Controller;
use ReviewModel;

class ReviewController extends Controller
{
    private ReviewModel $reviewModel;

    public function __construct()
    {
        $this->reviewModel = new ReviewModel();
    }

    public function beforeAction(string $action): void
    {
        requireAdmin();
    }

    public function index(): void
    {
        setLayout('layout/admin');

        $filters = [
            'status'  => ($_GET['status'] ?? '') !== '' ? (string) $_GET['status'] : '',
            'keyword' => trim((string) ($_GET['keyword'] ?? '')),
        ];

        $result = $this->reviewModel->getAllAdmin($filters, (int) ($_GET['page'] ?? 1));

        $this->view('admin/reviews', [
            'pageTitle' => 'Quản lý bình luận',
            'reviews'   => $result['items'],
            'count'     => $result['count'],
            'page'      => $result['page'],
            'pages'     => $result['pages'],
            'filters'   => $filters,
        ]);
    }

    public function toggle(): void
    {
        csrfCheck();

        $id = (int) ($_POST['id'] ?? 0);
        $review = $this->reviewModel->findById($id);
        if ($review === null) {
            sessionFlash('error', 'Bình luận không tồn tại.');
            redirect('/admin/binh-luan');
        }

        $newStatus = (int) $review['status'] === 1 ? 0 : 1;
        $this->reviewModel->setStatus($id, $newStatus);

        $message = $newStatus === 1
            ? 'Đã hiển thị bình luận.'
            : 'Đã ẩn bình luận khỏi trang công khai.';
        sessionFlash('success', $message);
        redirect('/admin/binh-luan');
    }

    public function delete(): void
    {
        csrfCheck();

        $id = (int) ($_POST['id'] ?? 0);
        $this->reviewModel->deleteById($id);
        sessionFlash('success', 'Đã xóa bình luận.');
        redirect('/admin/binh-luan');
    }
}