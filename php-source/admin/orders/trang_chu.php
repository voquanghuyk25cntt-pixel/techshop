<?php
// --------------------------------------------------------
// BƯỚC 21.1: QUẢN TRỊ ĐƠN HÀNG - DANH SÁCH ĐƠN HÀNG
// File: admin/orders/trang_chu.php
// --------------------------------------------------------

$page_title = "Quản Lý Đơn Hàng";
$page_css = "admin.css";

// Nhúng chốt chặn kiểm tra quyền Admin trước khi xuất HTML
require_once __DIR__ . '/../../includes/admin-check.php';
// Nhúng Header (Tự động nạp cấu hình, kết nối CSDL và các hàm kiểm tra)
require_once __DIR__ . '/../../includes/header.php';

$orders = [];

// Lấy danh sách tất cả các đơn hàng kèm họ tên người mua (JOIN bảng users)
try {
    $sql = "SELECT o.*, u.fullname AS customer_name 
            FROM orders o 
            INNER JOIN users u ON o.user_id = u.id 
            ORDER BY o.id DESC";
    $stmt = $pdo->query($sql);
    $orders = $stmt->fetchAll();
} catch (PDOException $e) {
    $_SESSION['error'] = "Lỗi khi nạp danh sách đơn hàng: " . $e->getMessage();
}
?>

<div class="admin-layout">
    <!-- Sidebar Quản trị trái -->
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

    <!-- Nội dung chính -->
    <main class="admin-main">
        <div class="admin-title-area">
            <h2>Quản Lý Đơn Hàng</h2>
        </div>

        <!-- Thông báo kết quả -->
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

        <div class="admin-table-wrapper">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th style="width: 100px;">Mã Đơn</th>
                        <th>Khách Hàng</th>
                        <th>Ngày Đặt</th>
                        <th>Tổng Tiền</th>
                        <th style="width: 150px; text-align: center;">Trạng Thái</th>
                        <th style="width: 150px; text-align: center;">Thao Tác</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($orders)): ?>
                        <?php foreach ($orders as $order): ?>
                            <tr>
                                <td><strong>#<?php echo $order['id']; ?></strong></td>
                                <td>
                                    <strong><?php echo sanitize($order['customer_name']); ?></strong>
                                </td>
                                <td style="color: var(--text-light);"><?php echo date('d/m/Y H:i', strtotime($order['created_at'])); ?></td>
                                <td style="font-weight: 700; color: var(--primary-color);">
                                    <?php echo format_price($order['total_amount']); ?>
                                </td>
                                <td style="text-align: center;">
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
                                </td>
                                <td style="text-align: center;">
                                    <a href="detail.php?id=<?php echo $order['id']; ?>" class="btn btn-secondary btn-sm">
                                        <i class="fas fa-eye"></i> Xem Chi Tiết
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" style="text-align: center; color: var(--text-light); padding: 3rem 0;">Chưa có đơn hàng nào được đặt.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </main>
</div>

<?php
// Nhúng Footer
require_once __DIR__ . '/../../includes/footer.php';
?>

