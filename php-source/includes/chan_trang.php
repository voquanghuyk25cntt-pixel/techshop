<?php
// --------------------------------------------------------
// BƯỚC 6: TEMPLATE PHẦN CHÂN TRANG HTML (FOOTER)
// File: includes/footer.php
// --------------------------------------------------------
?>
    </main> <!-- Đóng thẻ <main class="container"> đã mở ở header.php -->

    <!-- Phần Footer chính của Website -->
    <footer class="main-footer">
        <div class="footer-container">
            <!-- Cột 1: Thông tin thương hiệu -->
            <div class="footer-col">
                <h3 class="footer-logo">Tech<span>Shop</span></h3>
                <p class="footer-desc">Cửa hàng công nghệ chuyên cung cấp thiết bị chơi game, phụ kiện máy tính chính hãng, uy tín hàng đầu cho học sinh sinh viên.</p>
                <div class="footer-socials">
                    <a href="#" class="social-link zalo" data-platform="zalo" data-link="https://zalo.me/0901234567" aria-label="Zalo">
                        <span>Z</span>
                    </a>
                    <a href="#" class="social-link facebook" data-platform="facebook" data-link="https://www.facebook.com/techshopdemo" aria-label="Facebook">
                        <i class="fab fa-facebook-f"></i>
                    </a>
                    <a href="#" class="social-link instagram" data-platform="instagram" data-link="https://www.instagram.com/techshopdemo" aria-label="Instagram">
                        <i class="fab fa-instagram"></i>
                    </a>
                    <a href="#" class="social-link tiktok" data-platform="tiktok" data-link="https://www.tiktok.com/@techshopdemo" aria-label="TikTok">
                        <i class="fab fa-tiktok"></i>
                    </a>
                </div>
            </div>

            <!-- Cột 2: Danh mục liên kết nhanh -->
            <div class="footer-col">
                <h4>Khám Phá</h4>
                <ul>
                    <li><a href="<?php echo BASE_URL; ?>trang_chu.php">Trang Chủ</a></li>
                    <li><a href="<?php echo BASE_URL; ?>san_pham.php">Tất Cả Sản Phẩm</a></li>
                    <li><a href="<?php echo BASE_URL; ?>gio_hang.php">Giỏ Hàng</a></li>
                </ul>
            </div>

            <!-- Cột 3: Thông tin liên hệ -->
            <div class="footer-col">
                <h4>Liên Hệ</h4>
                <ul class="contact-info">
                    <li><i class="fas fa-map-marker-alt"></i> 123 Đường Cầu Giấy, Hà Nội</li>
                    <li><i class="fas fa-phone-alt"></i> 0123 456 789</li>
                    <li><i class="fas fa-envelope"></i> support@techshop.com</li>
                </ul>
            </div>
        </div>

        <!-- Bản quyền chân trang -->
        <div class="footer-bottom">
            <p>&copy; <?php echo date('Y'); ?> TechShop. Thiết kế và phát triển dành cho đồ án môn học PHP/MySQL.</p>
        </div>
    </footer>

    <div id="contactQrModal" class="contact-qr-modal" aria-hidden="true">
        <div class="contact-qr-dialog" role="dialog" aria-modal="true" aria-labelledby="contactQrTitle">
            <button type="button" class="contact-qr-close" aria-label="Đóng">
                <i class="fas fa-times"></i>
            </button>
            <div class="contact-qr-content">
                <h3 id="contactQrTitle">Kết nối với TechShop</h3>
                <img id="contactQrImage" src="" alt="Mã QR liên hệ" />
                <p id="contactQrText">Quét mã QR để kết bạn hoặc mở liên kết nhanh.</p>
                <a id="contactQrLink" href="#" target="_blank" rel="noopener noreferrer" class="contact-qr-button">Mở liên kết</a>
            </div>
        </div>
    </div>

    <!-- JS: File Javascript dùng chung cho toàn hệ thống -->
    <script defer src="<?php echo BASE_URL; ?>assets/js/main.js"></script>

    <!-- JS: Tự động nạp file Javascript riêng cho từng trang nếu được định nghĩa (ví dụ: $page_js = 'cart.js') -->
    <?php if (isset($page_js)): ?>
        <script defer src="<?php echo BASE_URL; ?>assets/js/<?php echo $page_js; ?>"></script>
    <?php endif; ?>

</body>
</html>

