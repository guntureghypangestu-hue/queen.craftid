 <?php
// File ini menangani permintaan AJAX untuk pagination

// Cek apakah ada parameter 'url' yang dikirim
if (isset($_POST['url'])) {

    require_once 'includes/db.php';
    require_once 'includes/functions.php';

    // --- PERBAIKAN: Cara parsing URL yang lebih andal ---
    $query_string = parse_url($_POST['url'], PHP_URL_QUERY);
    parse_str($query_string, $params);

    // --- KONFIGURASI PAGINATION (sama seperti di index.php) ---
    define('PRODUCTS_PER_PAGE', 10);
    $current_page = isset($params['page']) ? (int)$params['page'] : 1;
    $offset = ($current_page - 1) * PRODUCTS_PER_PAGE;
    $search_term = isset($params['search']) ? trim($params['search']) : '';

    // --- Logika Query (sama seperti di index.php) ---
    $products = [];
    $total_products = 0;
    $total_pages = 1;

    if (!empty($search_term)) {
        $param_search = '%' . $search_term . '%';
        // PERUBAHAN: Query untuk menghitung total produk yang aktif atau habis dan cocok dengan pencarian
        $count_sql = "
            SELECT COUNT(*) 
            FROM products p 
            JOIN categories c ON p.category_id = c.id 
            WHERE (p.name LIKE :search_term 
               OR p.description LIKE :search_term 
               OR c.name LIKE :search_term) 
            AND p.status IN ('active', 'out_of_stock')
        ";
        $stmt_count = $pdo->prepare($count_sql);
        $stmt_count->bindParam(':search_term', $param_search);
        $stmt_count->execute();
        $total_products = $stmt_count->fetchColumn();

        // PERUBAHAN: Query untuk mengambil produk yang aktif atau habis dan cocok dengan pencarian
        $sql_products = "
            SELECT p.*, c.name as category_name 
            FROM products p 
            JOIN categories c ON p.category_id = c.id 
            WHERE (p.name LIKE :search_term 
               OR p.description LIKE :search_term 
               OR c.name LIKE :search_term) 
            AND p.status IN ('active', 'out_of_stock')
            ORDER BY p.created_at DESC
            LIMIT :limit OFFSET :offset
        ";
        $stmt_products = $pdo->prepare($sql_products);
        $stmt_products->bindParam(':search_term', $param_search);
        $stmt_products->bindValue(':limit', PRODUCTS_PER_PAGE, PDO::PARAM_INT);
        $stmt_products->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt_products->execute();
        $products = $stmt_products->fetchAll(PDO::FETCH_ASSOC);
    } else {
        // PERUBAHAN: Query untuk menghitung total semua produk yang aktif atau habis
       // BENAR
 $stmt_count = $pdo->query("SELECT COUNT(*) FROM products WHERE status IN ('active', 'out_of_stock')");
        $total_products = $stmt_count->fetchColumn();

        // PERUBAHAN: Query untuk mengambil semua produk yang aktif atau habis
        $sql_products = "
            SELECT p.*, c.name as category_name 
            FROM products p 
            JOIN categories c ON p.category_id = c.id 
            WHERE p.status IN ('active', 'out_of_stock')
            ORDER BY p.created_at DESC
            LIMIT :limit OFFSET :offset
        ";
        $stmt_products = $pdo->prepare($sql_products);
        $stmt_products->bindValue(':limit', PRODUCTS_PER_PAGE, PDO::PARAM_INT);
        $stmt_products->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt_products->execute();
        $products = $stmt_products->fetchAll(PDO::FETCH_ASSOC);
    }
    
    $total_pages = ceil($total_products / PRODUCTS_PER_PAGE);

    // --- Bangun HTML untuk Produk dan Pagination ---
    ob_start(); // Mulai output buffering

    // Grid Produk (DIBUNGKUS DENGAN DIV PRODUCT-GRID)
    echo '<div id="product-grid-container" class="product-grid">';
    if (empty($products)) {
        echo '<p style="width: 100%; text-align: center; grid-column: 1 / -1; font-size: 1.1rem; color: #777;">Tidak ada produk yang ditemukan untuk halaman ini.</p>';
    } else {
        foreach ($products as $product) {
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
        }
    }
    echo '</div>'; // TUTUP WADAH PRODUCT-GRID

    // Pagination HTML
    if ($total_pages > 1) {
        ?>
        <div class="pagination">
            <?php if ($current_page > 1): ?>
                <?php $prev_page = $current_page - 1; $prev_link = !empty($search_term) ? "?search=$search_term&page=$prev_page" : "?page=$prev_page"; ?>
                <a href="<?php echo $prev_link; ?>" class="pagination-link"><i class="fas fa-chevron-left"></i> Previous</a>
            <?php else: ?>
                <span class="pagination-link disabled"><i class="fas fa-chevron-left"></i> Previous</span>
            <?php endif; ?>

            <?php for ($page = 1; $page <= $total_pages; $page++): ?>
                <?php $page_link = !empty($search_term) ? "?search=$search_term&page=$page" : "?page=$page"; ?>
                <a href="<?php echo $page_link; ?>" class="pagination-link <?php echo ($page == $current_page) ? 'active' : ''; ?>"><?php echo $page; ?></a>
            <?php endfor; ?>

            <?php if ($current_page < $total_pages): ?>
                <?php $next_page = $current_page + 1; $next_link = !empty($search_term) ? "?search=$search_term&page=$next_page" : "?page=$next_page"; ?>
                <a href="<?php echo $next_link; ?>" class="pagination-link">Next <i class="fas fa-chevron-right"></i></a>
            <?php else: ?>
                <span class="pagination-link disabled">Next <i class="fas fa-chevron-right"></i></span>
            <?php endif; ?>
        </div>
        <?php
    }

    $html_output = ob_get_clean(); // Ambil semua HTML yang di-generate

    // Kirim HTML kembali ke JavaScript dalam format JSON
    echo json_encode(['html' => $html_output]);

} else {
    header('HTTP/1.1 400 Bad Request');
    exit('Permintaan tidak valid.');
}
?> 