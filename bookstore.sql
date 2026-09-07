-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Máy chủ: 127.0.0.1
-- Thời gian đã tạo: Th8 25, 2026 lúc 06:47 AM
-- Phiên bản máy phục vụ: 10.4.32-MariaDB
-- Phiên bản PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Cơ sở dữ liệu: `bookstore`
--

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `addresses`
--

CREATE TABLE `addresses` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `province` varchar(100) NOT NULL,
  `district` varchar(100) NOT NULL,
  `ward` varchar(100) NOT NULL,
  `detail` varchar(255) NOT NULL,
  `is_default` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `addresses`
--

INSERT INTO `addresses` (`id`, `user_id`, `full_name`, `phone`, `province`, `district`, `ward`, `detail`, `is_default`, `created_at`) VALUES
(1, 2, 'Nguyen Van An', '0912345678', 'TP Ho Chi Minh', 'Quan 1', 'Phuong Ben Nghe', '123 Nguyen Hue', 1, '2026-08-19 13:36:41');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `banners`
--

CREATE TABLE `banners` (
  `id` int(11) NOT NULL,
  `title` varchar(200) NOT NULL,
  `subtitle` varchar(300) DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `link` varchar(255) DEFAULT NULL,
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `banners`
--

INSERT INTO `banners` (`id`, `title`, `subtitle`, `image`, `link`, `sort_order`, `status`, `created_at`, `updated_at`) VALUES
(1, 'Chào mừng đến với BookStore', 'Hàng nghìn đầu sách hay - giao hàng nhanh toàn quốc', 'uploads/banners/de47f92476c1bf03.jpg', 'http://localhost/bookstore/sach', 1, 1, '2026-08-19 16:37:45', '2026-08-19 17:55:44');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `books`
--

CREATE TABLE `books` (
  `id` int(11) NOT NULL,
  `category_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `author` varchar(150) DEFAULT NULL,
  `publisher` varchar(150) DEFAULT NULL,
  `isbn` varchar(20) DEFAULT NULL,
  `price` decimal(12,2) NOT NULL DEFAULT 0.00,
  `sale_price` decimal(12,2) DEFAULT NULL,
  `stock` int(11) NOT NULL DEFAULT 0,
  `volumes` int(11) NOT NULL DEFAULT 0,
  `description` text DEFAULT NULL,
  `search_key` varchar(500) NOT NULL DEFAULT '',
  `cover_image` varchar(255) DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `sold_count` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `books`
--

INSERT INTO `books` (`id`, `category_id`, `title`, `author`, `publisher`, `isbn`, `price`, `sale_price`, `stock`, `volumes`, `description`, `search_key`, `cover_image`, `status`, `sold_count`, `created_at`) VALUES
(1, 5, 'Nhà Giả Kim', 'Paulo Coelho', 'NXB Trẻ', '9786041120001', 89000.00, 65000.00, 49, 0, 'Cuốn tiểu thuyết nổi tiếng kể về hành trình của chàng chăn cừu Santiago đi tìm kho báu và ý nghĩa cuộc sống. Tác phẩm truyền cảm hứng về ước mơ và lòng dũng cảm.', 'nha gia kim paulo coelho nxb tre 9786041120001', 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQSisw-I1ia0Cr21EV9LgSr8WOSZK994AAGkNArmedSIQ&s=10', 1, 1, '2026-08-19 04:15:40'),
(2, 1, 'Tuổi thơ dữ dội', 'Phùng Quán', 'NXB Kim Đồng', '9786041120002', 130000.00, NULL, 29, 0, 'Một bản hùng ca cảm động về tuổi thiếu niên trong những năm tháng chiến tranh, với tình bạn trong sáng và lòng yêu nước mãnh liệt.', 'tuoi tho du doi phung quan nxb kim dong 9786041120002', 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcSOUJzBSLitwZ7Icqfz8ecfHP6fLw8-I3njnAolOjVXmw&s=10', 1, 1, '2026-08-19 04:15:40'),
(3, 3, 'Đắc Nhân Tâm', 'Dale Carnegie', 'NXB Tổng hợp TP.HCM', '9786041120003', 99000.00, 79000.00, 40, 0, 'Cuốn sách kinh điển về nghệ thuật ứng xử, giao tiếp và thuyết phục người khác. Bài học quý giá giúp bạn thành công trong công việc và cuộc sống.', 'dac nhan tam dale carnegie nxb tong hop tp.hcm 9786041120003', 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQc6LdO7h-XyLftfjDQZga2DEre7B5_H_YR2QGE_eZ6kw&s=10', 1, 0, '2026-08-19 04:15:40'),
(4, 3, 'Đời thay đổi khi chúng ta thay đổi', 'Andrew Matthews', 'NXB Trẻ', '9786041120004', 85000.00, NULL, 24, 0, 'Những câu chuyện và bài học giúp bạn nhìn cuộc sống tích cực hơn, thay đổi thái độ để thay đổi chính cuộc đời mình.', 'doi thay doi khi chung ta thay doi andrew matthews nxb tre 9786041120004', 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQ1AyiHrM6kdQLQSxzKX77m_iLHm-IF4b10ASAacy3IeA&s=10', 1, 1, '2026-08-19 04:15:40'),
(5, 3, 'Quẳng gánh lo đi và vui sống', 'Dale Carnegie', 'NXB Tổng hợp TP.HCM', '9786041120005', 75000.00, NULL, 31, 3, 'Cẩm nang giúp bạn loại bỏ lo âu, căng thẳng và tận hưởng cuộc sống một cách trọn vẹn với những phương pháp thực tiễn.', 'quang ganh lo di va vui song dale carnegie nxb tong hop tp.hcm 9786041120005', 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQ9mTAEvyXE36fAqyOKcnNqIkh0p-xm2IDf0dsu6LAVDQ&s=10', 1, 4, '2026-08-19 04:15:40'),
(6, 1, 'Cà phê cùng Tony', 'Tony Buổi Sáng', 'NXB Trẻ', '9786041120006', 89000.00, NULL, 45, 0, 'Tuyển tập những bài viết hài hước, sắc sảo về lối sống, kỹ năng và tư duy dành cho người trẻ Việt Nam.', 'ca phe cung tony tony buoi sang nxb tre 9786041120006', 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcR5lqYfKRv0iS1aCRm3n9jvInl8ubDI_0lcowYFtMdxSQ&s=10', 1, 0, '2026-08-19 04:15:40'),
(7, 1, 'Trên đường băng', 'Tony Buổi Sáng', 'NXB Trẻ', '9786041120007', 79000.00, NULL, 38, 0, 'Những câu chuyện truyền cảm hứng về sự chuẩn bị, nỗ lực và trưởng thành của người trẻ trên con đường lập nghiệp.', 'tren duong bang tony buoi sang nxb tre 9786041120007', 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQnizVbi4l5C2ll9ugFJd2orvJJZTELHn17A-Z6gM4jOg&s=10', 1, 0, '2026-08-19 04:15:40'),
(8, 2, 'Chiến tranh tiền tệ', 'Song Hongbing', 'NXB Trẻ', '9786041120008', 150000.00, NULL, 20, 0, 'Phân tích sâu sắc về lịch sử tài chính thế giới và những cuộc chiến kinh tế ngầm đằng sau đồng tiền.', 'chien tranh tien te song hongbing nxb tre 9786041120008', 'https://pos.nvncdn.com/fd5775-40602/ps/20240116_uR6xMnxDOF.jpeg?v=1705376473', 1, 0, '2026-08-19 04:15:40'),
(9, 2, 'Nguyên lý 80/20', 'Richard Koch', 'NXB Trẻ', '9786041120009', 110000.00, NULL, 32, 0, 'Nguyên tắc Pareto và cách áp dụng quy luật 80/20 để đạt hiệu quả tối đa trong công việc và cuộc sống.', 'nguyen ly 80/20 richard koch nxb tre 9786041120009', 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcRnvcfN-zwCnh_jxvuY1th9Cou2hP67vqPiDZGp5938AQ&s=10', 1, 0, '2026-08-19 04:15:40'),
(10, 2, 'Kinh tế học hài hước', 'Steven D. Levitt', 'NXB Trẻ', '9786041120010', 120000.00, NULL, 27, 0, 'Nhìn nhận các vấn đề kinh tế qua lăng kính thú vị và bất ngờ, thách thức những giả định thông thường.', 'kinh te hoc hai huoc steven d. levitt nxb tre 9786041120010', 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcSBR9EsvopWTUCahYjaY8Tib1didDC8L8rpu6RCYBLhBg&s=10', 1, 0, '2026-08-19 04:15:40'),
(11, 7, 'Marketing căn bản', 'Philip Kotler', 'NXB Giao thông vận tải', '9786041120011', 145000.00, NULL, 22, 0, 'Giáo trình kinh điển về marketing với các khái niệm nền tảng: nghiên cứu thị trường, phân khúc, định vị và chiến lược 4P.', 'marketing can ban philip kotler nxb giao thong van tai 9786041120011', 'https://sachhay24h.com/uploads/images/sach-marketing-can-ban-1.jpg', 1, 0, '2026-08-19 04:15:40'),
(12, 7, 'Marketing 4.0', 'Philip Kotler', 'NXB Trẻ', '9786041120012', 128000.00, NULL, 18, 0, 'Chiến lược marketing trong kỷ nguyên số, chuyển dịch từ tiếp cận truyền thống sang kết nối trực tuyến và trải nghiệm khách hàng.', 'marketing 4.0 philip kotler nxb tre 9786041120012', 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcTLaarfIGnQWmIl6hFH-fG5w3V_-yBJjg_FBOfoe3mmpQ&s=10', 1, 0, '2026-08-19 04:15:40'),
(13, 3, 'Nghĩ giàu làm giàu', 'Napoleon Hill', 'NXB Tr?', '9786041120013', 98000.00, 79000.00, 33, 0, 'Bí quyết thành công từ 13 nguyên tắc làm giàu, dựa trên hàng ngàn cuộc phỏng vấn những người thành đạt nhất nước Mỹ.', 'nghi giau lam giau napoleon hill nxb tr? 9786041120013', 'https://cdn1.fahasa.com/media/catalog/product/n/g/nghigiaulamgiau_110k-01_bia-1.jpg', 1, 0, '2026-08-19 13:45:58'),
(14, 3, '7 Thói quen hiệu quả', 'Stephen R. Covey', 'NXB Tổng hợp TP.HCM', '9786041120014', 105000.00, NULL, 26, 0, 'Hệ thống 7 thói quen giúp bạn làm chủ bản thân, xây dựng các mối quan hệ và phát triển bền vững trong sự nghiệp.', '7 thoi quen hieu qua stephen r. covey nxb tong hop tp.hcm 9786041120014', 'https://upload.wikimedia.org/wikipedia/vi/4/45/7_Th%C3%B3i_Quen_%C4%90%E1%BB%83_Th%C3%A0nh_%C4%90%E1%BA%A1t.jpg?utm_source=vi.wikipedia.org&utm_campaign=index&utm_content=original', 1, 0, '2026-08-19 04:15:40'),
(15, 3, 'Dám nghĩ lớn', 'David J. Schwartz', 'NXB Trẻ', '9786041120015', 82000.00, NULL, 30, 0, 'Lời khuyên thực tế để loại bỏ tư duy nhỏ hẹp, tự tin hành động và đạt được những mục tiêu lớn trong đời.', 'dam nghi lon david j. schwartz nxb tre 9786041120015', 'https://cdn1.fahasa.com/media/catalog/product/8/9/8935086856123.jpg', 1, 0, '2026-08-19 04:15:40'),
(16, 4, 'Dế Mèn phiêu lưu ký', 'Tô Hoài', 'NXB Kim Đồng', '9786041120016', 65000.00, NULL, 50, 0, 'Tác phẩm văn học thiếu nhi kinh điển kể về chuyến phiêu lưu đầy thú vị và bài học về tình bạn của chú Dế Mèn.', 'de men phieu luu ky to hoai nxb kim dong 9786041120016', 'https://tiemsach.org/wp-content/uploads/2023/08/De-Men-Phieu-Luu-Ky.jpg', 1, 0, '2026-08-19 04:15:40'),
(17, 4, 'Kính vạn hoa', 'Nguyễn Nhật Ánh', 'NXB Kim Đồng', '9786041120017', 72000.00, NULL, 44, 0, 'Bộ truyện thiếu nhi quen thuộc về tuổi học trò hồn nhiên, trong sáng với những câu chuyện vui nhộn và đầy ắp kỷ niệm.', 'kinh van hoa nguyen nhat anh nxb kim dong 9786041120017', 'https://www.netabooks.vn/Data/Sites/1/Product/47488/kinh-van-hoa-tap-1-ki-niem-65-nam-nxb-kim-dong-bia-cung.jpg', 1, 0, '2026-08-19 04:15:40'),
(18, 4, 'Chuyện con mèo dạy hải âu bay', 'Luis Sepúlveda', 'NXB Hội Nhà văn', '9786041120018', 68000.00, NULL, 36, 0, 'Câu chuyện cảm động về chú mèo Zorba dạy chú hải âu non biết bay, thấm đẫm tình yêu thương và trách nhiệm.', 'chuyen con meo day hai au bay luis sepulveda nxb hoi nha van 9786041120018', 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcT8ljCuLAxc7nRjn5C5DlvYUrSW1YfRAX8YFQabLxGwIQ&s=10', 1, 0, '2026-08-19 04:15:40');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `categories`
--

CREATE TABLE `categories` (
  `id` int(11) NOT NULL,
  `parent_id` int(11) DEFAULT NULL,
  `name` varchar(150) NOT NULL,
  `slug` varchar(180) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `categories`
--

INSERT INTO `categories` (`id`, `parent_id`, `name`, `slug`, `status`, `created_at`) VALUES
(1, NULL, 'Văn học', 'van-hoc', 1, '2026-08-19 04:15:40'),
(2, NULL, 'Kinh tế', 'kinh-te', 1, '2026-08-19 04:15:40'),
(3, NULL, 'Kỹ năng sống', 'ky-nang-song', 1, '2026-08-19 04:15:40'),
(4, NULL, 'Thiếu nhi', 'thieu-nhi', 1, '2026-08-19 04:15:40'),
(5, 1, 'Tiểu thuyết', 'tieu-thuyet', 1, '2026-08-19 04:15:40'),
(6, 1, 'Truyện ngắn', 'truyen-ngan', 1, '2026-08-19 04:15:40'),
(7, 2, 'Marketing', 'marketing', 1, '2026-08-19 04:15:40');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `coupons`
--

CREATE TABLE `coupons` (
  `id` int(11) NOT NULL,
  `code` varchar(30) NOT NULL,
  `type` enum('percent','fixed') NOT NULL DEFAULT 'percent',
  `value` decimal(12,2) NOT NULL,
  `min_order` decimal(12,2) NOT NULL DEFAULT 0.00,
  `max_uses` int(11) NOT NULL DEFAULT 100,
  `used_count` int(11) NOT NULL DEFAULT 0,
  `start_date` datetime DEFAULT NULL,
  `end_date` datetime DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `coupons`
--

INSERT INTO `coupons` (`id`, `code`, `type`, `value`, `min_order`, `max_uses`, `used_count`, `start_date`, `end_date`, `status`, `created_at`) VALUES
(1, 'GIAM10', 'percent', 10.00, 100000.00, 100, 3, NULL, NULL, 1, '2026-08-19 04:15:40');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `code` varchar(30) NOT NULL,
  `address_id` int(11) DEFAULT NULL,
  `coupon_id` int(11) DEFAULT NULL,
  `subtotal` decimal(12,2) NOT NULL DEFAULT 0.00,
  `discount` decimal(12,2) NOT NULL DEFAULT 0.00,
  `shipping_fee` decimal(12,2) NOT NULL DEFAULT 0.00,
  `total` decimal(12,2) NOT NULL DEFAULT 0.00,
  `payment_method` enum('cod','vnpay','momo') NOT NULL DEFAULT 'cod',
  `status` enum('pending','paid','processing','shipping','completed','cancelled') NOT NULL DEFAULT 'pending',
  `note` varchar(500) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `orders`
--

INSERT INTO `orders` (`id`, `user_id`, `code`, `address_id`, `coupon_id`, `subtotal`, `discount`, `shipping_fee`, `total`, `payment_method`, `status`, `note`, `created_at`) VALUES
(1, 2, 'BK20260819-0001', 1, NULL, 280000.00, 0.00, 30000.00, 310000.00, 'cod', 'completed', 'ðon m?u d? demo', '2026-08-19 13:44:59');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `order_items`
--

CREATE TABLE `order_items` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `book_id` int(11) NOT NULL,
  `volume` int(11) NOT NULL DEFAULT 1,
  `quantity` int(11) NOT NULL,
  `price` decimal(12,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `order_items`
--

INSERT INTO `order_items` (`id`, `order_id`, `book_id`, `volume`, `quantity`, `price`) VALUES
(11, 1, 1, 1, 1, 65000.00),
(12, 1, 2, 1, 1, 130000.00),
(13, 1, 4, 1, 1, 85000.00);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `order_status_history`
--

CREATE TABLE `order_status_history` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `status` varchar(20) NOT NULL,
  `note` varchar(255) DEFAULT NULL,
  `changed_by` varchar(50) NOT NULL DEFAULT 'system',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `order_status_history`
--

INSERT INTO `order_status_history` (`id`, `order_id`, `status`, `note`, `changed_by`, `created_at`) VALUES
(16, 1, 'pending', 'ð?t hÓng thÓnh c¶ng', 'system', '2026-08-19 13:44:59'),
(17, 1, 'processing', 'Xßc nh?n don hÓng', 'admin', '2026-08-19 13:44:59'),
(18, 1, 'shipping', 'Giao hÓng cho don v? v?n chuy?n', 'admin', '2026-08-19 13:44:59'),
(19, 1, 'completed', 'Khßch dÒ nh?n hÓng', 'admin', '2026-08-19 13:44:59');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `payments`
--

CREATE TABLE `payments` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `method` enum('vnpay','momo') DEFAULT NULL,
  `transaction_id` varchar(100) DEFAULT NULL,
  `amount` decimal(12,2) NOT NULL,
  `status` enum('pending','success','failed') NOT NULL DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `reviews`
--

CREATE TABLE `reviews` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `book_id` int(11) NOT NULL,
  `rating` tinyint(4) NOT NULL,
  `comment` text DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('member','admin') NOT NULL DEFAULT 'member',
  `avatar` varchar(255) DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `phone`, `password`, `role`, `avatar`, `status`, `created_at`) VALUES
(1, 'Quản trị viên', 'admin@bookstore.local', '0900000000', '$2y$10$q7G8CTeNp0Vjp3AdOP5YyuAr6ZEFgPKLZQtj5gR8/r/ldhO.2YzVm', 'admin', NULL, 1, '2026-08-19 04:15:40'),
(2, 'Nguyen Van An', 'test@gmail.com', '0912345678', '$2y$10$hrDb1whOBDDu52/6g98gX.Za5fk.rdN9U4At3jJ.vV80k91oMAI7G', 'member', NULL, 1, '2026-08-19 04:15:40');

--
-- Chỉ mục cho các bảng đã đổ
--

--
-- Chỉ mục cho bảng `addresses`
--
ALTER TABLE `addresses`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_addresses_user` (`user_id`);

--
-- Chỉ mục cho bảng `banners`
--
ALTER TABLE `banners`
  ADD PRIMARY KEY (`id`);

--
-- Chỉ mục cho bảng `books`
--
ALTER TABLE `books`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `isbn` (`isbn`),
  ADD KEY `fk_books_category` (`category_id`),
  ADD KEY `idx_books_title` (`title`),
  ADD KEY `idx_books_status` (`status`),
  ADD KEY `idx_books_status_created` (`status`,`created_at`),
  ADD KEY `idx_books_status_sold` (`status`,`sold_count`);

--
-- Chỉ mục cho bảng `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`),
  ADD KEY `fk_categories_parent` (`parent_id`);

--
-- Chỉ mục cho bảng `coupons`
--
ALTER TABLE `coupons`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `code` (`code`);

--
-- Chỉ mục cho bảng `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `code` (`code`),
  ADD KEY `fk_orders_address` (`address_id`),
  ADD KEY `fk_orders_coupon` (`coupon_id`),
  ADD KEY `idx_orders_status` (`status`),
  ADD KEY `idx_orders_user` (`user_id`),
  ADD KEY `idx_orders_created` (`created_at`);

--
-- Chỉ mục cho bảng `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_oi_order` (`order_id`),
  ADD KEY `fk_oi_book` (`book_id`);

--
-- Chỉ mục cho bảng `order_status_history`
--
ALTER TABLE `order_status_history`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_osh_order` (`order_id`);

--
-- Chỉ mục cho bảng `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_payments_order` (`order_id`);

--
-- Chỉ mục cho bảng `reviews`
--
ALTER TABLE `reviews`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_review_user_book` (`user_id`,`book_id`),
  ADD KEY `fk_reviews_book` (`book_id`);

--
-- Chỉ mục cho bảng `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT cho các bảng đã đổ
--

--
-- AUTO_INCREMENT cho bảng `addresses`
--
ALTER TABLE `addresses`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT cho bảng `banners`
--
ALTER TABLE `banners`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT cho bảng `books`
--
ALTER TABLE `books`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;

--
-- AUTO_INCREMENT cho bảng `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT cho bảng `coupons`
--
ALTER TABLE `coupons`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT cho bảng `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT cho bảng `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT cho bảng `order_status_history`
--
ALTER TABLE `order_status_history`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT cho bảng `payments`
--
ALTER TABLE `payments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `reviews`
--
ALTER TABLE `reviews`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT cho bảng `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- Các ràng buộc cho các bảng đã đổ
--

--
-- Các ràng buộc cho bảng `addresses`
--
ALTER TABLE `addresses`
  ADD CONSTRAINT `fk_addresses_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Các ràng buộc cho bảng `books`
--
ALTER TABLE `books`
  ADD CONSTRAINT `fk_books_category` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`);

--
-- Các ràng buộc cho bảng `categories`
--
ALTER TABLE `categories`
  ADD CONSTRAINT `fk_categories_parent` FOREIGN KEY (`parent_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL;

--
-- Các ràng buộc cho bảng `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `fk_orders_address` FOREIGN KEY (`address_id`) REFERENCES `addresses` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_orders_coupon` FOREIGN KEY (`coupon_id`) REFERENCES `coupons` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_orders_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Các ràng buộc cho bảng `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `fk_oi_book` FOREIGN KEY (`book_id`) REFERENCES `books` (`id`),
  ADD CONSTRAINT `fk_oi_order` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE;

--
-- Các ràng buộc cho bảng `order_status_history`
--
ALTER TABLE `order_status_history`
  ADD CONSTRAINT `fk_osh_order` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE;

--
-- Các ràng buộc cho bảng `payments`
--
ALTER TABLE `payments`
  ADD CONSTRAINT `fk_payments_order` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE;

--
-- Các ràng buộc cho bảng `reviews`
--
ALTER TABLE `reviews`
  ADD CONSTRAINT `fk_reviews_book` FOREIGN KEY (`book_id`) REFERENCES `books` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_reviews_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
