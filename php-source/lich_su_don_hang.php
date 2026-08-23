<?php
// --------------------------------------------------------
// BƯỚC 19: TRANG LỊCH SỬ ĐƠN HÀNG CỦA KHÁCH HÀNG (ORDERS)
// File: lich_su_don_hang.php
// --------------------------------------------------------

// 1. Nạp cấu hình, kết nối CSDL và helper functions LÊN ĐẦU TRANG trước khi xuất HTML
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/functions.php';

// Chốt chặn bảo mật: Chỉ cho phép người dùng đăng nhập
require_once __DIR__ . '/includes/auth-check.php';

$user_id = $_SESSION['user_id'];
$orders = [];

// 2. Lấy toàn bộ đơn hàng của khách hàng hiện tại
try {
    $stmt = $pdo->prepare("SELECT * FROM orders WHERE user_id = :user_id ORDER BY id DESC");
    $stmt->execute(['user_id' => $user_id]);
    $orders = $stmt->fetchAll();
} catch (PDOException $e) {
    $error = "Lỗi khi tải lịch sử đơn hàng: " . $e->getMessage();
}

$page_title = "Lịch Sử Đơn Hàng";
$page_css = "admin.css"; // Tận dụng các class CSS bảng biểu và badge trạng thái từ admin.css
require_once __DIR__ . '/includes/header.php';
?>

<div style="max-width: 900px; margin: 0 auto; background: var(--white); border: 1px solid var(--border-color); border-radius: var(--radius-lg); padding: 2.5rem; box-shadow: var(--shadow-sm);">
    <h2 style="font-size: 1.8rem; color: var(--secondary-color); font-weight: 800; margin-bottom: 2rem; border-bottom: 1px solid var(--border-color); padding-bottom: 1rem;">
        Lịch Sử Đơn Hàng Của Bạn
    </h2>

    <?php if (isset($error)): ?>
        <div class="alert alert-danger">
            <i class="fas fa-exclamation-circle"></i> <?php echo sanitize($error); ?>
        </div>
    <?php endif; ?>

    <div class="admin-table-wrapper">
        <table class="admin-table">
            <thead>
                <tr>
                    <th style="width: 100px;">Mã Đơn</th>
                    <th>Ngày Đặt</th>
                    <th>Tổng Tiền</th>
                    <th style="width: 160px; text-align: center;">Trạng Thái</th>
                    <th style="width: 150px; text-align: center;">Thao Tác</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($orders)): ?>
                    <?php foreach ($orders as $order): ?>
                        <tr>
                            <td><strong>#<?php echo $order['id']; ?></strong></td>
                            <td style="color: var(--text-light);"><?php echo date('d/m/Y H:i', strtotime($order['created_at'])); ?></td>
                            <td style="font-weight: 700; color: var(--primary-color);">
                                <?php echo format_price($order['total_amount']); ?>
                            </td>
                            <td style="text-align: center;">
                                <!-- Trạng thái đơn hàng đi kèm class CSS badge tương thích -->
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
                                <a href="chi_tiet_don_hang.php?id=<?php echo $order['id']; ?>" class="btn btn-secondary btn-sm" style="background-color: var(--bg-color); border: 1px solid var(--border-color); color: var(--secondary-color);">
                                    <i class="fas fa-eye"></i> Xem Chi Tiết
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5" style="text-align: center; color: var(--text-light); padding: 3rem 0;">Bạn chưa đặt đơn hàng nào.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php
require_once __DIR__ . '/includes/footer.php';
?>

