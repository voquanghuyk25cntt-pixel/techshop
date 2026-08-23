<?php
// --------------------------------------------------------
// BƯỚC 18.2: TRANG ĐẶT HÀNG THÀNH CÔNG (ORDER SUCCESS)
// File: dat_hang_thanh_cong.php
// --------------------------------------------------------

require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/functions.php';

// Chốt chặn bảo mật: Chỉ cho phép người dùng đăng nhập truy cập
require_once __DIR__ . '/includes/auth-check.php';

// Lấy ID đơn hàng từ URL
$order_id = isset($_GET['id']) && is_numeric($_GET['id']) ? (int)$_GET['id'] : null;

if (!$order_id) {
    redirect(BASE_URL . 'trang_chu.php');
}

$page_title = "Đặt Hàng Thành Công";
require_once __DIR__ . '/includes/header.php';
?>

<div class="order-success-container" style="max-width: 600px; margin: 4rem auto; background: var(--white); border: 1px solid var(--border-color); border-radius: var(--radius-lg); padding: 3rem 2rem; text-align: center; box-shadow: var(--shadow-sm);">
    <!-- Icon Thành công -->
    <i class="fas fa-check-circle" style="font-size: 5rem; color: var(--success-color); margin-bottom: 1.5rem;"></i>
    
    <h2 style="font-size: 1.8rem; color: var(--secondary-color); font-weight: 800; margin-bottom: 0.5rem;">Đặt Hàng Thành Công!</h2>
    <p style="color: var(--text-light); font-size: 1rem; margin-bottom: 2rem;">
        Cảm ơn bạn đã tin tưởng mua sắm tại TechShop. Mã số đơn hàng của bạn là: <strong>#<?php echo $order_id; ?></strong>.
    </p>

    <div style="background-color: var(--bg-color); border-radius: var(--radius-md); padding: 1.5rem; text-align: left; font-size: 0.95rem; margin-bottom: 2.5rem; border: 1px solid var(--border-color);">
        <h4 style="color: var(--secondary-color); margin-bottom: 0.8rem; font-weight: 700;">Các bước tiếp theo:</h4>
        <ul style="list-style-type: decimal; padding-left: 1.2rem; color: var(--text-color); display: flex; flex-direction: column; gap: 0.6rem;">
            <li>Chúng tôi đang tiến hành chuẩn bị đóng gói sản phẩm của bạn.</li>
            <li>Đơn hàng sẽ sớm được bàn giao cho đơn vị vận chuyển đối tác.</li>
            <li>Bạn có thể theo dõi hành trình đơn hàng bất kỳ lúc nào tại mục lịch sử đơn hàng.</li>
        </ul>
    </div>

    <!-- Các nút hành động điều hướng -->
    <div style="display: flex; gap: 1rem; justify-content: center;">
        <a href="<?php echo BASE_URL; ?>lich_su_don_hang.php" class="btn btn-primary" style="padding: 0.75rem 1.5rem;">
            <i class="fas fa-history"></i> Lịch Sử Đơn Hàng
        </a>
        <a href="<?php echo BASE_URL; ?>san_pham.php" class="btn btn-secondary" style="padding: 0.75rem 1.5rem;">
            Tiếp Tục Mua Sắm
        </a>
    </div>
</div>

<?php
require_once __DIR__ . '/includes/footer.php';
?>

