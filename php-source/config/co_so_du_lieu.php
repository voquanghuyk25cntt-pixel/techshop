<?php
// --------------------------------------------------------
// CẤU HÌNH KẾT NỐI CSDL CHO DEMO TECHSHOP
// Dễ hiểu và dễ sửa cho sinh viên
// --------------------------------------------------------

// Thông tin cơ bản
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_NAME', 'techshop');

// XAMPP thường dùng port 3306, nhưng có máy dùng 3310.
// Vì vậy ta thử nhiều cổng và nhiều mật khẩu để dễ demo hơn.
$port_list = [3306, 3310];
$password_list = ['', '123456'];

$pdo = null;
$connected_port = 3306;
$connected_password = '';

foreach ($port_list as $port) {
    foreach ($password_list as $password) {
        $dsn = 'mysql:host=' . DB_HOST . ';port=' . $port . ';dbname=' . DB_NAME . ';charset=utf8mb4';

        try {
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ];

            $pdo = new PDO($dsn, DB_USER, $password, $options);
            $connected_port = $port;
            $connected_password = $password;
            break 2;
        } catch (PDOException $e) {
            // Thử tiếp theo nếu cổng hoặc mật khẩu không đúng.
            $pdo = null;
        }
    }
}

if (!$pdo) {
    die('Kết nối cơ sở dữ liệu thất bại. Kiểm tra MySQL, tên DB, cổng và mật khẩu.');
}

/** @var PDO $pdo */

// Định nghĩa hằng số cuối cùng sau khi đã tìm được kết nối thành công.
define('DB_PORT', $connected_port);
define('DB_PASS', $connected_password);
