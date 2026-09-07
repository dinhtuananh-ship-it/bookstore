# BookStore – Website bán sách (PHP MVC + MySQL)

Dự án website bán sách trực tuyến xây dựng theo mô hình MVC thuần PHP, chạy trên XAMPP.

## 1. Yêu cầu môi trường

- XAMPP (Apache + PHP 8.2+ + MySQL/MariaDB) tại `C:\xampp`
- Bật extension GD trong `C:\xampp\php\php.ini`: `extension=gd`
- Mở `httpd.conf` đảm bảo đã cấu hình vhost/document root mặc định trỏ tới `C:\xampp\htdocs`

## 2. Cài đặt

1. Copy thư mục dự án vào `C:\xampp\htdocs\bookstore`
2. Tạo cơ sở dữ liệu:

```bash
mysql -u root -e "CREATE DATABASE bookstore CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
mysql -u root --default-character-set=utf8mb4 bookstore < database/bookstore.sql
```

3. Đảm bảo các thư mục sau có quyền ghi (Windows: đã có sẵn):
   - `storage/logs/`
   - `storage/uploads/` (nếu có)
   - `public/uploads/books/`

4. Cấu hình kết nối DB (nếu không phải root/không mật khẩu) tại `config/config.php`

5. Truy cập: http://localhost/bookstore/

## 3. Tài khoản mặc định

| Vai trò    | Email                   | Mật khẩu     |
| ---------- | ----------------------- | ------------ |
| Admin      | `admin@bookstore.local` | `admin123`   |
| Khách hàng | `test@gmail.com`        | `matkhau123` |

## 4. Cấu trúc dự án

```
bookstore/
├── index.php                  # Front controller + router + security headers
├── config/config.php          # Cấu hình DB, phí ship, log, error handler
├── database/bookstore.sql     # Schema + dữ liệu mẫu (import lần đầu)
├── app/
│   ├── core/                  # Core MVC: Router, Controller, Model, View, DB
│   ├── controllers/           # Site + Admin controllers (23 use case)
│   ├── models/
│   ├── views/                 # site/ + admin/ + partials
│   └── helpers/functions.php  # e(), csrfCheck(), currentUser(), formatPrice()...
├── public/                    # CSS, JS, ảnh, uploads/books
└── storage/logs/app.log       # Log lỗi
```

## 5. Tính năng chính

**Khách hàng (khách / thành viên):**

- Đăng ký, đăng nhập (BCrypt, khóa tài khoản 15 phút sau 5 lần sai), đăng xuất
- Tìm kiếm, lọc theo danh mục / tác giả / nhà xuất bản / giá, sắp xếp
- Chi tiết sách, sách liên quan, đánh giá (chỉ sách đã mua, tự duyệt)
- Giỏ hàng (session), quản lý địa chỉ giao hàng, đặt hàng COD + mã giảm giá
- Lịch sử đơn hàng, hủy đơn (hoàn tồn kho), cập nhật hồ sơ, đổi mật khẩu

**Admin (`/admin`):**

- Dashboard thống kê, báo cáo doanh thu + biểu đồ (Chart.js)
- Quản lý sách (CRUD, upload ảnh bìa nén GD, xuất CSV), danh mục (cây cha-con)
- Quản lý đơn hàng (quy trình trạng thái chặt: pending → processing → shipping → completed, ghi lịch sử), khách hàng (khóa/mở khóa), khuyến mãi, bình luận

## 6. Bảo mật đã áp dụng

- Prepared statements toàn bộ truy vấn (chống SQLi)
- `e()` = `htmlspecialchars` toàn nơi hiển thị (chống XSS)
- CSRF token (`hash_equals`) mọi form; HTTP 419 khi thiếu token
- Session cookie `HttpOnly; SameSite=Lax`; header bảo mật (X-Frame-Options, X-Content-Type-Options, CSP...)
- Upload ảnh: kiểm tra MIME thật (`finfo`), kích thước ≤ 2MB, đổi tên ngẫu nhiên
- Quyền admin kiểm tra ở mọi controller; không-admin → 403
- Trang 500 thân thiện khi lỗi nghiêm trọng (không lộ stack trace)

## 7. Kiểm thử

| Giai đoạn | Nội dung                                                                                 | Kết quả    |
| --------- | ---------------------------------------------------------------------------------------- | ---------- |
| T35       | Chức năng 23 use case (luồng chính + thay thế)                                           | 63/63 PASS |
| T36       | Bảo mật: SQLi, XSS, truy cập trái phép, upload độc hại, CSRF, rate limit, path traversal | 41/41 PASS |

Chi tiết: `TEST_REPORT_T35.md`, `TEST_REPORT_T36.md` (tại thư mục tài liệu dự án).

## 8. Ghi chú

- Phí ship cố định 30.000đ, miễn phí đơn ≥ 500.000đ (chỉnh trong `config/config.php`)
- Mã giảm giá mẫu: `GIAM10` (giảm 10%, tối thiểu 100.000đ)
- Thanh toán online (VNPay/Momo) có luồng mock `payment_mock` — cần cấu hình gateway thật khi triển khai
- Email xác nhận đơn dùng `mail()` của PHP — cấu hình SMTP trước khi triển khai
