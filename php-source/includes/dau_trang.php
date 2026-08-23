<?php
// --------------------------------------------------------
// BƯỚC 5: TEMPLATE PHẦN ĐẦU TRANG HTML (HEADER)
// File: includes/header.php
// --------------------------------------------------------

// Tự động nạp file cấu hình hệ thống, kết nối cơ sở dữ liệu và file hàm dùng chung
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/functions.php';
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <!-- SEO: Tiêu đề trang động (mỗi trang có tiêu đề riêng bằng cách định nghĩa biến $page_title) -->
    <title><?php echo isset($page_title) ? sanitize($page_title) . " - TechShop" : "TechShop - Thiết Bị Công Nghệ Chính Hãng"; ?></title>
    
    <!-- SEO: Meta mô tả -->
    <meta name="description" content="TechShop chuyên cung cấp chuột gaming, bàn phím cơ, tai nghe, laptop và phụ kiện công nghệ chính hãng giá rẻ cho sinh viên.">

    <!-- Tối ưu tốc độ tải: tránh phụ thuộc vào mạng bên ngoài cho font chữ và icon -->
    <!-- Hệ thống font mặc định của trình duyệt đã đủ đẹp và tải ngay mà không cần chờ CDN -->

    <!-- Icons: FontAwesome được lưu cục bộ để tránh chậm tải trang -->
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/vendor/fontawesome/css/all.min.css">
    
    <!-- CSS: File CSS hệ thống chung -->
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/css/style.css">
    
    <!-- CSS: Load file CSS riêng biệt cho từng trang nếu được định nghĩa (ví dụ: $page_css = 'auth.css') -->
    <?php if (isset($page_css)): ?>
        <link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/css/<?php echo $page_css; ?>">
    <?php endif; ?>
</head>
<body>

    <!-- Tự động đính kèm thanh điều hướng (Navbar) cho mọi trang -->
    <?php include_once __DIR__ . '/navbar.php'; ?>

    <!-- Khởi tạo khung hiển thị chính (Main container), sẽ đóng lại ở footer.php -->
    <main class="container">
