-- --------------------------------------------------------
-- BƯỚC 1: TẠO DATABASE VÀ CÁC BẢNG CHO TECHSHOP
-- File: database/database.sql
-- --------------------------------------------------------

-- 1. Tạo Database 'techshop' (sử dụng bảng mã UTF-8 để hỗ trợ tiếng Việt đầy đủ)
CREATE DATABASE IF NOT EXISTS `techshop` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `techshop`;

-- --------------------------------------------------------
-- 2. Tạo bảng 'users' (Lưu thông tin tài khoản Khách hàng và Admin)
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `users` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `fullname` VARCHAR(100) NOT NULL,
    `email` VARCHAR(150) NOT NULL UNIQUE,
    `password` VARCHAR(255) NOT NULL, -- Mật khẩu sẽ được mã hóa bằng password_hash() trong PHP
    `role` ENUM('admin', 'user') NOT NULL DEFAULT 'user',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Giải thích bảng 'users':
-- - `id`: Khóa chính tự tăng (Primary Key), định danh duy nhất cho mỗi người dùng.
-- - `email`: Ràng buộc UNIQUE đảm bảo không có 2 tài khoản trùng email.
-- - `role`: Kiểu dữ liệu ENUM giới hạn quyền chỉ nhận 'admin' hoặc 'user', mặc định là 'user'.

-- --------------------------------------------------------
-- 3. Tạo bảng 'categories' (Lưu danh mục sản phẩm như Chuột, Bàn phím, Laptop...)
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `categories` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(100) NOT NULL UNIQUE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Giải thích bảng 'categories':
-- - `name`: Tên danh mục là duy nhất (UNIQUE) để tránh trùng lặp danh mục (ví dụ: có hai danh mục "Laptop").

-- --------------------------------------------------------
-- 4. Tạo bảng 'products' (Lưu thông tin sản phẩm công nghệ)
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `products` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `category_id` INT NOT NULL,
    `name` VARCHAR(255) NOT NULL,
    `price` DECIMAL(10,2) NOT NULL, -- Định dạng 10 số, 2 số thập phân (Ví dụ: 999999.00)
    `quantity` INT NOT NULL DEFAULT 0,
    `image` VARCHAR(255) DEFAULT NULL, -- Lưu đường dẫn/tên file ảnh sản phẩm
    `description` TEXT DEFAULT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Giải thích bảng 'products':
-- - `category_id`: Khóa ngoại (Foreign Key) liên kết đến `categories(id)`.
-- - Ràng buộc `ON DELETE RESTRICT`: Không cho phép xóa danh mục nếu đang có sản phẩm thuộc danh mục đó. Điều này giúp tránh lỗi dữ liệu "mồ côi" (sản phẩm không có danh mục).
-- - `price`: Sử dụng DECIMAL thay vì FLOAT hay DOUBLE để tránh sai số khi tính toán tiền tệ.

-- --------------------------------------------------------
-- 5. Tạo bảng 'orders' (Lưu thông tin đơn hàng tổng quát)
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `orders` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT NOT NULL,
    `total_amount` DECIMAL(10,2) NOT NULL,
    `status` ENUM('pending', 'processing', 'shipping', 'completed', 'cancelled') NOT NULL DEFAULT 'pending',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Giải thích bảng 'orders':
-- - `user_id`: Khóa ngoại (Foreign Key) liên kết đến `users(id)`.
-- - Ràng buộc `ON DELETE CASCADE`: Khi xóa tài khoản người dùng, toàn bộ lịch sử đơn hàng của người dùng đó cũng sẽ tự động bị xóa (đơn giản hóa cho đồ án sinh viên).
-- - `status`: Trạng thái đơn hàng nhận 1 trong 5 giá trị được định nghĩa sẵn, mặc định là 'pending' (chờ xử lý).

-- --------------------------------------------------------
-- 6. Tạo bảng 'order_details' (Chi tiết các sản phẩm trong mỗi đơn hàng)
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `order_details` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `order_id` INT NOT NULL,
    `product_id` INT DEFAULT NULL, -- Cho phép NULL để nếu sản phẩm bị xóa, đơn hàng cũ vẫn giữ lại thông tin
    `quantity` INT NOT NULL,
    `price` DECIMAL(10,2) NOT NULL, -- Giá sản phẩm tại thời điểm mua (để tránh bị ảnh hưởng nếu sản phẩm đổi giá sau này)
    FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
    FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Giải thích bảng 'order_details':
-- - `order_id`: Khóa ngoại liên kết tới `orders(id)`. Ràng buộc `ON DELETE CASCADE` đảm bảo khi xóa đơn hàng lớn, các chi tiết đơn hàng con cũng tự động biến mất.
-- - `product_id`: Khóa ngoại liên kết tới `products(id)`. Ràng buộc `ON DELETE SET NULL` giúp giữ lại lịch sử đơn hàng cũ ngay cả khi sản phẩm đó đã bị Admin xóa khỏi cửa hàng (khi đó cột `product_id` sẽ chuyển thành NULL).
-- - `price`: Đây là giá bán thực tế tại thời điểm mua hàng. Điều này rất quan trọng vì giá sản phẩm trên cửa hàng có thể thay đổi theo thời gian, nhưng hóa đơn cũ của khách hàng thì phải giữ nguyên giá lúc mua.

-- --------------------------------------------------------
-- 7. Thiết lập Indexes (Chỉ mục) tối ưu hóa truy vấn
-- --------------------------------------------------------
-- Tăng tốc tìm kiếm sản phẩm theo tên
CREATE INDEX idx_products_name ON `products` (`name`);
-- Tăng tốc tìm kiếm tài khoản theo email (phục vụ đăng nhập)
CREATE INDEX idx_users_email ON `users` (`email`);
