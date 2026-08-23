<?php
// --------------------------------------------------------
// BƯỚC 11: QUẢN TRỊ - THÊM DANH MỤC (CATEGORY CREATE)
// File: admin/categories/create.php
// --------------------------------------------------------

$page_title = "Thêm Danh Mục Mới";
$page_css = "admin.css";

// Nhúng chốt chặn kiểm tra quyền Admin trước khi xuất HTML
require_once __DIR__ . '/../../includes/admin-check.php';
// Nhúng Header (Tự động nạp cấu hình, kết nối CSDL và các hàm kiểm tra)
require_once __DIR__ . '/../../includes/header.php';

$error = '';

// Xử lý khi Admin bấm Thêm Danh Mục
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);

    // 1. Kiểm tra rỗng
    if (empty($name)) {
        $error = "Tên danh mục không được để trống.";
    } else {
        try {
            // 2. Kiểm tra xem tên danh mục đã tồn tại chưa
            $stmt = $pdo->prepare("SELECT id FROM categories WHERE name = :name LIMIT 1");
            $stmt->execute(['name' => $name]);
            if ($stmt->fetch()) {
                $error = "Tên danh mục này đã tồn tại.";
            } else {
                // 3. Thực hiện chèn mới danh mục
                $insert_stmt = $pdo->prepare("INSERT INTO categories (name) VALUES (:name)");
                $insert_stmt->execute(['name' => $name]);

                $_SESSION['success'] = "Thêm danh mục mới thành công!";
                redirect(BASE_URL . 'admin/categories/trang_chu.php');
            }
        } catch (PDOException $e) {
            $error = "Có lỗi xảy ra: " . $e->getMessage();
        }
    }
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
            <h2>Thêm Danh Mục Mới</h2>
            <a href="trang_chu.php" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Quay Lại</a>
        </div>

        <!-- Hiển thị thông báo lỗi nếu có -->
        <?php if (!empty($error)): ?>
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-circle"></i> <?php echo sanitize($error); ?>
            </div>
        <?php endif; ?>

        <!-- Form nhập dữ liệu -->
        <form action="" method="POST" class="admin-form">
            <div class="form-group">
                <label for="name">Tên Danh Mục</label>
                <input type="text" name="name" id="name" placeholder="Ví dụ: Bàn Ghế Gaming, Phụ Kiện..." value="<?php echo isset($name) ? sanitize($name) : ''; ?>" required>
            </div>

            <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Lưu Danh Mục</button>
        </form>
    </main>
</div>

<?php
// Nhúng Footer
require_once __DIR__ . '/../../includes/footer.php';
?>

