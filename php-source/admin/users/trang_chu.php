<?php
$page_title = "Quản Lý Người Dùng";
$page_css = "admin.css";

require_once __DIR__ . '/../../includes/admin-check.php';
require_once __DIR__ . '/../../includes/header.php';

try {
    $stmt = $pdo->query("SELECT * FROM users ORDER BY id DESC");
    $users = $stmt->fetchAll();
} catch (PDOException $e) {
    $_SESSION['error'] = "Không thể tải danh sách người dùng: " . $e->getMessage();
    $users = [];
}
?>

<div class="admin-layout">
    <aside class="admin-sidebar">
        <h3>Bảng Điều Khiển</h3>
        <ul>
            <li><a href="<?php echo BASE_URL; ?>admin/dashboard.php"><i class="fas fa-chart-line"></i> Dashboard</a></li>
            <li><a href="<?php echo BASE_URL; ?>admin/categories/trang_chu.php"><i class="fas fa-tags"></i> Danh Mục</a></li>
            <li><a href="<?php echo BASE_URL; ?>admin/products/trang_chu.php"><i class="fas fa-box"></i> Sản Phẩm</a></li>
            <li><a href="<?php echo BASE_URL; ?>admin/orders/trang_chu.php"><i class="fas fa-shopping-bag"></i> Đơn Hàng</a></li>
            <li class="active"><a href="<?php echo BASE_URL; ?>admin/users/trang_chu.php"><i class="fas fa-users"></i> Người Dùng</a></li>
        </ul>
    </aside>

    <main class="admin-main">
        <div class="admin-title-area">
            <h2>Quản Lý Người Dùng</h2>
        </div>

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
                        <th>ID</th>
                        <th>Họ tên</th>
                        <th>Email</th>
                        <th>Vai trò</th>
                        <th>Ngày tạo</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($users)): ?>
                        <?php foreach ($users as $user): ?>
                            <tr>
                                <td>#<?php echo $user['id']; ?></td>
                                <td><strong><?php echo sanitize($user['fullname']); ?></strong></td>
                                <td><?php echo sanitize($user['email']); ?></td>
                                <td>
                                    <span class="badge badge-<?php echo ($user['role'] === 'admin') ? 'processing' : 'pending'; ?>">
                                        <?php echo $user['role'] === 'admin' ? 'Admin' : 'Khách hàng'; ?>
                                    </span>
                                </td>
                                <td><?php echo date('d/m/Y', strtotime($user['created_at'])); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" style="text-align: center; color: var(--text-light);">Chưa có người dùng nào.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </main>
</div>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>

