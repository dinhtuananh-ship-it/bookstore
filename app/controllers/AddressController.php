<?php
declare(strict_types=1);

class AddressController extends Controller
{
    private AddressModel $addressModel;

    public function __construct()
    {
        $this->addressModel = new AddressModel();
    }

    public function beforeAction(string $action): void
    {
        requireLogin();
    }

    public function index(): void
    {
        $user = currentUser();
        $editId = (int) ($_GET['edit'] ?? 0);
        $editing = $editId > 0 ? $this->addressModel->findById($editId) : null;

        if ($editing && (int) $editing['user_id'] !== (int) $user['id']) {
            $editing = null;
        }

        $this->view('site/addresses', [
            'pageTitle' => 'Sổ địa chỉ',
            'addresses' => $this->addressModel->getByUser((int) $user['id']),
            'editing'   => $editing,
        ]);
    }

    public function store(): void
    {
        if (!$this->isPost()) {
            redirect('/dia-chi');
        }
        csrfCheck();

        $user = currentUser();
        $data = $this->collect();

        if ($errors = $this->validate($data)) {
            sessionFlash('error', reset($errors));
            redirect('/dia-chi');
        }

        $data['user_id'] = (int) $user['id'];
        $this->addressModel->create($data);
        sessionFlash('success', 'Đã thêm địa chỉ mới.');
        redirect('/dia-chi');
    }

    public function update(): void
    {
        if (!$this->isPost()) {
            redirect('/dia-chi');
        }
        csrfCheck();

        $user = currentUser();
        $id = (int) ($_POST['id'] ?? 0);
        $address = $id > 0 ? $this->addressModel->findById($id) : null;

        if (!$address || (int) $address['user_id'] !== (int) $user['id']) {
            sessionFlash('error', 'Địa chỉ không tồn tại.');
            redirect('/dia-chi');
        }

        $data = $this->collect();
        if ($errors = $this->validate($data)) {
            sessionFlash('error', reset($errors));
            redirect('/dia-chi?edit=' . $id);
        }

        $this->addressModel->update($id, $data);
        sessionFlash('success', 'Đã cập nhật địa chỉ.');
        redirect('/dia-chi');
    }

    public function delete(): void
    {
        if (!$this->isPost()) {
            redirect('/dia-chi');
        }
        csrfCheck();

        $user = currentUser();
        $id = (int) ($_POST['id'] ?? 0);
        $address = $id > 0 ? $this->addressModel->findById($id) : null;

        if ($address && (int) $address['user_id'] === (int) $user['id']) {
            $wasDefault = (int) $address['is_default'] === 1;
            $this->addressModel->delete($id, (int) $user['id']);

            if ($wasDefault) {
                $remaining = $this->addressModel->getByUser((int) $user['id']);
                if ($remaining !== []) {
                    $this->addressModel->setDefault((int) $remaining[0]['id'], (int) $user['id']);
                }
            }
            sessionFlash('success', 'Đã xóa địa chỉ.');
        } else {
            sessionFlash('error', 'Địa chỉ không tồn tại.');
        }

        redirect('/dia-chi');
    }

    public function setDefault(): void
    {
        if (!$this->isPost()) {
            redirect('/dia-chi');
        }
        csrfCheck();

        $user = currentUser();
        $id = (int) ($_POST['id'] ?? 0);
        $address = $id > 0 ? $this->addressModel->findById($id) : null;

        if ($address && (int) $address['user_id'] === (int) $user['id']) {
            $this->addressModel->setDefault($id, (int) $user['id']);
            sessionFlash('success', 'Đã đặt làm địa chỉ mặc định.');
        }

        redirect('/dia-chi');
    }

    private function collect(): array
    {
        return [
            'full_name' => trim((string) ($_POST['full_name'] ?? '')),
            'phone'     => trim((string) ($_POST['phone'] ?? '')),
            'province'  => trim((string) ($_POST['province'] ?? '')),
            'district'  => trim((string) ($_POST['district'] ?? '')),
            'ward'      => trim((string) ($_POST['ward'] ?? '')),
            'detail'    => trim((string) ($_POST['detail'] ?? '')),
        ];
    }

    private function validate(array $data): array
    {
        $errors = [];
        if (mb_strlen($data['full_name']) < 2) {
            $errors['full_name'] = 'Họ tên không hợp lệ.';
        }
        if (!preg_match('/^0\d{9,10}$/', $data['phone'])) {
            $errors['phone'] = 'Số điện thoại không hợp lệ.';
        }
        foreach (['province', 'district', 'ward', 'detail'] as $field) {
            if ($data[$field] === '') {
                $errors[$field] = 'Thiếu thông tin địa chỉ.';
            }
        }
        return $errors;
    }
}