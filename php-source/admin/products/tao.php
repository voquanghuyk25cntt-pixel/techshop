<?php
// --------------------------------------------------------
// BƯỚC 12: QUẢN TRỊ - THÊM SẢN PHẨM (PRODUCT CREATE)
// File: admin/products/create.php
// --------------------------------------------------------

$page_title = "Thêm Sản Phẩm Mới";
$page_css = "admin.css";

// Nhúng chốt chặn kiểm tra quyền Admin trước khi xuất HTML
require_once __DIR__ . '/../../includes/admin-check.php';
// Nhúng Header (Tự động nạp cấu hình, kết nối CSDL và các hàm kiểm tra)
require_once __DIR__ . '/../../includes/header.php';

// Lấy danh sách danh mục để điền vào phần chọn (Select Dropdown)
try {
    $cat_stmt = $pdo->query("SELECT * FROM categories ORDER BY name ASC");
    $categories = $cat_stmt->fetchAll();
} catch (PDOException $e) {
    $categories = [];
}

$error = '';

// Xử lý khi bấm nút Lưu sản phẩm
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $category_id = $_POST['category_id'];
    $price = trim($_POST['price']);
    $quantity = trim($_POST['quantity']);
    $description = trim($_POST['description']);
    $image_name = null;

    // 1. Kiểm tra rỗng các trường quan trọng
    if (empty($name) || empty($category_id) || $price === '' || $quantity === '') {
        $error = "Vui lòng điền tên, danh mục, đơn giá và số lượng tồn kho.";
    } 
    // 2. Kiểm tra định dạng số cho giá và số lượng
    elseif (!is_numeric($price) || $price < 0) {
        $error = "Đơn giá sản phẩm phải là một số dương.";
    } 
    elseif (!is_numeric($quantity) || $quantity < 0) {
        $error = "Số lượng sản phẩm trong kho phải là một số nguyên dương.";
    } 
    // 3. Xử lý tải ảnh lên (File Upload)
    else {
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $file_tmp_path = $_FILES['image']['tmp_name'];
            $file_name = $_FILES['image']['name'];
            $file_size = $_FILES['image']['size'];
            $file_type = $_FILES['image']['type'];
            
            // Lấy đuôi mở rộng của file
            $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
            $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

            // Kiểm tra phần mở rộng file có hợp lệ không
            if (!in_array($file_ext, $allowed_extensions)) {
                $error = "Chỉ cho phép tải lên các định dạng ảnh: " . implode(', ', $allowed_extensions);
            } 
            // Kiểm tra dung lượng file (giới hạn 2MB)
            elseif ($file_size > 2 * 1024 * 1024) {
                $error = "Dung lượng hình ảnh không được vượt quá 2MB.";
            } 
            // Nếu không có lỗi, tiến hành lưu file
            else {
                // Tạo tên file ngẫu nhiên để tránh trùng tên ảnh cũ trên server
                $image_name = uniqid('prod_') . '.' . $file_ext;
                
                // Định nghĩa đường dẫn tuyệt đối thư mục lưu trữ ảnh sản phẩm
                $upload_dir = __DIR__ . '/../../uploads/products/';
                
                // Tạo thư mục tự động nếu chưa có trên ổ đĩa
                if (!is_dir($upload_dir)) {
                    mkdir($upload_dir, 0777, true);
                }
                
                $dest_path = $upload_dir . $image_name;

                // Di chuyển file từ thư mục tạm của server sang thư mục dự án
                if (!move_uploaded_file($file_tmp_path, $dest_path)) {
                    $error = "Không thể lưu trữ hình ảnh trên máy chủ.";
                }
            }
        }

        // 4. Thực hiện Insert vào Cơ sở dữ liệu nếu không có lỗi nào
        if (empty($error)) {
            try {
                $sql = "INSERT INTO products (category_id, name, price, quantity, image, description) 
                        VALUES (:category_id, :name, :price, :quantity, :image, :description)";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([
                    'category_id' => $category_id,
                    'name' => $name,
                    'price' => $price,
                    'quantity' => $quantity,
                    'image' => $image_name,
                    'description' => $description
                ]);

                $_SESSION['success'] = "Thêm sản phẩm thành công!";
                redirect(BASE_URL . 'admin/products/trang_chu.php');
            } catch (PDOException $e) {
                $error = "Lỗi khi lưu sản phẩm: " . $e->getMessage();
            }
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
            <li><a href="<?php echo BASE_URL; ?>admin/categories/trang_chu.php"><i class="fas fa-tags"></i> Danh Mục</a></li>
            <li class="active"><a href="<?php echo BASE_URL; ?>admin/products/trang_chu.php"><i class="fas fa-box"></i> Sản Phẩm</a></li>
            <li><a href="<?php echo BASE_URL; ?>admin/orders/trang_chu.php"><i class="fas fa-shopping-bag"></i> Đơn Hàng</a></li>
            <li><a href="<?php echo BASE_URL; ?>admin/users/trang_chu.php"><i class="fas fa-users"></i> Người Dùng</a></li>
        </ul>
    </aside>

    <!-- Nội dung chính -->
    <main class="admin-main">
        <div class="admin-title-area">
            <h2>Thêm Sản Phẩm Mới</h2>
            <a href="trang_chu.php" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Quay Lại</a>
        </div>

        <!-- Thông báo lỗi nếu có -->
        <?php if (!empty($error)): ?>
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-circle"></i> <?php echo sanitize($error); ?>
            </div>
        <?php endif; ?>

        <!-- Form nhập dữ liệu, bắt buộc phải có enctype="multipart/form-data" để upload file -->
        <form action="" method="POST" enctype="multipart/form-data" class="admin-form">
            <div class="form-group">
                <label for="name">Tên Sản Phẩm *</label>
                <input type="text" name="name" id="name" placeholder="Ví dụ: Chuột Logitech G502..." value="<?php echo isset($name) ? sanitize($name) : ''; ?>" required>
            </div>

            <div class="form-group">
                <label for="category_id">Danh Mục Sản Phẩm *</label>
                <select name="category_id" id="category_id" required>
                    <option value="">-- Chọn danh mục --</option>
                    <?php foreach ($categories as $cat): ?>
                        <option value="<?php echo $cat['id']; ?>" <?php echo (isset($category_id) && $category_id == $cat['id']) ? 'selected' : ''; ?>>
                            <?php echo sanitize($cat['name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label for="price">Đơn Giá (VND) *</label>
                <input type="number" name="price" id="price" min="0" step="1000" placeholder="Ví dụ: 1250000" value="<?php echo isset($price) ? sanitize($price) : ''; ?>" required>
            </div>

            <div class="form-group">
                <label for="quantity">Số Lượng Tồn Kho *</label>
                <input type="number" name="quantity" id="quantity" min="0" placeholder="Ví dụ: 50" value="<?php echo isset($quantity) ? sanitize($quantity) : ''; ?>" required>
            </div>

            <div class="form-group">
                <label for="image">Hình Ảnh Sản Phẩm</label>
                <input type="file" name="image" id="image" accept="image/*">
                <p style="font-size: 0.8rem; color: var(--text-light); margin-top: 0.2rem;">Hỗ trợ JPG, PNG, WEBP. Tối đa 2MB.</p>
            </div>

            <div class="form-group">
                <label for="description">Mô Tả Sản Phẩm</label>
                <textarea name="description" id="description" rows="5" placeholder="Nhập mô tả chi tiết sản phẩm..."><?php echo isset($description) ? sanitize($description) : ''; ?></textarea>
            </div>

            <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Lưu Sản Phẩm</button>
        </form>
    </main>
</div>

<?php
// Nhúng Footer
require_once __DIR__ . '/../../includes/footer.php';
?>

