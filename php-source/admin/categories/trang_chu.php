<?php
// --------------------------------------------------------
// BƯỚC 11: QUẢN TRỊ - DANH SÁCH DANH MỤC (CATEGORY READ)
// File: admin/categories/trang_chu.php
// --------------------------------------------------------

$page_title = "Quản Lý Danh Mục";
$page_css = "admin.css";

// Nhúng chốt chặn kiểm tra quyền Admin trước khi xuất HTML
require_once __DIR__ . '/../../includes/admin-check.php';
// Nhúng Header (Tự động nạp cấu hình, kết nối CSDL và các hàm kiểm tra)
require_once __DIR__ . '/../../includes/header.php';

// Lấy danh sách danh mục xếp theo ID mới nhất
try {
    $stmt = $pdo->query("SELECT * FROM categories ORDER BY id DESC");
    $categories = $stmt->fetchAll();
} catch (PDOException $e) {
    $_SESSION['error'] = "Không thể lấy danh sách danh mục: " . $e->getMessage();
    $categories = [];
}
?>

<div class="admin-layout">
    <!-- Sidebar Quản trị trái -->
    <aside class="admin-sidebar">
        <h3>Bảng Điều Khiển</h3>
        <ul>
            <li><a href="<?php echo BASE_URL; ?>admin/dashboard.php"><i class="fas fa-chart-line"></i> Dashboard</a></li>
            <li class="active"><a href="<?php echo BASE_URL; ?>admin/categories/trang_chu.php"><i class="fas fa-tags"></i> Danh Mục</a></li>
            <li><a href="<?php echo BASE_URL; ?>admin/products/trang_chu.php"><i class="fas fa-box"></i> Sản Phẩm</a></li>
            <li><a href="<?php echo BASE_URL; ?>admin/orders/trang_chu.php"><i class="fas fa-shopping-bag"></i> Đơn Hàng</a></li>
            <li><a href="<?php echo BASE_URL; ?>admin/users/trang_chu.php"><i class="fas fa-users"></i> Người Dùng</a></li>
        </ul>
    </aside>

    <!-- Nội dung chính -->
    <main class="admin-main">
        <div class="admin-title-area">
            <h2>Quản Lý Danh Mục</h2>
            <a href="create.php" class="btn btn-primary"><i class="fas fa-plus"></i> Thêm Danh Mục Mới</a>
        </div>

        <!-- Hiển thị thông báo Toast dạng Session nếu có -->
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
                        <th style="width: 80px;">ID</th>
                        <th>Tên Danh Mục</th>
                        <th style="width: 200px; text-align: center;">Thao Tác</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($categories)): ?>
                        <?php foreach ($categories as $cat): ?>
                            <tr>
                                <td><?php echo $cat['id']; ?></td>
                                <td><strong><?php echo sanitize($cat['name']); ?></strong></td>
                                <td style="text-align: center;">
                                    <a href="edit.php?id=<?php echo $cat['id']; ?>" class="btn btn-secondary btn-sm">
                                        <i class="fas fa-edit"></i> Sửa
                                    </a>
                                    <a href="delete.php?id=<?php echo $cat['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Chú ý: Xóa danh mục sẽ lỗi nếu có sản phẩm đang thuộc danh mục này. Bạn có chắc chắn muốn xóa?');">
                                        <i class="fas fa-trash-alt"></i> Xóa
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="3" style="text-align: center; color: var(--text-light);">Chưa có danh mục nào. Hãy tạo mới!</td>
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

