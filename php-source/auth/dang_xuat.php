<?php
// --------------------------------------------------------
// BƯỚC 10: FILE ĐĂNG XUẤT TÀI KHOẢN (LOGOUT)
// File: auth/logout.php
// --------------------------------------------------------

// Nhúng file config để lấy BASE_URL
require_once __DIR__ . '/../config/config.php';
// Nhúng file bổ trợ chứa hàm redirect và khởi tạo session
require_once __DIR__ . '/../includes/functions.php';

// 1. Giải phóng toàn bộ biến lưu trong Session hiện tại
$_SESSION = array();

// 2. Nếu sử dụng Cookie Session, xóa cookie session tương ứng để xóa sạch dấu vết
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// 3. Hủy bỏ Session hoàn toàn
session_destroy();

// 4. Chuyển hướng người dùng về trang Đăng nhập
redirect(BASE_URL . 'auth/login.php');
?>
