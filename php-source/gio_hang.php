<?php
// --------------------------------------------------------
// BƯỚC 17: GIỎ HÀNG SỬ DỤNG PHP SESSION
// File: gio_hang.php
// --------------------------------------------------------

// 1. Nạp cấu hình, kết nối CSDL và helper functions LÊN ĐẦU TRANG
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/functions.php';

// Khởi tạo mảng giỏ hàng trong Session nếu chưa tồn tại
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

$error = '';
$success = '';

// 2. XỬ LÝ CÁC THAO TÁC CỦA GIỎ HÀNG
// Chức năng: THÊM SẢN PHẨM (POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add') {
    $product_id = (int)$_POST['product_id'];
    $qty_to_add = isset($_POST['quantity']) ? (int)$_POST['quantity'] : 1;

    try {
        // Kiểm tra xem sản phẩm có tồn tại và lấy số lượng trong kho
        $stmt = $pdo->prepare("SELECT * FROM products WHERE id = :id LIMIT 1");
        $stmt->execute(['id' => $product_id]);
        $product = $stmt->fetch();

        if ($product) {
            // Kiểm tra số lượng tồn kho
            $current_in_cart = isset($_SESSION['cart'][$product_id]) ? $_SESSION['cart'][$product_id]['quantity'] : 0;
            $total_requested = $current_in_cart + $qty_to_add;

            if ($total_requested > $product['quantity']) {
                $_SESSION['error'] = "Không thể thêm sản phẩm. Số lượng yêu cầu vượt quá tồn kho hiện tại (" . $product['quantity'] . ").";
            } else {
                // Thêm hoặc cộng dồn vào giỏ hàng
                if (isset($_SESSION['cart'][$product_id])) {
                    $_SESSION['cart'][$product_id]['quantity'] = $total_requested;
                } else {
                    $_SESSION['cart'][$product_id] = [
                        'id' => $product['id'],
                        'name' => $product['name'],
                        'price' => (float)$product['price'],
                        'image' => $product['image'],
                        'quantity' => $qty_to_add,
                        'max_quantity' => $product['quantity']
                    ];
                }
                $_SESSION['success'] = "Đã thêm '" . $product['name'] . "' vào giỏ hàng thành công!";
            }
        }
    } catch (PDOException $e) {
        $_SESSION['error'] = "Có lỗi xảy ra: " . $e->getMessage();
    }
    // Chuyển hướng POST sang GET (PRG Pattern) để tránh tải lại trang bị submit lại form
    redirect(BASE_URL . 'gio_hang.php');
}

// Chức năng: XÓA SẢN PHẨM (GET)
if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    $product_id = (int)$_GET['id'];
    if (isset($_SESSION['cart'][$product_id])) {
        unset($_SESSION['cart'][$product_id]);
        $_SESSION['success'] = "Đã xóa sản phẩm khỏi giỏ hàng.";
    }
    redirect(BASE_URL . 'gio_hang.php');
}

// Chức năng: CẬP NHẬT SỐ LƯỢNG (GET)
if (isset($_GET['action']) && $_GET['action'] === 'update' && isset($_GET['id']) && isset($_GET['qty'])) {
    $product_id = (int)$_GET['id'];
    $new_qty = (int)$_GET['qty'];

    if (isset($_SESSION['cart'][$product_id])) {
        if ($new_qty <= 0) {
            unset($_SESSION['cart'][$product_id]);
            $_SESSION['success'] = "Đã xóa sản phẩm khỏi giỏ hàng.";
        } else {
            // Lấy lượng hàng tồn kho thực tế từ CSDL để đối chứng
            try {
                $stmt = $pdo->prepare("SELECT quantity FROM products WHERE id = :id LIMIT 1");
                $stmt->execute(['id' => $product_id]);
                $stock = $stmt->fetchColumn();
                
                if ($new_qty > $stock) {
                    $_SESSION['error'] = "Không thể cập nhật. Kho chỉ còn " . $stock . " sản phẩm.";
                } else {
                    $_SESSION['cart'][$product_id]['quantity'] = $new_qty;
                    $_SESSION['success'] = "Cập nhật số lượng thành công.";
                }
            } catch (PDOException $e) {
                $_SESSION['error'] = "Có lỗi xảy ra: " . $e->getMessage();
            }
        }
    }
    redirect(BASE_URL . 'gio_hang.php');
}

// 3. ĐỒNG BỘ GIAO DIỆN VÀ NHÚNG HEADER
$page_title = "Giỏ Hàng Của Bạn";
$page_css = "cart.css";
require_once __DIR__ . '/includes/header.php';
?>

<h2 style="font-size: 2rem; color: var(--secondary-color); margin-bottom: 2rem; font-weight: 800;">Giỏ Hàng</h2>

<!-- Hiển thị thông báo Toast nếu có -->
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

<?php if (!empty($_SESSION['cart'])): ?>
    <div class="cart-layout">
        <!-- Cột trái: Bảng danh sách sản phẩm trong giỏ -->
        <div class="cart-main">
            <table class="cart-table">
                <thead>
                    <tr>
                        <th>Sản Phẩm</th>
                        <th style="width: 120px;">Giá Bán</th>
                        <th style="width: 130px; text-align: center;">Số Lượng</th>
                        <th style="width: 150px; text-align: right;">Thành Tiền</th>
                        <th style="width: 60px; text-align: center;">Xóa</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($_SESSION['cart'] as $item): 
                        $subtotal = $item['price'] * $item['quantity'];
                    ?>
                        <tr>
                            <td>
                                <div class="cart-item-product">
                                    <?php if (!empty($item['image'])): ?>
                                        <img src="<?php echo BASE_URL . 'uploads/products/' . sanitize($item['image']); ?>" alt="img" class="cart-item-img">
                                    <?php else: ?>
                                        <div class="cart-item-img" style="display: flex; align-items: center; justify-content: center; color: #94a3b8;"><i class="fas fa-laptop"></i></div>
                                    <?php endif; ?>
                                    <div>
                                        <div class="cart-item-name"><?php echo sanitize($item['name']); ?></div>
                                    </div>
                                </div>
                            </td>
                            <td style="font-weight: 500;"><?php echo format_price($item['price']); ?></td>
                            <td style="text-align: center;">
                                <div class="quantity-control" style="margin: 0 auto;">
                                    <a href="?action=update&id=<?php echo $item['id']; ?>&qty=<?php echo $item['quantity'] - 1; ?>">
                                        <button type="button">-</button>
                                    </a>
                                    <input type="text" value="<?php echo $item['quantity']; ?>" readonly>
                                    <a href="?action=update&id=<?php echo $item['id']; ?>&qty=<?php echo $item['quantity'] + 1; ?>">
                                        <button type="button">+</button>
                                    </a>
                                </div>
                            </td>
                            <td style="text-align: right; font-weight: 700; color: var(--secondary-color);">
                                <?php echo format_price($subtotal); ?>
                            </td>
                            <td style="text-align: center;">
                                <a href="?action=delete&id=<?php echo $item['id']; ?>" class="btn-delete" style="color: var(--danger-color); font-size: 1.1rem;" onclick="return confirm('Xóa sản phẩm này khỏi giỏ hàng?');">
                                    <i class="fas fa-trash-alt"></i>
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            
            <a href="<?php echo BASE_URL; ?>san_pham.php" style="color: var(--primary-color); font-weight: 600; display: inline-flex; align-items: center; gap: 0.5rem; margin-top: 1rem;">
                <i class="fas fa-arrow-left"></i> Tiếp tục mua sắm
            </a>
        </div>

        <!-- Cột phải: Hóa đơn tổng cộng tiền -->
        <div class="cart-summary">
            <div class="summary-title">Tóm Tắt Đơn Hàng</div>
            
            <div class="summary-row">
                <span>Số lượng sản phẩm:</span>
                <strong><?php echo get_cart_count(); ?></strong>
            </div>

            <div class="summary-row">
                <span>Tạm tính:</span>
                <strong><?php echo format_price(get_cart_total()); ?></strong>
            </div>

            <div class="summary-row">
                <span>Phí vận chuyển:</span>
                <strong style="color: var(--success-color);">Miễn phí</strong>
            </div>

            <div class="summary-row total">
                <span>Tổng cộng:</span>
                <span><?php echo format_price(get_cart_total()); ?></span>
            </div>

            <?php if (is_logged_in()): ?>
                <a href="<?php echo BASE_URL; ?>thanh_toan.php" class="btn-checkout">Tiến Hành Đặt Hàng</a>
            <?php else: ?>
                <a href="<?php echo BASE_URL; ?>auth/login.php" class="btn-checkout" style="background-color: var(--secondary-color);">Đăng Nhập Để Thanh Toán</a>
            <?php endif; ?>
        </div>
    </div>
<?php else: ?>
    <div class="cart-empty" style="background: var(--white); border-radius: var(--radius-lg); border: 1px solid var(--border-color);">
        <i class="fas fa-shopping-basket"></i>
        <h2>Giỏ hàng của bạn đang trống!</h2>
        <p>Hãy dạo quanh cửa hàng và chọn những sản phẩm công nghệ bạn yêu thích.</p>
        <a href="<?php echo BASE_URL; ?>san_pham.php" class="btn btn-primary" style="padding: 0.8rem 2.5rem;">Mua Sắm Ngay</a>
    </div>
<?php endif; ?>

<?php
// Nhúng Footer HTML
require_once __DIR__ . '/includes/footer.php';
?>

