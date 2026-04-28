<?php
require_once 'includes/db.php';
require_once 'includes/functions.php';

// Cek apakah ID produk ada di URL
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header('Location: index.php');
    exit;
}

 $product_id = $_GET['id'];

// Ambil data produk spesifik dari database berdasarkan ID
 $stmt = $pdo->prepare("
    SELECT p.*, c.name as category_name 
    FROM products p 
    JOIN categories c ON p.category_id = c.id 
    WHERE p.id = ?
");
 $stmt->execute([$product_id]);
 $product = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$product) {
    echo "<h1 style='text-align:center; margin-top: 50px;'>Produk tidak ditemukan.</h1>";
    echo "<p style='text-align:center;'><a href='index.php'>Kembali ke Beranda</a></p>";
    exit;
}

// Siapkan data harga bertingkat untuk JavaScript
 $tiered_pricing_data = [];
if ($product['has_tiered_pricing']) {
    // PERUBAHAN: Loop dari 1 sampai 6
    for ($i = 1; $i <= 6; $i++) {
        if (!is_null($product["price_{$i}"])) {
            $tiered_pricing_data[] = [
                'min' => $product["sheet_{$i}_min"],
                'max' => $product["sheet_{$i}_max"],
                'price' => $product["price_{$i}"]
            ];
        }
    }
}
?>

<?php include 'includes/header.php'; ?>

<style>
    /* Tambahan CSS untuk notifikasi */
    #order-status {
        margin-top: 15px;
        padding: 10px;
        border-radius: 8px;
        text-align: center;
        font-weight: 500;
        display: none; /* Sembunyikan secara default */
    }
    #order-status.success {
        background-color: #d4edda;
        color: #155724;
        border: 1px solid #c3e6cb;
    }
    #order-status.error {
        background-color: #f8d7da;
        color: #721c24;
        border: 1px solid #f5c6cb;
    }

    /* ===============================
   LAYOUT DETAIL PRODUK
================================ */
.product-detail-container {
    display: grid;
    grid-template-columns: 1.1fr 1fr;
    gap: 50px;
    align-items: flex-start;
}

/* ===============================
   GAMBAR PRODUK
================================ */
.product-detail-image img {
    width: 100%;
    border-radius: 14px;
    object-fit: cover;
    box-shadow: 0 10px 25px rgba(0,0,0,0.08);
    transition: 0.3s;
}

.product-detail-image img:hover {
    transform: scale(1.02);
}

/* ===============================
   INFO PRODUK
================================ */
.product-detail-info h1 {
    font-size: 2.2rem;
    margin-bottom: 10px;
}

.product-detail-info .price {
    font-size: 1.8rem;
    font-weight: 700;
    color: var(--secondary-color);
    margin: 10px 0 20px;
}

.product-detail-info .description {
    line-height: 1.8;
    color: #555;
    margin-bottom: 25px;
}

/* ===============================
   FORM PEMESANAN
================================ */
.order-form {
    background: #ffffff;
    border-radius: 14px;
    padding: 25px;
    border: 1px solid #eee;
    box-shadow: 0 6px 18px rgba(0,0,0,0.05);
}

.order-form h3 {
    margin-bottom: 5px;
}

.order-form p {
    font-size: 0.95rem;
    color: #666;
    margin-bottom: 20px;
}

.order-form label {
    font-weight: 600;
    display: block;
    margin-bottom: 6px;
}

.order-form input,
.order-form textarea,
.order-form select {
    width: 100%;
    padding: 12px;
    border-radius: 8px;
    border: 1px solid #ddd;
    margin-bottom: 15px;
    font-size: 0.95rem;
}

.order-form input:focus,
.order-form textarea:focus,
.order-form select:focus {
    outline: none;
    border-color: var(--secondary-color);
    box-shadow: 0 0 0 2px rgba(0,0,0,0.05);
}

.order-form textarea {
    min-height: 100px;
    resize: vertical;
}

/* ===============================
   TOMBOL WHATSAPP
================================ */
.whatsapp-order-btn {
    display: block;
    width: 100%;
    background: #25D366;
    color: white;
    text-align: center;
    padding: 14px;
    border-radius: 8px;
    font-weight: 600;
    text-decoration: none;
    transition: 0.2s;
}

.whatsapp-order-btn:hover {
    background: #1ebe5d;
}

/* ===============================
   RESPONSIVE
================================ */
@media (max-width: 768px) {
    .product-detail-container {
        grid-template-columns: 1fr;
        gap: 25px;
    }

    .product-detail-info h1 {
        font-size: 1.6rem;
    }

    .product-detail-info .price {
        font-size: 1.4rem;
    }

    .order-form {
        padding: 20px;
    }
}

</style>

<main class="section-padding">
    <div class="container">
        <a href="javascript:history.back()" class="btn-secondary" style="margin-bottom: 20px;"><i class="fas fa-arrow-left"></i> Kembali</a>
        
        <div class="product-detail-container">
            <div class="product-detail-image">
                <img src="assets/images/uploads/<?php echo escape($product['image_url']); ?>" alt="<?php echo escape($product['name']); ?>">
            </div>
            
            <div class="product-detail-info">
                <h1><?php echo escape($product['name']); ?></h1>
                
                <!-- Kontainer Harga Normal -->
                <div id="normal-price-container" class="price">Rp <?php echo number_format($product['price'], 0, ',', '.'); ?></div>

                <!-- Kontainer Harga Bertingkat (Dinamis) -->
                <div id="tiered-pricing-container" style="display: none;">
                    <div class="price" id="selected-price-display"></div>
                </div>

                <p class="description"><?php echo nl2br(escape($product['description'])); ?></p>
                
                <?php if ($product['stock'] == 0 || $product['status'] === 'out_of_stock'): ?>
                    <div style="background-color: #f8d7da; color: #721c24; padding: 10px; border-radius: 5px; margin-bottom: 20px; font-weight: bold;">
                        <i class="fas fa-exclamation-triangle"></i> Produk ini sedang habis.
                    </div>
                <?php endif; ?>
                
                <div class="order-form">
                    <h3>Pesan Sekarang</h3>
                    <p>Isi form di bawah ini, dan kami akan menghubungi Anda via WhatsApp untuk konfirmasi.</p>
                    <form id="orderForm">
                        <label for="cust_name">Nama Lengkap</label>
                        <input type="text" id="cust_name" required>
                        
                       <label for="cust_phone">Nomor WhatsApp</label>
                        <input 
                            type="tel" 
                            id="cust_phone" 
                            name="cust_phone" 
                            placeholder="Contoh: 08123456789" 
                            required>
                        <small style="color: var(--light-text); font-size: 0.9rem; display: block; margin-top: 5px;">Mohon isi hanya dengan angka.</small>
                        
                        <!-- Dropdown Pilihan Lembar (Dinamis) -->
                        <div id="sheet-selection-container" style="display: none;">
                            <label for="sheet-selection">Pilih Jumlah Lembar</label>
                            <select id="sheet-selection" name="sheet-selection">
                                <!-- Opsi akan di-generate oleh JavaScript -->
                            </select>
                        </div>
                        
                        <label for="cust_address">Alamat Lengkap</label>
                        <textarea id="cust_address" required placeholder="Masukkan alamat lengkap"></textarea>
                        <label for="cust_message">Pesan (Ucapan, alamat, dll.)</label>
                        <textarea id="cust_message" placeholder="Contoh: Untuk ulang tahun ya, dikirim ke alamat Jl. Mawar No. 12. Terima kasih."></textarea>
                        
                        <?php if ($product['stock'] == 0 || $product['status'] === 'out_of_stock'): ?>
                            <button type="button" class="whatsapp-order-btn" disabled style="background-color: #ccc; cursor: not-allowed;">
                                <i class="fab fa-whatsapp"></i> Produk Habis
                            </button>
                        <?php else: ?>
                            <a href="#" id="whatsappBtn" class="whatsapp-order-btn">
                                <i class="fab fa-whatsapp"></i> Pesan via WhatsApp
                            </a>
                        <?php endif; ?>
                    </form>
                    <div id="order-status"></div>
                </div>
            </div>
        </div>
    </div>
</main>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // --- Data dari PHP ---
    const hasTieredPricing = <?php echo $product['has_tiered_pricing']; ?>;
    const tieredPricingData = <?php echo json_encode($tiered_pricing_data); ?>;
    const productName = "<?php echo escape($product['name']); ?>";
    const productCategory = "<?php echo escape($product['category_name']); ?>";
    const productLink = window.location.href;
    const normalPrice = "<?php echo number_format($product['price'], 0, ',', '.'); ?>";

    // --- Elemen DOM ---
    const normalPriceContainer = document.getElementById('normal-price-container');
    const tieredPricingContainer = document.getElementById('tiered-pricing-container');
    const selectedPriceDisplay = document.getElementById('selected-price-display');
    const sheetSelectionContainer = document.getElementById('sheet-selection-container');
    const sheetSelection = document.getElementById('sheet-selection');
    const whatsappBtn = document.getElementById('whatsappBtn');
    const orderStatus = document.getElementById('order-status');

    // --- Logika Harga Bertingkat ---
    if (hasTieredPricing && tieredPricingData.length > 0) {
        // Sembunyikan harga normal, tampilkan harga bertingkat
        normalPriceContainer.style.display = 'none';
        tieredPricingContainer.style.display = 'block';
        sheetSelectionContainer.style.display = 'block';

        // Isi dropdown dengan opsi harga
        tieredPricingData.forEach((tier, index) => {
            const option = document.createElement('option');
            const rangeText = `${tier.min} - ${tier.max} Lembar`;
            option.value = index; // Gunakan index sebagai value
            option.textContent = rangeText;
            option.dataset.price = tier.price; // Simpan harga di data attribute
            option.dataset.range = rangeText;   // Simpan teks range di data attribute (INI PENTING)
            sheetSelection.appendChild(option);
        });

        // Fungsi untuk memperbarui tampilan harga
        function updatePriceDisplay() {
            const selectedOption = sheetSelection.options[sheetSelection.selectedIndex];
            const price = parseFloat(selectedOption.dataset.price);
            selectedPriceDisplay.textContent = `Rp ${price.toLocaleString('id-ID')}`;
        }
        
        // Set harga awal dan tambahkan event listener
        updatePriceDisplay();
        sheetSelection.addEventListener('change', updatePriceDisplay);

    } else {
        // Jika tidak ada harga bertingkat, pastikan hanya harga normal yang tampil
        normalPriceContainer.style.display = 'block';
        tieredPricingContainer.style.display = 'none';
        sheetSelectionContainer.style.display = 'none';
    }

   whatsappBtn.addEventListener('click', async function(e) {
    e.preventDefault();

    const name = document.getElementById('cust_name').value.trim();
    const phone = document.getElementById('cust_phone').value.trim();
    const address = document.getElementById('cust_address').value.trim();
    const message = document.getElementById('cust_message').value.trim();

    if (!name || !phone || !address) {
        alert("Nama, nomor, dan alamat wajib diisi");
        return;
    }

    // Ambil harga
    let price = <?php echo $product['price']; ?>;

    // Variabel untuk menyimpan text jumlah lembar (default kosong)
    let sheetInfoText = "";

    if (hasTieredPricing && tieredPricingData.length > 0) {
        const selectedOption = sheetSelection.options[sheetSelection.selectedIndex];
        price = parseFloat(selectedOption.dataset.price);
        // AMBIL DATA RANGE LEMBAR DISINI
        sheetInfoText = selectedOption.dataset.range; 
    }

    // Format cart
    const cart = [
        {
            id: "<?php echo $product['id']; ?>",
            name: productName,
            price: price,
            quantity: 1,
            image: "<?php echo $product['image_url']; ?>"
        }
    ];

    // Buat FormData
    const formData = new FormData();
    formData.append('name', name);
    formData.append('phone', phone);
    formData.append('address', address);
    formData.append('notes', message);
    formData.append('cart', JSON.stringify(cart));

    try {
        const response = await fetch('process_order.php', {
            method: 'POST',
            body: formData
        });

        const result = await response.json();

        if (!result.success) {
            alert(result.message);
            return;
        }

        const orderId = result.order_id;

        // PEMBUATAN PESAN WHATSAPP YANG SUDAH DIPERBAIKI
        let msg = ` *ORDER BARU - BUKET QUEEN* \n`;
        msg += `━━━━━━━━━━━━━━━━━━━━━━━\n`;

        msg += ` *DETAIL PRODUK*\n`;
        msg += `• Nama Produk : ${productName}\n`;
        msg += `• Harga       : Rp ${price.toLocaleString('id-ID')}\n`;

        // LOGIKA BARU: Cek jika ada info lembar, maka masukkan ke pesan
        if (sheetInfoText !== "") {
            msg += `• Jumlah      : ${sheetInfoText}\n`;
        }

        msg += `\n━━━━━━━━━━━━━━━━━━━━━━━\n`;

        msg += ` *DATA PEMESAN*\n`;
        msg += `• Nama   : ${name}\n`;
        msg += `• No WA  : ${phone}\n`;
        msg += `• Alamat : ${address}\n`;

        msg += `\n━━━━━━━━━━━━━━━━━━━━━━━\n`;

        msg += ` *CATATAN*\n`;
        msg += `${message || '-'}\n`;

        msg += `━━━━━━━━━━━━━━━━━━━━━━━\n`;

        msg += `Mohon konfirmasi pesanan ini. Terima kasih. `;

        const wa = `https://wa.me/6282233278088?text=${encodeURIComponent(msg)}`;
        window.open(wa, '_blank');

        alert("Pesanan berhasil dikirim!");

    } catch (err) {
        console.error(err);
        alert("Terjadi error");
    }
});
});
</script>

<?php include 'includes/footer.php'; ?>