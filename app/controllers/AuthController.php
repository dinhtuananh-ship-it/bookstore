<?php
declare(strict_types=1);

class AuthController extends Controller
{
    private const MAX_ATTEMPTS = 5;
    private const LOCK_MINUTES = 15;

    private UserModel $userModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
    }

    public function register(): void
    {
        if (sessionGet('user')) {
            redirect('/');
        }

        $errors = [];
        $old = ['name' => '', 'email' => '', 'phone' => ''];

        if ($this->isPost()) {
            csrfCheck();
            $name = trim((string) ($_POST['name'] ?? ''));
            $email = trim((string) ($_POST['email'] ?? ''));
            $phone = trim((string) ($_POST['phone'] ?? ''));
            $password = (string) ($_POST['password'] ?? '');
            $confirm = (string) ($_POST['confirm_password'] ?? '');

            $old = ['name' => $name, 'email' => $email, 'phone' => $phone];

            if (mb_strlen($name) < 2) {
                $errors['name'] = 'Họ tên phải có ít nhất 2 ký tự';
            }
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $errors['email'] = 'Email không hợp lệ';
            } elseif ($this->userModel->emailExists($email)) {
                $errors['email'] = 'Email này đã được sử dụng';
            }
            if ($phone !== '' && !preg_match('/^0\d{9,10}$/', $phone)) {
                $errors['phone'] = 'Số điện thoại không hợp lệ';
            }
            if (strlen($password) < 6) {
                $errors['password'] = 'Mật khẩu phải có ít nhất 6 ký tự';
            }
            if ($password !== $confirm) {
                $errors['confirm_password'] = 'Xác nhận mật khẩu không khớp';
            }

            if (empty($errors)) {
                $this->userModel->create([
                    'name' => $name,
                    'email' => $email,
                    'phone' => $phone,
                    'password' => password_hash($password, PASSWORD_BCRYPT),
                ]);
                sessionFlash('success', 'Đăng ký thành công! Vui lòng đăng nhập.');
                redirect('/dang-nhap');
            }
        }

        $this->view('site/register', [
            'pageTitle' => 'Đăng ký tài khoản',
            'errors' => $errors,
            'old' => $old,
        ]);
    }

    public function login(): void
    {
        if (sessionGet('user')) {
            redirect('/');
        }

        $errors = [];
        $oldEmail = '';

        if ($this->isPost()) {
            csrfCheck();
            $email = trim((string) ($_POST['email'] ?? ''));
            $password = (string) ($_POST['password'] ?? '');
            $oldEmail = $email;

            $attempts = sessionGet('login_attempts', []);
            $now = time();

            if (($attempts['locked_until'] ?? 0) > $now) {
                $minutesLeft = (int) ceil((($attempts['locked_until'] ?? 0) - $now) / 60);
                $errors['general'] = "Đã nhập sai quá nhiều lần. Vui lòng thử lại sau {$minutesLeft} phút.";
            } else {
                $user = $email !== '' ? $this->userModel->findByEmail($email) : null;

                if ($user && password_verify($password, $user['password'])) {
                    if ((int) $user['status'] !== 1) {
                        $errors['general'] = 'Tài khoản đã bị vô hiệu hóa. Vui lòng liên hệ quản trị viên.';
                    } else {
                        session_regenerate_id(true);
                        unset($_SESSION['login_attempts']);
                        sessionSet('user', [
                            'id' => (int) $user['id'],
                            'name' => $user['name'],
                            'email' => $user['email'],
                            'role' => $user['role'],
                        ]);
                        redirect('/');
                    }
                } else {
                    $count = (int) ($attempts['count'] ?? 0) + 1;
                    if ($count >= self::MAX_ATTEMPTS) {
                        sessionSet('login_attempts', [
                            'count' => 0,
                            'locked_until' => $now + self::LOCK_MINUTES * 60,
                        ]);
                        $errors['general'] = 'Nhập sai quá nhiều lần. Tài khoản bị khóa 15 phút.';
                    } else {
                        sessionSet('login_attempts', [
                            'count' => $count,
                            'locked_until' => 0,
                        ]);
                        $errors['general'] = 'Email hoặc mật khẩu không đúng. Còn '
                            . (self::MAX_ATTEMPTS - $count) . ' lần thử.';
                    }
                }
            }
        }

        $this->view('site/login', [
            'pageTitle' => 'Đăng nhập',
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
        redirect('/');
    }
}