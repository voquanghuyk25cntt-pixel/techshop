<?php
// --------------------------------------------------------
// BƯỚC 12: QUẢN TRỊ - DANH SÁCH SẢN PHẨM (PRODUCT READ)
// File: admin/products/trang_chu.php
// --------------------------------------------------------

$page_title = "Quản Lý Sản Phẩm";
$page_css = "admin.css";

// Nhúng chốt chặn kiểm tra quyền Admin trước khi xuất HTML
require_once __DIR__ . '/../../includes/admin-check.php';
// Nhúng Header (Tự động nạp cấu hình, kết nối CSDL và các hàm kiểm tra)
require_once __DIR__ . '/../../includes/header.php';

// Lấy danh sách sản phẩm (sử dụng LEFT JOIN để lấy kèm tên Danh mục)
try {
    $sql = "SELECT p.*, c.name AS category_name 
            FROM products p 
            LEFT JOIN categories c ON p.category_id = c.id 
            ORDER BY p.id DESC";
    $stmt = $pdo->query($sql);
    $products = $stmt->fetchAll();
} catch (PDOException $e) {
    $_SESSION['error'] = "Không thể tải danh sách sản phẩm: " . $e->getMessage();
    $products = [];
}
?>

<div class="admin-layout">
    <!-- Sidebar Quản trị trái -->
    <aside class="admin-sidebar">
        <h3>Bảng Điều Khiển</h3>
        <ul>
            <li><a href="<?php echo BASE_URL; ?>admin/dashboard.php"><i class="fas fa-chart-line"></i> Dashboard</a></li>
            <li><a href="<?php echo BASE_URL; ?>admin/categories/trang_chu.php"><i class="fas fa-tags"></i> Danh Mục</a></li>
            <li class="active"><a href="<?php echo BASE_URL; ?>admin/products/trang_chu.php"><i class="fas fa-box"></i> Sản Phẩm</a></li>
            <li><a href="<?php echo BASE_URL; ?>admin/orders/trang_chu.php"><i class="fas fa-shopping-bag"></i> Đơn Hàng</a></li>
            <li><a href="<?php echo BASE_URL; ?>admin/users/trang_chu.php"><i class="fas fa-users"></i> Người Dùng</a></li>
        </ul>
    </aside>

    <!-- Nội dung chính -->
    <main class="admin-main">
        <div class="admin-title-area">
            <h2>Quản Lý Sản Phẩm</h2>
            <a href="create.php" class="btn btn-primary"><i class="fas fa-plus"></i> Thêm Sản Phẩm Mới</a>
        </div>

        <!-- Thông báo thành công/lỗi -->
        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success alert-auto-dismiss">
                <i class="fas fa-check-circle"></i> <?php echo sanitize($_SESSION['success']); unset($_SESSION['success']); ?>
            </div>
        <?php endif; ?>
        
        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger alert-auto-dismiss">
                <i class="fas fa-exclamation-circle"></i> <?php echo sanitize($_SESSION['error']); unset($_SESSION['error']); ?>
            </div>
        <?php endif; ?>

        <div class="admin-table-wrapper">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th style="width: 80px;">Hình Ảnh</th>
                        <th>Tên Sản Phẩm</th>
                        <th>Danh Mục</th>
                        <th>Đơn Giá</th>
                        <th style="width: 100px;">Tồn Kho</th>
                        <th style="width: 180px; text-align: center;">Thao Tác</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($products)): ?>
                        <?php foreach ($products as $prod): ?>
                            <tr>
                                <td>
                                    <?php if (!empty($prod['image'])): ?>
                                        <img src="<?php echo BASE_URL . 'uploads/products/' . sanitize($prod['image']); ?>" alt="product" style="width: 60px; height: 60px; object-fit: cover; border-radius: var(--radius-sm); border: 1px solid var(--border-color);">
                                    <?php else: ?>
                                        <div style="width: 60px; height: 60px; background-color: #f1f5f9; display: flex; align-items: center; justify-content: center; border-radius: var(--radius-sm); color: #94a3b8;">
                                            <i class="fas fa-image"></i>
                                        </div>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <strong><?php echo sanitize($prod['name']); ?></strong>
                                </td>
                                <td>
                                    <span class="badge badge-processing" style="text-transform: none;">
                                        <?php echo sanitize($prod['category_name'] ?? 'Không có'); ?>
                                    </span>
                                </td>
                                <td style="color: var(--primary-color); font-weight: 700;">
                                    <?php echo format_price($prod['price']); ?>
                                </td>
                                <td>
                                    <?php if ($prod['quantity'] > 0): ?>
                                        <?php echo $prod['quantity']; ?>
                                    <?php else: ?>
                                        <span style="color: var(--danger-color); font-weight: 700;">Hết hàng</span>
                                    <?php endif; ?>
                                </td>
                                <td style="text-align: center;">
                                    <a href="edit.php?id=<?php echo $prod['id']; ?>" class="btn btn-secondary btn-sm">
                                        <i class="fas fa-edit"></i> Sửa
                                    </a>
                                    <a href="delete.php?id=<?php echo $prod['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Bạn có thực sự muốn xóa sản phẩm này?');">
                                        <i class="fas fa-trash-alt"></i> Xóa
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" style="text-align: center; color: var(--text-light);">Chưa có sản phẩm nào được tạo.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </main>
</div>

<?php
// Nhúng Footer
require_once __DIR__ . '/../../includes/footer.php';
?>

