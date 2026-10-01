<?php
declare(strict_types=1);

namespace Admin;

use Controller;
use OrderModel;

class OrderController extends Controller
{
    /**
     * Các trạng thái admin được phép đặt cho đơn hàng.
     * pending/paid là trạng thái đầu vào (không cho chọn lại),
     * completed/cancelled là trạng thái kết thúc (không cho đổi tiếp).
     */
    private const EDITABLE_STATUSES = ['processing', 'shipping', 'completed', 'cancelled'];

    private const FINAL_STATUSES = ['completed', 'cancelled'];

    private const CANCELLABLE = ['pending', 'paid', 'processing', 'shipping'];

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

        // Đơn ở trạng thái kết thúc thì khóa cập nhật; còn lại cho chọn
        // tự do 1 trong 4 trạng thái: đang xử lý / đang giao / đã giao / đã hủy.
        $isFinal = in_array($order['status'], self::FINAL_STATUSES, true);
        $statusOptions = $isFinal ? [] : self::EDITABLE_STATUSES;

        $this->view('admin/order_detail', [
            'pageTitle'     => 'Đơn hàng ' . $order['code'],
            'order'         => $order,
            'items'         => $items,
            'history'       => $history,
            'statusOptions' => $statusOptions,
            'isFinal'       => $isFinal,
        ]);
    }

    public function status(): void
    {
        csrfCheck();

        $id = (int) ($_POST['id'] ?? 0);
        $status = (string) ($_POST['status'] ?? '');
        $note = trim((string) ($_POST['note'] ?? ''));

        $order = $this->orderModel->getById($id);
        if ($order === null) {
            sessionFlash('error', 'Đơn hàng không tồn tại.');
            redirect('/admin/don-hang');
        }

        if (in_array($order['status'], self::FINAL_STATUSES, true)) {
            sessionFlash('error', 'Đơn hàng đã kết thúc, không thể cập nhật nữa.');
            redirect('/admin/don-hang/chi-tiet?id=' . $id);
        }

        if (!in_array($status, self::EDITABLE_STATUSES, true)) {
            sessionFlash('error', 'Trạng thái không hợp lệ.');
            redirect('/admin/don-hang/chi-tiet?id=' . $id);
        }

        if ($status === $order['status']) {
            sessionFlash('error', 'Đơn hàng đang ở trạng thái này rồi.');
            redirect('/admin/don-hang/chi-tiet?id=' . $id);
        }

        // Hủy đơn: hoàn tồn kho qua cancel() thay vì update thường.
        if ($status === 'cancelled') {
            if (!in_array($order['status'], self::CANCELLABLE, true)) {
                sessionFlash('error', 'Đơn hàng này không thể hủy ở trạng thái hiện tại.');
                redirect('/admin/don-hang/chi-tiet?id=' . $id);
            }
            $this->orderModel->cancel($id, $note !== '' ? $note : 'Quản trị viên hủy đơn', 'admin');
            sessionFlash('success', 'Đã hủy đơn hàng và hoàn lại tồn kho.');
            redirect('/admin/don-hang/chi-tiet?id=' . $id);
        }

        $this->orderModel->updateStatus($id, $status);
        $this->orderModel->recordStatus(
            $id,
            $status,
            $note !== '' ? $note : 'Cập nhật bởi quản trị viên',
            'admin'
        );

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