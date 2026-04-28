<?php
// File ini secara khusus menangani permintaan AJAX dari form pencarian

// Cek apakah ada parameter 'search_term' yang dikirim via POST
if (isset($_POST['search_term'])) {

    require_once 'includes/db.php';
    require_once 'includes/functions.php';

    $search_term = trim($_POST['search_term']);
    $html_output = '';

    if (empty($search_term)) {
        // Jika search term kosong, ambil semua produk yang aktif atau habis
        // PERUBAHAN: Ditambahkan WHERE untuk status produk
        $sql = "SELECT p.*, c.name as category_name FROM products p JOIN categories c ON p.category_id = c.id WHERE p.status IN ('active', 'out_of_stock') ORDER BY p.created_at DESC";
        $stmt = $pdo->query($sql);
    } else {
        // Jika ada search term, lakukan pencarian pada produk yang aktif atau habis
        // PERUBAHAN: Ditambahkan WHERE untuk status produk dan bungkus dengan kurung
        $sql = "
            SELECT p.*, c.name as category_name 
            FROM products p 
            JOIN categories c ON p.category_id = c.id 
            WHERE (p.name LIKE :search_term 
               OR p.description LIKE :search_term 
               OR c.name LIKE :search_term) 
            AND p.status IN ('active', 'out_of_stock')
            ORDER BY p.created_at DESC
        ";
        $stmt = $pdo->prepare($sql);
        $param_search = '%' . $search_term . '%';
        $stmt->bindParam(':search_term', $param_search);
        $stmt->execute();
    }

    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Bangun HTML untuk setiap produk yang ditemukan
    if (!empty($products)) {
        foreach ($products as $product) {
            ob_start(); // Mulai output buffering
            ?>
            <div class="product-card" data-product-id="<?php echo $product['id']; ?>">
                <div class="product-image">
                    <img src="assets/images/uploads/<?php echo escape($product['image_url']); ?>" alt="<?php echo escape($product['name']); ?>">
                    <?php if ($product['is_featured']): ?>
                        <span class="product-badge">Unggulan</span>
                    <?php endif; ?>
                    <?php if ($product['stock'] == 0 || $product['status'] === 'out_of_stock'): ?>
                        <span class="product-badge out-of-stock">Habis</span>
                    <?php endif; ?>
                    <button class="wishlist-btn" data-product-id="<?php echo $product['id']; ?>" data-product-name="<?php echo escape($product['name']); ?>" data-product-price="<?php echo $product['price']; ?>" data-product-image="<?php echo escape($product['image_url']); ?>">
                        <i class="far fa-heart"></i>
                    </button>
                    <?php if ($product['stock'] == 0 || $product['status'] === 'out_of_stock'): ?>
                        <button class="cart-btn disabled" title="Produk habis">
                            <i class="fas fa-shopping-bag"></i>
                        </button>
                    <?php else: ?>
                        <button class="cart-btn" data-product-id="<?php echo $product['id']; ?>" data-product-name="<?php echo escape($product['name']); ?>" data-product-price="<?php echo $product['price']; ?>" data-product-image="<?php echo escape($product['image_url']); ?>" data-product-stock="<?php echo $product['stock']; ?>" data-has-tiered-pricing="<?php echo $product['has_tiered_pricing']; ?>">
                            <i class="fas fa-shopping-bag"></i>
                        </button>
                    <?php endif; ?>
                </div>
                <div class="product-info">
                    <h4><?php echo escape($product['name']); ?></h4>
                    <p><?php echo escape($product['category_name']); ?></p>
                    <div class="product-price">Rp <?php echo number_format($product['price'], 0, ',', '.'); ?></div>
                    <a href="detail.php?id=<?php echo $product['id']; ?>" class="btn-primary">Lihat Detail</a>
                </div>
            </div>
            <?php
            $html_output .= ob_get_clean(); // Ambil HTML yang di-generate
        }
    } else {
        // Jika tidak ada produk yang ditemukan
        $html_output = '<p style="width: 100%; text-align: center; grid-column: 1 / -1; font-size: 1.1rem; color: #777;">Tidak ada produk yang ditemukan untuk pencarian "<strong>' . htmlspecialchars($search_term) . '</strong>". Coba kata kunci lain.</p>';
    }

    // Kirim HTML kembali ke JavaScript
    echo $html_output;

} else {
    // Jika bukan permintaan POST, hentikan skrip dengan pesan yang lebih jelas
    header('HTTP/1.1 400 Bad Request');
    exit('Parameter pencarian tidak ditemukan. Pastikan Anda mengakses ini melalui form pencarian.');
}
?>