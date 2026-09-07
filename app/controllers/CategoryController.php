<?php
declare(strict_types=1);

class CategoryController extends Controller
{
    private BookModel $bookModel;
    private CategoryModel $categoryModel;

    public function __construct()
    {
        $this->bookModel = new BookModel();
        $this->categoryModel = new CategoryModel();
    }

    public function show(?string $slug = null): void
    {
        $slug = $slug ?: (string) ($_GET['slug'] ?? '');
        $category = $slug !== '' ? $this->categoryModel->findBySlug($slug) : null;

        if (!$category) {
            http_response_code(404);
            $this->view('site/404', ['pageTitle' => 'Không tìm thấy danh mục']);
            return;
        }

        $categoryIds = [(int) $category['id']];
        foreach ($this->categoryModel->getChildren((int) $category['id']) as $child) {
            $categoryIds[] = (int) $child['id'];
        }

        $page = max(1, (int) ($_GET['page'] ?? 1));
        $filters = [
            'category_ids' => $categoryIds,
            'sort'         => (string) ($_GET['sort'] ?? 'newest'),
        ];
        $result = $this->bookModel->search($filters, $page);

        $this->view('site/books', [
            'pageTitle'   => $category['name'],
            'books'       => $result['items'],
            'count'       => $result['count'],
            'page'        => $result['page'],
            'pages'       => $result['pages'],
            'filters'     => $filters,
            'category'    => $category,
            'authors'     => $this->bookModel->getAuthors(),
            'publishers'  => $this->bookModel->getPublishers(),
        ]);
    }
}