<?php 
require_once 'includes/db.php';
require_once 'includes/functions.php';

// API endpoint untuk fetch stock produk dari database
// Called via AJAX: /cart.php?action=getProductStocks
if (($_GET['action'] ?? '') === 'getProductStocks') {
    header('Content-Type: application/json');
    
    $productIds = $_GET['ids'] ?? '';
    if (!$productIds) {
        echo json_encode([]);
        exit;
    }
    
    $ids = array_filter(array_map('intval', explode(',', $productIds)));
    if (empty($ids)) {
        echo json_encode([]);
        exit;
    }
    
    // Build placeholders for IN clause
    $placeholders = implode(',', array_fill(0, count($ids), '?'));
    
    $stmt = $pdo->prepare("
        SELECT id, stock FROM products 
        WHERE id IN ($placeholders)
    ");
    $stmt->execute($ids);
    
    $stocks = [];
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $stocks[$row['id']] = (int)$row['stock'];
    }
    
    echo json_encode($stocks);
    exit;
}

include 'includes/header.php'; 
?>

<style>
    /* CSS Khusus Halaman Cart */
    .cart-page-container {
        background-color: #f4f4f4;
        padding: 40px 0;
        min-height: 80vh;
        margin-top: 60px;
    }

    .cart-wrapper {
        max-width: 900px;
        margin: 0 auto;
        background-color: #ffffff;
        border-radius: 8px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        overflow: hidden;
    }

    .cart-header {
        padding: 25px 30px;
        border-bottom: 1px solid #e0e0e0;
    }

    .cart-header h1 {
        font-family: var(--font-heading);
        font-size: 1.8rem;
        color: var(--secondary-color);
        margin: 0;
    }

    #cart-container {
        padding: 0 30px;
    }

    .cart-item {
        display: flex;
        align-items: center;
        padding: 20px 0;
        border-bottom: 1px solid #e0e0e0;
    }

    .cart-item:last-child {
        border-bottom: none;
    }

    .cart-item-image {
        width: 100px;
        height: 100px;
        object-fit: cover;
        border-radius: 8px;
        margin-right: 25px;
        flex-shrink: 0;
    }

    .cart-item-details {
        flex-grow: 1;
    }

    .cart-item-details h4 {
        font-size: 1.1rem;
        font-weight: 600;
        color: #333;
        margin: 0 0 5px 0;
    }

    .cart-item-details p {
        font-size: 0.9rem;
        color: #777;
        margin: 0;
    }

    .cart-item-quantity {
        display: flex;
        align-items: center;
        border: 1px solid #e0e0e0;
        border-radius: 6px;
        overflow: hidden;
        margin-right: 25px;
    }

    .quantity-btn {
        width: 36px;
        height: 36px;
        background-color: #f5f5f5;
        border: none;
        cursor: pointer;
        font-size: 1.2rem;
        color: #555;
        transition: background-color 0.2s;
    }
    .quantity-btn:hover {
        background-color: #e0e0e0;
    }

    .quantity-input {
        width: 50px;
        height: 36px;
        text-align: center;
        border: none;
        font-size: 1rem;
        font-weight: 500;
    }

    .cart-item-actions {
        text-align: right;
    }
    
    /* PERBAIKAN: Tombol Hapus yang lebih jelas */
    .cart-item-actions .remove-item {
        display: inline-block;
        padding: 8px 12px;
        background-color: #f8d7da;
        color: #721c24;
        border-radius: 6px;
        text-decoration: none;
        font-weight: 500;
        font-size: 0.9rem;
        transition: background-color 0.2s, color 0.2s;
    }
    .cart-item-actions .remove-item:hover {
        background-color: #f5c6cb;
        color: #5a1e1a;
        text-decoration: none;
    }

    .cart-summary {
        padding: 25px 30px;
        background-color: #fafafa;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .cart-summary h3 {
        font-size: 1.2rem;
        color: #333;
        margin: 0;
    }

    .cart-summary-actions {
        display: flex;
        align-items: center;
        gap: 15px;
    }

    .cart-summary .btn-secondary {
        padding: 12px 25px;
        border: 1px solid #ccc;
        background-color: white;
        color: #333;
        text-decoration: none;
        border-radius: 6px;
        font-weight: 500;
        transition: all 0.2s;
    }
    .cart-summary .btn-secondary:hover {
        background-color: #f5f5f5;
    }

    .cart-summary .btn-primary {
        padding: 12px 30px;
        background-color: var(--secondary-color);
        color: white;
        text-decoration: none;
        border-radius: 6px;
        font-weight: 600;
        transition: background-color 0.2s;
    }
    .cart-summary .btn-primary:hover {
        background-color: #3a5342;
    }

    /* Pesan jika keranjang kosong */
    .empty-cart-message {
        text-align: center;
        padding: 60px 20px;
    }
    .empty-cart-message img {
        width: 120px;
        opacity: 0.5;
        margin-bottom: 20px;
    }
    .empty-cart-message h2 {
        color: #777;
        margin-bottom: 15px;
    }

    /* --- PERBAIKAN RESPONSIVE UNTUK MOBILE (VERSI BARU) --- */
    @media (max-width: 768px) {
        .cart-page-container {
            padding: 15px 0;
        }

        .cart-wrapper {
            margin: 0 10px;
            border-radius: 12px;
        }

        .cart-header {
            padding: 20px;
            text-align: center;
        }
        
        .cart-header h1 {
            font-size: 1.5rem;
        }

        #cart-container {
            padding: 0 15px;
        }

        /* Struktur Baru untuk Item Keranjang Mobile */
        .cart-item {
            flex-direction: column;
            align-items: flex-start; /* Rata kiri */
            padding: 20px 10px;
            gap: 15px;
        }

        .cart-item-image {
            width: 100%;
            height: 200px; /* Gambar lebih lebar dan tinggi */
            margin-right: 0;
            border-radius: 8px;
        }

        .cart-item-details {
            width: 100%;
            text-align: center; /* Detail di tengah */
        }

        .cart-item-details h4 {
            font-size: 1.2rem;
        }
        
        .cart-item-details p {
            font-size: 1rem;
        }

        /* Wrapper untuk quantity dan actions di mobile */
        .cart-item-controls-wrapper {
            display: flex;
            justify-content: space-between;
            align-items: center;
            width: 100%;
            border-top: 1px solid #f0f0f0;
            padding-top: 15px;
        }

        .cart-item-quantity {
            margin: 0; /* Reset margin */
        }

        .quantity-btn {
            width: 40px;
            height: 40px;
            font-size: 1.2rem;
            border-radius: 8px;
        }

        .quantity-input {
            width: 60px;
            height: 40px;
            font-size: 1.1rem;
        }

        .cart-item-actions {
            margin: 0;
        }
        
        .cart-item-actions .remove-item {
            padding: 10px 15px;
            font-size: 1rem;
        }

        .cart-summary {
            flex-direction: column;
            justify-content: center;
            text-align: center;
            gap: 20px;
            padding: 20px;
        }

        .cart-summary h3 {
            font-size: 1.4rem;
            order: 1;
        }

        .cart-summary-actions {
            flex-direction: column;
            width: 100%;
            gap: 10px;
            order: 2;
        }

        .cart-summary .btn-secondary,
        .cart-summary .btn-primary {
            width: 100%;
            padding: 15px;
            text-align: center;
            border-radius: 8px;
            font-size: 1rem;
        }

        .empty-cart-message {
            padding: 80px 20px;
        }
        .empty-cart-message h2 {
            font-size: 1.5rem;
            color: #555;
        }
    }

    /* --- PERBAIKAN LAYOUT DESKTOP --- */
.cart-item {
    display: flex;
    align-items: center;
    gap: 20px;
}

.cart-item-details {
    flex: 1;
}

.cart-item-controls-wrapper {
    display: flex;
    align-items: center;
    gap: 20px;
    margin-left: auto;
}

.cart-item-quantity {
    margin-right: 0;
}

.cart-item-actions {
    text-align: right;
}

</style>

<main class="cart-page-container">
    <div class="cart-wrapper">
        <div class="cart-header">
            <h1>Keranjang Saya</h1>
        </div>
        <div id="cart-container">
            <!-- Konten keranjang akan dimuat oleh JavaScript -->
        </div>
    </div>
</main>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const cartContainer = document.getElementById('cart-container');
    const cartWrapper = document.querySelector('.cart-wrapper');
    let cart = JSON.parse(localStorage.getItem('cart')) || [];
    
    // Helper function untuk notification
    const showCartNotification = (message) => {
        if (typeof showNotification === 'function') {
            showNotification(message);
        } else {
            // Fallback ke alert jika showNotification belum tersedia
            alert(message);
        }
    };
    
    // Fetch stock dari database untuk produk yang tidak punya stock di localStorage
    const enrichCartWithStocks = async () => {
        // Cari produk yang tidak punya stock atau stock = 0
        const productsNeedingStock = cart.filter(item => !item.stock || item.stock === 0);
        
        if (productsNeedingStock.length === 0) {
            return; // Semua produk sudah punya stock
        }
        
        const productIds = productsNeedingStock.map(item => item.id).join(',');
        console.log('Fetching stocks for products:', productIds);
        
        try {
            const response = await fetch(`cart.php?action=getProductStocks&ids=${productIds}`);
            const stocks = await response.json();
            console.log('Fetched stocks from database:', stocks);
            
            // Update cart items dengan stock dari database
            cart.forEach(item => {
                if (!item.stock || item.stock === 0) {
                    if (stocks[item.id] !== undefined) {
                        item.stock = stocks[item.id];
                        console.log(`Updated stock for product ${item.id}: ${item.stock}`);
                    }
                }
            });
            
            localStorage.setItem('cart', JSON.stringify(cart));
            console.log('Cart updated with database stocks:', cart);
        } catch (error) {
            console.error('Error fetching product stocks:', error);
        }
    };
    
    // Tunggu stock enrichment sebelum menampilkan cart
    enrichCartWithStocks().then(() => {
        filterAndRenderCart();
    });
    
    // Filter dan validasi cart items
    const filterAndRenderCart = () => {
        cart = cart.filter(item => {
            // Pastikan item memiliki properties yang diperlukan
            if (!item.id || !item.name || typeof item.price === 'undefined') {
                console.warn('Invalid cart item removed:', item);
                return false;
            }
            // Konversi price menjadi number jika belum
            item.price = parseFloat(item.price) || 0;
            item.quantity = parseInt(item.quantity) || 1;
            item.stock = parseInt(item.stock) || 0;
            
            // Cap quantity ke stock maximum
            if (item.stock > 0 && item.quantity > item.stock) {
                console.warn('Quantity capped from', item.quantity, 'to stock', item.stock);
                item.quantity = item.stock;
            }
            
            return true;
        });
        
        // Simpan kembali cart yang sudah divalidasi
        if (cart.length > 0) {
            localStorage.setItem('cart', JSON.stringify(cart));
        }
        
        renderCart();
    };

    // Fungsi untuk menampilkan isi keranjang
    const renderCart = () => {
        if (cart.length === 0) {
            cartWrapper.innerHTML = `
                <div class="empty-cart-message">
                    <h2>Keranjang belanja Anda kosong</h2>
                    <p>Silakan tambahkan produk terlebih dahulu.</p>
                    <a href="index.php" class="btn-primary">Lanjut Belanja</a>
                </div>
            `;
            return;
        }

        let totalPrice = 0;
        const cartItemsHTML = cart.map(item => {
            // Validasi dan konversi price menjadi number
            const price = parseFloat(item.price) || 0;
            const quantity = parseInt(item.quantity) || 1;
            const stock = parseInt(item.stock) || 0;
            const itemTotal = price * quantity;
            totalPrice += itemTotal;
            
            // Info stok
            const stockInfo = stock > 0 ? `Stok: ${stock}` : 'Stok Habis';
            const stockClass = stock === 0 ? 'text-danger' : 'text-muted';
            
            return `
                <div class="cart-item">
                    <img src="assets/images/uploads/${item.image}" alt="${item.name}" class="cart-item-image">
                    <div class="cart-item-details">
                        <h4>${item.name}</h4>
                        <p>Variants: Standard</p>
                        <p>Rp ${price.toLocaleString('id-ID')}</p>
                        <p style="font-size: 0.85rem; color: #999;" class="${stockClass}">${stockInfo}</p>
                    </div>
                    <div class="cart-item-controls-wrapper">
                        <div class="cart-item-quantity">
                            <button class="quantity-btn decrease" data-id="${item.id}">-</button>
                            <input type="text" class="quantity-input" value="${quantity}" data-id="${item.id}" readonly>
                            <button class="quantity-btn increase" data-id="${item.id}">+</button>
                        </div>
                        <div class="cart-item-actions">
                            <a href="#" class="remove-item" data-id="${item.id}">Hapus</a>
                        </div>
                    </div>
                </div>
            `;
        }).join('');

        cartContainer.innerHTML = cartItemsHTML;

        const summaryDiv = document.createElement('div');
        summaryDiv.className = 'cart-summary';
        summaryDiv.innerHTML = `
            <h3>Total Pembayaran: Rp ${totalPrice.toLocaleString('id-ID')}</h3>
            <div class="cart-summary-actions">
                <a href="index.php" class="btn-secondary">Lanjut Belanja</a>
                <a href="checkout.php" class="btn-primary">Checkout</a>
            </div>
        `;
        cartWrapper.appendChild(summaryDiv);
    };

    cartWrapper.addEventListener('click', (e) => {
        const productId = e.target.dataset.id;
        
        if (productId && (e.target.matches('.increase') || e.target.matches('.decrease'))) {
            const item = cart.find(p => p.id === productId);
            if (item) {
                console.log('Item found:', item.name, 'Current qty:', item.quantity, 'Stock:', item.stock);
                
                if (e.target.matches('.increase')) {
                    // Validasi stock sebelum increase
                    const maxStock = parseInt(item.stock) || 0;
                    const currentQty = parseInt(item.quantity) || 0;
                    const newQty = currentQty + 1;
                    
                    console.log('Increase clicked. Max stock:', maxStock, 'Current qty:', currentQty, 'New qty:', newQty);
                    
                    // Cek jika stok habis atau melebihi batas
                    if (maxStock === 0) {
                        showCartNotification(`Stok ${item.name} sedang habis.`);
                        return;
                    }
                    
                    if (newQty > maxStock) {
                        showCartNotification(`Stok ${item.name} hanya tersedia ${maxStock} unit. Saat ini ${currentQty} sudah di keranjang.`);
                        return;
                    }
                    
                    item.quantity = newQty;
                    console.log('Quantity increased to', item.quantity);
                } else if (e.target.matches('.decrease')) {
                    if (item.quantity > 1) {
                        item.quantity--;
                        console.log('Quantity decreased to', item.quantity);
                    } else {
                        showCartNotification('Minimal 1 item harus ada di keranjang');
                        return;
                    }
                }
                
                localStorage.setItem('cart', JSON.stringify(cart));
                const oldSummary = cartWrapper.querySelector('.cart-summary');
                if(oldSummary) oldSummary.remove();
                renderCart();
                if (typeof updateHeaderIcons === 'function') {
                    updateHeaderIcons();
                }
            }
        }

            if (productId && e.target.matches('.remove-item')) {
            e.preventDefault();
            cart = cart.filter(p => p.id !== productId);
            localStorage.setItem('cart', JSON.stringify(cart));
            const oldSummary = cartWrapper.querySelector('.cart-summary');
            if(oldSummary) oldSummary.remove();
            renderCart();
            if (typeof updateHeaderIcons === 'function') {
                updateHeaderIcons();
            }
            
            // TAMBAHKAN 2 BARIS INI:
            window.dispatchEvent(new Event('cartUpdated'));
            updateProductCardUI(); 
        }
       
    });
});
</script>

<?php include 'includes/footer.php'; ?>