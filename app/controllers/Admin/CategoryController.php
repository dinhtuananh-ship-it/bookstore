<?php
declare(strict_types=1);

namespace Admin;

use Controller;
use CategoryModel;

class CategoryController extends Controller
{
    private CategoryModel $categoryModel;

    public function __construct()
    {
        $this->categoryModel = new CategoryModel();
    }

    public function beforeAction(string $action): void
    {
        requireAdmin();
    }

    public function index(): void
    {
        setLayout('layout/admin');

        $categories = $this->categoryModel->getAllAdmin();
        $tree = [];

        foreach ($categories as $cat) {
            if ((int) $cat['parent_id'] === 0) {
                $cat['children'] = [];
                $tree[$cat['id']] = $cat;
            }
        }
        foreach ($categories as $cat) {
            if ((int) $cat['parent_id'] !== 0 && isset($tree[$cat['parent_id']])) {
                $tree[$cat['parent_id']]['children'][] = $cat;
            }
        }

        $this->view('admin/categories', [
            'pageTitle'  => 'Quản lý danh mục',
            'tree'       => $tree,
            'categories' => $categories,
        ]);
    }

    public function create(): void
    {
        csrfCheck();

        $data = $this->validatedData();
        $this->categoryModel->create($data);
        sessionFlash('success', 'Thêm danh mục thành công.');
        redirect('/admin/danh-muc');
    }

    public function update(): void
    {
        csrfCheck();

        $id = (int) ($_POST['id'] ?? 0);
        $category = $this->categoryModel->findById($id);
        if ($category === null) {
            sessionFlash('error', 'Danh mục không tồn tại.');
            redirect('/admin/danh-muc');
        }

        $data = $this->validatedData($id);
        $this->categoryModel->update($id, $data);
        sessionFlash('success', 'Cập nhật danh mục thành công.');
        redirect('/admin/danh-muc');
    }

    public function delete(): void
    {
        csrfCheck();

        $id = (int) ($_POST['id'] ?? 0);
        $category = $this->categoryModel->findById($id);
        if ($category === null) {
            sessionFlash('error', 'Danh mục không tồn tại.');
            redirect('/admin/danh-muc');
        }

        $childCount = $this->categoryModel->countChildren($id);
        $bookCount = $this->categoryModel->countBooks($id);

        if ($childCount > 0 || $bookCount > 0) {
            sessionFlash(
                'error',
                "Không thể xóa danh mục này: còn {$childCount} danh mục con và {$bookCount} sách thuộc danh mục."
            );
            redirect('/admin/danh-muc');
        }

        $this->categoryModel->delete($id);
        sessionFlash('success', 'Đã xóa danh mục.');
        redirect('/admin/danh-muc');
    }

    private function validatedData(?int $excludeId = null): array
    {
        $name = trim((string) ($_POST['name'] ?? ''));
        $slug = trim((string) ($_POST['slug'] ?? ''));
        $parentId = (int) ($_POST['parent_id'] ?? 0);

        if ($name === '') {
            sessionFlash('error', 'Tên danh mục không được để trống.');
            redirect('/admin/danh-muc');
        }

        if ($slug === '') {
            $slug = slugify($name);
        }

        if ($this->categoryModel->slugExists($slug, $excludeId)) {
            sessionFlash('error', "Slug \"{$slug}\" đã tồn tại, vui lòng dùng slug khác.");
            redirect('/admin/danh-muc');
        }

        return [
            'name'      => $name,
            'slug'      => $slug,
            'parent_id' => $parentId > 0 ? $parentId : 0,
            'status'    => (int) ($_POST['status'] ?? 0),
        ];
    }
}