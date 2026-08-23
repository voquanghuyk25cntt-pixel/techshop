<?php
// --------------------------------------------------------
// BƯỚC 11: QUẢN TRỊ - SỬA DANH MỤC (CATEGORY UPDATE)
// File: admin/categories/edit.php
// --------------------------------------------------------

$page_title = "Sửa Danh Mục";
$page_css = "admin.css";

// Nhúng chốt chặn kiểm tra quyền Admin trước khi xuất HTML
require_once __DIR__ . '/../../includes/admin-check.php';
// Nhúng Header (Tự động nạp cấu hình, kết nối CSDL và các hàm kiểm tra)
require_once __DIR__ . '/../../includes/header.php';

// 1. Kiểm tra ID danh mục truyền từ thanh địa chỉ (GET)
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    $_SESSION['error'] = "Yêu cầu ID danh mục hợp lệ.";
    redirect('trang_chu.php');
}

$id = (int)$_GET['id'];
$error = '';

// 2. Lấy dữ liệu danh mục hiện tại để hiển thị lên form
try {
    $stmt = $pdo->prepare("SELECT * FROM categories WHERE id = :id LIMIT 1");
    $stmt->execute(['id' => $id]);
    $category = $stmt->fetch();

    // Nếu không tồn tại danh mục có ID này
    if (!$category) {
        $_SESSION['error'] = "Không tìm thấy danh mục yêu cầu.";
        redirect('trang_chu.php');
    }
} catch (PDOException $e) {
    $_SESSION['error'] = "Có lỗi xảy ra: " . $e->getMessage();
    redirect('trang_chu.php');
}

// 3. Xử lý khi Admin submit form cập nhật
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);

    // Kiểm tra rỗng
    if (empty($name)) {
        $error = "Tên danh mục không được để trống.";
    } else {
        try {
            // Kiểm tra trùng tên danh mục với danh mục khác (ngoại trừ chính nó)
            $check_stmt = $pdo->prepare("SELECT id FROM categories WHERE name = :name AND id != :id LIMIT 1");
            $check_stmt->execute(['name' => $name, 'id' => $id]);
            
            if ($check_stmt->fetch()) {
                $error = "Tên danh mục này đã tồn tại.";
            } else {
                // Tiến hành cập nhật
                $update_stmt = $pdo->prepare("UPDATE categories SET name = :name WHERE id = :id");
                $update_stmt->execute(['name' => $name, 'id' => $id]);

                $_SESSION['success'] = "Cập nhật danh mục thành công!";
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
            <h2>Chỉnh Sửa Danh Mục</h2>
            <a href="trang_chu.php" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Quay Lại</a>
        </div>

        <!-- Hiển thị thông báo lỗi nếu có -->
        <?php if (!empty($error)): ?>
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-circle"></i> <?php echo sanitize($error); ?>
            </div>
        <?php endif; ?>

        <!-- Form chỉnh sửa -->
        <form action="" method="POST" class="admin-form">
            <div class="form-group">
                <label for="name">Tên Danh Mục</label>
                <!-- Pre-populate dữ liệu cũ bằng sanitize() -->
                <input type="text" name="name" id="name" value="<?php echo sanitize($category['name']); ?>" required>
            </div>

            <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Cập Nhật Danh Mục</button>
        </form>
    </main>
</div>

<?php
// Nhúng Footer
require_once __DIR__ . '/../../includes/footer.php';
?>

