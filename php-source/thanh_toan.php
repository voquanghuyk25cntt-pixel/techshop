<?php
// --------------------------------------------------------
// BƯỚC 18: TRANG ĐẶT HÀNG / THANH TOÁN (CHECKOUT)
// File: thanh_toan.php
// --------------------------------------------------------

// 1. Nạp cấu hình, kết nối CSDL và helper functions LÊN ĐẦU TRANG
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/functions.php';

// Chốt chặn: Chỉ khách hàng đã đăng nhập mới được thanh toán
require_once __DIR__ . '/includes/auth-check.php';

// Kiểm tra xem giỏ hàng có sản phẩm không, nếu trống thì quay lại giỏ
if (empty($_SESSION['cart'])) {
    redirect(BASE_URL . 'gio_hang.php');
}

$error = '';
$success = '';

// 2. XỬ LÝ SUBMIT ĐẶT HÀNG
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $receiver_name = trim($_POST['receiver_name']);
    $receiver_phone = trim($_POST['receiver_phone']);
    $receiver_address = trim($_POST['receiver_address']);
    $order_note = trim($_POST['order_note']);

    if (empty($receiver_name) || empty($receiver_phone) || empty($receiver_address)) {
        $error = "Vui lòng điền đầy đủ Tên, Số điện thoại và Địa chỉ nhận hàng.";
    } else {
        try {
            // KHỞI ĐẦU TRANSACTION ĐỂ ĐẢM BẢO TOÀN VẸN DỮ LIỆU
            $pdo->beginTransaction();

            $total_amount = get_cart_total();
            $user_id = $_SESSION['user_id'];

            // A. Tạo bản ghi đơn hàng mới trong bảng 'orders'
            // Sử dụng thêm phần ghi chú và thông tin nhận hàng (lưu vào note của đơn hàng hoặc giả định đơn giản)
            // Để đơn giản hóa bảng cấu trúc mẫu sinh viên, ta lưu thông tin địa chỉ ghép vào phần note của đơn hàng, hoặc ghi nhận thông tin cơ bản.
            $sql_order = "INSERT INTO orders (user_id, total_amount, status) VALUES (:user_id, :total_amount, 'pending')";
            $stmt_order = $pdo->prepare($sql_order);
            $stmt_order->execute([
                'user_id' => $user_id,
                'total_amount' => $total_amount
            ]);

            // Lấy ra ID của đơn hàng vừa tạo tự động
            $order_id = $pdo->lastInsertId();

            // B. Thêm từng chi tiết sản phẩm vào bảng 'order_details' và cập nhật kho
            foreach ($_SESSION['cart'] as $product_id => $item) {
                $qty_ordered = $item['quantity'];
                $price_at_purchase = $item['price'];

                // 1. Kiểm tra lại số lượng tồn kho thực tế trong DB lúc bấm nút để khóa (SELECT FOR UPDATE)
                $stock_stmt = $pdo->prepare("SELECT quantity, name FROM products WHERE id = :id FOR UPDATE");
                $stock_stmt->execute(['id' => $product_id]);
                $db_product = $stock_stmt->fetch();

                if (!$db_product || $db_product['quantity'] < $qty_ordered) {
                    // Nếu không đủ hàng, ném ra ngoại lệ để kích hoạt Rollback
                    throw new Exception("Sản phẩm '" . $item['name'] . "' không đủ số lượng trong kho (Chỉ còn " . ($db_product['quantity'] ?? 0) . " sản phẩm).");
                }

                // 2. Insert vào 'order_details'
                $sql_detail = "INSERT INTO order_details (order_id, product_id, quantity, price) 
                               VALUES (:order_id, :product_id, :quantity, :price)";
                $stmt_detail = $pdo->prepare($sql_detail);
                $stmt_detail->execute([
                    'order_id' => $order_id,
                    'product_id' => $product_id,
                    'quantity' => $qty_ordered,
                    'price' => $price_at_purchase
                ]);

                // 3. Trừ số lượng tồn kho sản phẩm trong bảng 'products'
                $sql_update_stock = "UPDATE products SET quantity = quantity - :qty WHERE id = :id";
                $stmt_update_stock = $pdo->prepare($sql_update_stock);
                $stmt_update_stock->execute([
                    'qty' => $qty_ordered,
                    'id' => $product_id
                ]);
            }

            // COMMIT TRANSACTION NẾU MỌI THỨ THÀNH CÔNG VÀ KHÔNG GẶP LỖI TỒN KHO
            $pdo->commit();

            // Xóa sạch giỏ hàng trong Session
            unset($_SESSION['cart']);

            // Chuyển hướng sang trang mua hàng thành công
            redirect(BASE_URL . 'dat_hang_thanh_cong.php?id=' . $order_id);

        } catch (Exception $e) {
            // ROLLBACK HỦY BỎ TOÀN BỘ CÁC LỆNH SQL NẾU CÓ BẤT KỲ LỖI NÀO XẢY RA
            $pdo->rollBack();
            $error = $e->getMessage();
        }
    }
}

// 3. THIẾT LẬP GIAO DIỆN
$page_title = "Đặt Hàng Thanh Toán";
$page_css = "cart.css"; // Sử dụng chung giao diện cột chia của giỏ hàng
require_once __DIR__ . '/includes/header.php';
?>

<h2 style="font-size: 2rem; color: var(--secondary-color); margin-bottom: 2rem; font-weight: 800;">Đặt Hàng</h2>

<!-- Thông báo lỗi nếu có -->
<?php if (!empty($error)): ?>
    <div class="alert alert-danger">
        <i class="fas fa-exclamation-circle"></i> <?php echo sanitize($error); ?>
    </div>
<?php endif; ?>

<div class="cart-layout">
    <!-- Cột trái: Form nhập thông tin nhận hàng -->
    <div class="cart-main">
        <h3 style="font-size: 1.25rem; color: var(--secondary-color); margin-bottom: 1.5rem; font-weight: 700; border-bottom: 1px solid var(--border-color); padding-bottom: 0.5rem;">Thông Tin Giao Hàng</h3>
        
        <form action="" method="POST" class="checkout-form" style="display: flex; flex-direction: column; gap: 1.25rem;">
            <div class="form-group" style="display: flex; flex-direction: column; gap: 0.4rem;">
                <label for="receiver_name" style="font-weight: 600; font-size: 0.9rem;">Họ và Tên Người Nhận *</label>
                <input type="text" name="receiver_name" id="receiver_name" placeholder="Nhập tên người nhận hàng" value="<?php echo sanitize($_SESSION['user_fullname']); ?>" style="padding: 0.75rem 1rem; border: 1px solid var(--border-color); border-radius: var(--radius-md);" required>
            </div>

            <div class="form-group" style="display: flex; flex-direction: column; gap: 0.4rem;">
                <label for="receiver_phone" style="font-weight: 600; font-size: 0.9rem;">Số Điện Thoại Nhận Hàng *</label>
                <input type="text" name="receiver_phone" id="receiver_phone" placeholder="Ví dụ: 0987654321" style="padding: 0.75rem 1rem; border: 1px solid var(--border-color); border-radius: var(--radius-md);" required>
            </div>

            <div class="form-group" style="display: flex; flex-direction: column; gap: 0.4rem;">
                <label for="receiver_address" style="font-weight: 600; font-size: 0.9rem;">Địa Chỉ Giao Hàng *</label>
                <input type="text" name="receiver_address" id="receiver_address" placeholder="Số nhà, tên đường, phường/xã, quận/huyện..." style="padding: 0.75rem 1rem; border: 1px solid var(--border-color); border-radius: var(--radius-md);" required>
            </div>

            <div class="form-group" style="display: flex; flex-direction: column; gap: 0.4rem;">
                <label for="order_note" style="font-weight: 600; font-size: 0.9rem;">Ghi Chú Đơn Hàng (Nếu có)</label>
                <textarea name="order_note" id="order_note" rows="3" placeholder="Ví dụ: Giao ngoài giờ hành chính, gọi trước khi giao..." style="padding: 0.75rem 1rem; border: 1px solid var(--border-color); border-radius: var(--radius-md); font-family: inherit; resize: none;"></textarea>
            </div>

            <button type="submit" class="btn-checkout" style="border: none; cursor: pointer; margin-top: 1rem;">
                <i class="fas fa-check-double"></i> Xác Nhận và Đặt Hàng
            </button>
        </form>
    </div>

    <!-- Cột phải: Xem lại giỏ hàng và hóa đơn tổng cộng -->
    <div class="cart-summary">
        <div class="summary-title">Đơn Hàng Của Bạn</div>
        
        <div class="checkout-items" style="max-height: 250px; overflow-y: auto; margin-bottom: 1.5rem; border-bottom: 1px solid var(--border-color); padding-bottom: 1rem;">
            <?php foreach ($_SESSION['cart'] as $item): ?>
                <div class="checkout-item" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem; font-size: 0.9rem;">
                    <div style="flex: 1; padding-right: 1rem;">
                        <span style="font-weight: 600; color: var(--secondary-color);"><?php echo sanitize($item['name']); ?></span>
                        <span style="color: var(--text-light); display: block; font-size: 0.8rem;">Số lượng: <?php echo $item['quantity']; ?></span>
                    </div>
                    <span style="font-weight: 700;"><?php echo format_price($item['price'] * $item['quantity']); ?></span>
                </div>
            <?php endforeach; ?>
        </div>

        <div class="summary-row">
            <span>Tổng số lượng:</span>
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
    </div>
</div>

<?php
// Nhúng Footer HTML
require_once __DIR__ . '/includes/footer.php';
?>

