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
const h4 = (text) =>
  new Paragraph({ heading: HeadingLevel.HEADING_4, children: [B(text, 22)], spacing: { before: 180, after: 100 } });

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

// ============ BIA ============
for (let i = 0; i < 4; i++) children.push(new Paragraph({ children: [T("")] }));
children.push(new Paragraph({ children: [B("TRUONG DAI HOC / KHOA CONG NGHE THONG TIN", 24)], alignment: AlignmentType.CENTER }));
children.push(new Paragraph({ children: [T("----- oOo -----", 22)], alignment: AlignmentType.CENTER }));
children.push(new Paragraph({ children: [T("")] }));
children.push(new Paragraph({ children: [B("BAO CAO DO AN", 30)], alignment: AlignmentType.CENTER }));
children.push(new Paragraph({ children: [B("PHAN TICH VA THIET KE HE THONG", 30)], alignment: AlignmentType.CENTER }));
children.push(new Paragraph({ children: [B("WEBSITE BAN SACH TRUC TUYEN - BOOKSTORE", 28)], alignment: AlignmentType.CENTER }));
children.push(new Paragraph({ children: [T("")] }));
children.push(para("He thong khao sat thuc te: C:\\xampp\\htdocs\\bookstore - Website ban sach xay dung theo mo hinh MVC thuan PHP, chay tren XAMPP (Apache + PHP 8.x + MySQL/MariaDB). Co so du lieu bookstore voi 11 bang, 23 use case, 2 vai tro chinh (Khach hang / Admin).", { align: AlignmentType.CENTER }));
children.push(new Paragraph({ children: [T("")] }));
children.push(new Paragraph({ children: [T("Sinh vien thuc hien: ............................................  MSSV: ....................")] }));
children.push(new Paragraph({ children: [T("Giang vien huong dan: ..............................................................................")] }));
children.push(new Paragraph({ children: [T("Nam hoc: 2025 - 2026")] }));
children.push(new Paragraph({ children: [new TextRun({ text: "", break: 1 })] }));
children.push(new Paragraph({ children: [IT("TP. Ho Chi Minh, thang 09 nam 2026", 22)], alignment: AlignmentType.CENTER }));
children.push(new Paragraph({ children: [new TextRun({ text: "", break: 1 })], }));
children.push(new Paragraph({ children: [new PageBreak()] }));

// ============ MUC LUC ============
children.push(h1("Muc luc"));
[
  "CHUONG 1. TONG QUAN ........................................................................................ 1",
  "1.1. Ly do chon de tai ........................................................................................ 1",
  "1.2. Muc tieu cua de tai ....................................................................................... 1",
  "1.3. Gioi han va pham vi de tai ............................................................................ 2",
  "1.4. Ket qua du kien dat duoc ............................................................................. 2",
  "CHUONG 2. KIEN THUC NEN TANG ................................................................... 3",
  "2.1. Co so ly thuyet ............................................................................................ 3",
  "2.1.1. Kien truc website hien dai ..................................................................... 3",
  "2.1.2. CSS (Cascading Style Sheets) ................................................................ 4",
  "2.1.3. HTML (HyperText Markup Language) .................................................... 5",
  "2.2. Cong cu su dung ......................................................................................... 6",
  "2.2.1. React.js .................................................................................................... 6",
  "2.2.2. Framework Laravel (doi chieu MVC thuan PHP cua de tai) .................... 7",
  "2.2.3. MySQL / MariaDB ...................................................................................... 8",
  "CHUONG 3. PHAN TICH VA THIET KE HE THONG .............................................. 9",
  "3.1. Khao sat he thong ....................................................................................... 9",
  "3.1.1. Tong quan ve he thong .......................................................................... 9",
  "3.1.2. Danh gia hien trang ............................................................................... 10",
  "3.1.3. Xac dinh yeu cau he thong .................................................................... 11",
  "3.1.4. Ke hoach thuc hien ................................................................................ 12",
  "3.2. Phan tich he thong ...................................................................................... 13",
  "3.2.1. Xac dinh tac nhan (Actor) va chuc nang (Use case) ............................. 13",
  "3.2.2. Bieu do Use Case ................................................................................. 15",
  "3.2.3. Bieu do hoat dong (Activity) .................................................................. 17",
  "3.2.4. Bieu do trinh tu (Sequence) ................................................................... 19",
  "3.2.5. Bieu do lop (Class) ................................................................................ 21",
  "3.3. Thiet ke he thong ........................................................................................ 22",
  "3.3.1. Thiet ke tong the .................................................................................... 22",
  "3.3.2. Thiet ke chi tiet ....................................................................................... 24",
  "CHUONG 4. XAY DUNG CHUONG TRINH .......................................................... 30",
  "4.1. Moi truong va cai dat ................................................................................ 30",
  "4.2. Minh hoa giao dien ..................................................................................... 31",
  "4.3. Kiem thu ..................................................................................................... 32",
  "4.4. Danh gia ket qua ........................................................................................ 33",
  "KET LUAN VA HUONG PHAT TRIEN ................................................................. 34",
  "TAI LIEU THAM KHAO ....................................................................................... 34",
].forEach((l) => children.push(new Paragraph({ children: [T(l, 20)], spacing: { after: 40 } })));
children.push(new Paragraph({ children: [new PageBreak()] }));

// ============ CHUONG 1 ============
children.push(h1("Chuong 1. Tong quan"));
children.push(h2("1.1. Ly do chon de tai"));
children.push(para("Thuong mai dien tu (TMDT) tai Viet Nam tang truong manh me, trong do sach la mat hang co nhu cau on dinh, ty le mua lap lai cao va de van chuyen. Khach hang ngay cang quen voi viec tim kiem, loc theo danh muc / tac gia / nha xuat ban / gia, doc mo ta, xem danh gia, dat hang COD va theo doi don hang online thay vi den nha sach truyen thong."));
children.push(para("Do an chon he thong thuc te tai C:\\xampp\\htdocs\\bookstore de phan tich vi 4 ly do: (1) Nghiep vu day du 2 phan he Site (khach hang) va Admin (quan tri), the hien ro dac trung cua mot website TMDT; (2) Quy mo vua phai nhung bao quat cac van de kho cua phan tich thiet ke: gio hang theo session, dat hang - tru ton kho - hoan kho khi huy, ma giam gia, phan quyen, upload anh, thong ke doanh thu; (3) Ma nguon mo, de do luong: 23 use case, 11 bang du lieu, co san kich ban kiem thu chuc nang (T35: 63/63 PASS) va bao mat (T36: 41/41 PASS); (4) Kien truc MVC thuan PHP de hieu, de ve bieu do UML va de doi chieu voi cac framework hien dai (Laravel) va thu vien front-end (React.js) trong chuong 2."));
children.push(para("Ngoai ra, de tai con co y nghia thuc tien: chu nha sach co the dung san pham de ban sach that (quan ly sach, danh muc cay cha-con, don hang, khuyen mai, banner, binh luan), giam chi phi van hanh so voi thue mat bang; khach hang duoc mua sach moi luc, moi noi."));
children.push(h2("1.2. Muc tieu cua de tai"));
children.push(para("Muc tieu tong quat: phan tich, thiet ke va xay dung hoan chinh website ban sach truc tuyen Bookstore dap ung day du nghiep vu ban hang va quan tri, dam bao tinh dung dan, an toan va kha nang mo rong."));
children.push(bullet("Ve nghiep vu khach hang: cho phep dang ky / dang nhap (BCrypt, khoa 15 phut sau 5 lan sai), tim kiem - loc - sap xep sach, xem chi tiet + sach lien quan, danh gia (chi sach da mua), gio hang (session), quan ly dia chi giao hang, dat hang COD kem ma giam gia, xem lich su don, huy don (hoan ton kho), cap nhat ho so, doi mat khau."));
children.push(bullet("Ve nghiep vu quan tri (/admin): dashboard thong ke, bao cao doanh thu + bieu do Chart.js; CRUD sach (upload bia nen GD, xuat CSV), quan ly danh muc cay cha-con; quan ly don hang theo quy trinh chat pending -> processing -> shipping -> completed (ghi lich su, kem nhanh cancelled/paid), quan ly khach hang (khoa/mo khoa), khuyen mai, binh luan, banner."));
children.push(bullet("Ve ky thuat: ap dung dung mo hinh MVC (Router - Controller - Model - View), Prepared Statements 100% (chong SQLi), htmlspecialchars tai moi noi hien thi (chong XSS), CSRF token moi form (HTTP 419 khi thieu), session HttpOnly + SameSite=Lax, header bao mat, kiem tra MIME that + gioi han 2MB + doi ten ngau nhien khi upload, kiem tra quyen admin o moi controller (403 khi vi pham), trang 500 than thien."));
children.push(bullet("Ve tai lieu: hoan thanh bao cao phan tich thiet ke theo dung muc luc 4 chuong, day du bieu do Use Case, Activity, Sequence, Class va thiet ke CSDL chi tiet den muc cot/khoa."));
children.push(h2("1.3. Gioi han va pham vi de tai"));
children.push(h3("1.3.1. Pham vi chuc nang"));
children.push(bullet("Bao gom: trang chu, danh sach/chi tiet sach, gio hang, checkout COD, don hang, dia chi, ho so, danh gia, dang nhap/dang ky; admin: thong ke, sach, danh muc, don hang, khach hang, coupon, review, banner."));
children.push(bullet("Khong bao gom (nam ngoai pham vi do an): thanh toan online that (hien chi co luong mock payment_mock cho VNPay/Momo, can cau hinh gateway that khi trien khai); email kich hoat tai khoan/quen mat khau tu dong (hien email xac nhan don dung ham mail() cua PHP, can cau hinh SMTP); van chuyen da don vi, doi tra, tich diem thanh vien, chatbot, app mobile."));
children.push(h3("1.3.2. Pham vi cong nghe va du lieu"));
children.push(bullet("Moi truong: XAMPP (Apache + PHP 8.0/8.2+ + MySQL/MariaDB 10.4.32), bat extension GD; chay tai http://localhost/bookstore/; CSDL bookstore, charset utf8mb4_unicode_ci."));
children.push(bullet("Du lieu mau: 7 danh muc (4 cha + 3 con), ~18-29 dau sach (Nha Gia Kim, Dac Nhan Tam, De Men phieu luu ky...), 1 coupon GIAM10 (giam 10%, don toi thieu 100.000d), 2 tai khoan mac dinh admin@bookstore.local / admin123 va test@gmail.com / matkhau123; phi ship 30.000d, mien phi don >= 500.000d (config/config.php)."));
children.push(bullet("Han che da biet: tim kiem dung cot search_key + LIKE (chua dung Fulltext/Elasticsearch); phan trang ITEMS_PER_PAGE = 12; anh bia co the la URL ngoai hoac file upload trong public/uploads/books/."));
children.push(h3("1.3.3. Pham vi nguoi dung"));
children.push(para("He thong phuc vu 3 nhom: (1) Khach vang lai (chua dang nhap): xem, tim kiem, them gio hang; (2) Thanh vien (member): day du quyen mua hang, danh gia, quan ly dia chi/don hang; (3) Quan tri vien (admin): toan quyen quan tri qua /admin. Khong phan cap nhan vien kho/ke toan chi tiet trong pham vi do an."));
children.push(h2("1.4. Ket qua du kien dat duoc"));
children.push(bullet("San pham chay duoc: website http://localhost/bookstore/ hoat dong on dinh ca 2 phan he Site va Admin, import 1 lenh la chay (database/bookstore.sql)."));
children.push(bullet("Tai lieu: bao cao Word phan tich thiet ke dung muc luc, co bang dac ta 23 use case, 3 bieu do Activity, 3 bieu do Sequence, 1 bieu do Class, ERD 11 bang, tu dien du lieu chi tiet, ke hoach va ket qua kiem thu."));
children.push(bullet("Chat luong: 63/63 ca kiem thu chuc nang PASS, 41/41 ca kiem thu bao mat PASS; khong con loi SQLi/XSS/CSRF co ban; toc do tai trang danh sach < 2s voi du lieu mau."));
children.push(bullet("San pham phu: file bookstore.sql, so do CSDL, tai khoan demo, ma giam gia mau, bao cao doanh thu co bieu do de bao ve do an."));

// ============ CHUONG 2 ============
children.push(h1("Chuong 2. Kien thuc nen tang"));
children.push(h2("2.1. Co so ly thuyet"));
children.push(h3("2.1.1. Kien truc website hien dai"));
children.push(para("Kien truc website hien dai thuong to chuc theo 3 tang: (1) Presentation (giao dien: HTML/CSS/JS, template PHP); (2) Application/Business Logic (controller, model, validation, phan quyen, tinh tien, tru kho); (3) Data (MySQL/MariaDB). Luong request di qua Front Controller duy nhat (index.php) -> Router phan tich REQUEST_URI -> goi Controller tuong ung -> Controller goi Model truy van DB -> tra du lieu cho View render HTML. Day chinh la cach Bookstore cai dat (xem bang 2.1)."));
children.push(makeTable(["Thanh phan", "Vai tro tong quat", "Minh chung trong Bookstore"], [
  ["Front Controller", "Diem vao duy nhat, nap config/DB, bat loi, them security headers", "index.php: X-Frame-Options DENY, try/catch PDOException -> 500"],
  ["Router", "Anh xa URL -> Controller@action", "app/core/Router.php; /admin, /sach, /gio-hang, /dat-hang..."],
  ["Controller", "Nhan request, kiem tra quyen/CSRF, dieu phoi", "23 controller: Site + Admin (Book, Order, Coupon, Banner...)"],
  ["Model", "Truy van DB qua PDO Prepared Statements", "BookModel, OrderModel, UserModel, CouponModel..."],
  ["View", "Template PHP + e() chong XSS", "app/views/site/*, admin/*, layout/main.php, admin.php"],
  ["Helper", "Ham dung chung", "e(), csrfCheck(), currentUser(), formatPrice()"],
]));
children.push(para("Uu diem cua mo hinh nay: tach biet moi lo (Separation of Concerns), de bao tri, de viet test, de thay View ma khong cham Model. Ngoai ra he thong con ap dung PRG (Post-Redirect-Get) cho form, session gio hang cho khach vang lai, va co che khoa tai khoan tam thoi chong do mat khau (rate limiting). Ve mat trien khai, mo hinh Client-Server tren XAMPP: Apache phuc vu HTTP, PHP xu ly logic, MariaDB luu tru ben vung."));
children.push(h3("2.1.2. CSS (Cascading Style Sheets)"));
children.push(para("CSS dung de tach noi dung (HTML) khoi trinh bay (mau sac, bo cuc, font, responsive). Bookstore dat CSS trong public/ (style chung + CSS rieng cho Site/Admin), tuan thu nguyen tac cascade - ke thua - do dac hieu (specificity). Cac ky thuat duoc dung:"));
children.push(bullet("Bo cuc: Flexbox/Grid cho luoi sach (book_card partial), header - sidebar - content cho Admin; responsive de hien thi tot tren desktop/tablet/mobile."));
children.push(bullet("Component tai su dung: the sach (anh bia, ten, tac gia, gia / gia khuyen mai), badge giam gia, phan trang, form, bang du lieu admin, alert thanh cong/that bai."));
children.push(bullet("Hieu ung va Chart.js: bieu do doanh thu trong reports/dashboard; banner trang chu (banners) co sort_order va status."));
children.push(bullet("Bao mat hien thi: moi chuoi in ra View deu qua e() = htmlspecialchars(..., ENT_QUOTES, UTF-8) nen CSS khong the bi chen ma doc qua XSS."));
children.push(para("Vi du thuc te: trang danh sach hien thi 12 san pham/trang (ITEMS_PER_PAGE), gia hien thi qua formatPrice() kem dinh dang VND; trang chi tiet co khoi sach lien quan (cung danh muc)."));
children.push(h3("2.1.3. HTML (HyperText Markup Language)"));
children.push(para("HTML la ngon ngu danh dau sieu van ban, to chuc noi dung theo cay DOM (html > head + body; header/nav/main/footer). Bookstore dung template PHP nhung ban chat van xuat ra HTML5 chuan: the semantic (header, nav, section, article, footer), form (input, select, textarea, file), bang (table cho admin), anh (img bia sach)."));
children.push(bullet("Layout: app/views/layout/main.php (Site) va admin.php (Admin) boc cac view con; partials nhu book_card.php tai su dung khap noi."));
children.push(bullet("Form va bao mat: moi form co input hidden _token (CSRF); khi thieu/sai token tra HTTP 419; method POST cho cac hanh dong thay doi du lieu; upload file dung enctype multipart/form-data, gioi han 2MB."));
children.push(bullet("SEO va tra cuu: title/mo ta sach, slug danh muc (van-hoc, tieu-thuyet...), ISBN duy nhat, search_key khong dau phuc vu tim kiem LIKE."));
children.push(bullet("Tiep can: nhan label, thong bao loi than thien, trang 404/500 rieng (views/errors/), khong lo stack trace ra ngoai."));
children.push(h2("2.2. Cong cu su dung"));
children.push(para("Ghi chu quan trong de tranh hieu lam khi bao ve: de cuong mau yeu cau muc 2.2.1 React.js va 2.2.2 Laravel, nhung qua khao sat ma nguon thuc te (README.md, config/, app/core/) xac nhan Bookstore duoc viet bang PHP thuan theo MVC tu cai dat (khong dung Laravel nhu mot dependency) va front-end dung HTML/CSS/JS thuan + Chart.js (khong dung React). Vi vay muc nay trinh bay day du ly thuyet React/Laravel theo de cuong, dong thoi doi chieu ro diem tuong dong/khac biet voi cai dat thuc te de giang vien thay sinh vien nam vung ca hai." , {}));
children.push(h3("2.2.1. React.js"));
children.push(para("React.js la thu vien JavaScript cua Meta de xay dung giao dien theo component, noi bat voi Virtual DOM (chi cap nhat phan thay doi), JSX, state/props, hooks (useState, useEffect) va he sinh thai (React Router, Redux/Zustand, Next.js cho SSR). React phu hop cho SPA, loc/tim kiem khong tai lai trang, gio hang cap nhat realtime."));
children.push(makeTable(["Tieu chi", "React.js (ly thuyet)", "Bookstore thuc te"], [
  ["Render", "Client-side render, SPA", "Server-side render bang PHP View (SEO tot, don gian, khong can build)"],
  ["Tuong tac", "State, re-render cuc bo", "Form POST + redirect truyen thong; JS nhe cho menu, slider, Chart.js"],
  ["Component", "JSX component", "PHP partials (book_card.php, layout) - tu tuong tuong tu component"],
  ["Dinh huong", "De tai co the nang cap: chuyen khoi loc sach/gio hang sang React hoac giu PHP + fetch API", "Hien tai chua dung React (khong co package.json, node_modules)"],
]));
children.push(para("Nhan xet doi chieu: viec chua dung React la hop ly voi quy mo do an va yeu cau chay ngay tren XAMPP khong can build; neu mo rong (loc realtime, PWA, app mobile), co the boc API JSON tu cac Model san co va viet front-end React ma khong phai sua DB."));
children.push(h3("2.2.2. Framework Laravel"));
children.push(para("Laravel la framework PHP noi tieng theo MVC, cung cap san Router, Eloquent ORM, Blade, Middleware (auth/csrf), Migration/Seeder, Artisan CLI. Triet ly cua Laravel la 'quy uoc hon cau hinh', giup phat trien nhanh va an toan mac dinh."));
children.push(makeTable(["Thanh phan Laravel", "Tuong duong trong Bookstore (MVC thuan)", "Nhan xet"], [
  ["routes/web.php + Router", "app/core/Router.php + index.php", "Bookstore tu viet Router don gian, du dung cho ~23 controller"],
  ["Controller + Middleware", "app/controllers/* + kiem tra quyen admin dau moi action", "Tuong duong middleware auth/admin; tra 403 khi vi pham"],
  ["Eloquent ORM", "app/core/Model.php + PDO Prepared Statements", "Khong dung ORM, viet SQL truc tiep nhung an toan vi 100% prepared"],
  ["Blade + {{ }}", "View PHP + e()", "e() tuong duong {{ }} (auto-escape chong XSS)"],
  ["CSRF @csrf", "csrfCheck() + hash_equals, loi 419", "Tuong duong, dam bao moi form deu co token"],
  ["Migration/Seeder", "database/bookstore.sql + bookstore.sql goc", "Chua dung migration, import SQL truc tiep - chap nhan duoc cho do an"],
]));
children.push(para("Ket luan doi chieu: Bookstore la ban 'Laravel thu nho' tu cai dat, giup sinh vien hieu ban chat MVC thay vi chi biet dung san framework; khi can mo rong that, co the chuyen nguyen khoi Model/View sang Laravel ma giu nguyen CSDL."));
children.push(h3("2.2.3. MySQL / MariaDB"));
children.push(para("He thong dung MySQL/MariaDB 10.4.32 (XAMPP), charset utf8mb4_unicode_ci de luu tieng Viet co dau. CSDL ten bookstore gom 11 bang (chi tiet o 3.3.2): users, categories (tu tham chieu parent_id), books (khoa ngoai category_id, unique ISBN), addresses, coupons (unique code), orders, order_items, order_status_history, payments, reviews (unique user_id+book_id), banners. Toan bo quan he co FK + ON DELETE CASCADE/SET NULL hop ly; chi muc (index) duoc danh cho cot hay loc (title, status, created_at, sold_count, user_id...). Cau hinh ket noi tap trung o config/config.php + config/database.php (DB_HOST localhost, DB_PORT 3306, DB_NAME bookstore, DB_USER root, DB_PASS rong)."));
children.push(bullet("Uu diem ap dung: ACID cho dat hang/tru kho; prepared statements chong SQLi; search_key khong dau giup tim kiem LIKE nhanh ma khong can Fulltext o quy mo nho."));
children.push(bullet("Gioi han: tim kiem LIKE '%...%' se cham khi hang tram nghin sach - huong mo rong la Fulltext index hoac Elasticsearch; chua co replica/backup tu dong - can lam khi len production."));

children.push(codeBlock([
  "// config/config.php (trich - minh chung thuc te)",
  "define('DB_HOST','localhost'); define('DB_NAME','bookstore');",
  "define('SHIPPING_FEE',30000); define('FREE_SHIPPING_MIN',500000);",
  "define('ITEMS_PER_PAGE',12); define('APP_NAME','Kim Dong Bookstore');",
]));

// ============ CHUONG 3 ============
children.push(h1("Chuong 3. Phan tich va thiet ke he thong"));
children.push(h2("3.1. Khao sat he thong"));
children.push(h3("3.1.1. Tong quan ve he thong"));
children.push(para("Bookstore la website TMDT ban sach kieu B2C gom 2 phan he: (A) Site danh cho khach (trang chu, sach, chi tiet, gio hang, thanh toan, don hang cua toi, dia chi, ho so, danh gia) va (B) Admin danh cho quan tri (/admin: dashboard, sach, danh muc, don hang, khach hang, khuyen mai, binh luan, banner, bao cao). He thong chay tren XAMPP tai http://localhost/bookstore/ (RewriteBase /bookstore/ ve index.php), du lieu luu o MariaDB (database/bookstore.sql), anh upload luu o public/uploads/{books,banners,avatars} (ngoai ra anh bia cho phep dung URL ngoai)."));
children.push(para("Quy trinh nghiep vu chinh (end-to-end, doi chieu code that): Khach tim/loc sach -> xem chi tiet -> them gio hang (session, khoa bookId:volume, toi da 99) -> dang nhap (neu chua) -> chon/tao dia chi giao hang -> ap coupon GIAM10 (neu subtotal >= min_order 100.000d, con luot, con han) -> xac nhan dat hang. Voi COD, don sinh ra o trang thai processing; voi VNPay/Momo, don sinh ra o pending roi qua trang thanh-toan/online (payment_mock) de thanh paid. He thong tinh tien (subtotal theo gia snapshot - discount + ship 30.000d, free neu sau giam >= 500.000d), tru ton kho + tang sold_count + tang used_count coupon trong transaction, sinh ma BKyyyymmdd-xxxx, ghi order_status_history, gui mail xac nhan, xoa gio. Admin mo chi tiet don va chon tu do 1 trong 4 trang thai processing/shipping/completed/cancelled (completed/cancelled la ket thuc; huy se hoan kho) - moi buoc ghi lich su kem nguoi doi (admin/system/member). Khach nhan hang (completed) thi duoc danh gia (AUTO_APPROVE_REVIEWS=true, moi user 1 review/sach). Admin xem bao cao doanh thu (thanh bar CSS thuan + top sach/khach), xuat CSV sach."));
children.push(para("So lieu khao sat nhanh tu SQL mau: 7 danh muc (4 cha + 3 con), 18 dau sach gia 65.000 - 150.000 VND, ton kho 18-50/cuon, 1 don mau BK20260819-0001 (3 mon, subtotal 280.000 + ship 30.000 = 310.000, trang thai completed, du 4 moc lich su pending/processing/shipping/completed), 2 user (admin@bookstore.local, test@gmail.com), 1 dia chi mac dinh, 1 banner, 1 coupon GIAM10 (percent 10%, da dung 3/100)."));
children.push(h3("3.1.2. Danh gia hien trang"));
children.push(makeTable(["Khia canh", "Diem manh (kha nang hien tai)", "Ton tai / Rui ro"], [
  ["Nghiep vu", "Du vong doi mua-ban-co ban; quy trinh don chat, co lich su; coupon, banner, review day du", "Chua co thanh toan that, doi tra, van chuyen da doi tac; chua phan quyen NV kho/ke toan"],
  ["Ky thuat", "MVC gon, router tap trung, prepared 100%, e() moi noi, CSRF 419, khoa login 5 lan/15p", "Tim kiem LIKE se cham khi lon; upload phu thuoc GD; mail() can SMTP that"],
  ["Du lieu", "11 bang chuan hoa, FK day du, index hop ly, du lieu mau tieng Viet tot", "Anh bia lan URL ngoai + file noi bo (kho dong bo); thieu bang ton kho lich su, nhat ky admin"],
  ["Van hanh", "Cai 1 lenh, tai khoan demo ro rang, log storage/logs/app.log, loi 500 than thien", "Chua co backup, giam sat, CI/CD; phan quyen moi admin/member"],
  ["Bao mat", "41/41 ca T36 PASS (SQLi, XSS, CSRF, upload, path traversal, phan quyen)", "Can them 2FA, captcha, rate-limit nang cao khi len production"],
]));
children.push(para("Danh gia chung: he thong dat muc 'dung duoc ngay' cho nha sach nho/vua; kien truc du sach de nang cap dan (them payment gateway, Fulltext search, phan quyen chi tiet) ma khong dap di xay lai."));
children.push(h3("3.1.3. Xac dinh yeu cau he thong"));
children.push(h4("a) Yeu cau chuc nang (Functional - tong hop tu 23 use case)"));
children.push(bullet("QL nguoi dung: UC01 Dang ky, UC02 Dang nhap (khoa 15p sau 5 sai), UC03 Dang xuat, UC04 Cap nhat ho so, UC05 Doi mat khau, UC06 Quan ly dia chi (them/sua/xoa/dat mac dinh)."));
children.push(bullet("Mua hang: UC07 Tim kiem/loc/sap xep/phan trang sach, UC08 Xem chi tiet + sach lien quan, UC09 Quan ly gio hang (them/sua/xoa theo session), UC10 Ap coupon, UC11 Dat hang COD, UC12 Xem lich su don, UC13 Huy don (hoan kho), UC14 Danh gia sach da mua."));
children.push(bullet("Quan tri: UC15 Dashboard + bao cao doanh thu (bar CSS, top 10), UC16 CRUD sach + upload bia + xuat CSV, UC17 QL danh muc cay, UC18 QL don hang + cap nhat 4 trang thai + lich su, UC19 QL khach hang (khoa/mo), UC20 QL coupon (modal them/sua), UC21 Duyet/an review, UC22 QL banner, UC23 Dang nhap admin/phan quyen."));
children.push(h4("b) Yeu cau phi chuc nang (Non-functional)"));
children.push(bullet("Hieu nang: tai trang danh sach < 2s (du lieu mau), chi muc san cho cot loc; phan trang 12/trang."));
children.push(bullet("An toan: OWASP co ban (SQLi, XSS, CSRF, upload, IDOR, path traversal) da PASS; mat khau BCrypt; session HttpOnly+SameSite=Lax."));
children.push(bullet("Do tin cay: transaction khi dat hang (tru kho + tao don + lich su dong bo), hoan kho khi huy; log loi day du; trang 500 khong lo loi."));
children.push(bullet("Kha dung - kha bao tri: chay XAMPP khong can build; cau truc thu muc ro; config tap trung; ma tien Viet dinh dang thong nhat."));
children.push(bullet("Rang buoc: PHP 8.x, MariaDB 10.4, GD enabled; file upload <= 2MB, MIME that, doi ten ngau nhien; ma don duy nhat code."));

children.push(h3("3.1.4. Ke hoach thuc hien"));
children.push(makeTable(["Giai doan", "Cong viec", "San pham"], [
  ["T1. Khao sat (1 tuan)", "Cai XAMPP, import DB, di het luong Site+Admin, chup man hinh", "Bien ban khao sat + tai khoan demo"],
  ["T2. Phan tich (1.5 tuan)", "Xac dinh actor/use case, ve Use Case, Activity, Sequence, Class", "Chuong 3.2 + bo UML"],
  ["T3. Thiet ke (1.5 tuan)", "ERD + tu dien 11 bang, thiet ke giao dien, luat nghiep vu", "Chuong 3.3 + prototype"],
  ["T4. Lap trinh (3 tuan)", "Hoan thien MVC (19 controller, 9 model), gio hang session, checkout COD+mock online, coupon, admin, upload, bar CSS bao cao", "Source tai C:\\xampp\\htdocs\\bookstore"],
  ["T5. Kiem thu (1 tuan)", "T35 (63 ca chuc nang) + T36 (41 ca bao mat), sua loi", "63/63 PASS, 41/41 PASS"],
  ["T6. Viet bao cao (1 tuan)", "Viet Word 4 chuong + ket luan, chuan hoa trich dan", "File Word ban dang doc"],
]));
children.push(para("Phan cong goi y (nhom 2-3 SV): 1 nguoi phu trach Site (gio hang/checkout/review), 1 nguoi Admin (sach/danh muc/don/bao cao), 1 nguoi CSDL + bao mat + tai lieu. Tien do kiem soat bang checklist use case (23 muc) va 2 bo test T35/T36."));

children.push(h2("3.2. Phan tich he thong"));
children.push(h3("3.2.1. Xac dinh cac tac nhan (Actor) va chuc nang (Use case)"));
children.push(makeTable(["Actor", "Mo ta", "Use case lien quan"], [
  ["Khach vang lai (Guest)", "Chua dang nhap, dung session gio hang", "UC07, UC08, UC09 (them gio), UC01, UC02"],
  ["Thanh vien (Member)", "Da dang nhap, mua hang day du", "UC03-UC14 (tru quyen admin)"],
  ["Quan tri vien (Admin)", "Toan quyen /admin, dang nhap rieng", "UC15-UC23"],
  ["He thong (System)", "Tac nhan phu: gio, log, mail, payment mock", "Sinh ma don, tinh ship, khoa login, ghi lich su"],
]));
children.push(para("Bang tong hop 23 use case (danh so de tien truy vet voi controller/model):"));
children.push(makeTable(["Ma", "Use case", "Actor", "Controller/Model chinh"], [
  ["UC01", "Dang ky tai khoan", "Guest", "AuthController + UserModel"],
  ["UC02", "Dang nhap (khoa 15p sau 5 sai)", "Guest/Member/Admin", "AuthController + UserModel"],
  ["UC03", "Dang xuat", "Member/Admin", "AuthController"],
  ["UC04", "Cap nhat ho so", "Member", "ProfileController"],
  ["UC05", "Doi mat khau", "Member", "ProfileController"],
  ["UC06", "Quan ly dia chi giao hang", "Member", "AddressController + AddressModel"],
  ["UC07", "Tim kiem / loc / sap xep / phan trang", "Guest/Member", "BookController + BookModel (search_key)"],
  ["UC08", "Xem chi tiet + sach lien quan", "Guest/Member", "BookController + ReviewModel"],
  ["UC09", "Quan ly gio hang (session)", "Guest/Member", "CartController + CartModel"],
  ["UC10", "Ap ma giam gia", "Member", "OrderController::checkout + CouponModel::findByCode/isValid/calculateDiscount"],
  ["UC11", "Dat hang (COD ra processing, online ra pending->paid)", "Member", "OrderController::checkout/onlinePayment + OrderModel::create (transaction)"],
  ["UC12", "Xem lich su / chi tiet don", "Member", "OrderController::history/detail"],
  ["UC13", "Huy don (hoan kho, chi pending/paid/processing)", "Member", "OrderController::cancel + OrderModel::cancel"],
  ["UC14", "Danh gia sach (don completed + chua review)", "Member", "ReviewController::store + ReviewModel::hasPurchased"],
  ["UC15", "Dashboard + bao cao doanh thu (bar CSS + top 10)", "Admin", "Admin\\DashboardController, Admin\\ReportController + OrderModel::reportSummary/revenueByDay/topBooks/topCustomers"],
  ["UC16", "CRUD sach + upload bia + xuat CSV", "Admin", "Admin\\BookController + BookModel"],
  ["UC17", "Quan ly danh muc cay cha-con", "Admin", "Admin\\CategoryController + CategoryModel"],
  ["UC18", "Quan ly don + chuyen trang thai + lich su", "Admin", "Admin\\OrderController + OrderModel"],
  ["UC19", "Quan ly khach hang (khoa/mo)", "Admin", "Admin\\UserController + UserModel"],
  ["UC20", "Quan ly khuyen mai (coupon)", "Admin", "Admin\\CouponController + CouponModel"],
  ["UC21", "Duyet / an binh luan", "Admin", "Admin\\ReviewController"],
  ["UC22", "Quan ly banner", "Admin", "Admin\\BannerController + BannerModel"],
  ["UC23", "Dang nhap admin + phan quyen (403)", "Admin", "Admin\\AuthController + moi Admin controller"],
]));
children.push(para("Dac ta chi tiet 2 use case tieu bieu (doi chieu tung dong code, cac UC con lai trinh bay rut gon trong phu luc bao ve):"));
children.push(makeTable(["Muc", "UC11 - Dat hang", "UC18 - Cap nhat don (Admin)"], [
  ["Tien dieu kien", "Da dang nhap, gio co hang, co dia chi, ton kho du", "Admin da dang nhap; don chua ket thuc (chua completed/cancelled)"],
  ["Luong chinh", "1.Chon dia chi 2.Ap coupon (findByCode+isValid) 3.Xac nhan 4.He thong tinh tien, tru kho/sold_count/used_count trong transaction, sinh ma, ghi lich su, gui mail, xoa gio", "1.Mo chi tiet don 2.Chon 1 trong 4 trang thai (processing/shipping/completed/cancelled) + ghi chu 3.Ghi lich su (changed_by=admin)"],
  ["Luong thay the", "Online: don pending -> trang payment_mock -> paid. Coupon het han/toi thieu/het luot -> tu choi; ton kho khong du -> chan; chua login -> chuyen login", "Chon cancelled -> di nhanh huy (hoan kho, payments ve failed); don FINAL -> chan; chon trung trang thai hien tai -> bao loi"],
  ["Hau dieu kien", "COD: don processing; Online: pending (roi paid); gio hang rong; mail xac nhan da gui", "Don o trang thai moi + them 1 dong lich su"],
  ["Quy tac", "Ship 30k, free neu (subtotal-discount) >= 500k; ma don unique BKyyyymmdd-xxxx; transaction all-or-nothing", "EDITABLE = processing/shipping/completed/cancelled; member chi duoc huy pending/paid/processing (canCancelOrder)"],
]));
children.push(h3("3.2.2. Bieu do Use Case"));
children.push(para("Hinh 3.1 (mo ta de sinh vien dung lai trong Word/Visio/StarUML): he thong co 1 bien (System Boundary = Bookstore), 3 actor chinh ben ngoai (Guest, Member ke thua Guest, Admin doc lap) va cum use case gom 4 goi: (1) Tai khoan & Ho so (UC01-UC06), (2) Mua sam (UC07-UC14), (3) Quan tri danh muc/sach (UC15-UC17, UC20, UC22), (4) Quan tri don/khach/review (UC18, UC19, UC21). Quan he include: UC11 include UC10 (ap coupon) va UC06 (chon dia chi); UC14 include kiem tra 'da mua'; UC18 include ghi lich su. Quan he extend: UC10 extend UC11 (khong bat buoc); tim kiem nang cao extend UC07."));
children.push(codeBlock([
  "  [Guest] ----> (UC07 Tim kiem)   [Member] ----> (UC11 Dat hang COD)",
  "    |  \u25b6 ke thua      | include (UC10 Ap coupon, UC06 Chon dia chi)",
  "[Member] ----> (UC09 Gio hang)   (UC11) <include> (Kiem tra ton kho)",
  "  [Admin] ----> (UC16 CRUD sach)  [Admin] ----> (UC18 Duyet don)",
  "              (UC17 Danh muc)                <include> (Ghi lich su)",
  "              (UC15 Dashboard)   (UC14 Danh gia) <include> (Kiem tra da mua)",
]));
children.push(para("Cach doc: Guest chi thay goi Mua sam + Tai khoan co ban; sau khi dang nhap thanh Member mo them checkout/danh gia/lich su; Admin thay toan bo goi Quan tri nhung khong dat hang ho khach (dam bao phan quyen). Moi duong noi tu Admin controller deu co guard 'khong phai admin -> 403', tuong duong Test T36 ve truy cap trai phep."));
children.push(h3("3.2.3. Bieu do hoat dong (Activity)"));
children.push(h4("AC01 - Dang ky / Dang nhap (co khoa tai khoan)"));
children.push(codeBlock([
  "Bat dau -> Nhap email+mat khau -> [Sai? dem loi] -> (qua 5 lan) -> Khoa 15 phut -> Bao loi -> Ket thuc",
  "                 | dung -> Tao session (HttpOnly, SameSite=Lax) -> Chuyen trang chu -> Ket thuc",
  "Dang ky: Nhap ten/email/phone/mk -> Validate (email unique, mk manh) -> BCrypt hash -> Luu users -> Tu dong login -> Ket thuc",
]));
children.push(h4("AC02 - Dat hang (quan trong nhat, khop OrderController::checkout)"));
children.push(codeBlock([
  "Bat dau -> Duyet sach -> Them gio (session, khoa bookId:volume, max 99) -> Dang nhap? [chua -> /dang-nhap]",
  " -> Chon dia chi (mac dinh / tao moi) -> Ap coupon? [co -> findByCode (status+han o SQL) -> isValid (min_order, used<max) -> calculateDiscount]",
  " -> Chon COD/VNPay/Momo -> Xac nhan -> BEGIN TRANSACTION: kiem tra ton kho -> tru stock, tang sold_count",
  " -> tao orders (COD=processing / online=pending) + order_items (gia snapshot) -> tang used_count -> ghi lich su -> COMMIT",
  " -> gui mail xac nhan -> xoa gio -> [COD -> order_success] [online -> payment_mock -> paid -> order_success] -> Ket thuc",
  " [Loi bat ky buoc nao -> ROLLBACK -> bao loi + giu gio -> Ket thuc]",
  " [Huy don sau do (member): chi pending/paid/processing (canCancelOrder) -> hoan stock -> payments failed -> ghi cancelled -> Ket thuc]",
]));
children.push(h4("AC03 - Admin cap nhat don (khop Admin\\OrderController::status)"));
children.push(codeBlock([
  "Bat dau -> Admin mo /admin/don-hang (loc status/keyword/ngay) -> Mo chi tiet",
  " -> Don FINAL (completed/cancelled)? [dung -> khoa form, bao 'da ket thuc' -> Ket thuc]",
  " -> Chon 1 trong 4 trang thai (processing/shipping/completed/cancelled) + ghi chu -> Submit",
  " -> (cancelled) -> kiem tra CANCELLABLE -> OrderModel::cancel (hoan kho, payments failed, ghi cancelled) -> Ket thuc",
  " -> (con lai) -> updateStatus + recordStatus(changed_by=admin) -> flash success -> Ket thuc",
  " [Trung trang thai hien tai / don FINAL / huy don khong hop le -> bao loi -> Ket thuc]",
]));
children.push(para("Y nghia: 3 activity bao phu tron 'duong di du lieu' tu khach den kho; moi nhanh re deu anh xa truc tiep thanh if/else trong CartController/OrderController va Admin\\OrderController, giup kiem thu T35 de bao phu 63 ca."));

children.push(h3("3.2.4. Bieu do trinh tu (Sequence)"));
children.push(h4("SQ01 - Dang nhap (Site)"));
children.push(codeBlock([
  "Browser -> Router: POST /dang-nhap (email, pw, _token)",
  "Router -> AuthController::login(): csrfCheck() -> UserModel::findByEmail()",
  "AuthController -> UserModel: dem loi, kiem tra lock, password_verify()",
  "UserModel --> AuthController: user / loi",
  "AuthController -> Session: tao session, regenerate id",
  "AuthController --> Browser: redirect / (200 + Set-Cookie HttpOnly; SameSite=Lax)",
  "[Sai token -> 419] [Sai 5 lan -> lock 15p] [khong ton tai -> bao loi chung chung (chong doan user)]",
]));
children.push(h4("SQ02 - Dat hang (khop ham that)"));
children.push(codeBlock([
  "Browser -> OrderController::checkout(): lay CartModel::details()/subtotal + AddressModel::getByUser + CouponModel",
  "OrderController -> CouponModel::findByCode() -> isValid(subtotal) -> calculateDiscount()",
  "OrderController -> OrderModel::create(): BEGIN; tru stock/tang sold_count; INSERT orders+order_items",
  "OrderModel -> CouponModel::incrementUsed(); recordStatus(pending/processing, system); COMMIT",
  "OrderModel --> OrderController: orderId --> Browser: redirect order_success (COD) hoac payment_mock (online)",
  "[Online: POST thanh-toan/online -> recordPayment (payments success + orders=paid + lich su) -> order_success]",
  "[Exception -> ROLLBACK -> trang 500 than thien + writeLog(storage/logs/app.log)]",
]));
children.push(h4("SQ03 - Admin CRUD sach (them moi co upload)"));
children.push(codeBlock([
  "Browser(admin) -> Admin\\BookController::store(): guard admin (403 neu fail) + csrfCheck()",
  "Controller -> Validate: title, category_id, price, ISBN unique, file (finfo MIME that, <=2MB)",
  "Controller -> GD: nen anh, doi ten ngau nhien -> public/uploads/books/xxx.jpg",
  "Controller -> BookModel::create(...) incl. search_key (bo dau) --> Browser: redirect /admin/books + flash success",
  "[MIME gia / qua lon / khong phai anh -> tu choi + xoa file tam] [ISBN trung -> 422]",
]));
children.push(para("Nhan xet: ca 3 sequence deu tuan thu 'Controller mong - Model day': controller chi dieu phoi + bao mat, moi SQL nam trong Model qua PDO prepared; nho vay ma viec ve them sequence cho UC khac (huy don, danh gia, khoa user) chi la lap lai mau."));

children.push(h3("3.2.5. Bieu do lop (Class)"));
children.push(para("Hinh 3.5 - Lop thuc the (suy tu 11 bang + MVC): Users 1-n Addresses, Users 1-n Orders, Users 1-n Reviews; Categories tu hop (1-n, parent_id) 1-n Books; Books 1-n OrderItems + 1-n Reviews; Coupons 1-n Orders (SET NULL khi xoa); Addresses 1-n Orders (SET NULL khi xoa); Orders 1-n OrderItems + 1-n OrderStatusHistory + 0-n Payments; Banners doc lap. Cac lop dieu khien: Router; Controller (cha: view/isPost) <- 19 controller con (9 Site + 10 Admin); Model (cha: query/fetchAll/fetchOne qua PDO) <- 9 model con. Lop tien ich: helpers/functions (e, csrfToken/csrfCheck, requireLogin/requireAdmin, formatPrice, uploadImage/compressImage, slugify/normalizeSearchKey, paginationLinks)."));
children.push(codeBlock([
  "Users(1)---<(n)Addresses  Users(1)---<(n)Orders  Categories(1)---<(n)Books",
  "Books(1)---<(n)OrderItems  Orders(1)---<(n)OrderItems  Orders(1)---<(n)OrderStatusHistory",
  "Coupons(1)---<(n)Orders  Books(1)---<(n)Reviews  Users(1)---<(n)Reviews",
  "Categories(1)-self-<(n)Categories(parent_id)   Orders(1)---(0..n)Payments",
  "Router --> *Controller --> *Model --> DB(PDO, EMULATE_PREPARES=false) ; View <.. Controller : render + e()",
]));
children.push(makeTable(["Lop (bang)", "Thuoc tinh chinh", "Phuong thuc tieu bieu (ten that)"], [
  ["Users", "id, name, email(UQ), phone, password(BCrypt), role, avatar, status", "findByEmail(), create(), toggleStatus(), getStats()"],
  ["Categories", "id, parent_id(FK self, NULL/0=goc), name, slug(UQ), status", "getAllAdmin(), slugExists(), countChildren(), countBooks()"],
  ["Books", "id, category_id(FK), title, author, publisher, isbn(UQ), price, sale_price, stock, volumes, sold_count, search_key, cover_image", "search(), getNewest(), getBestSelling(), getRelated(), toggleStatus(), delete(), hasOrderItems()"],
  ["Orders", "id, code(UQ), user_id, address_id, coupon_id, subtotal, discount, shipping_fee, total, payment_method, status", "create(), updateStatus(), recordStatus(), cancel(), getAllAdmin(), reportSummary(), revenueByDay(), topBooks(), topCustomers(), stats()"],
  ["Coupons", "code(UQ), type, value, min_order, max_uses, used_count, dates, status", "findByCode(), isValid(), calculateDiscount(), incrementUsed(), codeExists()"],
  ["Reviews", "user_id+book_id(UQ), rating, comment, status", "hasPurchased(), getSummary(), getForBook(), setStatus()"],
]));
children.push(para("Rang buoc toan ven (trich bookstore.sql): FK CASCADE cho du lieu phu thuoc (xoa user -> xoa dia chi/don/review; xoa don -> xoa items/lich su/payment); SET NULL cho lien ket mem (xoa dia chi/coupon giu lai don de doi soat; parent_id danh muc ve NULL); UNIQUE chong trung (email, slug, isbn, code don, code coupon, cap user-book review)."));
children.push(para("Luu y doi chieu: tong 19 controller (9 Site: Address, Auth, Book, Cart, Category, Order, Profile, Review, Site + 10 Admin: Auth, Banner, Book, Category, Coupon, Dashboard, Order, Report, Review, User) va 9 model; moi Admin controller deu co beforeAction()->requireAdmin()."));

children.push(h2("3.3. Thiet ke he thong"));
children.push(h3("3.3.1. Thiet ke tong the"));
children.push(h4("a) Kien truc trien khai (3-tier tren XAMPP, khop code that)"));
children.push(codeBlock([
  "[Browser] --HTTP--> [Apache :80, DocumentRoot C:/xampp/htdocs, RewriteBase /bookstore/ --> index.php]",
  "index.php: Front Controller - nap config/database, security headers (DENY/nosniff/Referrer),",
  "  bat loi (PDOException/500 than thien + writeLog) --> new Router(REQUEST_URI) --> dispatch()",
  "Router: doi chieu bang ROUTES (~60 route tinh) --> Controller@action (+ co che doan admin/site mac dinh)",
  "Controller: beforeAction (requireLogin/requireAdmin) + csrfCheck + validate --> Model (PDO prepared, EMULATE_PREPARES=false)",
  "  --> [MariaDB 10.4:3306 bookstore, utf8mb4_unicode_ci] --> setLayout + view() --> HTML ve Browser",
  "[public/css/style.css + public/js/main.js + public/uploads/*] phuc vu tinh | [storage/logs/app.log] ghi loi",
  "Helper dung chung: url()/asset(), e(), csrfToken()/csrfCheck(), currentUser(), formatPrice(), uploadImage(), slugify(), paginationLinks()",
]));
children.push(h4("b) Cau truc thu muc (liet ke that tu source)"));
children.push(codeBlock([
  "bookstore/ index.php | .htaccess (RewriteBase /bookstore/) | config/config.php, database.php",
  "  database/bookstore.sql (+ ban root bookstore.sql) | README.md | PhanTichThietKe_Bookstore.docx",
  "app/core/ Router.php, Controller.php (view/isPost), Model.php (query/fetchAll/fetchOne/lastInsertId)",
  "app/controllers/ Address, Auth, Book, Cart, Category, Order, Profile, Review, Site (9 file)",
  "app/controllers/Admin/ Auth, Banner, Book, Category, Coupon, Dashboard, Order, Report, Review, User (10 file)",
  "app/models/ Address, Banner, Book, Cart, Category, Coupon, Order, Review, User (9 file)",
  "app/helpers/functions.php | app/views/site/ (14 file: home, books, book_detail, cart, checkout,",
  "  orders, order_detail, order_success, addresses, profile, login, register, payment_mock, 404)",
  "app/views/admin/ (13 file + partials/coupon_form_fields.php: dashboard, books, book_form, categories,",
  "  orders, order_detail, users, user_detail, coupons, reviews, banners, reports, login)",
  "app/views/layout/main.php, admin.php + app/views/partials/book_card.php + app/views/errors/404.php, 500.php",
  "public/css/style.css | public/js/main.js | public/uploads/avatars/, banners/ (+ books/ tu tao khi upload bia)",
  "storage/logs/app.log | tools/backfill_search_key.php + tools/docgen/ (script sinh bao cao Word nay)",
]));
children.push(h4("c) Thiet ke bao mat tong the (map voi T36, ten ham that)"));
children.push(makeTable(["Lop bao mat", "Bien phap trong Bookstore", "Kiem chung"], [
  ["Xac thuc", "BCrypt (password_hash/verify); khoa 15p sau 5 sai (MAX_ATTEMPTS/LOCK_MINUTES, session login_attempts/admin_login_attempts); session_regenerate_id(true); login site/admin rieng (admin bat role=admin)", "T36: do mk, tai su dung session"],
  ["Phan quyen", "requireAdmin() dau moi Admin controller (chua login -> /admin/dang-nhap, sai role -> 403); member chi thay don/dia chi cua minh (so user_id, IDOR check)", "T36: truy cap /admin trai phep"],
  ["Input", "Prepared 100% (Model::query, EMULATE_PREPARES=false); e() moi echo; validate server-side; csrfCheck() -> 419 khi thieu token", "T36: SQLi, XSS, CSRF 41 ca"],
  ["Upload", "uploadImage(): mime_content_type that trong {jpeg,png,webp,gif}, <=2MB, GD nen maxWidth 900, ten bin2hex(random_bytes(8)); avatar co ham rieng o ProfileController", "T36: upload php gia -> tu choi (INVALID_TYPE/SIZE)"],
  ["Van hanh", "Header DENY/nosniff/Referrer; trang 500 than thien + writeLog; .htaccess chi cho index.php xu ly (khong list thu muc)", "T36: path traversal, info leak"],
]));
children.push(h4("d) Bang dinh tuyen (trich app/core/Router.php, ~60 route)"));
children.push(para("Nhom Site (ai cung vao duoc, mot so can requireLogin):"));
children.push(makeTable(["URL", "Controller@action", "Ghi chu"], [
  ["/, /sach, /sach/chi-tiet, /sach/tac-gia, /sach/nxb, /danh-muc", "Site/Book/Category", "Trang chu, tim kiem, chi tiet, loc tac gia/NXB/danh muc"],
  ["/gio-hang(+/them/cap-nhat/xoa)", "CartController", "POST + CSRF, luu session"],
  ["/thanh-toan(+/thanh-cong, /online)", "OrderController", "checkout/onlinePayment/success; online chi don pending cua minh"],
  ["/don-hang(+/chi-tiet, /huy)", "OrderController", "history/detail/cancel (cancel chi pending/paid/processing)"],
  ["/dang-ky, /dang-nhap, /dang-xuat", "AuthController", "register/login/logout + khoa 5 lan/15p"],
  ["/ho-so(+/mat-khau), /dia-chi(+/them/sua/xoa/mac-dinh)", "Profile/AddressController", "Ho so, avatar, so dia chi"],
  ["/danh-gia(+/sua/xoa)", "ReviewController", "Chi don completed, 1 review/sach"],
]));
children.push(para("Nhom Admin (tat ca qua requireAdmin, POST kem CSRF):"));
children.push(makeTable(["URL", "Controller@action", "Ghi chu"], [
  ["/admin, /admin/bao-cao", "Dashboard/ReportController", "index: counters + bao cao theo from/to"],
  ["/admin/sach(+/them/sua/xoa/xoa-han/xuat)", "Admin\\BookController", "index/create/update/delete/deletePermanently/export CSV"],
  ["/admin/danh-muc(+/them/sua/xoa)", "Admin\\CategoryController", "Cay cha-con, chan xoa khi con con/sach"],
  ["/admin/don-hang(+/chi-tiet/trang-thai/huy)", "Admin\\OrderController", "status(): 4 trang thai + nhanh huy hoan kho"],
  ["/admin/khach-hang(+/chi-tiet/khoa)", "Admin\\UserController", "toggle khoa/mo khoa"],
  ["/admin/khuyen-mai(+/them/sua/xoa)", "Admin\\CouponController", "Modal them/sua, validate regex + percent<=100"],
  ["/admin/binh-luan(+/duyet/xoa)", "Admin\\ReviewController", "toggle an/hien"],
  ["/admin/banner(+/them/sua/xoa)", "Admin\\BannerController", "Upload hoac URL anh, sort_order"],
  ["/admin/dang-nhap, /admin/dang-xuat", "Admin\\AuthController", "Bat role=admin"],
]));
children.push(h3("3.3.2. Thiet ke chi tiet"));
children.push(h4("a) Thiet ke CSDL (ERD + tu dien - cot loi cua do an, doi chieu bookstore.sql)"));
children.push(para("Hinh 3.6 ERD (mo ta de ve lai): USERS trung tam, toa ra ADDRESSES (1-n, CASCADE), ORDERS (1-n, CASCADE), REVIEWS (1-n, CASCADE); CATEGORIES tu de quy qua parent_id (cha-con) tro toi BOOKS (1-n); BOOKS ket voi ORDERS qua ORDER_ITEMS (n-n, kem quantity/price/volume: price la snapshot gia tai luc mua, volume la so tap); ORDERS co them ORDER_STATUS_HISTORY (audit moi lan doi trang thai: status/note/changed_by system|admin|member) va PAYMENTS (0-n, luu ket qua mock VNPay/Momo); COUPONS (1-n) va ADDRESSES (1-n) gan vao ORDERS bang FK SET NULL de xoa khuyen mai/dia chi van giu lich su don; BANNERS doc lap cho hero trang chu."));
children.push(para("Tu dien du lieu (kieu theo MariaDB thuc te trong bookstore.sql, charset utf8mb4_unicode_ci):"));
children.push(makeTable(["Bang", "Cot chinh (kieu - rang buoc)", "Ghi chu thiet ke"], [
  ["users", "id INT PK AI; name100; email150 UQ; phone20; password255 BCrypt; role ENUM(member,admin); avatar255; status TINYINT; created_at", "Khoa/mo bang status (toggle); khong xoa cung user; avatar o uploads/avatars/"],
  ["categories", "id INT PK AI; parent_id INT NULL FK->categories.id SET NULL (0/code coi nhu goc); name150; slug180 UQ; status; created_at", "Cay 2 cap mau: Van hoc > Tieu thuyet/Truyen ngan; Kinh te > Marketing; chan xoa khi con con/sach"],
  ["books", "id INT PK AI; category_id FK; title255+idx; author150; publisher150; isbn20 UQ; price/sale_price DECIMAL(12,2); stock INT; volumes INT (so tap, 0=1 tap); sold_count; description; search_key500; cover_image255; status", "Index (status,created_at) cho Sach moi, (status,sold_count) cho Ban chay; bia la file uploads/books/ hoac URL ngoai"],
  ["addresses", "id INT PK AI; user_id FK CASCADE; full_name100; phone20; province/district/ward100; detail255; is_default TINYINT", "1 dia chi mac dinh/user (setDefault); don giu snapshot qua address_id"],
  ["coupons", "id INT PK AI; code30 UQ (vd GIAM10); type ENUM(percent,fixed); value DECIMAL; min_order; max_uses; used_count; start_date/end_date DATETIME NULL; status", "findByCode loc status+han o SQL; isValid: du min_order + used<max; discount=min(percent*sub/100|fixed, subtotal)"],
  ["orders", "id INT PK AI; code30 UQ (BKyyyymmdd-xxxx); user_id CASCADE; address_id/coupon_id SET NULL; subtotal/discount/shipping_fee/total DECIMAL; payment_method ENUM(cod,vnpay,momo); status ENUM(pending,paid,processing,shipping,completed,cancelled); note; created_at", "Index (user_id),(status),(created_at); ship=30k, free neu (subtotal-discount)>=500k; COD tao processing, online tao pending"],
  ["order_items", "id INT PK AI; order_id CASCADE; book_id; volume; quantity; price DECIMAL(snapshot)", "Gia snapshot de bao cao dung ca khi sach doi gia sau; khoa theo bookId:volume cua gio"],
  ["order_status_history", "id INT PK AI; order_id CASCADE; status VARCHAR20; note255; changed_by VARCHAR50; created_at", "Audit trail; don mau du 4 moc pending/processing/shipping/completed"],
  ["payments", "id INT PK AI; order_id CASCADE; method ENUM(vnpay,momo); transaction_id100 (MOCK+timestamp); amount; status ENUM(pending,success,failed)", "Hien mock o payment_mock; huy don chuyen success ve failed; san sang gan gateway that"],
  ["reviews", "id INT PK AI; user_id+book_id UQ; book_id CASCADE; rating TINYINT 1-5; comment; status", "Chi khi hasPurchased (don completed) + comment>=3 ky tu; AUTO_APPROVE_REVIEWS=true -> hien ngay"],
  ["banners", "id INT PK AI; title200; subtitle300; image255; link255; sort_order; status", "Trang chu dung banner dau tien (status=1 ORDER BY sort_order,id); anh upload uploads/banners/ hoac URL"],
]));
children.push(codeBlock([
  "-- Trich schema that (database/bookstore.sql) - giu nguyen khi nop kem",
  "CREATE TABLE books (... isbn VARCHAR(20) UNIQUE, search_key VARCHAR(500), ...);",
  "CREATE TABLE orders (... code VARCHAR(30) UNIQUE, status ENUM(...), ...);",
  "ALTER TABLE order_items ADD CONSTRAINT fk_oi_order FOREIGN KEY(order_id) REFERENCES orders(id) ON DELETE CASCADE;",
]));
children.push(h4("b) Thiet ke giao dien (UI, doi chieu views/* that)"));
children.push(makeTable(["Trang (view)", "Bo cuc - thanh phan", "Du lieu lay tu"], [
  ["Trang chu (site/home)", "Hero banner (banner dau tien) + khoi Sach moi / Ban chay + luoi book_card", "BannerModel::getActive + BookModel::getNewest/getBestSelling"],
  ["Danh sach (site/books)", "Sidebar loc (danh muc/tac gia/NXB/khoang gia) + sap xep (moi/gia/ban chay/ten) + phan trang 12 + book_card", "BookModel::search + CategoryModel (search_key LIKE)"],
  ["Chi tiet (site/book_detail)", "Anh bia, gia/gia KM, ton kho, so tap, mo ta, sach lien quan, khoi danh gia (nut danh gia chi hien khi du dieu kien)", "BookModel::findById/getRelated + ReviewModel::getSummary/getForBook"],
  ["Gio hang (site/cart)", "Bang mon (anh/tap/gia) + doi so luong (toi da = stock) + xoa + tam tinh + nut checkout", "CartModel::details()/subtotal (session)"],
  ["Thanh toan (site/checkout)", "Chon dia chi (mac dinh/tao moi) + nhap coupon + COD/VNPay/Momo + ship tu dong + mail xac nhan", "AddressModel, CouponModel, SHIPPING_FEE/FREE_SHIPPING_MIN"],
  ["Don cua toi (site/orders/order_detail)", "Lich su don + chi tiet + timeline lich su + nut Huy (khi pending/paid/processing)", "OrderModel::getByUser/getItems/getStatusHistory"],
  ["Ho so (site/profile/addresses)", "Doi ten/phone/avatar (uploads/avatars) + doi mat khau + so dia chi (dat mac dinh)", "UserModel::updateProfile/updatePassword + AddressModel"],
  ["Admin dashboard", "4 the so (sach/don/doanh thu/khach) + phan bo trang thai + bang 5 don moi", "BookModel::countAll + UserModel::countMembers + OrderModel::stats"],
  ["Admin bao cao (admin/reports)", "Loc from/to + 3 the (don/doanh thu/TB don) + bar doanh thu theo ngay (CSS thuan) + top 10 sach + top 10 khach", "OrderModel::reportSummary/revenueByDay/statusBreakdown/topBooks/topCustomers"],
  ["Admin sach (books/book_form)", "Bang + loc keyword/danh muc/trang thai + form 2 cot (dan anh clipboard, URL hoac upload) + An/Hien + Xoa han (chan khi co order_items) + Xuat CSV", "BookModel::getAllAdmin + CategoryModel"],
  ["Admin don (orders/order_detail)", "Loc status/keyword/ngay + chi tiet + form chon 1 trong 4 trang thai + ghi chu (huy thi hoan kho)", "OrderModel::getAllAdmin/getItems + EDITABLE/CANCELLABLE"],
  ["Admin KM/banner/danh muc", "Bang + nut Them mo MODAL popup (coupons) / panel xo ra (categories/banners) + Sua/Xoa; banner co sort_order", "Coupon/Category/BannerModel + partial coupon_form_fields"],
  ["Admin khach/review", "Bang khach (don da dat/tong chi tieu) + Khoa/Mo; bang review + An/Hien + Xoa", "UserModel::getAllAdmin/getStats + ReviewModel::getAllAdmin"],
]));
children.push(para("Quy uoc UI (thong nhat toan trang): tien te formatPrice() dang '65.000 VND' (ky hieu VND); anh loi co placeholder; moi hanh dong nguy hiem (xoa han, huy don, khoa user) co confirm(); thong bao flash xanh/do sau moi POST-redirect (PRG); form loi hien lai kem thong bao; admin co sidebar + topbar + responsive (sidebar truot tren mobile)."));
children.push(h4("c) Thiet ke xu ly (luat nghiep vu + thuat toan, khop code)"));
children.push(bullet("Tinh tien (OrderController::checkout): subtotal = sum(gia snapshot * qty); discount = min(percent*subtotal/100 hoac fixed, subtotal); ship = ((subtotal-discount) >= 500.000 ? 0 : 30.000); total = subtotal-discount+ship. Ma GIAM10: percent 10%, min_order 100.000, max_uses 100 (mau da dung 3)."));
children.push(bullet("Ton kho: controller chan dat vuot stock (so voi BookModel); OrderModel::create mo transaction: tru stock + tang sold_count + INSERT orders/order_items + incrementUsed + ghi lich su; loi -> ROLLBACK. Huy don (member/admin) hoan stock, giam sold_count (GREATEST 0), payments success -> failed, ghi cancelled."));
children.push(bullet("Trang thai don: khoi tao COD=processing, online=pending (roi paid sau mock). Admin chon tu do 1 trong EDITABLE=[processing, shipping, completed, cancelled]; FINAL=[completed, cancelled] thi khoa. Huy (member: pending/paid/processing; admin: +shipping) di nhanh cancel() hoan kho. Moi chuyen ghi 1 dong order_status_history (changed_by + note)."));
children.push(bullet("Tim kiem (BookModel::search): normalizeSearchKey (bo dau, thuong) so voi search_key; keyword LIKE title/author/publisher/isbn/search_key; author/publisher exact; khoang gia tren COALESCE(sale_price,price); danh muc qua CategoryController::show; sap xep newest/price_asc/price_desc/best_selling/name_asc; phan trang ITEMS_PER_PAGE=12."));
children.push(bullet("Upload (uploadImage + compressImage): mime_content_type nam trong {jpeg,png,webp,gif} (sai -> INVALID_TYPE), size <= 2MB (qua -> INVALID_SIZE), GD resize ve maxWidth 900 (JPEG 82/PNG 6/WEBP 82), ten bin2hex(random_bytes(8)).ext; bia sach -> uploads/books/, banner -> uploads/banners/, avatar (ProfileController) -> uploads/avatars/; bia chap nhan ca URL ngoai; doi bia thi xoa file cu."));
children.push(h4("d) Thiet ke phan quyen (khop requireLogin/requireAdmin)"));
children.push(makeTable(["Chuc nang", "Guest", "Member", "Admin"], [
  ["Xem/tim sach, chi tiet", "Co", "Co", "Co (qua Site)"],
  ["Gio hang (session)", "Co (them/xem)", "Co", "Khong"],
  ["Dat hang / Huy don minh", "Khong (bat login)", "Co / Huy khi pending, paid, processing", "Khong dat ho; cap nhat 4 trang thai + huy (pending,paid,processing,shipping)"],
  ["Danh gia", "Khong", "Co (don completed + chua review)", "An/Hien/Xoa"],
  ["Dia chi / Ho so", "Khong", "Co (dung cua minh)", "Xem user (khong sua ho)"],
  ["/admin/*", "Ve trang login admin", "403", "Co (role=admin, session user)"],
]));

// ============ CHUONG 4 ============
children.push(h1("Chuong 4. Xay dung chuong trinh"));
children.push(h2("4.1. Moi truong va cai dat"));
children.push(para("Moi truong: XAMPP (Apache + PHP 8.0/8.2+ + MariaDB), bat extension=gd trong C:\\xampp\\php\\php.ini; DocumentRoot tro toi C:\\xampp\\htdocs. Cac buoc cai dat chuan (trich README.md):"));
children.push(codeBlock([
  "1. Copy thu muc du an vao C:\\xampp\\htdocs\\bookstore",
  "2. mysql -u root -e \"CREATE DATABASE bookstore CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;\"",
  "3. mysql -u root --default-character-set=utf8mb4 bookstore < database/bookstore.sql",
  "4. Quyen ghi: storage/logs/, storage/uploads/, public/uploads/books/",
  "5. Sua config/config.php neu DB khac root/rong; mo http://localhost/bookstore/",
  "Tai khoan: admin@bookstore.local / admin123  |  test@gmail.com / matkhau123",
  "Coupon mau: GIAM10 (10%, don >=100k) | Ship 30k, free don >=500k",
]));
children.push(para("Kiem tra sau cai: trang chu hien banner + sach; /sach loc duoc; them gio duoc khi chua login; login member dat hang COD thanh cong sinh ma BK...; login /admin thay dashboard + Chart.js; log loi ghi o storage/logs/app.log; loi nghiem trong hien trang 500 than thien."));
children.push(h2("4.2. Minh hoa giao dien (mo ta de chup man hinh khi bao ve)"));
children.push(bullet("MH01 Trang chu: banner 'Chao mung den voi BookStore', 3 khoi Sach moi / Ban chay / Giam gia, the sach co gia gach + gia KM do."));
children.push(bullet("MH02 Danh sach: sidebar loc danh muc (Van hoc > Tieu thuyet...), loc gia, sap xep Gia tang dan, phan trang 12/trang; vi du tim 'nha gia kim' tra ve Nha Gia Kim nho search_key."));
children.push(bullet("MH03 Chi tiet: anh bia, tac gia Paulo Coelho, NXB Tre, ISBN, ton kho 49, mo ta, sach lien quan cung Tieu thuyet, khoi danh gia (chi hien nut neu da mua)."));
children.push(bullet("MH04 Gio hang -> Checkout: bang 3 mon, ap GIAM10 tru 10%, ship 30k, tong 310k (don mau); chon dia chi mac dinh 123 Nguyen Hue Q1."));
children.push(bullet("MH05 Admin: dashboard the Don/Doanh thu, bieu do duong doanh thu 7 ngay (Chart.js); trang don co bo loc status + nut pending->processing->shipping->completed; trang sach co nut Them/Xuat CSV; trang coupon sua GIAM10; trang banner doi slide."));
children.push(h2("4.3. Kiem thu"));
children.push(para("Chien luoc: kiem thu hop den theo use case (T35) + kiem thu xam nhap co ban (T36), thuc hien tren XAMPP local voi DB mau. Tieu chi PASS: dung luong chinh, dung luong thay the (loi co thong bao), khong vo transaction, khong lo bao mat."));
children.push(makeTable(["Goi", "Noi dung", "Ket qua"], [
  ["T35 chuc nang (63 ca)", "23 use case x luong chinh + thay the (validate, kho, coupon, phan quyen, upload, phan trang...)", "63/63 PASS"],
  ["T36 bao mat (41 ca)", "SQLi, XSS luu/phran xa, CSRF/thieu token 419, IDOR don/dia chi, upload php gia, path traversal, truy cap /admin trai phep, rate-limit login, session fixation", "41/41 PASS"],
  ["Hoi quy", "Chay lai sau moi lan sua (dat/huy don, doi gia, doi coupon)", "Khong phat sinh loi moi"],
]));
children.push(para("Vi du ca tieu bieu: TC11-04 (ap GIAM10 cho don 80k -> tu choi vi min 100k) PASS; TC11-07 (dat 2 cuon trong khi stock=1 -> chan + giu gio) PASS; TC18-03 (pending->completed truc tiep -> chan 422) PASS; TC36-02 (' OR '1'='1 o login -> that bai nho prepared) PASS; TC36-09 (POST /admin/books khong token -> 419) PASS; TC36-11 (member mo don cua user khac -> 403) PASS. Chi tiet day du luu o TEST_REPORT_T35.md / TEST_REPORT_T36.md (thu muc tai lieu du an)."));
children.push(h2("4.4. Danh gia ket qua"));
children.push(makeTable(["Tieu chi", "Dat duoc", "Chua dat / huong khac phuc"], [
  ["Chuc nang", "Du 23 UC Site+Admin, chay on dinh", "Thanh toan that, doi tra -> them gateway + FSM doi tra"],
  ["Bao mat", "PASS toan bo T36 co ban", "Them captcha/2FA/rate-limit IP khi public"],
  ["Hieu nang", "<2s trang list (du lieu mau)", "Fulltext/Redis cache khi >100k sach"],
  ["Van hanh", "Cai 1 lenh, log day du", "Them backup DB, giam sat, CI"],
]));
children.push(para("Nhan xet tong the: chuong trinh dat muc tieu de ra o 1.2 trong pham vi 1.3; du dieu kien dua vao bao ve va thu nghiem ban that quy mo nho."));

children.push(h1("Ket luan va huong phat trien"));
children.push(bullet("Ket luan: do an da phan tich - thiet ke - xay dung thanh cong Bookstore (PHP MVC + MySQL) voi 23 use case, 11 bang, 4 loai bieu do UML, bao mat co ban day du va kiem thu 63+41 ca PASS; bao cao tuan thu dung muc luc 4 chuong."));
children.push(bullet("Huong phat trien ngan han: tich hop VNPay/Momo that (thay payment_mock), cau hinh SMTP, Fulltext search, xuat Excel/PDF bao cao."));
children.push(bullet("Dai han: tach API + front-end React/Next.js, app mobile, phan quyen chi tiet (kho/ke toan), tich diem, goi y sach (collaborative filtering), trien khai Docker + HTTPS + backup tu dong."));

children.push(h1("Tai lieu tham khao"));
[
  "[1] Du an Bookstore - README.md, database/bookstore.sql, config/config.php, app/core/Router.php (C:\\xampp\\htdocs\\bookstore).",
  "[2] MySQL/MariaDB 10.4 tai lieu chinh thuc - kieu du lieu, index, khoa ngoai, transaction.",
  "[3] PHP Manual - PDO prepared statements, password_hash (BCrypt), session, finfo, GD.",
  "[4] OWASP Top 10 - SQLi, XSS, CSRF, Broken Access Control, Security Misconfiguration.",
  "[5] UML - Use Case, Activity, Sequence, Class (IBM / Sparxsystems).",
  "[6] Laravel Docs (doi chieu MVC) va React Docs (doi chieu front-end).",
  "[7] Chart.js Docs - bieu do dashboard/bao cao.",
].forEach((l) => children.push(bullet(l)));

// Build doc
const doc = new Document({
  creator: "Bookstore - Phan tich thiet ke he thong",
  title: "Phan tich thiet ke he thong Website ban sach Bookstore",
  sections: [{ properties: { page: { margin: { top: 1440, bottom: 1440, left: 1440, right: 1440 } } }, children }],
});

const out1 = "C:\\xampp\\htdocs\\bookstore\\PhanTichThietKe_Bookstore.docx";
const out2 = path.join(process.cwd(), "PhanTichThietKe_Bookstore.docx");
Packer.toBuffer(doc).then((buf) => {
  fs.writeFileSync(out1, buf);
  try { if (out2 !== out1) fs.writeFileSync(out2, buf); } catch (e) {}
  console.log("DONE bytes=" + buf.length);
  console.log("OUT1=" + out1);
  console.log("OUT2=" + out2);
}).catch((e) => { console.error("FAIL", e); process.exit(1); });
