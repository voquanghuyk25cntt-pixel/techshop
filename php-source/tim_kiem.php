<?php
// --------------------------------------------------------
// BƯỚC 15: TRANG TÌM KIẾM SẢN PHẨM (SEARCH)
// File: tim_kiem.php
// --------------------------------------------------------

// 1. Nạp cấu hình, kết nối CSDL và helper functions LÊN ĐẦU TRANG
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/functions.php';

// Lấy từ khóa tìm kiếm từ phương thức GET URL (ví dụ: tim_kiem.php?q=chuot)
$q = isset($_GET['q']) ? trim($_GET['q']) : '';
$products = [];
$error = '';

// 2. Chỉ thực hiện truy vấn nếu từ khóa không trống
if ($q !== '') {
    try {
        // Sử dụng câu lệnh Prepared Statement với từ khóa LIKE an toàn tuyệt đối chống SQL Injection
        $sql = "SELECT p.*, c.name AS category_name 
                FROM products p 
                INNER JOIN categories c ON p.category_id = c.id 
                WHERE p.name LIKE :q 
                ORDER BY p.id DESC";
        
        $stmt = $pdo->prepare($sql);
        
        // Ràng buộc tham số tìm kiếm (thêm ký tự % ở hai đầu để tìm kiếm gần đúng)
        $stmt->execute(['q' => '%' . $q . '%']);
        $products = $stmt->fetchAll();
    } catch (PDOException $e) {
        $error = "Có lỗi xảy ra khi tìm kiếm: " . $e->getMessage();
    }
} else {
    // Nếu truy cập trực tiếp không truyền từ khóa, chuyển hướng về trang danh sách sản phẩm
    redirect(BASE_URL . 'san_pham.php');
}

// 3. Thiết lập tiêu đề trang động và nhúng Header HTML
$page_title = "Kết quả tìm kiếm cho '" . sanitize($q) . "'";
$page_css = "product.css";
require_once __DIR__ . '/includes/header.php';
?>

<!-- Phần hiển thị kết quả -->
<div class="search-results-area">
    <div class="search-header" style="margin-bottom: 2.5rem; border-bottom: 1px solid var(--border-color); padding-bottom: 1rem;">
        <h2 style="font-size: 1.8rem; color: var(--secondary-color); font-weight: 700;">
            Kết quả tìm kiếm cho: "<span style="color: var(--primary-color);"><?php echo sanitize($q); ?></span>"
        </h2>
        <p style="color: var(--text-light); margin-top: 0.5rem;">
            Tìm thấy <strong><?php echo count($products); ?></strong> sản phẩm tương thích.
        </p>
    </div>

    <!-- Hiển thị lỗi nếu có -->
    <?php if (!empty($error)): ?>
        <div class="alert alert-danger">
            <i class="fas fa-exclamation-circle"></i> <?php echo sanitize($error); ?>
        </div>
    <?php endif; ?>

    <!-- Khung lưới hiển thị sản phẩm kết quả -->
    <div class="product-grid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(260px, 1fr)); gap: 2rem;">
        <?php if (!empty($products)): ?>
            <?php foreach ($products as $product): ?>
                <div class="product-card" style="background: var(--white); border-radius: var(--radius-lg); border: 1px solid var(--border-color); overflow: hidden; box-shadow: var(--shadow-sm); transition: var(--transition); display: flex; flex-direction: column; height: 100%;" onmouseover="this.style.transform='translateY(-5px)'; this.style.boxShadow='var(--shadow-lg)';" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='var(--shadow-sm)';">
                    <!-- Ảnh sản phẩm -->
                    <div class="product-image" style="background-color: #f1f5f9; padding: 2rem; display: flex; align-items: center; justify-content: center; height: 200px; position: relative;">
                        <?php if (!empty($product['image'])): ?>
                            <img src="<?php echo BASE_URL . 'uploads/products/' . sanitize($product['image']); ?>" alt="product" style="max-height: 100%; object-fit: contain;">
                        <?php else: ?>
                            <i class="fas fa-laptop" style="font-size: 4rem; color: #cbd5e1;"></i>
                        <?php endif; ?>
                        
                        <span style="position: absolute; top: 10px; left: 10px; background-color: rgba(37, 99, 235, 0.1); color: var(--primary-color); font-size: 0.75rem; font-weight: 700; padding: 0.2rem 0.6rem; border-radius: var(--radius-sm);">
                            <?php echo sanitize($product['category_name']); ?>
                        </span>
                    </div>

                    <!-- Thân card -->
                    <div class="product-body" style="padding: 1.5rem; display: flex; flex-direction: column; flex: 1; justify-content: space-between;">
                        <div>
                            <h3 style="font-size: 1.1rem; font-weight: 600; color: var(--secondary-color); margin-bottom: 0.5rem; line-height: 1.4;">
                                <?php echo sanitize($product['name']); ?>
                            </h3>
                            <div class="product-stock" style="font-size: 0.85rem; margin-bottom: 1rem;">
                                Tình trạng: 
                                <?php if ($product['quantity'] > 0): ?>
                                    <span style="color: var(--success-color); font-weight: 700;">Còn hàng (<?php echo $product['quantity']; ?>)</span>
                                <?php else: ?>
                                    <span style="color: var(--danger-color); font-weight: 700;">Hết hàng</span>
                                <?php endif; ?>
                            </div>
                        </div>
                        
                        <div>
                            <div class="product-price" style="font-size: 1.25rem; font-weight: 800; color: var(--primary-color); margin-bottom: 1rem;">
                                <?php echo format_price($product['price']); ?>
                            </div>
                            <a href="<?php echo BASE_URL; ?>chi_tiet_san_pham.php?id=<?php echo $product['id']; ?>" style="border: 1px solid var(--primary-color); color: var(--primary-color); display: block; text-align: center; padding: 0.6rem; border-radius: var(--radius-md); font-weight: 600; font-size: 0.9rem; transition: var(--transition);" onmouseover="this.style.backgroundColor='var(--primary-color)'; this.style.color='white';" onmouseout="this.style.backgroundColor='transparent'; this.style.color='var(--primary-color)';">
                                Chi Tiết Sản Phẩm
                            </a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div style="grid-column: 1/-1; text-align: center; padding: 5rem 0;">
                <i class="fas fa-search-minus" style="font-size: 3rem; color: #cbd5e1; margin-bottom: 1rem;"></i>
                <p style="color: var(--text-light); font-size: 1.1rem;">Không tìm thấy sản phẩm nào khớp với từ khóa của bạn.</p>
                <a href="<?php echo BASE_URL; ?>san_pham.php" class="btn btn-primary" style="margin-top: 1.5rem; display: inline-flex;">Quay lại Cửa Hàng</a>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php
// Nhúng Footer HTML
require_once __DIR__ . '/includes/footer.php';
?>

