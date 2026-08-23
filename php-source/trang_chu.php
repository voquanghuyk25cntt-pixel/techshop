<?php
// --------------------------------------------------------
// TRANG CHỦ TECHSHOP
// Chức năng: hiển thị 4 sản phẩm mới nhất
// --------------------------------------------------------

$page_title = 'Trang Chủ - TechShop Bán Đồ Công Nghệ';
require_once __DIR__ . '/includes/header.php';

try {
    $sql = "SELECT p.*, c.name AS category_name
            FROM products p
            INNER JOIN categories c ON p.category_id = c.id
            ORDER BY p.id DESC
            LIMIT 4";

    $stmt = $pdo->query($sql);
    $featured_products = $stmt->fetchAll();
} catch (PDOException $e) {
    $featured_products = [];
}
?>

<!-- 1. Banner chào mừng (Hero Section) -->
<section class="hero-section" style="background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%); color: white; padding: 4rem 2rem; border-radius: var(--radius-lg); margin-bottom: 3rem; text-align: center; box-shadow: var(--shadow-md);">
    <div class="hero-content" style="max-width: 800px; margin: 0 auto;">
        <h1 style="font-size: 2.5rem; font-weight: 800; margin-bottom: 1rem; line-height: 1.2;">Nâng Tầm Trải Nghiệm Công Nghệ</h1>
        <p style="font-size: 1.1rem; color: #94a3b8; margin-bottom: 2rem;">Chuyên cung cấp chuột gaming, bàn phím cơ, tai nghe chơi game chính hãng với mức giá ưu đãi nhất dành cho học sinh, sinh viên.</p>
        <a href="<?php echo BASE_URL; ?>san_pham.php" style="background-color: var(--primary-color); color: white; padding: 0.8rem 2rem; border-radius: var(--radius-md); font-weight: 700; display: inline-block; transition: var(--transition);" onmouseover="this.style.backgroundColor='#1d4ed8'" onmouseout="this.style.backgroundColor='#2563eb'">Mua Sắm Ngay</a>
    </div>
</section>

<!-- 2. Phần danh sách sản phẩm nổi bật -->
<section class="featured-section">
    <div class="section-header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
        <h2 style="font-size: 1.8rem; font-weight: 700; color: var(--secondary-color); position: relative;">Sản Phẩm Mới Nhất</h2>
        <a href="<?php echo BASE_URL; ?>san_pham.php" style="color: var(--primary-color); font-weight: 600;">Xem tất cả <i class="fas fa-arrow-right"></i></a>
    </div>

    <!-- Khung lưới hiển thị sản phẩm -->
    <div class="product-grid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(260px, 1fr)); gap: 2rem;">
        <?php if (!empty($featured_products)): ?>
            <?php foreach ($featured_products as $product): ?>
                <div class="product-card" style="background: var(--white); border-radius: var(--radius-lg); border: 1px solid var(--border-color); overflow: hidden; box-shadow: var(--shadow-sm); transition: var(--transition); display: flex; flex-direction: column; height: 100%;" onmouseover="this.style.transform='translateY(-5px)'; this.style.boxShadow='var(--shadow-lg)';" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='var(--shadow-sm)';">
                    <!-- Ảnh sản phẩm -->
                    <div class="product-image" style="background-color: #f1f5f9; padding: 2rem; display: flex; align-items: center; justify-content: center; height: 200px; position: relative;">
                        <!-- Icon thể hiện loại thiết bị tương ứng vì chưa có ảnh thật trong thư mục uploads -->
                        <i class="fas fa-laptop" style="font-size: 4rem; color: #cbd5e1;"></i>
                        <span style="position: absolute; top: 10px; left: 10px; background-color: rgba(37, 99, 235, 0.1); color: var(--primary-color); font-size: 0.75rem; font-weight: 700; padding: 0.2rem 0.6rem; border-radius: var(--radius-sm);">
                            <?php echo sanitize($product['category_name']); ?>
                        </span>
                    </div>

                    <!-- Nội dung sản phẩm -->
                    <div class="product-body" style="padding: 1.5rem; display: flex; flex-direction: column; flex: 1; justify-content: space-between;">
                        <div>
                            <h3 style="font-size: 1.1rem; font-weight: 600; color: var(--secondary-color); margin-bottom: 0.5rem; line-height: 1.4;">
                                <?php echo sanitize($product['name']); ?>
                            </h3>
                            <p style="font-size: 0.9rem; color: var(--text-light); margin-bottom: 1rem; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; height: 2.8em;">
                                <?php echo sanitize($product['description']); ?>
                            </p>
                        </div>
                        
                        <div>
                            <div class="product-price" style="font-size: 1.25rem; font-weight: 700; color: var(--primary-color); margin-bottom: 1rem;">
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
            <p style="grid-column: 1/-1; text-align: center; color: var(--text-light); padding: 3rem 0;">Chưa có sản phẩm nào nổi bật.</p>
        <?php endif; ?>
    </div>
</section>

<?php
// Nhúng footer
require_once 'includes/footer.php';
?>

