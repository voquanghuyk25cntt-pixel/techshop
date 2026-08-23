<?php
// --------------------------------------------------------
// BƯỚC 8: TRANG ĐĂNG KÝ TÀI KHOẢN (REGISTER)
// File: auth/register.php
// --------------------------------------------------------

// 1. Nạp cấu hình, kết nối CSDL và helper functions LÊN ĐẦU TRANG
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';

if (!$pdo instanceof PDO) {
    throw new RuntimeException('Không thể kết nối cơ sở dữ liệu.');
}

// 2. Chuyển hướng ngay lập tức nếu người dùng đã đăng nhập (không xuất HTML trước)
if (is_logged_in()) {
    redirect(BASE_URL . 'trang_chu.php');
}

$error = '';
$success = '';

// 3. Xử lý logic submit form đăng ký
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fullname = trim($_POST['fullname'] ?? '');
    $email = strtolower(trim($_POST['email'] ?? ''));
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    if (empty($fullname) || empty($email) || empty($password) || empty($confirm_password)) {
        $error = "Vui lòng nhập đầy đủ tất cả các trường.";
    }
    elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Định dạng email không hợp lệ.";
    }
    elseif (strlen($password) < 6) {
        $error = "Mật khẩu phải có độ dài tối thiểu 6 ký tự.";
    }
    elseif ($password !== $confirm_password) {
        $error = "Mật khẩu xác nhận không khớp.";
    }
    else {
        try {
            $stmt = $pdo->prepare("SELECT id FROM users WHERE LOWER(email) = LOWER(:email) LIMIT 1");
            $stmt->execute(['email' => $email]);
            $user = $stmt->fetch();

            if ($user) {
                $error = "Email này đã được sử dụng. Vui lòng chọn email khác.";
            } else {
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);

                $insert_stmt = $pdo->prepare("INSERT INTO users (fullname, email, password, role) VALUES (:fullname, :email, :password, 'user')");
                $insert_stmt->execute([
                    'fullname' => $fullname,
                    'email' => $email,
                    'password' => $hashed_password
                ]);

                $success = "Đăng ký tài khoản thành công! Đang chuyển hướng...";
            }
        } catch (PDOException $e) {
            $error = "Có lỗi xảy ra trong quá trình xử lý: " . $e->getMessage();
        }
    }
}

// 4. Thiết lập các thông số giao diện và tiến hành nhúng Header HTML
$page_title = "Đăng Ký Tài Khoản";
$page_css = "auth.css";
require_once __DIR__ . '/../includes/header.php';
?>

<div class="auth-wrapper">
    <div class="auth-header">
        <h2>Đăng Ký</h2>
        <p>Tạo tài khoản TechShop mới ngay hôm nay</p>
    </div>

    <!-- Hiển thị thông báo lỗi hoặc thành công -->
    <?php if (!empty($error)): ?>
        <div class="alert alert-danger">
            <i class="fas fa-exclamation-circle"></i> <?php echo sanitize($error); ?>
        </div>
    <?php endif; ?>

    <?php if (!empty($success)): ?>
        <div class="alert alert-success">
            <i class="fas fa-check-circle"></i> <?php echo sanitize($success); ?>
            <?php $login_url = BASE_URL . 'auth/login.php'; ?>
            <script>
                setTimeout(function() {
                    window.location.href = "<?php echo $login_url; ?>";
                }, 2000);
            </script>
        </div>
    <?php endif; ?>

    <!-- Form Đăng ký -->
    <form action="" method="POST" class="auth-form" novalidate>
        <div class="form-group">
            <label for="fullname">Họ và Tên</label>
            <input type="text" name="fullname" id="fullname" placeholder="Nhập họ và tên..." value="<?php echo isset($fullname) ? sanitize($fullname) : ''; ?>" required>
        </div>

        <div class="form-group">
            <label for="email">Địa chỉ Email</label>
            <input type="email" name="email" id="email" placeholder="email@example.com" value="<?php echo isset($email) ? sanitize($email) : ''; ?>" required>
        </div>

        <div class="form-group">
            <label for="password">Mật khẩu</label>
            <input type="password" name="password" id="password" placeholder="Tối thiểu 6 ký tự" required>
        </div>

        <div class="form-group">
            <label for="confirm_password">Xác nhận mật khẩu</label>
            <input type="password" name="confirm_password" id="confirm_password" placeholder="Nhập lại mật khẩu" required>
        </div>

        <button type="submit" class="auth-btn">Đăng Ký Tài Khoản</button>
    </form>

    <div class="auth-footer">
        Đã có tài khoản? <a href="<?php echo BASE_URL; ?>auth/login.php">Đăng nhập tại đây</a>
    </div>
</div>

<?php
// Nhúng Footer HTML
require_once __DIR__ . '/../includes/footer.php';
?>

