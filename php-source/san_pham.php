<?php
// --------------------------------------------------------
// TRANG CỬA HÀNG - LIST PRODUCTS
// File: san_pham.php
// --------------------------------------------------------

require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/functions.php';

$category_id = isset($_GET['category_id']) && is_numeric($_GET['category_id']) ? (int)$_GET['category_id'] : null;
$selected_category = null;

try {
    $sql = "SELECT p.*, c.name AS category_name 
            FROM products p 
            LEFT JOIN categories c ON p.category_id = c.id";
    $params = [];

    if ($category_id !== null) {
        $sql .= " WHERE p.category_id = :category_id";
        $params['category_id'] = $category_id;
    }

    $sql .= " ORDER BY p.id DESC";
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $products = $stmt->fetchAll();

    if ($category_id !== null) {
        $cat_stmt = $pdo->prepare("SELECT name FROM categories WHERE id = :id LIMIT 1");
        $cat_stmt->execute(['id' => $category_id]);
        $selected_category = $cat_stmt->fetch();
    }
} catch (PDOException $e) {
    $products = [];
    $selected_category = null;
}

$page_title = $selected_category ? 'Danh Mục: ' . $selected_category['name'] : 'Cửa Hàng';
$page_css = 'product.css';
require_once __DIR__ . '/includes/header.php';
?>

<div class="page-header" style="margin-bottom: 2rem;">
    <h1 style="font-size: 2rem; font-weight: 800; color: var(--secondary-color); margin: 0;">
        <?php echo sanitize($selected_category ? $selected_category['name'] : 'Cửa Hàng'); ?>
    </h1>
    <p style="margin: 0.5rem 0 0; color: var(--text-light);">
        Khám phá các sản phẩm công nghệ mới nhất với giá tốt nhất.
    </p>
</div>

<div class="breadcrumb" style="margin-bottom: 2rem; font-size: 0.9rem; color: var(--text-light);">
    <a href="<?php echo BASE_URL; ?>trang_chu.php">Trang Chủ</a> /
    <span style="color: var(--secondary-color); font-weight: 600;">Cửa Hàng</span>
</div>

<?php if (!empty($products)): ?>
    <div class="product-grid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(260px, 1fr)); gap: 2rem;">
        <?php foreach ($products as $product): ?>
            <div class="product-card" style="background: var(--white); border-radius: var(--radius-lg); border: 1px solid var(--border-color); overflow: hidden; box-shadow: var(--shadow-sm); display: flex; flex-direction: column; height: 100%; transition: var(--transition);">
                <div class="product-image" style="background-color: #f1f5f9; padding: 2rem; display: flex; align-items: center; justify-content: center; height: 200px; position: relative;">
                    <?php if (!empty($product['image'])): ?>
                        <img src="<?php echo BASE_URL; ?>uploads/products/<?php echo sanitize($product['image']); ?>" alt="<?php echo sanitize($product['name']); ?>" style="max-width: 100%; max-height: 150px; object-fit: contain;">
                    <?php else: ?>
                        <i class="fas fa-laptop" style="font-size: 4rem; color: #cbd5e1;"></i>
                    <?php endif; ?>
                    <span style="position: absolute; top: 10px; left: 10px; background-color: rgba(37, 99, 235, 0.1); color: var(--primary-color); font-size: 0.75rem; font-weight: 700; padding: 0.2rem 0.6rem; border-radius: var(--radius-sm);">
                        <?php echo sanitize($product['category_name'] ?? 'Khác'); ?>
                    </span>
                </div>

                <div class="product-body" style="padding: 1.5rem; display: flex; flex-direction: column; flex: 1; justify-content: space-between;">
                    <div>
                        <h3 style="font-size: 1.08rem; font-weight: 700; color: var(--secondary-color); margin-bottom: 0.5rem; line-height: 1.4;">
                            <?php echo sanitize($product['name']); ?>
                        </h3>
                        <p style="font-size: 0.9rem; color: var(--text-light); margin-bottom: 1rem; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; height: 2.8em;">
                            <?php echo sanitize($product['description'] ?? ''); ?>
                        </p>
                    </div>

                    <div>
                        <div class="product-price" style="font-size: 1.25rem; font-weight: 800; color: var(--primary-color); margin-bottom: 1rem;">
                            <?php echo format_price($product['price']); ?>
                        </div>

                        <div style="display: flex; gap: 0.75rem;">
                            <a href="<?php echo BASE_URL; ?>chi_tiet_san_pham.php?id=<?php echo $product['id']; ?>" class="btn btn-secondary" style="flex: 1; text-align: center;">
                                Chi tiết
                            </a>

                            <form action="<?php echo BASE_URL; ?>gio_hang.php" method="POST" style="flex: 1; margin: 0;">
                                <input type="hidden" name="action" value="add">
                                <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                                <input type="hidden" name="quantity" value="1">
                                <button type="submit" class="btn btn-primary" style="width: 100%;">
                                    Thêm giỏ
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php else: ?>
    <div class="empty-state" style="padding: 3rem 1rem; text-align: center; border: 1px dashed var(--border-color); background: #f8fafc; border-radius: var(--radius-lg); color: var(--text-light);">
        <i class="fas fa-box-open" style="font-size: 2rem; margin-bottom: 1rem; display: block;"></i>
        Chưa có sản phẩm nào trong danh mục này.
    </div>
<?php endif; ?>

<?php require_once __DIR__ . '/includes/footer.php'; ?>

