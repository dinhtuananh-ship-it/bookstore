<?php
declare(strict_types=1);

class ReviewController extends Controller
{
    private ReviewModel $reviewModel;
    private BookModel $bookModel;

    public function __construct()
    {
        $this->reviewModel = new ReviewModel();
        $this->bookModel = new BookModel();
    }

    public function beforeAction(string $action): void
    {
        requireLogin();
    }

    public function store(): void
    {
        if (!$this->isPost()) {
            redirect('/');
        }
        csrfCheck();

        $user = currentUser();
        $bookId = (int) ($_POST['book_id'] ?? 0);
        $rating = (int) ($_POST['rating'] ?? 0);
        $comment = trim((string) ($_POST['comment'] ?? ''));
        $book = $bookId > 0 ? $this->bookModel->findById($bookId) : null;

        if (!$book) {
            sessionFlash('error', 'Sách không tồn tại.');
            redirect('/sach');
        }

        $back = '/sach/chi-tiet?id=' . $bookId;

        if ($rating < 1 || $rating > 5) {
            sessionFlash('error', 'Vui lòng chọn số sao đánh giá (1-5).');
            redirect($back);
        }
        if ($comment === '' || mb_strlen($comment) < 3) {
            sessionFlash('error', 'Nội dung bình luận quá ngắn.');
            redirect($back);
        }
        if (!$this->reviewModel->hasPurchased((int) $user['id'], $bookId)) {
            sessionFlash('error', 'Bạn chỉ có thể đánh giá sách đã mua và nhận hàng.');
            redirect($back);
        }
        if ($this->reviewModel->getByUserAndBook((int) $user['id'], $bookId)) {
            sessionFlash('error', 'Bạn đã đánh giá sách này rồi.');
            redirect($back);
        }

        $this->reviewModel->create(
            (int) $user['id'],
            $bookId,
            $rating,
            $comment,
            AUTO_APPROVE_REVIEWS ? 1 : 0
        );

        sessionFlash('success', 'Cảm ơn bạn đã đánh giá sản phẩm!');
        redirect($back);
    }

    public function update(): void
    {
        if (!$this->isPost()) {
            redirect('/');
        }
        csrfCheck();

        $user = currentUser();
        $id = (int) ($_POST['id'] ?? 0);
        $rating = (int) ($_POST['rating'] ?? 0);
        $comment = trim((string) ($_POST['comment'] ?? ''));
        $review = $id > 0 ? $this->reviewModel->findById($id) : null;

        if (!$review || (int) $review['user_id'] !== (int) $user['id']) {
            sessionFlash('error', 'Đánh giá không tồn tại.');
            redirect('/sach');
        }

        if ($rating < 1 || $rating > 5 || $comment === '') {
            sessionFlash('error', 'Dữ liệu đánh giá không hợp lệ.');
            redirect('/sach/chi-tiet?id=' . (int) $review['book_id'] . '&edit=1');
        }

        $this->reviewModel->update($id, $rating, $comment);
        sessionFlash('success', 'Đã cập nhật đánh giá.');
        redirect('/sach/chi-tiet?id=' . (int) $review['book_id']);
    }

    public function delete(): void
    {
        if (!$this->isPost()) {
            redirect('/');
        }
        csrfCheck();

        $user = currentUser();
        $id = (int) ($_POST['id'] ?? 0);
        $review = $id > 0 ? $this->reviewModel->findById($id) : null;

        if ($review && (int) $review['user_id'] === (int) $user['id']) {
            $this->reviewModel->delete($id, (int) $user['id']);
            sessionFlash('success', 'Đã xóa đánh giá.');
            redirect('/sach/chi-tiet?id=' . (int) $review['book_id']);
        }

        sessionFlash('error', 'Đánh giá không tồn tại.');
        redirect('/sach');
    }
}