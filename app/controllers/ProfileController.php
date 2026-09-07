<?php
declare(strict_types=1);

class ProfileController extends Controller
{
    private UserModel $userModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
    }

    public function beforeAction(string $action): void
    {
        requireLogin();
    }

    public function index(): void
    {
        $user = currentUser();
        $errors = [];

        if ($this->isPost()) {
            csrfCheck();

            $name = trim((string) ($_POST['name'] ?? ''));
            $phone = trim((string) ($_POST['phone'] ?? ''));

            if (mb_strlen($name) < 2) {
                $errors['name'] = 'Họ tên phải có ít nhất 2 ký tự';
            }
            if ($phone !== '' && !preg_match('/^0\d{9,10}$/', $phone)) {
                $errors['phone'] = 'Số điện thoại không hợp lệ';
            }

            $avatarPath = null;
            if (empty($errors) && !empty($_FILES['avatar']['name'])) {
                $result = $this->uploadAvatar((int) $user['id']);
                if ($result === 'INVALID_TYPE') {
                    $errors['avatar'] = 'Ảnh phải là JPG, PNG, WEBP hoặc GIF.';
                } elseif ($result === 'INVALID_SIZE') {
                    $errors['avatar'] = 'Ảnh không được quá 2MB.';
                } elseif ($result !== null) {
                    $avatarPath = $result;
                }
            }

            if (empty($errors)) {
                $this->userModel->updateProfile((int) $user['id'], [
                    'name'   => $name,
                    'phone'  => $phone,
                    'avatar' => $avatarPath,
                ]);

                $updated = $this->userModel->findById((int) $user['id']);
                sessionSet('user', [
                    'id'     => (int) $updated['id'],
                    'name'   => $updated['name'],
                    'email'  => $updated['email'],
                    'role'   => $updated['role'],
                    'avatar' => $updated['avatar'],
                ]);

                sessionFlash('success', 'Đã cập nhật hồ sơ thành công.');
                redirect('/ho-so');
            }
        }

        $this->view('site/profile', [
            'pageTitle' => 'Hồ sơ tài khoản',
            'errors'    => $errors,
        ]);
    }

    public function changePassword(): void
    {
        if (!$this->isPost()) {
            redirect('/ho-so');
        }
        csrfCheck();

        $user = currentUser();
        $errors = [];
        $old = (string) ($_POST['old_password'] ?? '');
        $new = (string) ($_POST['new_password'] ?? '');
        $confirm = (string) ($_POST['confirm_password'] ?? '');

        $dbUser = $this->userModel->findById((int) $user['id']);
        if (!password_verify($old, $dbUser['password'])) {
            $errors['old_password'] = 'Mật khẩu hiện tại không đúng.';
        }
        if (strlen($new) < 6) {
            $errors['new_password'] = 'Mật khẩu mới phải có ít nhất 6 ký tự.';
        }
        if ($new !== $confirm) {
            $errors['confirm_password'] = 'Xác nhận mật khẩu không khớp.';
        }

        if (empty($errors)) {
            $this->userModel->updatePassword((int) $user['id'], password_hash($new, PASSWORD_BCRYPT));
            sessionFlash('success', 'Đã đổi mật khẩu thành công.');
        } else {
            sessionFlash('error', reset($errors));
        }

        redirect('/ho-so');
    }

    private function uploadAvatar(int $userId): ?string
    {
        $file = $_FILES['avatar'];
        if ($file['error'] !== UPLOAD_ERR_OK) {
            return null;
        }

        $allowed = [
            'image/jpeg' => 'jpg',
            'image/png'  => 'png',
            'image/webp' => 'webp',
            'image/gif'  => 'gif',
        ];

        $mime = mime_content_type((string) $file['tmp_name']) ?: $file['type'];
        if (!isset($allowed[$mime])) {
            return 'INVALID_TYPE';
        }
        if ((int) $file['size'] > 2 * 1024 * 1024) {
            return 'INVALID_SIZE';
        }

        $dir = APP_ROOT . '/public/uploads/avatars';
        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
        }

        $filename = 'u' . $userId . '_' . bin2hex(random_bytes(8)) . '.' . $allowed[$mime];
        if (!move_uploaded_file((string) $file['tmp_name'], $dir . '/' . $filename)) {
            return null;
        }

        return 'uploads/avatars/' . $filename;
    }
}