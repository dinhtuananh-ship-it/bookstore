<?php
declare(strict_types=1);

namespace Admin;

use Controller;
use CouponModel;

class CouponController extends Controller
{
    private CouponModel $couponModel;

    public function __construct()
    {
        $this->couponModel = new CouponModel();
    }

    public function beforeAction(string $action): void
    {
        requireAdmin();
    }

    public function index(): void
    {
        setLayout('layout/admin');

        $this->view('admin/coupons', [
            'pageTitle' => 'Quản lý khuyến mãi',
            'coupons'   => $this->couponModel->getAll(),
        ]);
    }

    public function create(): void
    {
        csrfCheck();

        $data = $this->validatedData();
        $this->couponModel->create($data);
        sessionFlash('success', 'Tạo mã khuyến mãi thành công.');
        redirect('/admin/khuyen-mai');
    }

    public function update(): void
    {
        csrfCheck();

        $id = (int) ($_POST['id'] ?? 0);
        $data = $this->validatedData($id);
        $this->couponModel->update($id, $data);
        sessionFlash('success', 'Cập nhật mã khuyến mãi thành công.');
        redirect('/admin/khuyen-mai');
    }

    public function delete(): void
    {
        csrfCheck();

        $id = (int) ($_POST['id'] ?? 0);
        $this->couponModel->delete($id);
        sessionFlash('success', 'Đã xóa mã khuyến mãi.');
        redirect('/admin/khuyen-mai');
    }

    private function validatedData(?int $excludeId = null): array
    {
        $code = strtoupper(trim((string) ($_POST['code'] ?? '')));
        $type = (string) ($_POST['type'] ?? 'percent');
        $value = (float) ($_POST['value'] ?? 0);
        $minOrder = (float) ($_POST['min_order'] ?? 0);
        $maxUses = (int) ($_POST['max_uses'] ?? 0);
        $startDateRaw = trim((string) ($_POST['start_date'] ?? ''));
        $endDateRaw = trim((string) ($_POST['end_date'] ?? ''));

        if ($code === '' || !preg_match('/^[A-Z0-9_-]{3,30}$/', $code)) {
            sessionFlash('error', 'Mã khuyến mãi không hợp lệ (3-30 ký tự, chỉ chữ in hoa/số/_-).');
            redirect('/admin/khuyen-mai');
        }

        if ($this->couponModel->codeExists($code, $excludeId)) {
            sessionFlash('error', "Mã \"{$code}\" đã tồn tại.");
            redirect('/admin/khuyen-mai');
        }

        if (!in_array($type, ['percent', 'fixed'], true)) {
            $type = 'percent';
        }
        if ($value <= 0) {
            sessionFlash('error', 'Giá trị khuyến mãi phải lớn hơn 0.');
            redirect('/admin/khuyen-mai');
        }
        if ($type === 'percent' && $value > 100) {
            sessionFlash('error', 'Phần trăm giảm tối đa là 100%.');
            redirect('/admin/khuyen-mai');
        }

        return [
            'code'       => $code,
            'type'       => $type,
            'value'      => $value,
            'min_order'  => $minOrder > 0 ? $minOrder : 0,
            'max_uses'   => $maxUses > 0 ? $maxUses : 0,
            'start_date' => $startDateRaw !== '' ? $startDateRaw : null,
            'end_date'   => $endDateRaw !== '' ? $endDateRaw : null,
            'status'     => (int) ($_POST['status'] ?? 0),
        ];
    }
}