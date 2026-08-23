<?php
// --------------------------------------------------------
// BƯỚC 7: THANH ĐIỀU HƯỚNG / MENU CHÍNH (NAVBAR)
// File: includes/navbar.php
// --------------------------------------------------------

// Lấy danh mục sản phẩm từ CSDL để hiển thị trên Menu thả xuống (Dropdown)
try {
    $nav_stmt = $pdo->query("SELECT * FROM categories ORDER BY id ASC");
    $nav_categories = $nav_stmt->fetchAll();
} catch (PDOException $e) {
    // Dự phòng mảng rỗng nếu chưa có bảng categories trong CSDL
    $nav_categories = [];
}
?>

<header class="main-header-nav">
    <div class="nav-container">
        <!-- Logo cửa hàng -->
        <a href="<?php echo BASE_URL; ?>trang_chu.php" class="logo">
            Tech<span>Shop</span>
        </a>

        <!-- Thanh tìm kiếm sản phẩm nhanh -->
        <form action="<?php echo BASE_URL; ?>tim_kiem.php" method="GET" class="search-form">
            <input type="text" name="q" placeholder="Tìm sản phẩm công nghệ..." value="<?php echo isset($_GET['q']) ? sanitize($_GET['q']) : ''; ?>" required>
            <button type="submit"><i class="fas fa-search"></i></button>
        </form>

        <!-- Menu các liên kết điều hướng chính -->
        <nav class="nav-menu">
            <a href="<?php echo BASE_URL; ?>trang_chu.php" class="nav-link"><i class="fas fa-home"></i> Trang Chủ</a>
            
            <!-- Menu Dropdown Danh Mục -->
            <div class="dropdown">
                <a href="<?php echo BASE_URL; ?>san_pham.php" class="nav-link dropbtn">
                    <i class="fas fa-th-list"></i> Danh Mục <i class="fas fa-chevron-down"></i>
                </a>
                <div class="dropdown-content">
                    <a href="<?php echo BASE_URL; ?>san_pham.php">Tất Cả Sản Phẩm</a>
                    <?php foreach ($nav_categories as $cat): ?>
                        <a href="<?php echo BASE_URL; ?>san_pham.php?category_id=<?php echo $cat['id']; ?>">
                            <?php echo sanitize($cat['name']); ?>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>

            <a href="<?php echo BASE_URL; ?>san_pham.php" class="nav-link">Cửa Hàng</a>
        </nav>

        <!-- Khu vực tiện ích: Giỏ hàng & Tài khoản -->
        <div class="nav-actions">
            <!-- Biểu tượng giỏ hàng kèm số lượng động -->
            <a href="<?php echo BASE_URL; ?>gio_hang.php" class="cart-icon-btn">
                <i class="fas fa-shopping-cart"></i>
                <span class="cart-badge"><?php echo get_cart_count(); ?></span>
            </a>

            <!-- Khối Tài khoản người dùng -->
            <div class="user-menu-dropdown">
                <?php if (is_logged_in()): ?>
                    <!-- Đăng nhập rồi: Hiện tên và menu cá nhân -->
                    <a href="#" class="user-btn">
                        <i class="fas fa-user-circle"></i> Hi, <?php echo sanitize($_SESSION['user_fullname']); ?> <i class="fas fa-caret-down"></i>
                    </a>
                    <div class="user-dropdown-content">
                        <?php if (is_admin()): ?>
                            <a href="<?php echo BASE_URL; ?>admin/dashboard.php" class="admin-link"><i class="fas fa-user-shield"></i> Trang Quản Trị</a>
                        <?php endif; ?>
                        <a href="<?php echo BASE_URL; ?>lich_su_don_hang.php"><i class="fas fa-history"></i> Lịch Sử Đơn Hàng</a>
                        <hr>
                        <a href="<?php echo BASE_URL; ?>auth/logout.php" class="logout-link"><i class="fas fa-sign-out-alt"></i> Đăng Xuất</a>
                    </div>
                <?php else: ?>
                    <!-- Chưa đăng nhập: Hiện nút đi đến trang đăng nhập / đăng ký -->
                    <a href="<?php echo BASE_URL; ?>auth/login.php" class="login-btn">
                        <i class="fas fa-sign-in-alt"></i> Đăng Nhập
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</header>

