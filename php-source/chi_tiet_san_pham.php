<?php
// --------------------------------------------------------
// BƯỚC 14: TRANG CHI TIẾT SẢN PHẨM (PRODUCT DETAIL)
// File: chi_tiet_san_pham.php
// --------------------------------------------------------

// 1. Nạp cấu hình, kết nối CSDL và helper functions LÊN ĐẦU TRANG trước khi xuất HTML
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/functions.php';

// 2. Kiểm tra ID sản phẩm truyền từ GET URL
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    redirect(BASE_URL . 'san_pham.php');
}

$id = (int)$_GET['id'];

// 3. Lấy thông tin sản phẩm và tên danh mục của sản phẩm
try {
    $sql = "SELECT p.*, c.name AS category_name 
            FROM products p 
            INNER JOIN categories c ON p.category_id = c.id 
            WHERE p.id = :id 
            LIMIT 1";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['id' => $id]);
    $product = $stmt->fetch();

    // Nếu sản phẩm không tồn tại, chuyển hướng an toàn về cửa hàng
    if (!$product) {
        redirect(BASE_URL . 'san_pham.php');
    }
} catch (PDOException $e) {
    die("Lỗi kết nối dữ liệu: " . $e->getMessage());
}

// 4. Thiết lập tiêu đề trang động và nhúng Header HTML
$page_title = sanitize($product['name']);
$page_css = "product.css";
require_once __DIR__ . '/includes/header.php';
?>

<!-- Breadcrumb điều hướng phụ -->
<div class="breadcrumb" style="margin-bottom: 2rem; font-size: 0.9rem; color: var(--text-light);">
    <a href="<?php echo BASE_URL; ?>trang_chu.php">Trang Chủ</a> / 
    <a href="<?php echo BASE_URL; ?>san_pham.php">Cửa Hàng</a> / 
    <a href="<?php echo BASE_URL; ?>san_pham.php?category_id=<?php echo $product['category_id']; ?>"><?php echo sanitize($product['category_name']); ?></a> / 
    <span style="color: var(--secondary-color); font-weight: 600;"><?php echo sanitize($product['name']); ?></span>
</div>

<!-- Layout chi tiết sản phẩm -->
<div class="product-detail-container">
    <!-- Cột trái: Ảnh sản phẩm -->
    <div class="product-detail-image-box">
        <?php if (!empty($product['image'])): ?>
            <img src="<?php echo BASE_URL . 'uploads/products/' . sanitize($product['image']); ?>" alt="<?php echo sanitize($product['name']); ?>" style="max-width: 100%; max-height: 380px; object-fit: contain;">
        <?php else: ?>
            <i class="fas fa-laptop" style="font-size: 8rem; color: #cbd5e1;"></i>
        <?php endif; ?>
    </div>

    <!-- Cột phải: Thông tin chi tiết sản phẩm -->
    <div class="product-detail-info">
        <span class="product-detail-cat"><?php echo sanitize($product['category_name']); ?></span>
        
        <h1><?php echo sanitize($product['name']); ?></h1>
        
        <!-- Giá bán lớn nổi bật -->
        <div class="product-detail-price"><?php echo format_price($product['price']); ?></div>
        
        <!-- Trạng thái tồn kho -->
        <div class="product-detail-stock">
            Tình trạng kho hàng: 
            <?php if ($product['quantity'] > 0): ?>
                <span class="stock-status stock-in"><i class="fas fa-check-circle"></i> Còn hàng (<?php echo $product['quantity']; ?> sản phẩm có sẵn)</span>
            <?php else: ?>
                <span class="stock-status stock-out"><i class="fas fa-times-circle"></i> Hết hàng</span>
            <?php endif; ?>
        </div>

        <!-- Nút Thêm Vào Giỏ Hàng -->
        <form action="<?php echo BASE_URL; ?>gio_hang.php" method="POST">
            <input type="hidden" name="action" value="add">
            <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
            
            <div style="margin-bottom: 2rem; display: flex; align-items: center; gap: 1rem;">
                <label for="quantity_input" style="font-weight: 600; color: var(--secondary-color);">Số lượng mua:</label>
                <input type="number" name="quantity" id="quantity_input" value="1" min="1" max="<?php echo $product['quantity']; ?>" style="width: 80px; padding: 0.5rem; border: 1px solid var(--border-color); border-radius: var(--radius-md); text-align: center;" <?php echo ($product['quantity'] <= 0) ? 'disabled' : ''; ?>>
            </div>

            <button type="submit" class="btn-add-to-cart" <?php echo ($product['quantity'] <= 0) ? 'disabled' : ''; ?>>
                <i class="fas fa-cart-plus"></i> Thêm Vào Giỏ Hàng
            </button>
        </form>

        <!-- Phần mô tả sản phẩm -->
        <div class="product-detail-desc">
            <h3 style="font-size: 1.1rem; color: var(--secondary-color); margin-bottom: 0.8rem; font-weight: 700;">Mô tả sản phẩm</h3>
            <p><?php echo nl2br(sanitize($product['description'])); ?></p>
        </div>
    </div>
</div>

<?php
// Nhúng Footer HTML
require_once __DIR__ . '/includes/footer.php';
?>

