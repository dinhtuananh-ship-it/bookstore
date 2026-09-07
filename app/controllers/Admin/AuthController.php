<?php
declare(strict_types=1);

namespace Admin;

use Controller;

class AuthController extends Controller
{
    private const MAX_ATTEMPTS = 5;
    private const LOCK_MINUTES = 15;

    private \UserModel $userModel;

    public function __construct()
    {
        $this->userModel = new \UserModel();
    }

    public function login(): void
    {
        if (currentUser() && (currentUser()['role'] ?? '') === 'admin') {
            redirect('/admin');
        }

        $errors = [];
        $oldEmail = '';

        if ($this->isPost()) {
            csrfCheck();
            $email = trim((string) ($_POST['email'] ?? ''));
            $password = (string) ($_POST['password'] ?? '');
            $oldEmail = $email;

            $attempts = sessionGet('admin_login_attempts', []);
            $now = time();

            if (($attempts['locked_until'] ?? 0) > $now) {
                $minutesLeft = (int) ceil((($attempts['locked_until'] ?? 0) - $now) / 60);
                $errors['general'] = "Đã nhập sai quá nhiều lần. Vui lòng thử lại sau {$minutesLeft} phút.";
            } else {
                $user = $email !== '' ? $this->userModel->findByEmail($email) : null;

                if ($user
                    && ($user['role'] ?? '') === 'admin'
                    && password_verify($password, $user['password'])
                ) {
                    if ((int) $user['status'] !== 1) {
                        $errors['general'] = 'Tài khoản đã bị vô hiệu hóa.';
                    } else {
                        session_regenerate_id(true);
                        unset($_SESSION['admin_login_attempts']);
                        sessionSet('user', [
                            'id' => (int) $user['id'],
                            'name' => $user['name'],
                            'email' => $user['email'],
                            'role' => $user['role'],
                        ]);
                        redirect('/admin');
                    }
                } else {
                    $count = (int) ($attempts['count'] ?? 0) + 1;
                    if ($count >= self::MAX_ATTEMPTS) {
                        sessionSet('admin_login_attempts', [
                            'count' => 0,
                            'locked_until' => $now + self::LOCK_MINUTES * 60,
                        ]);
                        $errors['general'] = 'Nhập sai quá nhiều lần. Tạm khóa 15 phút.';
                    } else {
                        sessionSet('admin_login_attempts', [
                            'count' => $count,
                            'locked_until' => 0,
                        ]);
                        $errors['general'] = 'Email hoặc mật khẩu không đúng. Còn '
                            . (self::MAX_ATTEMPTS - $count) . ' lần thử.';
                    }
                }
            }
        }

        setLayout('');
        $this->view('admin/login', [
            'pageTitle' => 'Đăng nhập quản trị',
            'errors' => $errors,
            'oldEmail' => $oldEmail,
        ]);
    }

    public function logout(): void
    {
        $_SESSION = [];

        if (ini_get('session.use_cookies')) {
            $p = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000, $p['path'], $p['domain'], $p['secure'], $p['httponly']);
        }

        session_destroy();
        redirect('/admin/dang-nhap');
    }
}