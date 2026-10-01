const { Document, Packer, Paragraph, TextRun, HeadingLevel, AlignmentType, Table, TableRow, TableCell, WidthType, PageBreak, ShadingType } = require("docx");
const fs = require("fs");
const path = require("path");

const B = (t, size = 22) => new TextRun({ text: t, bold: true, size });
const T = (t, size = 22) => new TextRun({ text: t, size });
const IT = (t, size = 22) => new TextRun({ text: t, italics: true, size });

const para = (text, opts = {}) =>
  new Paragraph({
    children: typeof text === "string" ? [T(text, opts.size || 22)] : text,
    alignment: opts.align || AlignmentType.JUSTIFIED,
    spacing: { after: 120, line: 276 },
  });

const bullet = (text, level = 0) =>
  new Paragraph({
    children: [T(text)],
    bullet: { level },
    alignment: AlignmentType.JUSTIFIED,
    spacing: { after: 80, line: 276 },
  });

const h1 = (text) =>
  new Paragraph({ heading: HeadingLevel.HEADING_1, children: [B(text.toUpperCase(), 28)], spacing: { before: 360, after: 200 } });
const h2 = (text) =>
  new Paragraph({ heading: HeadingLevel.HEADING_2, children: [B(text, 26)], spacing: { before: 280, after: 160 } });
const h3 = (text) =>
  new Paragraph({ heading: HeadingLevel.HEADING_3, children: [B(text, 24)], spacing: { before: 220, after: 120 } });

function cell(text, bold = false, shade = false) {
  return new TableCell({
    children: [new Paragraph({ children: [bold ? B(text, 20) : T(text, 20)], spacing: { after: 40 } })],
    width: { size: 0, type: WidthType.AUTO },
    shading: shade ? { type: ShadingType.CLEAR, fill: "D9E2F3" } : undefined,
  });
}
function makeTable(headers, rows) {
  return new Table({
    width: { size: 100, type: WidthType.PERCENTAGE },
    rows: [
      new TableRow({ children: headers.map((hh) => cell(hh, true, true)) }),
      ...rows.map((r) => new TableRow({ children: r.map((c) => cell(String(c))) })),
    ],
  });
}
function codeBlock(lines) {
  return new Paragraph({
    children: lines.map((l, i) => new TextRun({ text: l + (i < lines.length - 1 ? "\n" : ""), size: 18, font: "Consolas" })),
    shading: { type: ShadingType.CLEAR, fill: "F2F2F2" },
    spacing: { after: 160 },
  });
}

const children = [];

// ===== BÌA =====
for (let i = 0; i < 4; i++) children.push(new Paragraph({ children: [T("")] }));
children.push(new Paragraph({ children: [B("TÀI LIỆU TỔNG QUAN HỆ THỐNG", 28)], alignment: AlignmentType.CENTER }));
children.push(new Paragraph({ children: [B("WEBSITE BÁN SÁCH TRỰC TUYẾN – BOOKSTORE", 30)], alignment: AlignmentType.CENTER }));
children.push(new Paragraph({ children: [T("Mô hình MVC thuần PHP + MySQL/MariaDB trên XAMPP", 22)], alignment: AlignmentType.CENTER }));
children.push(new Paragraph({ children: [T("")] }));
children.push(para("Hệ thống khảo sát thực tế: C:\\xampp\\htdocs\\bookstore – Website bán sách B2C gồm 2 phân hệ Site (khách hàng) và Admin (quản trị), 23 use case, cơ sở dữ liệu 11 bảng, phân quyền Member/Admin.", { align: AlignmentType.CENTER }));
children.push(new Paragraph({ children: [T("")] }));
children.push(new Paragraph({ children: [T("Người lập: ............................................   Ngày lập: 01/10/2026")] }));
children.push(new Paragraph({ children: [T("Nguồn mã nguồn: C:\\xampp\\htdocs\\bookstore (index.php, config/, app/, public/, database/bookstore.sql)")] }));
children.push(new Paragraph({ children: [new TextRun({ text: "", break: 1 })] }));
children.push(new Paragraph({ children: [IT("TP. Hồ Chí Minh, tháng 10 năm 2026", 22)], alignment: AlignmentType.CENTER }));
children.push(new Paragraph({ children: [new PageBreak()] }));

// ===== 1. GIỚI THIỆU =====
children.push(h1("1. Giới thiệu hệ thống"));
children.push(para("Bookstore (tên hiển thị: Kim Đồng Bookstore – cấu hình APP_NAME trong config/config.php) là website thương mại điện tử bán sách trực tuyến, xây dựng bằng PHP thuần theo mô hình MVC, chạy trên XAMPP (Apache + PHP 8.2+ + MySQL/MariaDB), truy cập tại http://localhost/bookstore/."));
children.push(para("Hệ thống phục vụ 3 nhóm người dùng: (1) Khách vãng lai – xem, tìm kiếm, thêm giỏ hàng; (2) Thành viên (member) – mua hàng đầy đủ: đặt hàng COD, quản lý địa chỉ/đơn hàng, đánh giá sách đã mua; (3) Quản trị viên (admin) – quản trị toàn bộ qua /admin: sách, danh mục, đơn hàng, khách hàng, khuyến mãi, banner, bình luận, báo cáo doanh thu."));
children.push(para("Điểm nổi bật về quy mô: ~71 route (định nghĩa trong app/core/Router.php), 19 controller (9 Site + 10 Admin), 9 model, 14 view site + 13 view admin, cơ sở dữ liệu 11 bảng với dữ liệu mẫu tiếng Việt (~29 đầu sách, 7 danh mục, coupon GIAM10, 2 tài khoản demo)."));
children.push(h2("1.1. Lý do xây dựng"));
children.push(bullet("Thương mại điện tử sách tăng trưởng ổn định, tỷ lệ mua lặp lại cao, dễ vận chuyển; khách hàng cần tìm/lọc theo danh mục/tác giả/NXB/giá, xem đánh giá, đặt COD và theo dõi đơn online."));
children.push(bullet("Nghiệp vụ đủ đầy nhưng quy mô vừa phải: giỏ hàng session, đặt – trừ kho – hoàn kho khi hủy, mã giảm giá, phân quyền, upload ảnh bìa, thống kê doanh thu – rất phù hợp làm đồ án phân tích thiết kế."));
children.push(bullet("Mã nguồn mở, dễ đo lường: đã có kịch bản kiểm thử chức năng 63/63 PASS và bảo mật 41/41 PASS."));
children.push(h2("1.2. Mục tiêu"));
children.push(bullet("Về nghiệp vụ: đáp ứng trọn vòng đời mua – bán: đăng ký/đăng nhập, tìm kiếm/lọc/sắp xếp, chi tiết + sách liên quan, giỏ hàng, địa chỉ giao hàng, đặt COD + coupon, lịch sử/hủy đơn, đánh giá; quản trị: dashboard, CRUD sách/danh mục/coupon/banner, xử lý đơn theo quy trình chặt, khóa/mở khách, duyệt bình luận, báo cáo."));
children.push(bullet("Về kỹ thuật: đúng MVC (Router – Controller – Model – View), an toàn (Prepared Statements, XSS-escape, CSRF 419, khóa login sau 5 lần sai 15 phút), dễ bảo trì và mở rộng lên thanh toán thật / tìm kiếm Fulltext / front-end React."));
children.push(bullet("Về vận hành: cài 1 lệnh là chạy (import database/bookstore.sql), có tài khoản demo, log lỗi đầy đủ, trang 500 thân thiện."));

// ===== 2. CÔNG NGHỆ =====
children.push(h1("2. Công nghệ và môi trường"));
children.push(makeTable(["Thành phần", "Công nghệ / phiên bản", "Vai trò trong hệ thống"], [
  ["Web Server", "Apache (XAMPP), RewriteBase /bookstore/ → index.php", "Phục vụ HTTP, front-controller duy nhất"],
  ["Ngôn ngữ", "PHP 8.2+ thuần, không dùng framework", "Router, Controller, Model, View tự cài đặt"],
  ["CSDL", "MySQL/MariaDB 10.4, charset utf8mb4_unicode_ci", "Lưu 11 bảng, transaction đặt hàng"],
  ["Giao diện", "HTML5 + CSS (public/css) + JS thuần (public/js/main.js) + Chart.js", "Site responsive, admin sidebar, biểu đồ doanh thu"],
  ["Thư viện PHP", "PDO, GD (nén ảnh), finfo (MIME), mail()", "Truy vấn an toàn, upload bìa, gửi mail xác nhận"],
  ["Môi trường", "Windows + XAMPP tại C:\\xampp, extension=gd", "Chạy tại http://localhost/bookstore/"],
]));
children.push(para("Cấu hình tập trung tại config/config.php: DB_HOST localhost, DB_PORT 3306, DB_NAME bookstore, DB_USER root, SHIPPING_FEE 30.000đ, FREE_SHIPPING_MIN 500.000đ, ITEMS_PER_PAGE 12, AUTO_APPROVE_REVIEWS true. Kết nối singleton tại config/database.php với PDO::ATTR_EMULATE_PREPARES = false."));
children.push(codeBlock([
  "Copy dự án vào C:\\xampp\\htdocs\\bookstore",
  "mysql -u root -e \"CREATE DATABASE bookstore CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;\"",
  "mysql -u root --default-character-set=utf8mb4 bookstore < database/bookstore.sql",
  "Truy cập: http://localhost/bookstore/  |  Admin: http://localhost/bookstore/admin",
]));
children.push(makeTable(["Tài khoản", "Email", "Mật khẩu", "Quyền"], [
  ["Quản trị", "admin@bookstore.local", "admin123", "Toàn quyền /admin"],
  ["Khách hàng", "test@gmail.com", "matkhau123", "Mua hàng, đánh giá"],
]));
children.push(para("Mã giảm giá mẫu: GIAM10 (giảm 10%, đơn tối thiểu 100.000đ). Phí ship cố định 30.000đ, miễn phí đơn sau giảm ≥ 500.000đ."));

// ===== 3. KIẾN TRÚC =====
children.push(h1("3. Kiến trúc tổng thể (MVC)"));
children.push(para("Luồng request: Browser → index.php (security headers X-Frame-Options, nạp config/database, try/catch → trang 500) → Router($_SERVER['REQUEST_URI']) đối chiếu bảng ROUTES (~60 route tĩnh) → Controller@action (gọi beforeAction kiểm tra quyền) → Model (PDO prepared) → MariaDB → View render qua layout/main.php (site) hoặc layout/admin.php (admin) → HTML."));
children.push(codeBlock([
  "[Browser] --HTTP--> [Apache :80] --> index.php (Front Controller)",
  "  --> Router.php: ROUTES['', 'sach', 'gio-hang', 'thanh-toan', 'admin/...']",
  "  --> Controller (requireLogin/requireAdmin + csrfCheck + validate)",
  "  --> Model (query/fetchAll/fetchOne, PDO prepared) --> [MariaDB bookstore]",
  "  --> View + helpers (e, url, formatPrice, paginationLinks) --> HTML",
]));
children.push(makeTable(["Lớp", "File đại diện", "Nhiệm vụ"], [
  ["Router", "app/core/Router.php (164 dòng)", "Ánh xạ URL tiếng Việt SEO → Controller@action; fallback admin/... và danh-muc/{slug}"],
  ["Controller", "app/core/Controller.php + 19 controller con", "Nhận request, kiểm tra quyền/CSRF, điều phối; Site: Site/Auth/Book/Cart/Order/Review...; Admin: Dashboard/Book/Order/User/Coupon..."],
  ["Model", "app/core/Model.php + 9 model", "Truy vấn DB: Book::search, Order::create/cancel (transaction), Coupon::isValid, Review::hasPurchased..."],
  ["View", "app/views/site/*, admin/*, layout/*", "Template PHP, mọi echo qua e() chống XSS; partial book_card.php tái sử dụng"],
  ["Helper", "app/helpers/functions.php (407 dòng)", "e, url/asset, render/setLayout, session, csrfToken/csrfCheck (419), bookPrice/formatPrice, uploadImage/compressImage, slugify"],
]));
children.push(para("Cấu trúc thư mục: index.php | .htaccess | config/ | database/bookstore.sql | app/core – controllers – models – views – helpers/ | public/css-js-uploads/ | storage/logs/app.log | tools/backfill_search_key.php + docgen/."));
children.push(h2("3.1. Định tuyến chính"));
children.push(makeTable(["Nhóm", "URL tiêu biểu", "Controller@action"], [
  ["Site công khai", "/, /sach, /sach/chi-tiet?id=, /danh-muc/{slug}", "Site@index, Book@search/detail, Category@show"],
  ["Giỏ – Đặt hàng", "/gio-hang(/them/cap-nhat/xoa), /thanh-toan(/thanh-cong,/online), /don-hang(/chi-tiet,/huy)", "Cart@index/add/update/remove, Order@checkout/success/onlinePayment/history/detail/cancel"],
  ["Tài khoản", "/dang-ky, /dang-nhap, /dang-xuat, /ho-so(/mat-khau), /dia-chi(...), /danh-gia(...)", "Auth, Profile, Address, Review"],
  ["Admin", "/admin, /admin/sach, /danh-muc, /don-hang, /khach-hang, /khuyen-mai, /binh-luan, /banner, /bao-cao", "Admin\\Dashboard/Book/Category/Order/User/Coupon/Review/Banner/Report"],
]));

// ===== 4. CHỨC NĂNG =====
children.push(h1("4. Chức năng hệ thống (23 use case)"));
children.push(h2("4.1. Phân hệ khách hàng (Site)"));
children.push(makeTable(["Mã", "Chức năng", "Mô tả ngắn"], [
  ["UC01-03", "Đăng ký / Đăng nhập / Đăng xuất", "BCrypt, khóa 15 phút sau 5 lần sai, session regenerate, flash thông báo"],
  ["UC04-06", "Hồ sơ / Đổi mật khẩu / Sổ địa chỉ", "Sửa tên/phone/avatar (2MB, jpg/png/webp/gif); địa chỉ đầu auto mặc định"],
  ["UC07-08", "Tìm kiếm / Xem chi tiết", "Lọc danh mục/tác giả/NXB/giá, sắp xếp, 12/trang, cột search_key không dấu; chi tiết + sách liên quan + tóm tắt đánh giá"],
  ["UC09", "Giỏ hàng", "Lưu session key bookId:volume, kẹp theo stock, tối đa 99"],
  ["UC10-11", "Áp coupon / Đặt hàng", "Coupon findByCode+isValid+calculateDiscount; COD → processing, online (mock) → pending → paid; transaction trừ kho + sinh mã BKyyyymmdd-xxxx + mail xác nhận"],
  ["UC12-13", "Lịch sử / Hủy đơn", "Xem + timeline; chỉ hủy pending/paid/processing, hoàn tồn kho"],
  ["UC14", "Đánh giá", "Chỉ khi đơn completed + chưa review, 1 user/sách, tự duyệt"],
]));
children.push(h2("4.2. Phân hệ quản trị (Admin – /admin)"));
children.push(makeTable(["Mã", "Chức năng", "Mô tả ngắn"], [
  ["UC15", "Dashboard + Báo cáo", "Thống kê đơn/doanh thu/sách/khách; biểu đồ doanh thu theo ngày, top 10 sách/khách"],
  ["UC16", "Quản lý sách", "CRUD + upload bìa nén GD max 900px + chấp nhận URL ngoài + xuất CSV BOM UTF-8; chặn xóa khi đã có order_items"],
  ["UC17", "Danh mục", "Cây cha–con 2 cấp, slug tự sinh, chặn xóa khi còn con/sách"],
  ["UC18", "Đơn hàng", "Quy trình pending → processing → shipping → completed (+nhánh paid/cancelled); ghi lịch sử changed_by; hủy hoàn kho"],
  ["UC19", "Khách hàng", "Lọc theo từ khóa/trạng thái, xem chi tiết + lịch sử mua, khóa/mở (chặn tự khóa)"],
  ["UC20-22", "Khuyến mãi / Bình luận / Banner", "Coupon percent/fixed + validate regex; duyệt/ẩn review; banner sort_order + upload/URL"],
  ["UC23", "Đăng nhập admin", "Login riêng, bắt role=admin, mọi controller đều requireAdmin (403 nếu vi phạm)"],
]));

// ===== 5. CSDL =====
children.push(h1("5. Cơ sở dữ liệu (11 bảng)"));
children.push(para("CSDL tên bookstore, InnoDB, utf8mb4_unicode_ci. Quan hệ chính: Users 1-n Addresses/Orders/Reviews; Categories tự đệ quy parent_id 1-n Books; Books 1-n OrderItems + Reviews; Orders 1-n OrderItems/OrderStatusHistory/Payments; Coupons 1-n Orders (SET NULL); Addresses 1-n Orders (SET NULL); Banners độc lập."));
children.push(makeTable(["Bảng", "Cột/khóa chính", "Ghi chú"], [
  ["users", "id PK, email UQ, password BCrypt, role member/admin, status", "Seed admin + member demo"],
  ["categories", "id PK, parent_id FK self, slug UQ", "7 mẫu: Văn học, Kinh tế, Kỹ năng, Thiếu nhi + con"],
  ["books", "id PK, category_id FK, isbn UQ, price/sale_price, stock, search_key", "~29 sách, index status/created/sold"],
  ["addresses", "id PK, user_id CASCADE, is_default", "1 địa chỉ mặc định/user"],
  ["coupons", "id PK, code UQ (GIAM10), type percent/fixed", "10% min 100k, đã dùng 3/100"],
  ["orders", "id PK, code UQ BK..., status 6 giá trị, payment cod/vnpay/momo", "Đơn mẫu BK20260819-0001 completed 310k"],
  ["order_items", "order_id CASCADE, book_id, volume, quantity, price snapshot", "Giá snapshot giữ đúng báo cáo"],
  ["order_status_history", "order_id CASCADE, status, changed_by", "Audit mọi lần đổi trạng thái"],
  ["payments", "order_id CASCADE, method, transaction_id MOCK+time", "Mock online, hủy → failed"],
  ["reviews", "user_id+book_id UQ, rating 1-5", "Chỉ sách đã mua completed"],
  ["banners", "id PK, image, sort_order, status", "1 banner chào mừng trang chủ"],
]));

// ===== 6. LUỒNG CHÍNH =====
children.push(h1("6. Luồng nghiệp vụ chính"));
children.push(h2("6.1. Luồng mua hàng (Site)"));
children.push(codeBlock([
  "Tìm/lọc sách -> Chi tiết -> Thêm giỏ (session) -> Đăng nhập (nếu chưa)",
  "-> Chọn/tạo địa chỉ -> Áp coupon GIAM10 (check min_order/hạn/lượt)",
  "-> Chọn COD/VNPay/Momo -> Xác nhận -> TRANSACTION:",
  "   kiểm tra stock -> trừ stock, tăng sold_count -> INSERT orders + items",
  "   -> tăng used_count coupon -> ghi lịch sử -> COMMIT -> gửi mail -> xóa giỏ",
  "COD -> trang thanh-cong (processing) | Online -> payment_mock -> paid -> thanh-cong",
  "Nhận hàng (completed) -> được đánh giá | Muốn hủy (pending/paid/processing) -> hoàn kho",
]));
children.push(h2("6.2. Luồng quản trị đơn (Admin)"));
children.push(codeBlock([
  "Mở /admin/don-hang (lọc status/từ khóa/ngày) -> Mở chi tiết",
  "-> Nếu completed/cancelled: khóa form (FINAL)",
  "-> Chọn 1 trong processing/shipping/completed/cancelled + ghi chú -> Submit",
  "-> cancelled: gọi cancel() hoàn kho + payments failed",
  "-> còn lại: updateStatus + recordStatus(changed_by=admin)",
]));

// ===== 7. BẢO MẬT =====
children.push(h1("7. Bảo mật đã áp dụng"));
children.push(makeTable(["Nhóm", "Biện pháp (tên hàm thật)", "Kết quả kiểm thử"], [
  ["Xác thực", "password_hash/verify BCrypt; khóa 5 lần/15p; session_regenerate_id; cookie HttpOnly SameSite=Lax", "Chống dò mật khẩu, fixation"],
  ["Phân quyền", "requireLogin / requireAdmin (403); check sở hữu đơn/địa chỉ (chống IDOR)", "Chặn /admin trái phép"],
  ["Input", "100% Prepared Statements (EMULATE_PREPARES false); e() mọi echo; csrfCheck → 419", "41/41 ca SQLi/XSS/CSRF PASS"],
  ["Upload", "uploadImage: finfo MIME thật, whitelist jpg/png/webp/gif, ≤2MB, tên random, GD nén 900px", "Chặn shell PHP giả ảnh"],
  ["Vận hành", "Headers DENY/nosniff/Referrer; writeLog + trang 500 thân thiện; .htaccess", "Chống clickjacking, lộ stack"],
]));

// ===== 8. KẾT LUẬN =====
children.push(h1("8. Đánh giá và hướng phát triển"));
children.push(bullet("Kết quả: website chạy ổn định cả Site + Admin, cài 1 lệnh là chạy, 63/63 ca chức năng PASS, 41/41 ca bảo mật PASS, tốc độ trang danh sách < 2s với dữ liệu mẫu."));
children.push(bullet("Hạn chế: thanh toán online mới mock (cần gateway thật), mail() cần SMTP thật, tìm kiếm LIKE chưa Fulltext, chưa có đổi trả/đa đơn vị vận chuyển, chưa phân quyền kho/kế toán chi tiết."));
children.push(bullet("Hướng phát triển ngắn hạn: tích hợp VNPay/Momo thật, SMTP, Fulltext index, xuất Excel/PDF báo cáo, thêm captcha/2FA."));
children.push(bullet("Dài hạn: tách API + front-end React/Next.js, app mobile, gợi ý sách, tích điểm, Docker + HTTPS + backup tự động."));
children.push(para("Tài liệu tham khảo mã nguồn: README.md, database/bookstore.sql, config/config.php, app/core/Router.php, app/helpers/functions.php, storage/logs/app.log tại C:\\xampp\\htdocs\\bookstore."));

const doc = new Document({
  creator: "Bookstore - Tong quan he thong",
  title: "Tong quan he thong Website ban sach Bookstore",
  sections: [{ properties: { page: { margin: { top: 1440, bottom: 1440, left: 1440, right: 1440 } } }, children }],
});

const out1 = "C:\\xampp\\htdocs\\bookstore\\TongQuan_HeThong_Bookstore.docx";
const out2 = path.join("C:\\Users\\DINH TUAN ANH\\OneDrive\\Documents\\Default Project", "TongQuan_HeThong_Bookstore.docx");
Packer.toBuffer(doc).then((buf) => {
  fs.writeFileSync(out1, buf);
  try { fs.writeFileSync(out2, buf); } catch (e) { console.error("OUT2 fail", e.message); }
  console.log("DONE bytes=" + buf.length);
  console.log("OUT1=" + out1);
  console.log("OUT2=" + out2);
}).catch((e) => { console.error("FAIL", e); process.exit(1); });
