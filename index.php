<?php
// Memanggil file-file yang diperlukan
require_once 'includes/db.php';
require_once 'includes/functions.php';

// --- KONFIGURASI PAGINATION ---
define('PRODUCTS_PER_PAGE', 10); // Jumlah produk per halaman
define('CATEGORIES_PER_PAGE', 6); // Jumlah kategori per halaman

// Inisialisasi variabel
 $products = [];
 $search_term = '';
 $total_products = 0;
 $total_pages = 1;
 $current_page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
 $offset = ($current_page - 1) * PRODUCTS_PER_PAGE;

// Pagination untuk kategori
 $current_cat_page = isset($_GET['cat_page']) ? (int)$_GET['cat_page'] : 1;
 $cat_offset = ($current_cat_page - 1) * CATEGORIES_PER_PAGE;
 $total_categories = 0;
 $total_cat_pages = 1;

// Cek apakah ada parameter pencarian
if (isset($_GET['search']) && !empty(trim($_GET['search']))) {
    $search_term = trim($_GET['search']);
    $param_search = '%' . $search_term . '%';

    // Query untuk MENGHITUNG total produk yang cocok dengan pencarian
    // PERUBAHAN: Ditambahkan WHERE untuk status produk (active atau out_of_stock)
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

    // Query untuk MENGAMBIL produk dengan LIMIT dan OFFSET
    // PERUBAHAN: Ditambahkan WHERE untuk status produk (active atau out_of_stock)
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
    // Query untuk MENGHITUNG total semua produk yang aktif
    // PERUBAHAN: Ditambahkan WHERE untuk status produk (active atau out_of_stock)
    $stmt_count = $pdo->query("SELECT COUNT(*) FROM products WHERE status IN ('active', 'out_of_stock')");
    $total_products = $stmt_count->fetchColumn();

    // Query untuk MENGAMBIL semua produk yang aktif dengan LIMIT dan OFFSET
    // PERUBAHAN: Ditambahkan WHERE untuk status produk (active atau out_of_stock)
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

// Hitung total halaman
 $total_pages = ceil($total_products / PRODUCTS_PER_PAGE);

// Query untuk menghitung total kategori
$stmt_count_cat = $pdo->query("SELECT COUNT(*) FROM categories");
$total_categories = $stmt_count_cat->fetchColumn();

// Hitung total halaman kategori
$total_cat_pages = ceil($total_categories / CATEGORIES_PER_PAGE);

// Query untuk mengambil data kategori dengan pagination
$sql_categories = "SELECT * FROM categories ORDER BY name ASC LIMIT :limit OFFSET :offset";
$stmt_categories = $pdo->prepare($sql_categories);
$stmt_categories->bindValue(':limit', CATEGORIES_PER_PAGE, PDO::PARAM_INT);
$stmt_categories->bindValue(':offset', $cat_offset, PDO::PARAM_INT);
$stmt_categories->execute();
$categories = $stmt_categories->fetchAll(PDO::FETCH_ASSOC);
?>

<?php include 'includes/header.php'; ?>

<main>
   <!-- Hero Section dengan Background Slideshow -->
<section id="beranda" class="hero-slideshow">
    
    <!-- Slide 1 (Aktif saat pertama kali dimuat) -->
    <div class="hero-slide active" style="background-image: url('assets/images/slideshow/slide1.jpg');"></div>
    
    <!-- Slide 2 -->
    <div class="hero-slide" style="background-image: url('assets/images/slideshow/slide2.jpg');"></div>
    
    <!-- Slide 3 -->
    <div class="hero-slide" style="background-image: url('assets/images/slideshow/slide3.jpg');"></div>

     <!-- Slide 4 -->
    <div class="hero-slide" style="background-image: url('assets/images/slideshow/slide3.jpg');"></div>

     <!-- Slide 5 -->
    <div class="hero-slide" style="background-image: url('assets/images/slideshow/slide3.jpg');"></div>

    <!-- Konten Statis yang Tidak Berubah -->
    <div class="hero-content">
        <h1 class="animate-on-load delay-1">Ungkapkan Perasaan dengan Keindahan Buket</h1>
        <p class="animate-on-load delay-2">Pilihan buket bunga, uang, snack, dan balon terindah untuk setiap momen spesial Anda.</p>
        <a href="/buketqueen/index.php#kategori" class="cta-button animate-on-load delay-3">Jelajahi Koleksi Kami</a>
    </div>
</section>
    <!-- Kategori Section -->
<section id="kategori" class="section-padding">
    <div class="container">
        <div class="section-header animate-on-load delay-4">
            <h2>Kategori Pilihan</h2>
            <p>Temukan buket sempurna untuk setiap kesempatan.</p>
        </div>
        <div class="category-grid">
             <?php 
             $counter = 0;
             foreach ($categories as $category): 
             $counter++;
             ?>
                 <div class="category-card animate-on-load delay-<?php echo $counter + 4; ?>">
                      <!-- PERUBAHAN ADA DI BARIS INI -->
                      <img src="/buketqueen/assets/images/categories/<?php echo escape($category['image_url']); ?>" alt="<?php echo escape($category['name']); ?>">
                     <h3><?php echo escape($category['name']); ?></h3>
                     <a href="kategori.php?slug=<?php echo $category['slug']; ?>" class="btn-secondary">Lihat Koleksi</a>
        </div>
    <?php endforeach; ?>
</div>

<!-- ==================== KONTROL PAGINATION KATEGORI ==================== -->
<?php if ($total_cat_pages > 1): ?>
<div class="pagination">
    <!-- Tombol Previous -->
    <?php if ($current_cat_page > 1): ?>
        <?php $prev_cat_page = $current_cat_page - 1; ?>
        <a href="?cat_page=<?php echo $prev_cat_page; ?>" class="pagination-link">
            <i class="fas fa-chevron-left"></i> Previous
        </a>
    <?php else: ?>
        <span class="pagination-link disabled"><i class="fas fa-chevron-left"></i> Previous</span>
    <?php endif; ?>

    <!-- Nomor Halaman -->
    <?php for ($page = 1; $page <= $total_cat_pages; $page++): ?>
        <?php $is_active = ($page == $current_cat_page); ?>
        <a href="?cat_page=<?php echo $page; ?>" class="pagination-link <?php echo $is_active ? 'active' : ''; ?>">
            <?php echo $page; ?>
        </a>
    <?php endfor; ?>

    <!-- Tombol Next -->
    <?php if ($current_cat_page < $total_cat_pages): ?>
        <?php $next_cat_page = $current_cat_page + 1; ?>
        <a href="?cat_page=<?php echo $next_cat_page; ?>" class="pagination-link">
            Next <i class="fas fa-chevron-right"></i>
        </a>
    <?php else: ?>
        <span class="pagination-link disabled">Next <i class="fas fa-chevron-right"></i></span>
    <?php endif; ?>
</div>
<?php endif; ?>
<!-- ======================================================= -->

    </div>
</section>

    <!-- Produk Unggulan Section -->
    <section id="produk-unggulan" class="section-padding bg-light">
        <div class="container">
            <div class="section-header animate-on-load delay-<?php echo $counter + 5; ?>">
                <?php if (!empty($search_term)): ?>
                    <h2>Hasil Pencarian untuk: "<?php echo htmlspecialchars($search_term); ?>"</h2>
                    <p>Menampilkan <?php echo count($products); ?> produk dari total <?php echo $total_products; ?> produk yang ditemukan.</p>
                <?php else: ?>
                    <h2>Semua Produk</h2>
                    <p>Temukan berbagai pilihan buket dari kami.</p>
                <?php endif; ?>
            </div>

            <!-- Form Pencarian -->
            <?php if (empty($search_term)): ?>
            <form action="index.php" method="GET" class="page-search-form">
                <div style="position: relative; flex: 1;">
                    <input type="text" name="search" id="live-search-input" placeholder="Cari produk favorit Anda..." autocomplete="off">
                    <button type="button" id="clear-search-btn" title="Hapus pencarian">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <button type="submit"><i class="fas fa-search"></i></button>
            </form>
            <?php endif; ?>

            <div id="main-content-area">
            <div id="product-grid-container" class="product-grid">
                <?php if (empty($products)): ?>
                    <p style="width: 100%; text-align: center; grid-column: 1 / -1; font-size: 1.1rem; color: #777;">
                        <?php if (!empty($search_term)): ?>
                            Tidak ada produk yang ditemukan untuk pencarian "<strong><?php echo htmlspecialchars($search_term); ?></strong>". Coba kata kunci lain.
                        <?php else: ?>
                            Belum ada produk. Silakan tambahkan produk melalui <a href="/buketqueen/admin/">panel admin</a>.
                        <?php endif; ?>
                    </p>
                <?php else: ?>
                    <?php 
                    $product_counter = 0;
                    foreach ($products as $product): 
                    $product_counter++;
                    ?>
                        <div class="product-card animate-on-load delay-<?php echo $product_counter + 6; ?>" data-product-id="<?php echo $product['id']; ?>">
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
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>

           
<!-- ==================== KONTROL PAGINATION ==================== -->
<?php if ($total_pages > 1): ?>
<div class="pagination">
    <!-- Tombol Previous -->
    <?php if ($current_page > 1): ?>
        <?php 
        $prev_page = $current_page - 1;
        $prev_link = !empty($search_term) ? "?search=$search_term&page=$prev_page" : "?page=$prev_page";
        ?>
        <a href="<?php echo $prev_link; ?>" class="pagination-link">
            <i class="fas fa-chevron-left"></i> Previous
        </a>
    <?php else: ?>
        <span class="pagination-link disabled"><i class="fas fa-chevron-left"></i> Previous</span>
    <?php endif; ?>

    <!-- Nomor Halaman -->
    <?php for ($page = 1; $page <= $total_pages; $page++): ?>
        <?php 
        $page_link = !empty($search_term) ? "?search=$search_term&page=$page" : "?page=$page";
        $is_active = ($page == $current_page);
        ?>
        <a href="<?php echo $page_link; ?>" class="pagination-link <?php echo $is_active ? 'active' : ''; ?>">
            <?php echo $page; ?>
        </a>
    <?php endfor; ?>

    <!-- Tombol Next -->
    <?php if ($current_page < $total_pages): ?>
        <?php 
        $next_page = $current_page + 1;
        $next_link = !empty($search_term) ? "?search=$search_term&page=$next_page" : "?page=$next_page";
        ?>
        <a href="<?php echo $next_link; ?>" class="pagination-link">
            Next <i class="fas fa-chevron-right"></i>
        </a>
    <?php else: ?>
        <span class="pagination-link disabled">Next <i class="fas fa-chevron-right"></i></span>
    <?php endif; ?>
</div>
<?php endif; ?>
<!-- ======================================================= -->
        </div>
    </section>
</main>

<!-- ==================== JAVASCRIPT UNTUK LIVE SEARCH & AJAX PAGINATION (VERSI PERBAIKAN) ==================== -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // --- NONAKTIFKAN PERILAKU SCROLL OTOMATIS BROWSER ---
    if ('scrollRestoration' in history) {
        history.scrollRestoration = 'manual';
    }

    const searchInput = document.getElementById('live-search-input');
    const clearBtn = document.getElementById('clear-search-btn');
    const mainContentArea = document.getElementById('main-content-area');

   
    // --- Fungsi untuk memuat konten (produk & pagination) via AJAX ---
    function loadContent(url) {
        const productGridContainer = document.getElementById('product-grid-container');
        const paginationContainer = document.querySelector('.pagination');

        if(productGridContainer) productGridContainer.style.opacity = '0.5';
        if(paginationContainer) paginationContainer.style.opacity = '0.5';

        const formData = new FormData();
        formData.append('url', url);

        fetch('pagination_handler.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            const tempDiv = document.createElement('div');
            tempDiv.innerHTML = data.html;

            const newGrid = tempDiv.querySelector('#product-grid-container');
            const newPagination = tempDiv.querySelector('.pagination');

            if (newGrid && productGridContainer) {
                productGridContainer.parentNode.replaceChild(newGrid, productGridContainer);
            }
            if (newPagination && paginationContainer) {
                paginationContainer.parentNode.replaceChild(newPagination, paginationContainer);
            }
            
            updateProductCardUI();
            
            // *** PERUBAHAN 1: GULIR KE #PRODUK-UNGGULAN ***
            setTimeout(() => {
                document.getElementById('produk-unggulan').scrollIntoView({ behavior: 'smooth', block: 'start' });
            }, 100);
        })
        .catch(error => {
            console.error('Error during content load:', error);
            if(productGridContainer) productGridContainer.innerHTML = '<p style="width: 100%; text-align: center; color: red;">Terjadi kesalahan. Silakan coba lagi. (Cek konsol untuk detail)</p>';
            if(productGridContainer) productGridContainer.style.opacity = '1';
        })
        .finally(() => {
            if(productGridContainer) productGridContainer.style.opacity = '1';
            if(paginationContainer) paginationContainer.style.opacity = '1';
        });
    }

    // --- Event Listener untuk PAGINATION (LEBIH SPESIFIK) ---
    mainContentArea.addEventListener('click', function(e) {
        // Hanya berjalan jika yang diklik adalah link pagination
        if (e.target.closest('.pagination-link')) {
            const paginationLink = e.target.closest('.pagination-link');
            if (!paginationLink.classList.contains('disabled')) {
                e.preventDefault();
                const url = paginationLink.href;
                loadContent(url);
                history.pushState(null, null, url);
            }
        }
    });

    // --- Event Listener untuk LIVE SEARCH (LEBIH SPESIFIK) ---
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            const searchTerm = this.value.trim();
            if(clearBtn) clearBtn.style.display = searchTerm ? 'block' : 'none';
            
            const searchUrl = 'index.php?search=' + encodeURIComponent(searchTerm);
            loadContent(searchUrl);
            history.pushState(null, null, searchUrl);
        });
    }
    
    // *** PERUBAHAN 2: PERBAIKI TOMBOL HAPUS PENCARIAN ***
    if (clearBtn) {
        clearBtn.addEventListener('click', function() {
            // Kosongkan input search dan sembunyikan tombol clear
            searchInput.value = '';
            clearBtn.style.display = 'none';

            // Muat ulang konten untuk menampilkan semua produk
            loadContent('index.php'); 
            
            // Perbarui URL di browser tanpa reload
            history.pushState(null, null, 'index.php');
        });
    }

    // --- Handle Browser Back/Forward Button (LEBIH AMAN) ---
    window.addEventListener('popstate', function(e) {
        const currentUrl = window.location.pathname + window.location.search;
        // Hanya jalankan loadContent jika URL mengandung 'page' atau 'search'
        if (currentUrl.includes('page=') || currentUrl.includes('search=')) {
            loadContent(currentUrl);
        }
    });
});
</script>
<!-- ======================================================================================= -->

<!-- ==================== JAVASCRIPT UNTUK CROSS-FADE SLIDESHOW ==================== -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const slides = document.querySelectorAll('.hero-slideshow .hero-slide');
    if (slides.length === 0) {
        return;
    }

    let currentSlide = 0;

    function showNextSlide() {
        if (!slides || slides.length === 0 || !slides[currentSlide]) {
            return;
        }

        slides[currentSlide].classList.remove('active');
        currentSlide = (currentSlide + 1) % slides.length;
        slides[currentSlide].classList.add('active');
    }

    if (slides.length > 1) {
        setInterval(showNextSlide, 5000);
    }
});
</script>
<!-- ======================================================================= -->

<?php include 'includes/footer.php'; ?> 