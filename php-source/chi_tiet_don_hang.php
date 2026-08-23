<?php
// --------------------------------------------------------
// BƯỚC 20: TRANG CHI TIẾT ĐƠN HÀNG CỦA KHÁCH HÀNG (ORDER DETAIL)
// File: chi_tiet_don_hang.php
// --------------------------------------------------------

// 1. Nạp cấu hình, kết nối CSDL và helper functions LÊN ĐẦU TRANG
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/functions.php';

// Chốt chặn bảo mật: Chỉ cho phép người dùng đăng nhập
require_once __DIR__ . '/includes/auth-check.php';

// Kiểm tra mã đơn hàng truyền vào
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    redirect(BASE_URL . 'lich_su_don_hang.php');
}

$order_id = (int)$_GET['id'];
$user_id = $_SESSION['user_id'];

try {
    // 2. Lấy thông tin đơn hàng lớn (Đảm bảo đơn hàng này đúng là của người đang đăng nhập!)
    // Đây là chốt chặn chống lỗi phân quyền ngang (ID Harvesting / Broken Object Level Authorization)
    $stmt_order = $pdo->prepare("SELECT * FROM orders WHERE id = :id AND user_id = :user_id LIMIT 1");
    $stmt_order->execute([
        'id' => $order_id,
        'user_id' => $user_id
    ]);
    $order = $stmt_order->fetch();

    if (!$order) {
        // Nếu đơn hàng không thuộc về người dùng này hoặc không tồn tại, đá về danh sách
        redirect(BASE_URL . 'lich_su_don_hang.php');
    }

    // 3. Lấy thông tin chi tiết các sản phẩm trong đơn hàng
    // Sử dụng LEFT JOIN để liên kết bảng sản phẩm lấy tên và ảnh tương ứng
    $sql_details = "SELECT od.*, p.name AS product_name, p.image AS product_image 
                    FROM order_details od 
                    LEFT JOIN products p ON od.product_id = p.id 
                    WHERE od.order_id = :order_id";
    $stmt_details = $pdo->prepare($sql_details);
    $stmt_details->execute(['order_id' => $order_id]);
    $order_details = $stmt_details->fetchAll();

} catch (PDOException $e) {
    die("Lỗi kết nối dữ liệu: " . $e->getMessage());
}

$page_title = "Chi Tiết Đơn Hàng #" . $order_id;
$page_css = "admin.css"; // Tận dụng style các table, badges của admin
require_once __DIR__ . '/includes/header.php';
?>

<div style="max-width: 900px; margin: 0 auto; background: var(--white); border: 1px solid var(--border-color); border-radius: var(--radius-lg); padding: 2.5rem; box-shadow: var(--shadow-sm);">
    
    <div class="admin-title-area">
        <h2>Chi Tiết Đơn Hàng #<?php echo $order_id; ?></h2>
        <a href="lich_su_don_hang.php" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Quay Lại</a>
    </div>

    <!-- Thông tin trạng thái đơn hàng -->
    <div style="background-color: var(--bg-color); border: 1px solid var(--border-color); border-radius: var(--radius-md); padding: 1.5rem; margin-bottom: 2rem; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
        <div>
            <p style="margin-bottom: 0.4rem; color: var(--text-light); font-size: 0.9rem;">Ngày đặt hàng:</p>
            <strong><?php echo date('d/m/Y H:i', strtotime($order['created_at'])); ?></strong>
        </div>

        <div>
            <p style="margin-bottom: 0.4rem; color: var(--text-light); font-size: 0.9rem;">Trạng thái đơn hàng:</p>
            <span class="badge badge-<?php echo sanitize($order['status']); ?>" style="font-size: 0.9rem; padding: 0.4rem 1rem;">
                <?php 
                    $status_labels = [
                        'pending' => 'Chờ xử lý',
                        'processing' => 'Đang xử lý',
                        'shipping' => 'Đang giao hàng',
                        'completed' => 'Hoàn thành',
                        'cancelled' => 'Đã hủy'
                    ];
                    echo sanitize($status_labels[$order['status']] ?? $order['status']);
                ?>
            </span>
        </div>

        <div>
            <p style="margin-bottom: 0.4rem; color: var(--text-light); font-size: 0.9rem;">Tổng hóa đơn:</p>
            <strong style="color: var(--primary-color); font-size: 1.3rem;"><?php echo format_price($order['total_amount']); ?></strong>
        </div>
    </div>

    <!-- Danh sách sản phẩm đã mua -->
    <h3 style="font-size: 1.2rem; color: var(--secondary-color); margin-bottom: 1rem; font-weight: 700;">Danh Sách Sản Phẩm</h3>
    <div class="admin-table-wrapper" style="margin-bottom: 2rem;">
        <table class="admin-table">
            <thead>
                <tr>
                    <th style="width: 80px;">Hình Ảnh</th>
                    <th>Tên Sản Phẩm</th>
                    <th style="width: 120px; text-align: right;">Đơn Giá</th>
                    <th style="width: 100px; text-align: center;">Số Lượng</th>
                    <th style="width: 150px; text-align: right;">Thành Tiền</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($order_details as $item): 
                    $subtotal = $item['price'] * $item['quantity'];
                ?>
                    <tr>
                        <td>
                            <?php if (!empty($item['product_image'])): ?>
                                <img src="<?php echo BASE_URL . 'uploads/products/' . sanitize($item['product_image']); ?>" alt="product" style="width: 50px; height: 50px; object-fit: cover; border-radius: var(--radius-sm); border: 1px solid var(--border-color);">
                            <?php else: ?>
                                <div style="width: 50px; height: 50px; background-color: #f1f5f9; display: flex; align-items: center; justify-content: center; border-radius: var(--radius-sm); color: #94a3b8;">
                                    <i class="fas fa-laptop"></i>
                                </div>
                            <?php endif; ?>
                        </td>
                        <td>
                            <strong><?php echo sanitize($item['product_name'] ?? 'Sản phẩm đã bị xóa hoặc ngừng kinh doanh'); ?></strong>
                        </td>
                        <td style="text-align: right;"><?php echo format_price($item['price']); ?></td>
                        <td style="text-align: center;"><?php echo $item['quantity']; ?></td>
                        <td style="text-align: right; font-weight: 700; color: var(--secondary-color);">
                            <?php echo format_price($subtotal); ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php
require_once __DIR__ . '/includes/footer.php';
?>

