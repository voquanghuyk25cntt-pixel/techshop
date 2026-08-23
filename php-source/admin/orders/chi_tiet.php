<?php
$page_title = "Chi Tiết Đơn Hàng";
$page_css = "admin.css";

require_once __DIR__ . '/../../includes/admin-check.php';
require_once __DIR__ . '/../../includes/header.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    redirect(BASE_URL . 'admin/orders/trang_chu.php');
}

$order_id = (int) $_GET['id'];

try {
    $stmt_order = $pdo->prepare("SELECT o.*, u.fullname AS customer_name FROM orders o INNER JOIN users u ON o.user_id = u.id WHERE o.id = :id LIMIT 1");
    $stmt_order->execute(['id' => $order_id]);
    $order = $stmt_order->fetch();

    if (!$order) {
        redirect(BASE_URL . 'admin/orders/trang_chu.php');
    }

    $stmt_items = $pdo->prepare("SELECT od.*, p.name AS product_name, p.image AS product_image FROM order_details od LEFT JOIN products p ON od.product_id = p.id WHERE od.order_id = :order_id");
    $stmt_items->execute(['order_id' => $order_id]);
    $order_items = $stmt_items->fetchAll();
} catch (PDOException $e) {
    $_SESSION['error'] = "Không thể tải chi tiết đơn hàng: " . $e->getMessage();
    $order = null;
    $order_items = [];
}
?>

<div class="admin-layout">
    <aside class="admin-sidebar">
        <h3>Bảng Điều Khiển</h3>
        <ul>
            <li><a href="<?php echo BASE_URL; ?>admin/dashboard.php"><i class="fas fa-chart-line"></i> Dashboard</a></li>
            <li><a href="<?php echo BASE_URL; ?>admin/categories/trang_chu.php"><i class="fas fa-tags"></i> Danh Mục</a></li>
            <li><a href="<?php echo BASE_URL; ?>admin/products/trang_chu.php"><i class="fas fa-box"></i> Sản Phẩm</a></li>
            <li class="active"><a href="<?php echo BASE_URL; ?>admin/orders/trang_chu.php"><i class="fas fa-shopping-bag"></i> Đơn Hàng</a></li>
            <li><a href="<?php echo BASE_URL; ?>admin/users/trang_chu.php"><i class="fas fa-users"></i> Người Dùng</a></li>
        </ul>
    </aside>

    <main class="admin-main">
        <div class="admin-title-area">
            <h2>Chi Tiết Đơn Hàng #<?php echo $order_id; ?></h2>
            <a href="trang_chu.php" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Quay Lại</a>
        </div>

        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger alert-auto-dismiss">
                <i class="fas fa-exclamation-circle"></i> <?php echo sanitize($_SESSION['error']); unset($_SESSION['error']); ?>
            </div>
        <?php endif; ?>

        <?php if ($order): ?>
            <div style="background-color: var(--bg-color); border: 1px solid var(--border-color); border-radius: var(--radius-md); padding: 1.5rem; margin-bottom: 2rem; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
                <div>
                    <p style="margin-bottom: 0.4rem; color: var(--text-light); font-size: 0.9rem;">Khách hàng:</p>
                    <strong><?php echo sanitize($order['customer_name']); ?></strong>
                </div>
                <div>
                    <p style="margin-bottom: 0.4rem; color: var(--text-light); font-size: 0.9rem;">Ngày đặt:</p>
                    <strong><?php echo date('d/m/Y H:i', strtotime($order['created_at'])); ?></strong>
                </div>
                <div>
                    <p style="margin-bottom: 0.4rem; color: var(--text-light); font-size: 0.9rem;">Trạng thái:</p>
                    <span class="badge badge-<?php echo sanitize($order['status']); ?>">
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
                    <p style="margin-bottom: 0.4rem; color: var(--text-light); font-size: 0.9rem;">Tổng tiền:</p>
                    <strong style="color: var(--primary-color); font-size: 1.3rem;">
                        <?php echo format_price($order['total_amount']); ?>
                    </strong>
                </div>
            </div>

            <div class="admin-table-wrapper">
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
                        <?php if (!empty($order_items)): ?>
                            <?php foreach ($order_items as $item): ?>
                                <?php $subtotal = $item['price'] * $item['quantity']; ?>
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
                                    <td><strong><?php echo sanitize($item['product_name'] ?? 'Sản phẩm đã bị xóa'); ?></strong></td>
                                    <td style="text-align: right;"><?php echo format_price($item['price']); ?></td>
                                    <td style="text-align: center;"><?php echo $item['quantity']; ?></td>
                                    <td style="text-align: right; font-weight: 700; color: var(--secondary-color);">
                                        <?php echo format_price($subtotal); ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5" style="text-align: center; color: var(--text-light);">Không có sản phẩm nào trong đơn hàng.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </main>
</div>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>

