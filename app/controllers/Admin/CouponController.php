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

        $keyword = trim((string) ($_GET['keyword'] ?? ''));
        $status = trim((string) ($_GET['status'] ?? ''));
        $coupons = $this->couponModel->getAll();

        // Lọc đơn giản ngay trên PHP để không phải sửa Model nhiều
        if ($keyword !== '') {
            $kw = mb_strtolower($keyword);
            $coupons = array_values(array_filter($coupons, function ($c) use ($kw) {
                return mb_strpos(mb_strtolower((string) ($c['code'] ?? '')), $kw) !== false;
            }));
        }
        if ($status !== '') {
            $coupons = array_values(array_filter($coupons, function ($c) use ($status) {
                $now = time();
                $start = !empty($c['start_date']) ? strtotime((string) $c['start_date']) : null;
                $end = !empty($c['end_date']) ? strtotime((string) $c['end_date']) : null;
                $maxUses = (int) ($c['max_uses'] ?? 0);
                $active = (int) ($c['status'] ?? 0) === 1
                    && ($start === null || $start === false || $start <= $now)
                    && ($end === null || $end === false || $end >= $now)
                    && ($maxUses === 0 || (int) $c['used_count'] < $maxUses);
                return ($status === '1' && $active) || ($status === '0' && !$active);
            }));
        }

        $this->view('admin/coupons', [
            'pageTitle' => 'Quản lý khuyến mãi',
            'coupons'   => $coupons,
            'keyword'   => $keyword,
            'statusFilter' => $status,
        ]);
    }

    // GET: hiện trang thêm riêng (dễ dùng hơn modal). POST: tạo mới.
    public function create(): void
    {
        setLayout('layout/admin');

        if (!$this->isPost()) {
            $this->view('admin/coupon_form', [
                'pageTitle' => 'Tạo mã khuyến mãi',
                'coupon'    => sessionFlash('coupon_old') ?? null,
                'errors'    => sessionFlash('coupon_errors') ?? [],
                'isEdit'    => false,
            ]);
            return;
        }

        csrfCheck();
        [$data, $errors] = $this->validatedData();
        if ($errors !== []) {
            sessionFlash('coupon_old', $_POST);
            sessionFlash('coupon_errors', $errors);
            redirect('/admin/khuyen-mai/them');
        }

        $this->couponModel->create($data);
        sessionFlash('success', 'Tạo mã khuyến mãi ' . $data['code'] . ' thành công.');
        redirect('/admin/khuyen-mai');
    }

    // GET /admin/khuyen-mai/sua?id= : hiện form. POST: cập nhật.
    public function update(): void
    {
        setLayout('layout/admin');

        $id = (int) ($_GET['id'] ?? $_POST['id'] ?? 0);
        $coupon = $id > 0 ? $this->couponModel->findById($id) : null;
        if ($coupon === null) {
            sessionFlash('error', 'Mã khuyến mãi không tồn tại.');
            redirect('/admin/khuyen-mai');
        }

        if (!$this->isPost()) {
            // Ưu tiên hiện dữ liệu cũ khi vừa validate lỗi
            $old = sessionFlash('coupon_old');
            $this->view('admin/coupon_form', [
                'pageTitle' => 'Sửa mã: ' . $coupon['code'],
                'coupon'    => is_array($old) ? array_merge($coupon, $old) : $coupon,
                'errors'    => sessionFlash('coupon_errors') ?? [],
                'isEdit'    => true,
            ]);
            return;
        }

        csrfCheck();
        [$data, $errors] = $this->validatedData($id);
        if ($errors !== []) {
            sessionFlash('coupon_old', $_POST);
            sessionFlash('coupon_errors', $errors);
            redirect('/admin/khuyen-mai/sua?id=' . $id);
        }

        $this->couponModel->update($id, $data);
        sessionFlash('success', 'Cập nhật mã ' . $data['code'] . ' thành công.');
        redirect('/admin/khuyen-mai');
    }

    public function delete(): void
    {
        csrfCheck();

        $id = (int) ($_POST['id'] ?? 0);
        $coupon = $id > 0 ? $this->couponModel->findById($id) : null;
        if ($coupon === null) {
            sessionFlash('error', 'Mã khuyến mãi không tồn tại.');
            redirect('/admin/khuyen-mai');
        }
        // Nếu mã đã được dùng trong đơn hàng thì khuyên tắt thay vì xóa để giữ lịch sử đối soát
        if ((int) ($coupon['used_count'] ?? 0) > 0) {
            $force = (string) ($_POST['force'] ?? '') === '1';
            if (!$force) {
                sessionFlash('error', 'Mã ' . $coupon['code'] . ' đã dùng ' . (int) $coupon['used_count'] . ' lần. Hãy Tắt thay vì Xóa để giữ lịch sử đơn hàng.');
                redirect('/admin/khuyen-mai');
            }
        }
        $this->couponModel->delete($id);
        sessionFlash('success', 'Đã xóa mã khuyến mãi ' . $coupon['code'] . '.');
        redirect('/admin/khuyen-mai');
    }

    // Bật/tắt nhanh 1 click bằng link GET, không cần form POST để tránh lỗi 419 hết phiên.
    public function toggle(): void
    {
        $id = (int) ($_GET['id'] ?? $_POST['id'] ?? 0);
        if ($id <= 0) {
            sessionFlash('error', 'Thiếu mã khuyến mãi cần bật tắt.');
            redirect('/admin/khuyen-mai');
        }
        $coupon = $this->couponModel->findById($id);
        if ($coupon === null) {
            sessionFlash('error', 'Mã khuyến mãi không tồn tại.');
            redirect('/admin/khuyen-mai');
        }
        $newStatus = (int) ($coupon['status'] ?? 0) === 1 ? 0 : 1;
        $this->couponModel->setStatus($id, $newStatus);
        sessionFlash('success', 'Đã ' . ($newStatus === 1 ? 'bật' : 'tắt') . ' mã ' . $coupon['code'] . '.');
        redirect('/admin/khuyen-mai');
    }

    /**
     * Chuẩn hóa datetime-local (2026-10-01T10:00) về MySQL (2026-10-01 10:00:00).
     * Trả về [data, errors]. Lỗi hiển thị lại trên form thay vì redirect trắng.
     */
    private function validatedData(?int $excludeId = null): array
    {
        $errors = [];
        $code = strtoupper(trim((string) ($_POST['code'] ?? '')));
        $type = (string) ($_POST['type'] ?? 'percent');
        $value = (float) ($_POST['value'] ?? 0);
        $minOrder = (float) ($_POST['min_order'] ?? 0);
        $maxUses = (int) ($_POST['max_uses'] ?? 0);

        if ($code === '' || !preg_match('/^[A-Z0-9_-]{3,30}$/', $code)) {
            $errors['code'] = 'Mã 3-30 ký tự, chỉ chữ in hoa, số, gạch dưới, gạch ngang. Ví dụ: SALE50.';
        } elseif ($this->couponModel->codeExists($code, $excludeId)) {
            $errors['code'] = 'Mã "' . $code . '" đã tồn tại, hãy đặt mã khác.';
        }

        if (!in_array($type, ['percent', 'fixed'], true)) {
            $type = 'percent';
        }
        if ($value <= 0) {
            $errors['value'] = 'Giá trị giảm phải lớn hơn 0.';
        } elseif ($type === 'percent' && $value > 100) {
            $errors['value'] = 'Giảm theo % tối đa là 100.';
        } elseif ($type === 'fixed' && $value < 1000) {
            $errors['value'] = 'Giảm tiền mặt nên từ 1.000đ trở lên cho dễ hiểu.';
        }
        if ($minOrder < 0) {
            $errors['min_order'] = 'Đơn tối thiểu không được âm.';
        }
        if ($maxUses < 0) {
            $errors['max_uses'] = 'Lượt dùng không được âm (0 = không giới hạn).';
        }

        $start = $this->normalizeDatetime((string) ($_POST['start_date'] ?? ''));
        $end = $this->normalizeDatetime((string) ($_POST['end_date'] ?? ''));
        if ($start === false) {
            $errors['start_date'] = 'Ngày bắt đầu không đúng định dạng.';
            $start = null;
        }
        if ($end === false) {
            $errors['end_date'] = 'Ngày kết thúc không đúng định dạng.';
            $end = null;
        }
        if ($start && $end && strtotime($end) < strtotime($start)) {
            $errors['end_date'] = 'Ngày kết thúc phải sau ngày bắt đầu.';
        }

        $data = [
            'code'       => $code,
            'type'       => $type,
            'value'      => $value,
            'min_order'  => $minOrder > 0 ? $minOrder : 0,
            'max_uses'   => $maxUses > 0 ? $maxUses : 0,
            'start_date' => $start,
            'end_date'   => $end,
            'status'     => (int) ($_POST['status'] ?? 1) === 1 ? 1 : 0,
        ];
        return [$data, $errors];
    }

    private function normalizeDatetime(string $raw): string|bool|null
    {
        $raw = trim($raw);
        if ($raw === '') {
            return null;
        }
        // datetime-local: 2026-10-01T10:00 hoặc 2026-10-01T10:00:00
        $raw = str_replace('T', ' ', $raw);
        if (preg_match('/^\d{4}-\d{2}-\d{2} \d{2}:\d{2}$/', $raw)) {
            $raw .= ':00';
        }
        $ts = strtotime($raw);
        if ($ts === false) {
            return false;
        }
        return date('Y-m-d H:i:s', $ts);
    }
}
