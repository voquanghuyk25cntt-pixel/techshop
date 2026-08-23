<?php
// --------------------------------------------------------
// FILE KIỂM TRA QUYỀN ADMIN (ADMIN AUTHORIZATION)
// File: includes/admin-check.php
// --------------------------------------------------------

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/functions.php';

// Nếu chưa đăng nhập hoặc đăng nhập rồi nhưng không có quyền admin
// thì chuyển hướng ngay lập tức về trang chủ
if (!is_logged_in() || !is_admin()) {
    redirect(BASE_URL . 'trang_chu.php');
}
?>

