<?php
$page_title = "Dashboard";
$page_css = "admin.css";

require_once __DIR__ . '/../includes/admin-check.php';
require_once __DIR__ . '/../includes/header.php';

$stats = [
    'categories' => 0,
    'products' => 0,
    'orders' => 0,
    'users' => 0,
];

try {
    $stats['categories'] = (int) $pdo->query("SELECT COUNT(*) FROM categories")->fetchColumn();
    $stats['products'] = (int) $pdo->query("SELECT COUNT(*) FROM products")->fetchColumn();
    $stats['orders'] = (int) $pdo->query("SELECT COUNT(*) FROM orders")->fetchColumn();
    $stats['users'] = (int) $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();

    $recent_orders = $pdo->query("SELECT o.*, u.fullname AS customer_name FROM orders o INNER JOIN users u ON o.user_id = u.id ORDER BY o.id DESC LIMIT 5")->fetchAll();
} catch (PDOException $e) {
    $recent_orders = [];
}
?>

<div class="admin-layout">
    <aside class="admin-sidebar">
        <h3>Bảng Điều Khiển</h3>
        <ul>
            <li class="active"><a href="<?php echo BASE_URL; ?>admin/dashboard.php"><i class="fas fa-chart-line"></i> Dashboard</a></li>
            <li><a href="<?php echo BASE_URL; ?>admin/categories/trang_chu.php"><i class="fas fa-tags"></i> Danh Mục</a></li>
            <li><a href="<?php echo BASE_URL; ?>admin/products/trang_chu.php"><i class="fas fa-box"></i> Sản Phẩm</a></li>
            <li><a href="<?php echo BASE_URL; ?>admin/orders/trang_chu.php"><i class="fas fa-shopping-bag"></i> Đơn Hàng</a></li>
            <li><a href="<?php echo BASE_URL; ?>admin/users/trang_chu.php"><i class="fas fa-users"></i> Người Dùng</a></li>
        </ul>
    </aside>

    <main class="admin-main">
        <div class="admin-title-area">
            <h2>Dashboard</h2>
        </div>

        <div class="stats-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1.5rem; margin-bottom: 2rem;">
            <div class="stat-card" style="background: var(--white); border: 1px solid var(--border-color); border-radius: var(--radius-lg); padding: 1.5rem; box-shadow: var(--shadow-sm);">
                <div style="color: var(--text-light);">Danh mục</div>
                <div style="font-size: 2rem; font-weight: 800; color: var(--secondary-color); margin-top: 0.5rem;">
                    <?php echo $stats['categories']; ?>
                </div>
            </div>
            <div class="stat-card" style="background: var(--white); border: 1px solid var(--border-color); border-radius: var(--radius-lg); padding: 1.5rem; box-shadow: var(--shadow-sm);">
                <div style="color: var(--text-light);">Sản phẩm</div>
                <div style="font-size: 2rem; font-weight: 800; color: var(--secondary-color); margin-top: 0.5rem;">
                    <?php echo $stats['products']; ?>
                </div>
            </div>
            <div class="stat-card" style="background: var(--white); border: 1px solid var(--border-color); border-radius: var(--radius-lg); padding: 1.5rem; box-shadow: var(--shadow-sm);">
                <div style="color: var(--text-light);">Đơn hàng</div>
                <div style="font-size: 2rem; font-weight: 800; color: var(--secondary-color); margin-top: 0.5rem;">
                    <?php echo $stats['orders']; ?>
                </div>
            </div>
            <div class="stat-card" style="background: var(--white); border: 1px solid var(--border-color); border-radius: var(--radius-lg); padding: 1.5rem; box-shadow: var(--shadow-sm);">
                <div style="color: var(--text-light);">Người dùng</div>
                <div style="font-size: 2rem; font-weight: 800; color: var(--secondary-color); margin-top: 0.5rem;">
                    <?php echo $stats['users']; ?>
                </div>
            </div>
        </div>

        <div class="admin-table-wrapper">
            <h3 style="margin-bottom: 1rem;">Đơn hàng gần đây</h3>
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Mã đơn</th>
                        <th>Khách hàng</th>
                        <th>Tổng tiền</th>
                        <th>Ngày đặt</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($recent_orders)): ?>
                        <?php foreach ($recent_orders as $order): ?>
                            <tr>
                                <td>#<?php echo $order['id']; ?></td>
                                <td><?php echo sanitize($order['customer_name']); ?></td>
                                <td><?php echo format_price($order['total_amount']); ?></td>
                                <td><?php echo date('d/m/Y H:i', strtotime($order['created_at'])); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="4" style="text-align: center; color: var(--text-light);">Chưa có đơn hàng nào.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </main>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>

