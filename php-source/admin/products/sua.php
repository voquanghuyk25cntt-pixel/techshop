<?php
// --------------------------------------------------------
// BƯỚC 12: QUẢN TRỊ - CHỈNH SỬA SẢN PHẨM (PRODUCT UPDATE)
// File: admin/products/edit.php
// --------------------------------------------------------

$page_title = "Chỉnh Sửa Sản Phẩm";
$page_css = "admin.css";

// Nhúng chốt chặn kiểm tra quyền Admin trước khi xuất HTML
require_once __DIR__ . '/../../includes/admin-check.php';
// Nhúng Header (Tự động nạp cấu hình, kết nối CSDL và các hàm kiểm tra)
require_once __DIR__ . '/../../includes/header.php';

// 1. Kiểm tra ID sản phẩm trên thanh địa chỉ (GET)
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    $_SESSION['error'] = "Yêu cầu ID sản phẩm hợp lệ.";
    redirect('trang_chu.php');
}

$id = (int)$_GET['id'];
$error = '';

// 2. Lấy dữ liệu sản phẩm hiện có
try {
    $stmt = $pdo->prepare("SELECT * FROM products WHERE id = :id LIMIT 1");
    $stmt->execute(['id' => $id]);
    $product = $stmt->fetch();

    if (!$product) {
        $_SESSION['error'] = "Không tìm thấy sản phẩm yêu cầu.";
        redirect('trang_chu.php');
    }
} catch (PDOException $e) {
    $_SESSION['error'] = "Lỗi CSDL: " . $e->getMessage();
    redirect('trang_chu.php');
}

// Lấy danh sách danh mục để đổ vào dropdown
try {
    $cat_stmt = $pdo->query("SELECT * FROM categories ORDER BY name ASC");
    $categories = $cat_stmt->fetchAll();
} catch (PDOException $e) {
    $categories = [];
}

// 3. Xử lý khi Admin ấn Cập Nhật Sản Phẩm
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $category_id = $_POST['category_id'];
    $price = trim($_POST['price']);
    $quantity = trim($_POST['quantity']);
    $description = trim($_POST['description']);
    
    // Giữ nguyên ảnh cũ làm mặc định
    $image_name = $product['image']; 

    // Kiểm tra rỗng
    if (empty($name) || empty($category_id) || $price === '' || $quantity === '') {
        $error = "Vui lòng nhập đầy đủ tên, danh mục, đơn giá và số lượng tồn kho.";
    } 
    // Kiểm tra định dạng số dương
    elseif (!is_numeric($price) || $price < 0) {
        $error = "Đơn giá sản phẩm phải là một số dương.";
    } 
    elseif (!is_numeric($quantity) || $quantity < 0) {
        $error = "Số lượng sản phẩm phải là một số nguyên dương.";
    } 
    // Xử lý upload ảnh mới (nếu có)
    else {
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $file_tmp_path = $_FILES['image']['tmp_name'];
            $file_name = $_FILES['image']['name'];
            $file_size = $_FILES['image']['size'];
            $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
            $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

            if (!in_array($file_ext, $allowed_extensions)) {
                $error = "Chỉ cho phép tải lên các định dạng ảnh: " . implode(', ', $allowed_extensions);
            } 
            elseif ($file_size > 2 * 1024 * 1024) {
                $error = "Dung lượng hình ảnh không được vượt quá 2MB.";
            } 
            else {
                // Đặt tên ảnh mới
                $new_image_name = uniqid('prod_') . '.' . $file_ext;
                $upload_dir = __DIR__ . '/../../uploads/products/';
                $dest_path = $upload_dir . $new_image_name;

                if (move_uploaded_file($file_tmp_path, $dest_path)) {
                    // Nếu tải ảnh mới thành công, xóa ảnh cũ trên đĩa cứng để giải phóng dung lượng
                    if (!empty($product['image'])) {
                        $old_image_path = $upload_dir . $product['image'];
                        if (file_exists($old_image_path)) {
                            unlink($old_image_path);
                        }
                    }
                    // Cập nhật tên ảnh mới vào biến lưu Database
                    $image_name = $new_image_name;
                } else {
                    $error = "Không thể lưu trữ hình ảnh mới trên máy chủ.";
                }
            }
        }

        // 4. Cập nhật dữ liệu nếu không gặp lỗi
        if (empty($error)) {
            try {
                $sql = "UPDATE products 
                        SET category_id = :category_id, name = :name, price = :price, quantity = :quantity, image = :image, description = :description 
                        WHERE id = :id";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([
                    'category_id' => $category_id,
                    'name' => $name,
                    'price' => $price,
                    'quantity' => $quantity,
                    'image' => $image_name,
                    'description' => $description,
                    'id' => $id
                ]);

                $_SESSION['success'] = "Cập nhật sản phẩm thành công!";
                redirect(BASE_URL . 'admin/products/trang_chu.php');
            } catch (PDOException $e) {
                $error = "Lỗi CSDL khi cập nhật: " . $e->getMessage();
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
            <h2>Chỉnh Sửa Sản Phẩm</h2>
            <a href="trang_chu.php" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Quay Lại</a>
        </div>

        <!-- Thông báo lỗi nếu có -->
        <?php if (!empty($error)): ?>
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-circle"></i> <?php echo sanitize($error); ?>
            </div>
        <?php endif; ?>

        <!-- Form chỉnh sửa -->
        <form action="" method="POST" enctype="multipart/form-data" class="admin-form">
            <div class="form-group">
                <label for="name">Tên Sản Phẩm *</label>
                <input type="text" name="name" id="name" value="<?php echo sanitize($product['name']); ?>" required>
            </div>

            <div class="form-group">
                <label for="category_id">Danh Mục Sản Phẩm *</label>
                <select name="category_id" id="category_id" required>
                    <?php foreach ($categories as $cat): ?>
                        <option value="<?php echo $cat['id']; ?>" <?php echo ($product['category_id'] == $cat['id']) ? 'selected' : ''; ?>>
                            <?php echo sanitize($cat['name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label for="price">Đơn Giá (VND) *</label>
                <input type="number" name="price" id="price" min="0" step="1000" value="<?php echo (int)$product['price']; ?>" required>
            </div>

            <div class="form-group">
                <label for="quantity">Số Lượng Tồn Kho *</label>
                <input type="number" name="quantity" id="quantity" min="0" value="<?php echo $product['quantity']; ?>" required>
            </div>

            <div class="form-group">
                <label>Hình Ảnh Hiện Tại</label>
                <div style="margin-bottom: 0.5rem;">
                    <?php if (!empty($product['image'])): ?>
                        <img src="<?php echo BASE_URL . 'uploads/products/' . sanitize($product['image']); ?>" alt="product" style="width: 120px; height: 120px; object-fit: cover; border-radius: var(--radius-md); border: 1px solid var(--border-color);">
                    <?php else: ?>
                        <p style="font-size: 0.9rem; color: var(--text-light);">Sản phẩm này chưa có ảnh.</p>
                    <?php endif; ?>
                </div>
                <label for="image">Thay đổi hình ảnh mới (Nếu có)</label>
                <input type="file" name="image" id="image" accept="image/*">
            </div>

            <div class="form-group">
                <label for="description">Mô Tả Sản Phẩm</label>
                <textarea name="description" id="description" rows="5"><?php echo sanitize($product['description']); ?></textarea>
            </div>

            <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Cập Nhật Sản Phẩm</button>
        </form>
    </main>
</div>

<?php
// Nhúng Footer
require_once __DIR__ . '/../../includes/footer.php';
?>

