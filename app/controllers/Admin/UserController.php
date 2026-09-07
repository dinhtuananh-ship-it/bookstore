<?php
declare(strict_types=1);

namespace Admin;

use Controller;
use UserModel;
use OrderModel;

class UserController extends Controller
{
    private UserModel $userModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
    }

    public function beforeAction(string $action): void
    {
        requireAdmin();
    }

    public function index(): void
    {
        setLayout('layout/admin');

        $filters = [
            'keyword' => trim((string) ($_GET['keyword'] ?? '')),
            'status'  => ($_GET['status'] ?? '') !== '' ? (string) $_GET['status'] : '',
        ];

        $result = $this->userModel->getAllAdmin($filters, (int) ($_GET['page'] ?? 1));

        $this->view('admin/users', [
            'pageTitle' => 'Quản lý khách hàng',
            'users'     => $result['items'],
            'count'     => $result['count'],
            'page'      => $result['page'],
            'pages'     => $result['pages'],
            'filters'   => $filters,
        ]);
    }

    public function detail(): void
    {
        setLayout('layout/admin');

        $id = (int) ($_GET['id'] ?? 0);
        $user = $this->userModel->findById($id);
        if ($user === null) {
            sessionFlash('error', 'Khách hàng không tồn tại.');
            redirect('/admin/khach-hang');
        }

        $orders = (new OrderModel())->getByUser($id, 1, 50);
        $stats = $this->userModel->getStats($id);

        $this->view('admin/user_detail', [
            'pageTitle' => 'Khách hàng: ' . $user['name'],
            'user'      => $user,
            'orders'    => $orders['items'],
            'stats'     => $stats,
        ]);
    }

    public function toggle(): void
    {
        csrfCheck();

        $id = (int) ($_POST['id'] ?? 0);
        $user = $this->userModel->findById($id);
        if ($user === null) {
            sessionFlash('error', 'Khách hàng không tồn tại.');
            redirect('/admin/khach-hang');
        }

        if ((int) $user['id'] === (int) (currentUser()['id'] ?? 0)) {
            sessionFlash('error', 'Không thể khóa tài khoản của chính mình.');
            redirect('/admin/khach-hang');
        }

        $this->userModel->toggleStatus($id);
        $message = (int) $user['status'] === 1
            ? 'Đã khóa tài khoản ' . $user['email'] . '.'
            : 'Đã mở khóa tài khoản ' . $user['email'] . '.';
        sessionFlash('success', $message);
        redirect('/admin/khach-hang');
    }
}