<?php
declare(strict_types=1);

class BookController extends Controller
{
    private BookModel $bookModel;

    public function __construct()
    {
        $this->bookModel = new BookModel();
    }

    public function search(): void
    {
        $page = max(1, (int) ($_GET['page'] ?? 1));

        $filters = [
            'keyword'    => trim((string) ($_GET['keyword'] ?? '')),
            'author'     => trim((string) ($_GET['author'] ?? '')),
            'publisher'  => trim((string) ($_GET['publisher'] ?? '')),
            'price_min'  => (float) ($_GET['price_min'] ?? 0),
            'price_max'  => (float) ($_GET['price_max'] ?? 0),
            'sort'       => (string) ($_GET['sort'] ?? 'newest'),
        ];

        $result = $this->bookModel->search($filters, $page);

        $this->view('site/books', [
            'pageTitle'  => $filters['keyword'] !== '' ? 'Kết quả tìm kiếm: ' . $filters['keyword'] : 'Tất cả sách',
            'books'      => $result['items'],
            'count'      => $result['count'],
            'page'       => $result['page'],
            'pages'      => $result['pages'],
            'filters'    => $filters,
            'authors'    => $this->bookModel->getAuthors(),
            'publishers' => $this->bookModel->getPublishers(),
        ]);
    }

    public function detail(): void
    {
        $id = (int) ($_GET['id'] ?? 0);
        $book = $id > 0 ? $this->bookModel->findById($id) : null;

        if (!$book) {
            http_response_code(404);
            $this->view('site/404', ['pageTitle' => 'Không tìm thấy sách']);
            return;
        }

        $reviewModel = new ReviewModel();
        $user = currentUser();
        $userId = (int) ($user['id'] ?? 0);

        $reviewPage = max(1, (int) ($_GET['page'] ?? 1));
        $reviews = $reviewModel->getForBook($id, $reviewPage);
        $userReview = $userId > 0 ? $reviewModel->getByUserAndBook($userId, $id) : null;

        $this->view('site/book_detail', [
            'pageTitle'  => $book['title'],
            'book'       => $book,
            'related'    => $this->bookModel->getRelated((int) $book['id'], (int) $book['category_id'], 4),
            'reviewSummary' => $reviewModel->getSummary($id),
            'reviews'    => $reviews['items'],
            'reviewPage' => $reviews['page'],
            'reviewPages'=> $reviews['pages'],
            'reviewCount'=> $reviews['count'],
            'userReview' => $userReview,
            'canReview'  => $userId > 0
                && !$userReview
                && $reviewModel->hasPurchased($userId, $id),
            'editingReview' => ($_GET['edit'] ?? '') === '1' && $userReview,
        ]);
    }

    public function byAuthor(): void
    {
        $author = trim((string) ($_GET['author'] ?? ''));
        $page = max(1, (int) ($_GET['page'] ?? 1));

        $filters = ['author' => $author, 'sort' => (string) ($_GET['sort'] ?? 'newest')];
        $result = $this->bookModel->search($filters, $page);

        $this->view('site/books', [
            'pageTitle'  => 'Sách của tác giả: ' . ($author !== '' ? $author : '(trống)'),
            'books'      => $result['items'],
            'count'      => $result['count'],
            'page'       => $result['page'],
            'pages'      => $result['pages'],
            'filters'    => $filters,
            'authors'    => $this->bookModel->getAuthors(),
            'publishers' => $this->bookModel->getPublishers(),
        ]);
    }

    public function byPublisher(): void
    {
        $publisher = trim((string) ($_GET['publisher'] ?? ''));
        $page = max(1, (int) ($_GET['page'] ?? 1));

        $filters = ['publisher' => $publisher, 'sort' => (string) ($_GET['sort'] ?? 'newest')];
        $result = $this->bookModel->search($filters, $page);

        $this->view('site/books', [
            'pageTitle'  => 'Sách của NXB: ' . ($publisher !== '' ? $publisher : '(trống)'),
            'books'      => $result['items'],
            'count'      => $result['count'],
            'page'       => $result['page'],
            'pages'      => $result['pages'],
            'filters'    => $filters,
            'authors'    => $this->bookModel->getAuthors(),
            'publishers' => $this->bookModel->getPublishers(),
        ]);
    }
}