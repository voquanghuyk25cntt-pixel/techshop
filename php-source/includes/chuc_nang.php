<?php
// --------------------------------------------------------
// FILE HÀM HỖ TRỢ CHUNG CHO TOÀN BỘ PROJECT
// Dễ hiểu, ngắn gọn, phù hợp cho đồ án sinh viên
// --------------------------------------------------------

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Lọc dữ liệu đầu vào để tránh XSS.
 */
function sanitize($data) {
    if ($data === null) {
        return '';
    }

    if (!is_scalar($data)) {
        $data = json_encode($data, JSON_UNESCAPED_UNICODE);
    }

    return htmlspecialchars(trim((string) $data), ENT_QUOTES, 'UTF-8');
}

/**
 * Định dạng tiền Việt Nam.
 */
function format_price($price) {
    return number_format((float) $price, 0, ',', '.') . ' đ';
}

/**
 * Chuyển hướng trang an toàn.
 */
function redirect($url) {
    if (headers_sent()) {
        echo '<script>window.location.href = ' . json_encode($url) . ';</script>';
        exit;
    }

    header('Location: ' . $url);
    exit;
}

/**
 * Kiểm tra người dùng đã đăng nhập chưa.
 */
function is_logged_in() {
    return isset($_SESSION['user_id']);
}

/**
 * Kiểm tra người dùng có quyền admin hay không.
 */
function is_admin() {
    return isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin';
}

/**
 * Đếm tổng số lượng sản phẩm trong giỏ hàng.
 */
function get_cart_count() {
    $count = 0;

    if (!empty($_SESSION['cart']) && is_array($_SESSION['cart'])) {
        foreach ($_SESSION['cart'] as $item) {
            $count += (int) ($item['quantity'] ?? 0);
        }
    }

    return $count;
}

/**
 * Tính tổng tiền giỏ hàng hiện tại.
 */
function get_cart_total() {
    $total = 0;

    if (!empty($_SESSION['cart']) && is_array($_SESSION['cart'])) {
        foreach ($_SESSION['cart'] as $item) {
            $price = (float) ($item['price'] ?? 0);
            $quantity = (int) ($item['quantity'] ?? 0);
            $total += $price * $quantity;
        }
    }

    return $total;
}
