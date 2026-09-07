<?php
declare(strict_types=1);

class CartController extends Controller
{
    private CartModel $cartModel;
    private BookModel $bookModel;

    public function __construct()
    {
        $this->cartModel = new CartModel();
        $this->bookModel = new BookModel();
    }

    public function index(): void
    {
        $rows = $this->cartModel->details();
        $subtotal = $this->cartModel->subtotal();

        $this->view('site/cart', [
            'pageTitle' => 'Giỏ hàng',
            'rows'      => $rows,
            'subtotal'  => $subtotal,
        ]);
    }

    public function add(): void
    {
        if (!$this->isPost()) {
            redirect('/');
        }
        csrfCheck();

        $bookId = (int) ($_POST['book_id'] ?? 0);
        $quantity = max(1, (int) ($_POST['quantity'] ?? 1));
        $book = $bookId > 0 ? $this->bookModel->findById($bookId) : null;

        if (!$book) {
            sessionFlash('error', 'Sách không tồn tại.');
            redirect('/sach');
        }

        if ((int) $book['stock'] <= 0) {
            sessionFlash('error', 'Sách này đang hết hàng.');
            redirect('/sach/chi-tiet?id=' . $bookId);
        }

        $volumes = max(1, (int) ($book['volumes'] ?? 0));
        $volume = max(1, (int) ($_POST['volume'] ?? 1));
        if ($volume > $volumes) {
            $volume = $volumes;
        }

        $this->cartModel->add($bookId, min($quantity, (int) $book['stock']), $volume);

        $label = $volumes > 1 ? ' "' . $book['title'] . ' - Tập ' . $volume . '"' : ' "' . $book['title'] . '"';
        sessionFlash('success', 'Đã thêm ' . $label . ' vào giỏ hàng.');

        $referer = (string) ($_SERVER['HTTP_REFERER'] ?? '');
        if (strpos($referer, '/sach/chi-tiet') !== false) {
            redirect('/sach/chi-tiet?id=' . $bookId);
        }
        redirect('/gio-hang');
    }

    public function update(): void
    {
        if (!$this->isPost()) {
            redirect('/gio-hang');
        }
        csrfCheck();

        $bookId = (int) ($_POST['book_id'] ?? 0);
        $volume = max(1, (int) ($_POST['volume'] ?? 1));
        $quantity = (int) ($_POST['quantity'] ?? 0);
        $key = $bookId . ':' . $volume;

        if ($bookId > 0) {
            $book = $this->bookModel->findById($bookId);
            if ($book && $quantity > (int) $book['stock']) {
                $quantity = (int) $book['stock'];
                sessionFlash('error', 'Số lượng vượt quá tồn kho, đã giới hạn về ' . $quantity . '.');
            }
            $this->cartModel->update($key, $quantity);
        }

        redirect('/gio-hang');
    }

    public function remove(): void
    {
        if (!$this->isPost()) {
            redirect('/gio-hang');
        }
        csrfCheck();

        $bookId = (int) ($_POST['book_id'] ?? 0);
        $volume = max(1, (int) ($_POST['volume'] ?? 1));
        if ($bookId > 0) {
            $this->cartModel->remove($bookId . ':' . $volume);
        }

        redirect('/gio-hang');
    }
}