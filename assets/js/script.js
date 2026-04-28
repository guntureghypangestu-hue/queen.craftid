// --- FUNGSI GLOBAL (BISA DIPANGGIL DARI HALAMAN MANAPUN) ---
const updateHeaderIcons = () => {
    const wishlist = JSON.parse(localStorage.getItem('wishlist')) || [];
    const cart = JSON.parse(localStorage.getItem('cart')) || [];
    const wishlistEl = document.querySelector('.wishlist-count');
    const cartEl = document.querySelector('.cart-count');
    if(wishlistEl) wishlistEl.textContent = wishlist.length;
    if(cartEl) cartEl.textContent = cart.reduce((total, item) => total + item.quantity, 0);
};

const showNotification = (message) => {
    const notification = document.createElement('div');
    notification.className = 'notification-toast';
    notification.textContent = message;
    document.body.appendChild(notification);
    setTimeout(() => notification.classList.add('show'), 10);
    setTimeout(() => {
        notification.classList.remove('show');
        setTimeout(() => notification.remove(), 300);
    }, 2000);
};

// --- FUNGSI GLOBAL UNTUK UPDATE UI (DIPINDAHKAN KE LUAR DOMCONTENTLOADED) ---
const updateProductCardUI = () => {
    const wishlist = JSON.parse(localStorage.getItem('wishlist')) || [];
    const cart = JSON.parse(localStorage.getItem('cart')) || [];
    
    const wishlistIds = new Set(wishlist.map(item => String(item.id)));
    const cartIds = new Set(cart.map(item => String(item.id)));

    document.querySelectorAll('.product-card').forEach(card => {
        const productId = String(card.dataset.productId);
        
        // --- UPDATE UI WISHLIST ---
        const wishlistBtn = card.querySelector('.wishlist-btn');
        if (wishlistBtn) {
            const wishIcon = wishlistBtn.querySelector('i');
            if (wishIcon) {
                if (wishlistIds.has(productId)) {
                    wishlistBtn.classList.add('active');
                    wishIcon.classList.remove('far');
                    wishIcon.classList.add('fas');
                } else {
                    wishlistBtn.classList.remove('active');
                    wishIcon.classList.remove('fas');
                    wishIcon.classList.add('far');
                }
            }
        }

        // --- UPDATE UI CART (PINK JIKA SUDAH ADA) ---
        const cartBtn = card.querySelector('.cart-btn');
        if (cartBtn) {
            if (cartBtn.classList.contains('disabled') && !cartIds.has(productId)) {
                return; 
            }

            if (cartIds.has(productId)) {
                cartBtn.classList.add('active'); // Kunci dan ubah warna
            } else {
                cartBtn.classList.remove('active'); // Buka kunci dan warna normal
            }
        }
    });
};


// --- EVENT LISTENER UTAMA ---
document.addEventListener('DOMContentLoaded', function() {
    // --- Mobile Menu Toggle (PERBAIKAN) ---
    const hamburger = document.querySelector('.hamburger');
    const mainNav = document.querySelector('.main-nav');

    if (hamburger && mainNav) {
        // Event listener untuk hamburger menu
        hamburger.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            mainNav.classList.toggle('active');
            hamburger.classList.toggle('active');
        });

        // Tutup sidebar saat klik di luar menu atau klik link
        const navLinks = document.querySelectorAll('.main-nav ul li a');
        navLinks.forEach(link => {
            link.addEventListener('click', (e) => {
                mainNav.classList.remove('active');
                hamburger.classList.remove('active');
            });
        });

        // Tutup sidebar saat escape key ditekan
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && mainNav.classList.contains('active')) {
                mainNav.classList.remove('active');
                hamburger.classList.remove('active');
            }
        });
    }

    // --- Smooth Scroll for Anchor Links ---
    const links = document.querySelectorAll('a[href^="#"]');
    for (const link of links) {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            const targetId = this.getAttribute('href').substring(1);
            const targetElement = document.getElementById(targetId);
            if (targetElement) {
                if (mainNav && mainNav.classList.contains('active')) {
                    mainNav.classList.remove('active');
                    hamburger.classList.remove('active');
                }
                targetElement.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }
        });
    }

    // --- Header on Scroll Effect ---
    const header = document.querySelector('.sticky-header');
    if (header) {
        window.addEventListener('scroll', () => {
            if (window.scrollY > 100) {
                header.style.padding = '10px 0';
            } else {
                header.style.padding = '15px 0';
            }
        });
    }

    const saveToWishlist = (product, buttonElement) => {
        let wishlist = JSON.parse(localStorage.getItem('wishlist')) || [];
        const productId = String(product.id);
        const existingIndex = wishlist.findIndex(item => String(item.id) === productId);

        if (existingIndex === -1) {
            wishlist.push(product);
            buttonElement.classList.add('active');
            const icon = buttonElement.querySelector('i');
            icon.classList.remove('far');
            icon.classList.add('fas');
            showNotification(`${product.name} ditambahkan ke wishlist!`);
        } else {
            showNotification(`${product.name} sudah ada di wishlist.`);
        }
        localStorage.setItem('wishlist', JSON.stringify(wishlist));
        updateHeaderIcons();
    };
    
    const saveToCart = (product, buttonElement) => {
        if (!product.id || !product.name) {
            showNotification('Produk tidak valid');
            return;
        }

        let cart = JSON.parse(localStorage.getItem('cart')) || [];
        const productId = String(product.id);
        const existingIndex = cart.findIndex(item => String(item.id) === productId);

        if (existingIndex === -1) {
            if (product.stock === 0) {
                showNotification('Stok habis');
                return;
            }

            const normalizedProduct = {
                id: productId,
                name: String(product.name),
                price: parseFloat(product.price) || 0,
                image: String(product.image || ''),
                stock: parseInt(product.stock) || 0,
                quantity: 1
            };
            
            cart.push(normalizedProduct);
            localStorage.setItem('cart', JSON.stringify(cart));

            if (buttonElement) {
                buttonElement.classList.add('active');
            }

            let wishlist = JSON.parse(localStorage.getItem('wishlist')) || [];
            wishlist = wishlist.filter(item => String(item.id) !== productId);
            localStorage.setItem('wishlist', JSON.stringify(wishlist));

            updateHeaderIcons();
            updateProductCardUI(); // Update semua kartu
            showNotification(`${product.name} ditambahkan ke keranjang!`);
        } else {
            showNotification(`${product.name} sudah ada di keranjang.`);
        }
    };

    // --- EVENT DELEGATION ---
    document.addEventListener('click', (e) => {
        const cartBtn = e.target.closest('.cart-btn');
        if (cartBtn) {
            e.preventDefault();
            e.stopPropagation();
            
            if (cartBtn.classList.contains('disabled')) {
                showNotification('Produk ini sedang habis stok.');
                return;
            }

            const hasTimeredPricing = cartBtn.dataset.hasTimeredPricing === '1' || cartBtn.dataset.hasTimeredPricing === 'true';
            
            // CEK JIKA PRODUK MEMILIKI HARGA BERTINGKAT
            if (hasTimeredPricing) {
                showNotification('Produk ini harus dipesan dari halaman detail untuk memilih jumlah lembar.');
                const productId = cartBtn.dataset.productId;
                setTimeout(() => {
                    window.location.href = `detail.php?id=${productId}`;
                }, 1000);
                return;
            }

            // CEGAH KLIK KEDUA KALI
            if (cartBtn.classList.contains('active')) {
                showNotification('Produk ini sudah ada di keranjang.');
                return;
            }
            
            const productId = cartBtn.dataset.productId;
            const productName = cartBtn.dataset.productName;
            const productPrice = cartBtn.dataset.productPrice;
            const productImage = cartBtn.dataset.productImage;
            const productStock = cartBtn.dataset.productStock;
            
            if (!productId || !productName || !productPrice) {
                showNotification('Data produk tidak lengkap');
                return;
            }
            
            const product = {
                id: String(productId),
                name: String(productName),
                price: parseFloat(productPrice),
                image: String(productImage || ''),
                stock: parseInt(productStock) || 0
            };
            
            saveToCart(product, cartBtn);
            return;
        }

        const wishlistBtn = e.target.closest('.wishlist-btn');
        if (wishlistBtn) {
            e.preventDefault();
            e.stopPropagation();
            const product = {
                id: String(wishlistBtn.dataset.productId),
                name: String(wishlistBtn.dataset.productName),
                price: parseFloat(wishlistBtn.dataset.productPrice) || 0,
                image: String(wishlistBtn.dataset.productImage || ''),
                stock: parseInt(wishlistBtn.dataset.productStock) || 0
            };
            saveToWishlist(product, wishlistBtn);
            return;
        }
    }, true);

    const observer = new MutationObserver(() => {
        updateProductCardUI();
    });
    
    observer.observe(document.querySelector('.product-grid') || document.body, {
        childList: true,
        subtree: true
    });

    updateHeaderIcons();

    if (document.querySelector('.product-card')) {
        setTimeout(() => {
            updateProductCardUI(); 
        }, 100);
    }
    
    window.addEventListener('storage', () => {
        updateProductCardUI();
        updateHeaderIcons();
    });
    window.addEventListener('cartUpdated', () => {
        updateProductCardUI();
        updateHeaderIcons();
    });
});

// --- Toast CSS & CSS Tambahan untuk Cart Pink ---
const style = document.createElement('style');
style.textContent = `
.notification-toast {
    position: fixed;
    bottom: 20px;
    left: 50%;
    transform: translateX(-50%) translateY(100px);
    background-color: #333;
    color: white;
    padding: 12px 24px;
    border-radius: 50px;
    font-size: 0.9rem;
    z-index: 9999;
    opacity: 0;
    transition: all 0.3s ease;
}
.notification-toast.show {
    transform: translateX(-50%) translateY(0);
    opacity: 1;
}

/* CSS IKON CART JADI PINK */
.cart-btn.active {
    background-color: #ff69b4 !important; 
    border-color: #ff69b4 !important;
    pointer-events: none; /* Tidak bisa diklik lagi */
}
.cart-btn.active i {
    color: white !important; 
}`;
document.head.appendChild(style);