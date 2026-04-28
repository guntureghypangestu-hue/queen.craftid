<?php include 'includes/header.php'; ?>

<style>
    /* CSS Khusus Halaman Wishlist */
    .wishlist-page-container {
        background-color: #f4f4f4;
        padding: 40px 0;
        min-height: 80vh;
        margin-top: 60px;
    }
    .wishlist-wrapper {
        max-width: 900px;
        margin: 0 auto;
        background-color: #ffffff;
        border-radius: 8px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        overflow: hidden;
    }
    .wishlist-header {
        padding: 25px 30px;
        border-bottom: 1px solid #e0e0e0;
    }
    .wishlist-header h1 {
        font-family: var(--font-heading);
        font-size: 1.8rem;
        color: var(--secondary-color);
        margin: 0;
    }
    #wishlist-container {
        padding: 0 30px;
    }
    .wishlist-item {
        display: flex;
        align-items: center;
        padding: 25px 0; /* Tambah sedikit padding vertikal */
        border-bottom: 1px solid #e0e0e0;
    }
    .wishlist-item:last-child {
        border-bottom: none;
    }
    .wishlist-item-image {
        width: 100px;
        height: 100px;
        object-fit: cover;
        border-radius: 8px;
        margin-right: 25px;
        flex-shrink: 0;
    }
    .wishlist-item-details {
        flex-grow: 1;
    }
    .wishlist-item-details h4 {
        font-size: 1.1rem;
        font-weight: 600;
        color: #333;
        margin: 0 0 5px 0;
    }
    .wishlist-item-details p {
        font-size: 0.9rem;
        color: #777;
        margin: 0;
    }
    
    /* --- Gaya Tombol Aksi untuk DESKTOP (Versi Diperbaiki) --- */
    .wishlist-item-actions {
        display: flex;
        flex-direction: column; /* Susun vertikal untuk tampilan yang lebih bersih */
        gap: 10px;
        align-items: stretch; /* Buat tombol sama lebar */
    }

    .wishlist-item-actions button {
        padding: 10px 18px;
        border: none;
        border-radius: 8px; /* Lebih membulat */
        font-weight: 600;
        font-size: 0.9rem;
        cursor: pointer;
        transition: all 0.2s ease-in-out;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        white-space: nowrap;
        min-width: 160px; /* Lebar minimum agar seragam */
    }

    .wishlist-item-actions .add-to-cart-from-wishlist {
        background-color: var(--secondary-color); /* Gunakan warna sekunder (hijau) */
        color: white;
        box-shadow: 0 2px 4px rgba(74, 106, 82, 0.2);
    }
    .wishlist-item-actions .add-to-cart-from-wishlist:hover {
        background-color: #3a5342;
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(74, 106, 82, 0.3);
    }

    .wishlist-item-actions .remove-item {
        background-color: #f8d7da;
        color: #721c24; /* Warna merah yang lebih lembut */
       
    }
    .wishlist-item-actions .remove-item:hover {
        background-color: #f8d7da; /* Background merah muda saat hover */
        color: #721c24;
        transform: translateY(-2px);
    }
    
    .empty-wishlist-message {
        text-align: center;
        padding: 60px 20px;
    }
    .empty-wishlist-message h2 {
        color: #777;
        margin-bottom: 15px;
    }

    /* --- PERBAIKAN RESPONSIVE UNTUK MOBILE --- */
    @media (max-width: 768px) {
        .wishlist-page-container {
            padding: 15px 0;
        }

        .wishlist-wrapper {
            margin: 0 10px;
            border-radius: 12px;
        }

        .wishlist-header {
            padding: 20px;
            text-align: center;
        }
        
        .wishlist-header h1 {
            font-size: 1.5rem;
        }

        #wishlist-container {
            padding: 0 15px;
        }

        /* Struktur Baru untuk Item Wishlist Mobile */
        .wishlist-item {
            flex-direction: column;
            align-items: flex-start;
            padding: 20px 10px;
            gap: 15px;
        }

        .wishlist-item-image {
            width: 100%;
            height: 200px;
            margin-right: 0;
            border-radius: 8px;
        }

        .wishlist-item-details {
            width: 100%;
            text-align: center;
        }

        .wishlist-item-details h4 {
            font-size: 1.2rem;
        }
        
        .wishlist-item-details p {
            font-size: 1rem;
        }

        /* Wrapper untuk tombol aksi di mobile */
        .wishlist-item-controls-wrapper {
            display: flex;
            justify-content: center;
            align-items: center;
            width: 100%;
            gap: 15px;
            border-top: 1px solid #f0f0f0;
            padding-top: 15px;
        }
        
        /* Override gaya tombol untuk mobile */
        .wishlist-item-actions {
            width: 100%;
            flex-direction: row; /* Kembali ke horizontal di mobile */
            justify-content: center;
            margin: 0;
        }

        .wishlist-item-actions button {
            flex: 1; /* Biarkan tombol mengisi ruang yang tersedia */
            padding: 12px 15px;
            font-size: 1rem;
            border-radius: 8px;
            min-width: auto; /* Hapus lebar minimum */
        }

        .empty-wishlist-message {
            padding: 80px 20px;
        }
        .empty-wishlist-message h2 {
            font-size: 1.5rem;
            color: #555;
        }
    }
</style>

<main class="wishlist-page-container">
    <div class="wishlist-wrapper">
        <div class="wishlist-header">
            <h1>Wishlist Saya</h1>
        </div>
        <div id="wishlist-container">
            <!-- Konten wishlist akan dimuat oleh JavaScript -->
        </div>
    </div>
</main>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const wishlistContainer = document.getElementById('wishlist-container');
    const wishlistWrapper = document.querySelector('.wishlist-wrapper');
    let wishlist = JSON.parse(localStorage.getItem('wishlist')) || [];

    // Fungsi untuk merender ulang tampilan wishlist
    const renderWishlist = () => {
        if (wishlist.length === 0) {
            wishlistWrapper.innerHTML = `
                <div class="empty-wishlist-message">
                    <h2>Wishlist Anda kosong</h2>
                    <p>Silakan tambahkan produk yang Anda sukai.</p>
                    <a href="index.php" class="btn-primary">Lanjut Belanja</a>
                </div>
            `;
            return;
        }

        const wishlistItemsHTML = wishlist.map(item => `
            <div class="wishlist-item">
                <img src="assets/images/uploads/${item.image}" alt="${item.name}" class="wishlist-item-image">
                <div class="wishlist-item-details">
                    <h4>${item.name}</h4>
                    <p>Rp ${item.price.toLocaleString('id-ID')}</p>
                </div>
                <div class="wishlist-item-actions">
                    <button class="add-to-cart-from-wishlist" 
                            data-id="${item.id}" 
                            data-name="${item.name}" 
                            data-price="${item.price}" 
                            data-image="${item.image}"
                            data-stock="${item.stock || 0}">
                        <i class="fas fa-cart-plus"></i> Tambah ke Keranjang
                    </button>
                    <button class="remove-item" data-id="${item.id}" title="Hapus dari Wishlist">
                        <i class="fas fa-trash-alt"></i> Hapus
                    </button>
                </div>
            </div>
        `).join('');

        wishlistContainer.innerHTML = wishlistItemsHTML;
    };

    // Event listener untuk klik pada tombol aksi
    wishlistWrapper.addEventListener('click', (e) => {
        const clickedButton = e.target.closest('button');
        if (!clickedButton) return;

        const productId = String(clickedButton.dataset.id);
        if (!productId || productId === 'undefined') return;

        if (clickedButton.matches('.remove-item')) {
            wishlist = wishlist.filter(item => String(item.id) !== productId);
            localStorage.setItem('wishlist', JSON.stringify(wishlist));
            if (typeof updateHeaderIcons === 'function') updateHeaderIcons();
            renderWishlist();
            if (typeof showNotification === 'function') showNotification('Produk berhasil dihapus dari wishlist.');
            return;
        }

        if (clickedButton.matches('.add-to-cart-from-wishlist')) {
            e.preventDefault();
            e.stopPropagation();

            const productId = String(clickedButton.dataset.id);
            const productName = String(clickedButton.dataset.name || 'Unnamed Product');
            const productPrice = parseFloat(clickedButton.dataset.price) || 0;
            const productImage = String(clickedButton.dataset.image || 'default.jpg');

            // Tambahkan ke cart (tanpa validasi stok ketat, karena stok tidak tersimpan di wishlist)
            let cart = JSON.parse(localStorage.getItem('cart')) || [];
            
            const normalizedProduct = {
                id: productId,
                name: productName,
                price: productPrice,
                image: productImage,
                quantity: 1
            };

            const existingProduct = cart.find(item => String(item.id) === productId);
            if (existingProduct) {
                existingProduct.quantity += 1;
            } else {
                cart.push(normalizedProduct);
            }

            // Simpan cart ke localStorage
            localStorage.setItem('cart', JSON.stringify(cart));

            // Hapus dari wishlist
            wishlist = wishlist.filter(item => String(item.id) !== productId);
            localStorage.setItem('wishlist', JSON.stringify(wishlist));

            // Update UI
            if (typeof updateHeaderIcons === 'function') updateHeaderIcons();
            renderWishlist();
            
            if (typeof showNotification === 'function') {
                showNotification(`${productName} ditambahkan ke keranjang!`);
            }
        }
    });

    // Panggil fungsi saat halaman dimuat
    if (typeof updateHeaderIcons === 'function') updateHeaderIcons();
    renderWishlist();
});
</script>

<?php include 'includes/footer.php'; ?>