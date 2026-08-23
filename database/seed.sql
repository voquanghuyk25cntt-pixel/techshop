-- --------------------------------------------------------
-- BƯỚC 2: THÊM DỮ LIỆU MẪU (SEED DATA) CHO TECHSHOP
-- File: database/seed.sql
-- --------------------------------------------------------

USE `techshop`;

-- Đảm bảo tắt tạm thời khóa ngoại để tránh xung đột khi làm sạch dữ liệu cũ (nếu có)
SET FOREIGN_KEY_CHECKS = 0;
TRUNCATE TABLE `order_details`;
TRUNCATE TABLE `orders`;
TRUNCATE TABLE `products`;
TRUNCATE TABLE `categories`;
TRUNCATE TABLE `users`;
SET FOREIGN_KEY_CHECKS = 1;

-- --------------------------------------------------------
-- 1. Thêm dữ liệu mẫu vào bảng 'users'
-- Mật khẩu thực tế để đăng nhập:
-- Admin: '@Voquanghuy11' -> hash bằng password_hash() = $2y$10$wog.6ky0m9RoiSycqq7l/.nFmb8CuQm2jfoGeukpEUN7bXUkztBoS
-- User: '123456789' -> hash bằng password_hash() = $2y$10$i7Lq3GHBENXgz6W85pFimuEiCyCPaJwqzSEQI4Orw7cqlrRWT4eWC
-- --------------------------------------------------------
INSERT INTO `users` (`id`, `fullname`, `email`, `password`, `role`) VALUES
(1, 'TechShop Admin', 'huyq84622@gmail.com', '$2y$10$wog.6ky0m9RoiSycqq7l/.nFmb8CuQm2jfoGeukpEUN7bXUkztBoS', 'admin'),
(2, 'Nguyễn Văn A', 'user@techshop.com', '$2y$10$i7Lq3GHBENXgz6W85pFimuEiCyCPaJwqzSEQI4Orw7cqlrRWT4eWC', 'user');

-- --------------------------------------------------------
-- 2. Thêm dữ liệu mẫu vào bảng 'categories'
-- --------------------------------------------------------
INSERT INTO `categories` (`id`, `name`) VALUES
(1, 'Chuột Gaming'),
(2, 'Bàn Phím Cơ'),
(3, 'Tai Nghe'),
(4, 'Màn Hình'),
(5, 'Laptop'),
(6, 'Phụ Kiện');

-- --------------------------------------------------------
-- 3. Thêm dữ liệu mẫu vào bảng 'products'
-- --------------------------------------------------------
INSERT INTO `products` (`id`, `category_id`, `name`, `price`, `quantity`, `image`, `description`) VALUES
-- Chuột Gaming
(1, 1, 'Chuột Logitech G502 Hero', 1250000.00, 20, 'logitech-g502.jpg', 'Chuột chơi game Logitech G502 Hero hiệu năng cao, cảm biến HERO 25K, 11 nút lập trình được.'),
(2, 1, 'Chuột Razer DeathAdder Essential', 450000.00, 50, 'razer-deathadder.jpg', 'Chuột chơi game Razer DeathAdder Essential thiết kế công thái học huyền thoại, cảm biến quang học 6.400 DPI.'),

-- Bàn Phím Cơ
(3, 2, 'Bàn phím cơ AKKO 3087 DS Horizon', 1390000.00, 15, 'akko-3087.jpg', 'Bàn phím cơ AKKO Horizon sử dụng Akko switch v2 chất lượng, keycap PBT Double-Shot bền bỉ.'),
(4, 2, 'Bàn phím cơ Corsair K63 Wireless', 2190000.00, 10, 'corsair-k63.jpg', 'Bàn phím cơ không dây Corsair K63 Blue switch, đèn LED xanh độc đáo, thiết kế Tenkeyless nhỏ gọn.'),

-- Tai Nghe
(5, 3, 'Tai nghe Kingston HyperX Cloud II', 1850000.00, 25, 'hyperx-cloud-ii.jpg', 'Tai nghe gaming chụp tai HyperX Cloud II âm thanh giả lập 7.1, đệm tai giả da cực kỳ êm ái.'),
(6, 3, 'Tai nghe Sony WH-1000XM4', 5490000.00, 8, 'sony-wh1000xm4.jpg', 'Tai nghe không dây chống ồn chủ động đỉnh cao Sony WH-1000XM4, chất âm cao cấp Hi-Res Audio.'),

-- Màn Hình
(7, 4, 'Màn hình ASUS VY249HE 24 inch IPS 75Hz', 2890000.00, 12, 'asus-vy249he.jpg', 'Màn hình ASUS bảo vệ mắt IPS 24 inch Full HD, tần số quét 75Hz thời gian phản hồi 1ms.'),
(8, 4, 'Màn hình Dell Ultrasharp U2422H 23.8 inch', 5850000.00, 10, 'dell-u2422h.jpg', 'Màn hình Dell Ultrasharp màu sắc chuẩn xác chuyên thiết kế đồ họa, viền siêu mỏng ấn tượng.'),

-- Laptop
(9, 5, 'Laptop ASUS TUF Gaming F15', 18990000.00, 5, 'asus-tuf-f15.jpg', 'Laptop chơi game Asus TUF Gaming F15 Core i5, card đồ họa GTX 1650, RAM 8GB, SSD 512GB mạnh mẽ.'),
(10, 5, 'MacBook Air M2 8GB 256GB', 26490000.00, 7, 'macbook-air-m2.jpg', 'MacBook Air M2 thiết kế siêu mỏng nhẹ sang trọng, chip Apple M2 hiệu năng xử lý cực đỉnh.'),

-- Phụ Kiện
(11, 6, 'Lót chuột Razer Goliathus Mobile', 250000.00, 100, 'razer-goliathus.jpg', 'Bàn di chuột Razer Goliathus độ dày 1.5mm cực mịn, dễ cuộn lại để mang đi học, đi làm.'),
(12, 6, 'Giá treo tai nghe RGB', 350000.00, 30, 'headphone-stand-rgb.jpg', 'Giá treo tai nghe tích hợp đèn led RGB 16.8 triệu màu và 2 cổng USB 2.0 mở rộng tiện lợi.');

-- --------------------------------------------------------
-- 4. Thêm dữ liệu mẫu vào bảng 'orders'
-- --------------------------------------------------------
INSERT INTO `orders` (`id`, `user_id`, `total_amount`, `status`) VALUES
(1, 2, 1700000.00, 'completed'),
(2, 2, 2190000.00, 'pending');

-- --------------------------------------------------------
-- 5. Thêm dữ liệu mẫu vào bảng 'order_details'
-- --------------------------------------------------------
INSERT INTO `order_details` (`id`, `order_id`, `product_id`, `quantity`, `price`) VALUES
-- Đơn hàng 1 (Mua 1 chuột Logitech + 1 chuột Razer = 1,700,000đ)
(1, 1, 1, 1, 1250000.00),
(2, 1, 2, 1, 450000.00),

-- Đơn hàng 2 (Mua 1 bàn phím Corsair K63 = 2,190,000đ)
(3, 2, 4, 1, 2190000.00);
