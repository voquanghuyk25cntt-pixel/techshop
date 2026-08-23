<?php
// --------------------------------------------------------
// BƯỚC 11: QUẢN TRỊ - XÓA DANH MỤC (CATEGORY DELETE)
// File: admin/categories/delete.php
// --------------------------------------------------------

// Nhúng file config để lấy BASE_URL
require_once __DIR__ . '/../../config/config.php';
// Nhúng file kết nối database
require_once __DIR__ . '/../../config/database.php';
// Nhúng file helper functions
require_once __DIR__ . '/../../includes/functions.php';
// Nhúng chốt chặn kiểm tra quyền Admin
require_once __DIR__ . '/../../includes/admin-check.php';

// 1. Kiểm tra ID danh mục truyền vào qua GET
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id = (int)$_GET['id'];

    try {
        // Thực hiện xóa danh mục
        $stmt = $pdo->prepare("DELETE FROM categories WHERE id = :id");
        $stmt->execute(['id' => $id]);

        $_SESSION['success'] = "Xóa danh mục thành công!";
    } catch (PDOException $e) {
        // 2. Bắt lỗi ràng buộc khóa ngoại (Integrity constraint violation)
        // Khi xóa danh mục có sản phẩm liên kết, CSDL sẽ ném lỗi mã 23000 do luật 'ON DELETE RESTRICT'
        if ($e->getCode() == '23000' || strpos($e->getMessage(), '1451') !== false) {
            $_SESSION['error'] = "Không thể xóa! Danh mục này hiện đang chứa sản phẩm. Vui lòng di chuyển hoặc xóa các sản phẩm đó trước.";
        } else {
            $_SESSION['error'] = "Có lỗi xảy ra: " . $e->getMessage();
        }
    }
} else {
    $_SESSION['error'] = "ID danh mục không hợp lệ.";
}

// 3. Chuyển hướng quay trở lại danh sách danh mục
redirect(BASE_URL . 'admin/categories/trang_chu.php');
?>

