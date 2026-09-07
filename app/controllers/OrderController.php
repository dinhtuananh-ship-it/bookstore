<?php
declare(strict_types=1);

class OrderController extends Controller
{
    private CartModel $cartModel;
    private OrderModel $orderModel;
    private AddressModel $addressModel;
    private CouponModel $couponModel;

    public function __construct()
    {
        $this->cartModel = new CartModel();
        $this->orderModel = new OrderModel();
        $this->addressModel = new AddressModel();
        $this->couponModel = new CouponModel();
    }

    public function beforeAction(string $action): void
    {
        if ($action !== 'success') {
            requireLogin();
        }
    }

    public function checkout(): void
    {
        $user = currentUser();
        $rows = $this->cartModel->details();

        if ($rows === []) {
            sessionFlash('error', 'Giỏ hàng của bạn đang trống.');
            redirect('/gio-hang');
        }

        $errors = [];
        $coupon = null;
        $discount = 0.0;
        $subtotal = $this->cartModel->subtotal();
        $couponCode = trim((string) ($_POST['coupon_code'] ?? ''));
        $appliedCode = trim((string) ($_GET['coupon'] ?? ''));

        if ($couponCode !== '') {
            $coupon = $this->couponModel->findByCode($couponCode);
            if (!$this->couponModel->isValid($coupon, $subtotal)) {
                $errors['coupon'] = 'Mã giảm giá không hợp lệ hoặc không đủ điều kiện.';
                $coupon = null;
            } else {
                $discount = $this->couponModel->calculateDiscount($coupon, $subtotal);
            }
        } elseif ($appliedCode !== '') {
            $coupon = $this->couponModel->findByCode($appliedCode);
            $discount = $this->couponModel->calculateDiscount($coupon, $subtotal);
        }

        $afterDiscount = $subtotal - $discount;
        $shippingFee = $afterDiscount >= FREE_SHIPPING_MIN ? 0.0 : (float) SHIPPING_FEE;
        $total = $afterDiscount + $shippingFee;

        if ($this->isPost()) {
            csrfCheck();

            $useNewAddress = (string) ($_POST['address_mode'] ?? '') === 'new';
            $addressId = null;

            if ($useNewAddress) {
                $name = trim((string) ($_POST['full_name'] ?? ''));
                $phone = trim((string) ($_POST['phone'] ?? ''));
                $province = trim((string) ($_POST['province'] ?? ''));
                $district = trim((string) ($_POST['district'] ?? ''));
                $ward = trim((string) ($_POST['ward'] ?? ''));
                $detail = trim((string) ($_POST['detail'] ?? ''));

                if (mb_strlen($name) < 2) $errors['address'] = 'Họ tên người nhận không hợp lệ.';
                if (!preg_match('/^0\d{9,10}$/', $phone)) $errors['address'] = 'Số điện thoại không hợp lệ.';
                if ($province === '' || $district === '' || $ward === '' || $detail === '') {
                    $errors['address'] = 'Vui lòng nhập đầy đủ địa chỉ giao hàng.';
                }

                if (empty($errors['address'])) {
                    $addressId = $this->addressModel->create([
                        'user_id'   => (int) $user['id'],
                        'full_name' => $name,
                        'phone'     => $phone,
                        'province'  => $province,
                        'district'  => $district,
                        'ward'      => $ward,
                        'detail'    => $detail,
                    ]);
                }
            } else {
                $addressId = (int) ($_POST['address_id'] ?? 0);
                $address = $this->addressModel->findById($addressId);
                if (!$address || (int) $address['user_id'] !== (int) $user['id']) {
                    $errors['address'] = 'Vui lòng chọn địa chỉ giao hàng.';
                    $addressId = null;
                }
            }

            if ($couponCode !== '' && $coupon === null) {
                $errors['coupon'] = 'Mã giảm giá không hợp lệ.';
            }

            foreach ($rows as $row) {
                if ((int) $row['quantity'] > (int) $row['book']['stock']) {
                    $errors['stock'] = 'Sách "' . $row['book']['title'] . '" không đủ hàng (còn '
                        . (int) $row['book']['stock'] . ').';
                }
            }

            if (empty($errors) && $addressId) {
                $paymentMethod = (string) ($_POST['payment_method'] ?? 'cod');
                if (!in_array($paymentMethod, ['cod', 'vnpay', 'momo'], true)) {
                    $paymentMethod = 'cod';
                }

                $items = array_map(
                    fn($row) => [
                        'book_id'  => (int) $row['book']['id'],
                        'volume'   => (int) $row['volume'],
                        'quantity' => (int) $row['quantity'],
                        'price'    => $row['price'],
                    ],
                    $rows
                );

                $orderId = $this->orderModel->create([
                    'user_id'       => (int) $user['id'],
                    'address_id'    => $addressId,
                    'coupon_id'     => $coupon ? (int) $coupon['id'] : null,
                    'subtotal'      => $subtotal,
                    'discount'      => $discount,
                    'shipping_fee'  => $shippingFee,
                    'total'         => $total,
                    'payment_method'=> $paymentMethod,
                    'status'        => $paymentMethod === 'cod' ? 'processing' : 'pending',
                    'note'          => trim((string) ($_POST['note'] ?? '')),
                    'items'         => $items,
                ]);

                $this->cartModel->clear();

                $order = $this->orderModel->getById($orderId);
                $orderItems = $this->orderModel->getItems($orderId);
                $order['items'] = $orderItems;

                $rowsHtml = '<table border="1" cellpadding="8" cellspacing="0" style="border-collapse:collapse;width:100%">'
                    . '<tr><th>Sách</th><th>Số lượng</th><th>Thành tiền</th></tr>';
                foreach ($orderItems as $oi) {
                    $name = $oi['title'] . ((int) $oi['volume'] > 1 ? ' - Tập ' . (int) $oi['volume'] : '');
                    $rowsHtml .= '<tr><td>' . e($name) . '</td><td>' . (int) $oi['quantity']
                        . '</td><td>' . e(formatPrice((float) $oi['price'] * (int) $oi['quantity'])) . '</td></tr>';
                }
                $rowsHtml .= '</table>';
                $rowsHtml .= '<p><strong>Tổng cộng: ' . e(formatPrice((float) $order['total'])) . '</strong></p>';

                sendMail(
                    $user['email'],
                    'Xác nhận đơn hàng ' . $order['code'],
                    '<h2>Cảm ơn bạn đã đặt hàng!</h2><p>Mã đơn: <strong>' . e($order['code'])
                    . '</strong></p>' . $rowsHtml
                );

                if ($paymentMethod === 'cod') {
                    sessionFlash('success', 'Đặt hàng thành công! Mã đơn: ' . $order['code']);
                    redirect('/thanh-toan/thanh-cong?id=' . $orderId);
                }

                redirect('/thanh-toan/online?id=' . $orderId);
            }
        }

        $this->view('site/checkout', [
            'pageTitle'   => 'Thanh toán',
            'rows'        => $rows,
            'subtotal'    => $subtotal,
            'coupon'      => $coupon,
            'couponCode'  => $couponCode,
            'discount'    => $discount,
            'shippingFee' => $shippingFee,
            'total'       => $total,
            'errors'      => $errors,
            'addresses'   => $this->addressModel->getByUser((int) $user['id']),
        ]);
    }

    public function onlinePayment(): void
    {
        $user = currentUser();
        $orderId = (int) ($_GET['id'] ?? 0);
        $order = $orderId > 0 ? $this->orderModel->getById($orderId) : null;

        if (!$order || (int) $order['user_id'] !== (int) $user['id'] || $order['status'] !== 'pending') {
            redirect('/');
        }

        if ($this->isPost()) {
            csrfCheck();
            $this->orderModel->recordPayment(
                $orderId,
                (string) $order['payment_method'],
                'MOCK' . date('YmdHis') . random_int(1000, 9999),
                (float) $order['total']
            );
            sessionFlash('success', 'Thanh toán thành công! Mã đơn: ' . $order['code']);
            redirect('/thanh-toan/thanh-cong?id=' . $orderId);
        }

        $this->view('site/payment_mock', [
            'pageTitle' => 'Thanh toán online',
            'order'     => $order,
        ]);
    }

    public function success(): void
    {
        $user = currentUser();
        $orderId = (int) ($_GET['id'] ?? 0);
        $order = $orderId > 0 ? $this->orderModel->getById($orderId) : null;

        if (!$order || (int) $order['user_id'] !== (int) $user['id']) {
            redirect('/');
        }

        $this->view('site/order_success', [
            'pageTitle' => 'Đặt hàng thành công',
            'order'     => $order,
            'items'     => $this->orderModel->getItems($orderId),
        ]);
    }

    public function history(): void
    {
        $user = currentUser();
        $page = max(1, (int) ($_GET['page'] ?? 1));
        $result = $this->orderModel->getByUser((int) $user['id'], $page);

        $this->view('site/orders', [
            'pageTitle' => 'Đơn hàng của tôi',
            'orders'    => $result['items'],
            'count'     => $result['count'],
            'page'      => $result['page'],
            'pages'     => $result['pages'],
        ]);
    }

    public function detail(): void
    {
        $user = currentUser();
        $orderId = (int) ($_GET['id'] ?? 0);
        $order = $orderId > 0 ? $this->orderModel->getById($orderId) : null;

        if (!$order || (int) $order['user_id'] !== (int) $user['id']) {
            sessionFlash('error', 'Không tìm thấy đơn hàng.');
            redirect('/don-hang');
        }

        $this->view('site/order_detail', [
            'pageTitle' => 'Đơn hàng ' . $order['code'],
            'order'     => $order,
            'items'     => $this->orderModel->getItems($orderId),
            'history'   => $this->orderModel->getStatusHistory($orderId),
        ]);
    }

    public function cancel(): void
    {
        if (!$this->isPost()) {
            redirect('/don-hang');
        }
        csrfCheck();

        $user = currentUser();
        $orderId = (int) ($_POST['id'] ?? 0);
        $order = $orderId > 0 ? $this->orderModel->getById($orderId) : null;

        if (!$order || (int) $order['user_id'] !== (int) $user['id']) {
            sessionFlash('error', 'Không tìm thấy đơn hàng.');
            redirect('/don-hang');
        }

        if (!canCancelOrder($order)) {
            sessionFlash('error', 'Đơn hàng này không thể hủy ở trạng thái hiện tại.');
            redirect('/don-hang/chi-tiet?id=' . $orderId);
        }

        $this->orderModel->cancel($orderId);
        sessionFlash('success', 'Đã hủy đơn hàng ' . $order['code']
            . ((float) $order['total'] > 0 && $order['status'] === 'paid' ? '. Tiền sẽ được hoàn trong 3-5 ngày.' : '.'));
        redirect('/don-hang');
    }
}