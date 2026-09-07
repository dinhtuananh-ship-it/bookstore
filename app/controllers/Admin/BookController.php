<?php
declare(strict_types=1);

namespace Admin;

use Controller;
use BookModel;
use CategoryModel;

class BookController extends Controller
{
    private BookModel $bookModel;

    public function __construct()
    {
        $this->bookModel = new BookModel();
    }

    public function beforeAction(string $action): void
    {
        requireAdmin();
    }

    public function index(): void
    {
        setLayout('layout/admin');

        $filters = [
            'keyword'     => trim((string) ($_GET['keyword'] ?? '')),
            'category_id' => (int) ($_GET['category_id'] ?? 0),
            'status'      => ($_GET['status'] ?? '') !== '' ? (string) $_GET['status'] : '',
        ];

        $result = $this->bookModel->getAllAdmin($filters, (int) ($_GET['page'] ?? 1));

        $this->view('admin/books', [
            'pageTitle'  => 'Quản lý sách',
            'books'      => $result['items'],
            'count'      => $result['count'],
            'page'       => $result['page'],
            'pages'      => $result['pages'],
            'filters'    => $filters,
            'categories' => (new CategoryModel())->getAll(),
        ]);
    }

    public function create(): void
    {
        setLayout('layout/admin');

        if ($this->isPost()) {
            csrfCheck();
            $data = $this->validatedBookData();
            $cover = $this->resolveCover();

            if (isset($cover['error'])) {
                sessionFlash('error', $cover['error']);
                redirect('/admin/sach/them');
            }

            $data['cover_image'] = $cover['cover'];
            $this->bookModel->create($data);
            sessionFlash('success', 'Thêm sách thành công.');
            redirect('/admin/sach');
        }

        $this->view('admin/book_form', [
            'pageTitle'  => 'Thêm sách mới',
            'book'       => null,
            'categories' => (new CategoryModel())->getAll(),
        ]);
    }

    public function update(): void
    {
        setLayout('layout/admin');

        $id = (int) ($_GET['id'] ?? 0);
        $book = $this->bookModel->findRaw($id);
        if ($book === null) {
            sessionFlash('error', 'Sách không tồn tại.');
            redirect('/admin/sach');
        }

        if ($this->isPost()) {
            csrfCheck();
            $data = $this->validatedBookData($id);
            $cover = $this->resolveCover($book['cover_image'] ?? null);

            if (isset($cover['error'])) {
                sessionFlash('error', $cover['error']);
                redirect('/admin/sach/sua?id=' . $id);
            }

            $data['cover_image'] = $cover['cover'];

            $this->bookModel->update($id, $data);
            sessionFlash('success', 'Cập nhật sách thành công.');
            redirect('/admin/sach');
        }

        $this->view('admin/book_form', [
            'pageTitle'  => 'Sửa sách: ' . ($book['title'] ?? ''),
            'book'       => $book,
            'categories' => (new CategoryModel())->getAll(),
        ]);
    }

    public function delete(): void
    {
        csrfCheck();

        $id = (int) ($_POST['id'] ?? 0);
        $book = $this->bookModel->findRaw($id);

        if ($book === null) {
            sessionFlash('error', 'Sách không tồn tại.');
        } elseif ($this->bookModel->hasOrderItems($id)) {
            sessionFlash('error', 'Không thể xóa sách đã có trong đơn hàng. Bạn có thể ẩn sách này khỏi trang công khai.');
        } else {
            $cover = (string) ($book['cover_image'] ?? '');
            $this->bookModel->delete($id);
            if ($cover !== '' && !preg_match('#^https?://#i', $cover)) {
                $file = APP_ROOT . '/public/' . ltrim($cover, '/');
                if (is_file($file)) {
                    @unlink($file);
                }
            }
            sessionFlash('success', 'Đã xóa sách khỏi hệ thống.');
        }
        redirect('/admin/sach');
    }

    public function deletePermanently(): void
    {
        csrfCheck();

        $id = (int) ($_POST['id'] ?? 0);
        $book = $this->bookModel->findRaw($id);

        if ($book === null) {
            sessionFlash('error', 'Sách không tồn tại.');
        } elseif ($this->bookModel->hasOrderItems($id)) {
            sessionFlash('error', 'Không thể xóa sách đã có trong đơn hàng. Bạn có thể ẩn sách này khỏi trang công khai.');
        } else {
            $cover = (string) ($book['cover_image'] ?? '');
            $this->bookModel->delete($id);
            if ($cover !== '' && !preg_match('#^https?://#i', $cover)) {
                $file = APP_ROOT . '/public/' . ltrim($cover, '/');
                if (is_file($file)) {
                    @unlink($file);
                }
            }
            sessionFlash('success', 'Đã xóa sách khỏi hệ thống.');
        }
        redirect('/admin/sach');
    }

    public function export(): void
    {
        csrfCheck((string) ($_GET['csrf_token'] ?? ''));

        $books = $this->bookModel->getAllAdmin([], 1, PHP_INT_MAX)['items'];

        header('Content-Type: text/csv; charset=UTF-8');
        header('Content-Disposition: attachment; filename="books.csv"');
        echo "\xEF\xBB\xBF";

        $out = fopen('php://output', 'w');
        fputcsv($out, ['ID', 'Tên sách', 'Tác giả', 'NXB', 'ISBN', 'Danh mục', 'Giá', 'Giá KM', 'Tồn kho', 'Số tập', 'Đã bán', 'Trạng thái']);
        foreach ($books as $b) {
            fputcsv($out, [
                $b['id'],
                $b['title'],
                $b['author'],
                $b['publisher'],
                $b['isbn'],
                $b['category_name'],
                $b['price'],
                $b['sale_price'],
                $b['stock'],
                (int) $b['volumes'],
                $b['sold_count'],
                (int) $b['status'] === 1 ? 'hiển thị' : 'ẩn',
            ]);
        }
        fclose($out);
        exit;
    }

    private function resolveCover(?string $current = null): array
    {
        if (!empty($_FILES['cover_image']['name'])) {
            $cover = uploadImage('cover_image', 'uploads/books');
            if ($cover === 'INVALID_TYPE' || $cover === 'INVALID_SIZE') {
                return ['error' => 'Ảnh bìa không hợp lệ (chỉ chấp nhận jpg/png/webp/gif, tối đa 2MB).'];
            }
            return ['cover' => $cover];
        }

        $url = trim((string) ($_POST['cover_url'] ?? ''));
        if ($url !== '') {
            if (!preg_match('#^https?://#i', $url) || filter_var($url, FILTER_VALIDATE_URL) === false) {
                return ['error' => 'URL ảnh bìa không hợp lệ.'];
            }
            return ['cover' => $url];
        }

        return ['cover' => $current];
    }

    private function validatedBookData(?int $excludeId = null): array
    {
        $errors = [];

        $title = trim((string) ($_POST['title'] ?? ''));
        $categoryId = (int) ($_POST['category_id'] ?? 0);
        $price = (float) ($_POST['price'] ?? 0);
        $salePriceRaw = trim((string) ($_POST['sale_price'] ?? ''));
        $salePrice = $salePriceRaw === '' ? null : (float) $salePriceRaw;
        $stock = (int) ($_POST['stock'] ?? 0);
        $isbn = trim((string) ($_POST['isbn'] ?? ''));

        if ($title === '') {
            $errors['title'] = 'Tên sách không được để trống.';
        }
        if ($categoryId <= 0) {
            $errors['category_id'] = 'Vui lòng chọn danh mục.';
        }
        if ($price <= 0) {
            $errors['price'] = 'Giá phải lớn hơn 0.';
        }
        if ($salePrice !== null && $salePrice >= $price) {
            $errors['sale_price'] = 'Giá khuyến mãi phải nhỏ hơn giá bán.';
        }
        if ($stock < 0) {
            $errors['stock'] = 'Tồn kho không được âm.';
        }
        if ($isbn !== '' && $this->bookModel->findByIsbn($isbn, $excludeId)) {
            $errors['isbn'] = 'ISBN đã tồn tại.';
        }

        if ($errors !== []) {
            sessionFlash('error', 'Vui lòng kiểm tra lại thông tin: ' . implode(' ', $errors));
            redirect('/admin/sach' . ($excludeId !== null ? '/sua?id=' . $excludeId : '/them'));
        }

        return [
            'title'       => $title,
            'category_id' => $categoryId,
            'author'      => trim((string) ($_POST['author'] ?? '')) ?: null,
            'publisher'   => trim((string) ($_POST['publisher'] ?? '')) ?: null,
            'isbn'        => $isbn !== '' ? $isbn : null,
            'price'       => $price,
            'sale_price'  => $salePrice,
            'stock'       => $stock,
            'volumes'     => max(0, (int) ($_POST['volumes'] ?? 0)),
            'description' => trim((string) ($_POST['description'] ?? '')) ?: null,
            'status'      => (int) ($_POST['status'] ?? 0),
        ];
    }
}