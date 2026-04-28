<?php 
include 'includes/header.php';
require_once 'includes/db.php';
?>

<style>
    /* CSS Khusus Halaman Checkout */
    .checkout-page-container {
        background-color: #f4f4f4;
        padding: 40px 0;
        min-height: 80vh;
        margin-top: 70px;
    }

    .checkout-wrapper {
        max-width: 900px;
        margin: 0 auto;
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 30px;
    }

    .checkout-section, .order-summary-section {
        background-color: #ffffff;
        border-radius: 8px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        padding: 30px;
    }

    .section-title {
        font-family: var(--font-heading);
        font-size: 1.5rem;
        color: var(--secondary-color);
        margin-top: 0;
        margin-bottom: 25px;
        padding-bottom: 15px;
        border-bottom: 2px solid #f0f0f0;
    }

    .form-group {
        margin-bottom: 20px;
    }

    .form-group label {
        display: block;
        font-weight: 600;
        margin-bottom: 8px;
        color: #333;
    }

    .form-group input,
    .form-group textarea {
        width: 100%;
        padding: 12px 15px;
        border: 1px solid #ddd;
        border-radius: 6px;
        font-size: 1rem;
        transition: border-color 0.2s;
    }

    .form-group input:focus,
    .form-group textarea:focus {
        outline: none;
        border-color: var(--secondary-color);
    }

    .order-summary-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 15px 0;
        border-bottom: 1px solid #e0e0e0;
    }

    .order-summary-item:last-of-type {
        border-bottom: none;
    }
    
    .order-summary-item-details h4 {
        margin: 0 0 5px 0;
        font-size: 1rem;
    }

    .order-summary-item-details p {
        margin: 0;
        font-size: 0.9rem;
        color: #777;
    }

    .order-summary-item-price {
        font-weight: 600;
        color: #333;
    }

    .order-summary-total {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-top: 20px;
        padding-top: 20px;
        border-top: 2px solid #e0e0e0;
        font-size: 1.2rem;
        font-weight: 700;
    }

    .btn-place-order {
        width: 100%;
        padding: 15px;
        background-color: var(--secondary-color);
        color: white;
        border: none;
        border-radius: 6px;
        font-size: 1.1rem;
        font-weight: 600;
        cursor: pointer;
        transition: background-color 0.2s;
        margin-top: 25px;
    }
    .btn-place-order:hover {
        background-color: #3a5342;
    }

    .empty-checkout-message {
        text-align: center;
        padding: 40px;
        color: #777;
    }

    /* Responsive untuk Mobile */
    @media (max-width: 768px) {
        .checkout-wrapper {
            grid-template-columns: 1fr;
            margin: 0 10px;
        }
    }
</style>

<main class="checkout-page-container">
    <div class="checkout-wrapper">
        <!-- ========== SEKSI FORM PENGIRIMAN ========== -->
        <section class="checkout-section">
            <h2 class="section-title">Informasi Pengiriman</h2>
            <form id="checkout-form">
                <div class="form-group">
                    <label for="name">Nama Lengkap *</label>
                    <input type="text" id="name" name="name" required>
                </div>
                <div class="form-group">
                    <label for="phone">Nomor Telepon / WhatsApp *</label>
                    <input type="tel" id="phone" name="phone" required>
                </div>
                <div class="form-group">
                    <label for="address">Alamat Lengkap *</label>
                    <textarea id="address" name="address" rows="4" required></textarea>
                </div>
                <div class="form-group">
                    <label for="notes">Catatan untuk Pesanan (Opsional)</label>
                    <textarea id="notes" name="notes" rows="3"></textarea>
                </div>
                <button type="submit" class="btn-place-order">Kirim Pesanan via WhatsApp</button>
            </form>
        </section>

        <!-- ========== SEKSI RINGKASAN PESANAN ========== -->
        <section class="order-summary-section">
            <h2 class="section-title">Ringkasan Pesanan</h2>
            <div id="order-summary-container">
                <!-- Konten ringkasan akan dimuat oleh JavaScript -->
            </div>
        </section>
    </div>
</main>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const orderSummaryContainer = document.getElementById('order-summary-container');
    const checkoutForm = document.getElementById('checkout-form');
    
    // Ambil data keranjang dari localStorage
    let cart = JSON.parse(localStorage.getItem('cart')) || [];

    // Fungsi untuk mengecek status produk
    const checkProductStatus = async () => {
        const outOfStockItems = [];
        for (const item of cart) {
            try {
                const response = await fetch(`check_product_status.php?id=${item.id}`);
                const data = await response.json();
                if (data.is_out_of_stock) {
                    outOfStockItems.push(item.name);
                }
            } catch (error) {
                console.error('Error checking product status:', error);
            }
        }
        return outOfStockItems;
    };

    // Fungsi untuk mengecek apakah ada produk bertingkat
    const checkTieredPricingProducts = async () => {
        const tieredPricingItems = [];
        for (const item of cart) {
            try {
                const response = await fetch(`check_product_status.php?id=${item.id}`);
                const data = await response.json();
                if (data.has_tiered_pricing) {
                    tieredPricingItems.push(item.name);
                }
            } catch (error) {
                console.error('Error checking tiered pricing:', error);
            }
        }
        return tieredPricingItems;
    };

    // Fungsi untuk menampilkan ringkasan pesanan
    const renderOrderSummary = async () => {
        if (cart.length === 0) {
            orderSummaryContainer.innerHTML = '<div class="empty-checkout-message">Keranjang Anda kosong. <a href="index.php">Kembali berbelanja</a>.</div>';
            // Non-aktifkan tombol submit jika keranjang kosong
            document.querySelector('.btn-place-order').disabled = true;
            return;
        }

        // Cek produk habis
        const outOfStockItems = await checkProductStatus();
        if (outOfStockItems.length > 0) {
            orderSummaryContainer.innerHTML = `
                <div style="background-color: #f8d7da; color: #721c24; padding: 15px; border-radius: 5px; margin-bottom: 20px;">
                    <strong>Produk habis:</strong> ${outOfStockItems.join(', ')}<br>
                    Silakan hapus produk habis dari keranjang atau pilih produk lain.
                </div>
            `;
            document.querySelector('.btn-place-order').disabled = true;
            return;
        }

        // Cek produk bertingkat
        const tieredPricingItems = await checkTieredPricingProducts();
        if (tieredPricingItems.length > 0) {
            orderSummaryContainer.innerHTML = `
                <div style="background-color: #fff3cd; color: #856404; padding: 15px; border-radius: 5px; margin-bottom: 20px; border: 1px solid #ffeaa7;">
                    <strong><i class="fas fa-info-circle"></i> Produk Bertingkat Ditemukan!</strong><br><br>
                    Produk berikut: <strong>${tieredPricingItems.join(', ')}</strong> merupakan produk bertingkat (uang/buket dengan pilihan jumlah lembar).<br><br>
                    <strong>Produk bertingkat tidak dapat dipesan melalui halaman ini.</strong> Silakan pesan langsung melalui halaman detail produk untuk memilih jumlah lembar yang diinginkan.<br><br>
                    <a href="index.php" style="color: #856404; text-decoration: underline; font-weight: bold;">← Kembali ke halaman utama</a>
                </div>
            `;
            document.querySelector('.btn-place-order').disabled = true;
            return;
        }

        let grandTotal = 0;
        const summaryItemsHTML = cart.map(item => {
            const itemTotal = item.price * item.quantity;
            grandTotal += itemTotal;
            return `
                <div class="order-summary-item">
                    <div class="order-summary-item-details">
                        <h4>${item.name}</h4>
                        <p>${item.quantity} pcs x Rp ${item.price.toLocaleString('id-ID')}</p>
                    </div>
                    <div class="order-summary-item-price">
                        Rp ${itemTotal.toLocaleString('id-ID')}
                    </div>
                </div>
            `;
        }).join('');

        orderSummaryContainer.innerHTML = summaryItemsHTML + `
            <div class="order-summary-total">
                <span>Total Pembayaran:</span>
                <span>Rp ${grandTotal.toLocaleString('id-ID')}</span>
            </div>
        `;
    };

    // Handle submit form
    checkoutForm.addEventListener('submit', async function(e) {
        e.preventDefault();

        // Cek lagi status produk sebelum checkout
        const outOfStockItems = await checkProductStatus();
        if (outOfStockItems.length > 0) {
            alert(`Produk habis: ${outOfStockItems.join(', ')}. Silakan hapus dari keranjang.`);
            return;
        }

        // Cek produk bertingkat sebelum checkout
        const tieredPricingItems = await checkTieredPricingProducts();
        if (tieredPricingItems.length > 0) {
            alert(`Produk bertingkat tidak dapat dipesan melalui halaman ini: ${tieredPricingItems.join(', ')}.\n\nSilakan pesan langsung melalui halaman detail produk.`);
            return;
        }

        // Ambil data dari form
        const name = document.getElementById('name').value;
        const phone = document.getElementById('phone').value;
        const address = document.getElementById('address').value;
        const notes = document.getElementById('notes').value;

        // Simpan order ke database
        const formData = new FormData();
        formData.append('name', name);
        formData.append('phone', phone);
        formData.append('address', address);
        formData.append('notes', notes);
        formData.append('cart', JSON.stringify(cart));

        try {
            const response = await fetch('process_order.php', {
                method: 'POST',
                body: formData
            });
            const result = await response.json();

            if (!result.success) {
                alert('Error: ' + result.message);
                return;
            }

            const orderId = result.order_id;

            // 1. GANTI DENGAN NOMOR WHATSAPP ANDA
            const businessPhoneNumber = '6282233278088'; 

            // 3. Bangun pesan WhatsApp dengan order ID
            let message = `Halo QUEENCRAFT.ID, saya ingin mengkonfirmasi pesanan.\n\n`;
            message += `*ID PESANAN: #${orderId}*\n\n`;
            message += `*DATA PEMESAN:*\n`;
            message += `Nama: ${name}\n`;
            message += `No. HP/WA: ${phone}\n`;
            message += `Alamat: ${address}\n`;
            if (notes) {
                message += `Catatan: ${notes}\n`;
            }
            message += `\n*DETAIL PESANAN:*\n`;
            
            let grandTotal = 0;
            cart.forEach(item => {
                const itemTotal = item.price * item.quantity;
                grandTotal += itemTotal;
                message += `• ${item.name} (${item.quantity} pcs) x Rp ${item.price.toLocaleString('id-ID')}\n`;
            });

            message += `\n-------------------------\n`;
            message += `*TOTAL PEMBAYARAN: Rp ${grandTotal.toLocaleString('id-ID')}*\n\n`;
            message += `Mohon konfirmasi pesanan ini. Terima kasih.`;

            // 4. Encode pesan dan buka URL WhatsApp
            const encodedMessage = encodeURIComponent(message);
            const whatsappUrl = `https://wa.me/${businessPhoneNumber}?text=${encodedMessage}`;
            window.open(whatsappUrl, '_blank');

            // Kosongkan keranjang setelah pesanan terkirim
            localStorage.removeItem('cart');
            updateHeaderIcons();
            alert('Pesanan berhasil dikirim! Keranjang telah dikosongkan.');
            window.location.href = 'index.php'; // Redirect ke beranda
        } catch (error) {
            console.error('Error processing order:', error);
            alert('Terjadi kesalahan saat memproses pesanan.');
        }
    });

    // Tampilkan ringkasan saat halaman dimuat
    renderOrderSummary();
});
</script>

<?php include 'includes/footer.php'; ?>