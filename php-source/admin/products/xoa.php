<?php
// --------------------------------------------------------
// BƯỚC 12: QUẢN TRỊ - XÓA SẢN PHẨM (PRODUCT DELETE)
// File: admin/products/delete.php
// --------------------------------------------------------

// Nhúng file config để lấy BASE_URL
require_once __DIR__ . '/../../config/config.php';
// Nhúng file kết nối database
require_once __DIR__ . '/../../config/database.php';
// Nhúng file helper functions
require_once __DIR__ . '/../../includes/functions.php';
// Nhúng chốt chặn kiểm tra quyền Admin
require_once __DIR__ . '/../../includes/admin-check.php';

// 1. Kiểm tra ID sản phẩm truyền vào qua GET
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id = (int)$_GET['id'];

    try {
        // Lấy thông tin ảnh sản phẩm trước để xóa trên đĩa cứng
        $select_stmt = $pdo->prepare("SELECT image FROM products WHERE id = :id LIMIT 1");
        $select_stmt->execute(['id' => $id]);
        $product = $select_stmt->fetch();

        if ($product) {
            // Xóa file ảnh trên server nếu tồn tại
            if (!empty($product['image'])) {
                $image_path = __DIR__ . '/../../uploads/products/' . $product['image'];
                if (file_exists($image_path)) {
                    unlink($image_path);
                }
            }

            // Thực hiện xóa sản phẩm khỏi Database
            // Do order_details cấu hình 'FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE SET NULL'
            // việc xóa sản phẩm ở đây sẽ diễn ra bình thường, order_details tương ứng tự động chuyển sang product_id = NULL
            $delete_stmt = $pdo->prepare("DELETE FROM products WHERE id = :id");
            $delete_stmt->execute(['id' => $id]);

            $_SESSION['success'] = "Xóa sản phẩm thành công!";
        } else {
            $_SESSION['error'] = "Sản phẩm không tồn tại.";
        }
    } catch (PDOException $e) {
        $_SESSION['error'] = "Có lỗi xảy ra khi xóa sản phẩm: " . $e->getMessage();
    }
} else {
    $_SESSION['error'] = "ID sản phẩm không hợp lệ.";
}

// 3. Quay lại trang danh sách sản phẩm
redirect(BASE_URL . 'admin/products/trang_chu.php');
?>

