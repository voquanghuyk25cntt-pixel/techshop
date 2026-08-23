<?php
// --------------------------------------------------------
// BƯỚC 9: TRANG ĐĂNG NHẬP (LOGIN)
// File: auth/login.php
// --------------------------------------------------------

// 1. Nạp cấu hình, kết nối CSDL và helper functions LÊN ĐẦU TRANG
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';

if (!$pdo instanceof PDO) {
    throw new RuntimeException('Không thể kết nối cơ sở dữ liệu.');
}

// 2. Chuyển hướng ngay lập tức nếu người dùng đã đăng nhập
if (is_logged_in()) {
    redirect(BASE_URL . 'trang_chu.php');
}

$error = '';
$success = '';
$redirect_url = '';

// 3. Xử lý logic submit form đăng nhập
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    if (empty($email) || empty($password)) {
        $error = "Vui lòng điền đầy đủ cả Email và Mật khẩu.";
    } 
    elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Định dạng email không hợp lệ.";
    } 
    else {
        try {
            $stmt = $pdo->prepare("SELECT * FROM users WHERE email = :email LIMIT 1");
            $stmt->execute(['email' => $email]);
            $user = $stmt->fetch();

            if ($user && password_verify($password, $user['password'])) {
                // Đăng nhập thành công, thiết lập session
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_email'] = $user['email'];
                $_SESSION['user_fullname'] = $user['fullname'];
                $_SESSION['user_role'] = $user['role'];

                $success = "Đăng nhập thành công! Đang chuyển hướng...";
                
                // Xác định đường dẫn chuyển hướng dựa vào quyền
                $redirect_url = ($user['role'] === 'admin') ? BASE_URL . "admin/dashboard.php" : BASE_URL . "trang_chu.php";
            } else {
                $error = "Email hoặc mật khẩu không chính xác.";
            }
        } catch (PDOException $e) {
            $error = "Có lỗi xảy ra: " . $e->getMessage();
        }
    }
}

// 4. Thiết lập các thông số giao diện và tiến hành nhúng Header HTML
$page_title = "Đăng Nhập Hệ Thống";
$page_css = "auth.css";
require_once __DIR__ . '/../includes/header.php';
?>

<div class="auth-wrapper">
    <div class="auth-header">
        <h2>Đăng Nhập</h2>
        <p>Đăng nhập tài khoản TechShop của bạn</p>
    </div>

    <!-- Hiển thị thông báo lỗi/thành công -->
    <?php if (!empty($error)): ?>
        <div class="alert alert-danger">
            <i class="fas fa-exclamation-circle"></i> <?php echo sanitize($error); ?>
        </div>
    <?php endif; ?>

    <?php if (!empty($success)): ?>
        <div class="alert alert-success">
            <i class="fas fa-check-circle"></i> <?php echo sanitize($success); ?>
            <!-- Chuyển hướng Javascript an toàn -->
            <script>
                setTimeout(function() {
                    window.location.href = "<?php echo $redirect_url; ?>";
                }, 1500);
            </script>
        </div>
    <?php endif; ?>

    <!-- Form Đăng nhập -->
    <form action="" method="POST" class="auth-form" novalidate>
        <div class="form-group">
            <label for="email">Địa chỉ Email</label>
            <input type="email" name="email" id="email" placeholder="email@example.com" value="<?php echo isset($email) ? sanitize($email) : ''; ?>" required>
        </div>

        <div class="form-group">
            <label for="password">Mật khẩu</label>
            <input type="password" name="password" id="password" placeholder="Nhập mật khẩu..." required>
        </div>

        <button type="submit" class="auth-btn">Đăng Nhập</button>
    </form>

    <div class="auth-footer">
        Chưa có tài khoản? <a href="<?php echo BASE_URL; ?>auth/register.php">Đăng ký ngay</a>
    </div>
</div>

<?php
// Nhúng Footer HTML
require_once __DIR__ . '/../includes/footer.php';
?>

