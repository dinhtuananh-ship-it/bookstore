<?php
declare(strict_types=1);

namespace Admin;

use Controller;
use OrderModel;

class OrderController extends Controller
{
    private const TRANSITIONS = [
        'pending'    => ['processing'],
        'processing' => ['shipping'],
        'shipping'   => ['completed'],
    ];

    private const CANCELLABLE = ['pending', 'processing', 'shipping'];

    private OrderModel $orderModel;

    public function __construct()
    {
        $this->orderModel = new OrderModel();
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
            'from'    => trim((string) ($_GET['from'] ?? '')),
            'to'      => trim((string) ($_GET['to'] ?? '')),
        ];

        $result = $this->orderModel->getAllAdmin($filters, (int) ($_GET['page'] ?? 1));

        $this->view('admin/orders', [
            'pageTitle' => 'Quản lý đơn hàng',
            'orders'    => $result['items'],
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
        $order = $this->orderModel->getById($id);
        if ($order === null) {
            sessionFlash('error', 'Đơn hàng không tồn tại.');
            redirect('/admin/don-hang');
        }

        $items = $this->orderModel->getItems($id);
        $history = $this->orderModel->getStatusHistory($id);

        $allowedStatuses = self::TRANSITIONS[$order['status']] ?? [];
        $canCancel = in_array($order['status'], self::CANCELLABLE, true);

        $this->view('admin/order_detail', [
            'pageTitle'       => 'Đơn hàng ' . $order['code'],
            'order'           => $order,
            'items'           => $items,
            'history'         => $history,
            'allowedStatuses' => $allowedStatuses,
            'canCancel'       => $canCancel,
        ]);
    }

    public function status(): void
    {
        csrfCheck();

        $id = (int) ($_POST['id'] ?? 0);
        $status = (string) ($_POST['status'] ?? '');

        $order = $this->orderModel->getById($id);
        if ($order === null) {
            sessionFlash('error', 'Đơn hàng không tồn tại.');
            redirect('/admin/don-hang');
        }

        $allowed = self::TRANSITIONS[$order['status']] ?? [];
        if (!in_array($status, $allowed, true)) {
            sessionFlash('error', 'Không thể chuyển trạng thái như vậy.');
            redirect('/admin/don-hang/chi-tiet?id=' . $id);
        }

        $this->orderModel->updateStatus($id, $status);
        $this->orderModel->recordStatus($id, $status, 'Cập nhật bởi quản trị viên', 'admin');

        sessionFlash('success', 'Đã cập nhật trạng thái đơn hàng.');
        redirect('/admin/don-hang/chi-tiet?id=' . $id);
    }

    public function cancel(): void
    {
        csrfCheck();

        $id = (int) ($_POST['id'] ?? 0);
        $order = $this->orderModel->getById($id);
        if ($order === null) {
            sessionFlash('error', 'Đơn hàng không tồn tại.');
            redirect('/admin/don-hang');
        }

        if (!in_array($order['status'], self::CANCELLABLE, true)) {
            sessionFlash('error', 'Đơn hàng này không thể hủy ở trạng thái hiện tại.');
            redirect('/admin/don-hang/chi-tiet?id=' . $id);
        }

        $note = trim((string) ($_POST['note'] ?? '')) ?: 'Quản trị viên hủy đơn';
        $this->orderModel->cancel($id, $note, 'admin');
        sessionFlash('success', 'Đã hủy đơn hàng và hoàn lại tồn kho.');
        redirect('/admin/don-hang/chi-tiet?id=' . $id);
    }
}