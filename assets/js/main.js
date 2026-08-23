// --------------------------------------------------------
// BƯỚC THÊM: FILE JAVASCRIPT HỆ THỐNG DÙNG CHUNG (MAIN JS)
// File: assets/js/main.js
// --------------------------------------------------------

const CART_KEY = 'techshop_cart_v1';

function getCart() {
    try {
        return JSON.parse(localStorage.getItem(CART_KEY) || '{}');
    } catch (e) {
        return {};
    }
}

function saveCart(cart) {
    localStorage.setItem(CART_KEY, JSON.stringify(cart));
    updateCartBadge();
}

function addToCart(item, qty = 1) {
    const cart = getCart();
    const id = String(item.id);
    if (cart[id]) {
        cart[id].quantity = Math.min((cart[id].quantity || 0) + qty, item.max_quantity || 9999);
    } else {
        cart[id] = Object.assign({}, item, { quantity: qty });
    }
    saveCart(cart);
    showTemporaryAlert("Đã thêm vào giỏ hàng", 'success');
}

function removeFromCart(id) {
    const cart = getCart();
    delete cart[String(id)];
    saveCart(cart);
}

function setQuantity(id, qty) {
    const cart = getCart();
    if (!cart[String(id)]) return;
    if (qty <= 0) {
        delete cart[String(id)];
    } else {
        cart[String(id)].quantity = qty;
    }
    saveCart(cart);
}

function getCartCount() {
    const cart = getCart();
    return Object.values(cart).reduce((sum, it) => sum + (it.quantity || 0), 0);
}

function getCartTotal() {
    const cart = getCart();
    return Object.values(cart).reduce((sum, it) => sum + (it.quantity || 0) * (parseFloat(it.price) || 0), 0);
}

function updateCartBadge() {
    const count = getCartCount();
    document.querySelectorAll('.cart-badge').forEach(function(el) {
        el.textContent = count;
    });
}

function addToCartFromButton(btn) {
    const id = btn.dataset.id;
    const name = btn.dataset.name;
    const price = btn.dataset.price;
    const image = btn.dataset.image || '';
    const max = btn.dataset.max || 9999;
    addToCart({ id: id, name: name, price: price, image: image, max_quantity: parseInt(max, 10) }, 1);
}

function showTemporaryAlert(message, type = 'info') {
    const div = document.createElement('div');
    div.className = 'alert alert-' + (type === 'success' ? 'success' : 'info') + ' alert-auto-dismiss';
    div.innerHTML = message;
    document.body.appendChild(div);
    setTimeout(() => {
        div.style.opacity = '0';
        setTimeout(() => div.remove(), 500);
    }, 2500);
}

// Existing UI behaviours (alerts, QR modal)
document.addEventListener('DOMContentLoaded', function() {
    console.log('TechShop Client App initialized successfully!');

    // Update cart badge on load
    updateCartBadge();

    // Tự động ẩn các thông báo alert/notification sau 5 giây nếu có
    const alerts = document.querySelectorAll('.alert-auto-dismiss');
    alerts.forEach(function(alert) {
        setTimeout(function() {
            alert.style.opacity = '0';
            setTimeout(function() {
                alert.remove();
            }, 500);
        }, 5000);
    });

    // Ensure modal exists. If not, create it dynamically so all pages have the QR/contact modal.
    let modal = document.getElementById('contactQrModal');
    if (!modal) {
        modal = document.createElement('div');
        modal.id = 'contactQrModal';
        modal.className = 'contact-qr-modal';
        modal.setAttribute('aria-hidden', 'true');
        modal.innerHTML = `
            <div class="contact-qr-dialog" role="dialog" aria-modal="true" aria-labelledby="contactQrTitle">
                <button class="contact-qr-close" aria-label="Close">✕</button>
                <div class="contact-qr-content">
                    <h3 id="contactQrTitle">Kết nối</h3>
                    <img id="contactQrImage" src="" alt="QR code" />
                    <p id="contactQrText">Quét mã QR để kết nối.</p>
                    <a id="contactQrLink" class="contact-qr-button" href="#" target="_blank">Mở liên hệ</a>
                </div>
            </div>`;
        document.body.appendChild(modal);
    }
    const modalImage = document.getElementById('contactQrImage');
    const modalTitle = document.getElementById('contactQrTitle');
    const modalText = document.getElementById('contactQrText');
    const modalLink = document.getElementById('contactQrLink');
    const closeButton = document.querySelector('.contact-qr-close');
    const socialLinks = document.querySelectorAll('.social-link');

    if (modal && modalImage && modalTitle && modalText && modalLink && closeButton) {
        // create floating contact widget (only once)
        if (!document.getElementById('contactFloating')) {
            const contacts = window.TECHSHOP_CONTACTS || {
                zalo: 'https://zalo.me/0123456789',
                messenger: 'https://m.me/yourpage',
            };

            const wrap = document.createElement('div');
            wrap.className = 'contact-floating';
            wrap.id = 'contactFloating';
            // toggle button
            const toggle = document.createElement('button');
            toggle.className = 'cf-toggle';
            toggle.title = 'Liên hệ nhanh';
            toggle.innerHTML = '<i class="fas fa-comment"></i>';
            wrap.appendChild(toggle);

            // Zalo button
            const zaloBtn = document.createElement('a');
            zaloBtn.className = 'cf-btn zalo';
            zaloBtn.href = contacts.zalo;
            zaloBtn.target = '_blank';
            zaloBtn.rel = 'noopener';
            zaloBtn.innerHTML = '<span class="icon" aria-hidden="true">💬</span><span>Zalo</span>';
            wrap.appendChild(zaloBtn);

            // Messenger button
            const mesBtn = document.createElement('a');
            mesBtn.className = 'cf-btn messenger';
            mesBtn.href = contacts.messenger;
            mesBtn.target = '_blank';
            mesBtn.rel = 'noopener';
            mesBtn.innerHTML = '<span class="icon" aria-hidden="true">✉️</span><span>Messenger</span>';
            wrap.appendChild(mesBtn);

            document.body.appendChild(wrap);

            // simple toggle (collapse/expand)
            let open = true;
            function setOpen(state) {
                open = state;
                if (open) {
                    zaloBtn.style.display = 'inline-flex';
                    mesBtn.style.display = 'inline-flex';
                } else {
                    zaloBtn.style.display = 'none';
                    mesBtn.style.display = 'none';
                }
            }
            setOpen(false);
            toggle.addEventListener('click', function(){ setOpen(!open); });
        }

        const platformLabels = {
            zalo: 'Zalo',
            facebook: 'Facebook',
            instagram: 'Instagram',
            tiktok: 'TikTok'
        };

        const createQrUrl = function(url) {
            return 'https://api.qrserver.com/v1/create-qr-code/?size=240x240&data=' + encodeURIComponent(url);
        };

        socialLinks.forEach(function(link) {
            link.addEventListener('click', function(event) {
                event.preventDefault();

                const platform = link.dataset.platform || 'contact';
                const contactUrl = link.dataset.link || 'https://techshop.com';
                const title = platformLabels[platform] || 'Liên hệ';

                modalTitle.textContent = 'Kết nối với ' + title;
                modalText.textContent = 'Quét mã QR để kết bạn hoặc mở đường dẫn liên hệ nhanh.';
                modalImage.src = createQrUrl(contactUrl);
                modalImage.alt = 'Mã QR ' + title;
                modalLink.href = contactUrl;
                modalLink.textContent = 'Mở ' + title;
                modal.classList.add('show');
                modal.setAttribute('aria-hidden', 'false');
            });
        });

        closeButton.addEventListener('click', function() {
            modal.classList.remove('show');
            modal.setAttribute('aria-hidden', 'true');
        });

        modal.addEventListener('click', function(event) {
            if (event.target === modal) {
                modal.classList.remove('show');
                modal.setAttribute('aria-hidden', 'true');
            }
        });

        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape' && modal.classList.contains('show')) {
                modal.classList.remove('show');
                modal.setAttribute('aria-hidden', 'true');
            }
        });
    }
});
