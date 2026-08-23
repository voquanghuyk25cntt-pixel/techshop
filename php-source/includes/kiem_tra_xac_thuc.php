<?php
// --------------------------------------------------------
// FILE KIỂM TRA ĐĂNG NHẬP (USER AUTHORIZATION)
// File: includes/auth-check.php
// --------------------------------------------------------

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/functions.php';

// Nếu người dùng chưa đăng nhập, lập tức chuyển hướng về trang Đăng nhập
if (!is_logged_in()) {
    redirect(BASE_URL . 'auth/login.php');
}
?>
