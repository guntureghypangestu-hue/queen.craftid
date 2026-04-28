<?php
require_once '../includes/auth.php';
require_once '../includes/admin-header.php';
require_once '../../includes/db.php';

// Ambil pesan sukses/hapus
 $message = '';
if (isset($_GET['status']) && $_GET['status'] == 'deleted') {
    $message = 'Produk berhasil dihapus.';
}
if (isset($_GET['status']) && $_GET['status'] == 'updated') {
    $message = 'Produk berhasil diperbarui.';
}
if (isset($_GET['status']) && $_GET['status'] == 'created') {
    $message = 'Produk baru berhasil ditambahkan.';
}

// --- KONFIGURASI PAGINATION ---
define('PRODUCTS_PER_PAGE', 8);
// PERBAIKAN: Menggunakan 'hal_produk' agar tidak bentrok dengan ?page= dari router admin
 $current_page = isset($_GET['hal_produk']) ? (int)$_GET['hal_produk'] : 1;
if ($current_page < 1) $current_page = 1;
 $offset = ($current_page - 1) * PRODUCTS_PER_PAGE;

// --- FITUR SEARCH ---
$search_product_name = isset($_GET['search_name']) ? trim($_GET['search_name']) : '';
$search_category_id = isset($_GET['search_category']) ? (int)$_GET['search_category'] : 0;

// Update status otomatis jika stock 0
 $pdo->exec("UPDATE products SET status = 'out_of_stock' WHERE stock = 0 AND status != 'out_of_stock'");

// Ambil daftar kategori untuk dropdown
$categories_stmt = $pdo->query("SELECT id, name FROM categories ORDER BY name ASC");
$categories = $categories_stmt->fetchAll(PDO::FETCH_ASSOC);

// Hitung total produk dengan filter search
$where_clauses = [];
$where_params = [];

if (!empty($search_product_name)) {
    $where_clauses[] = "p.name LIKE ?";
    $where_params[] = '%' . $search_product_name . '%';
}

if ($search_category_id > 0) {
    $where_clauses[] = "p.category_id = ?";
    $where_params[] = $search_category_id;
}

$where_clause = !empty($where_clauses) ? "WHERE " . implode(" AND ", $where_clauses) : "";

$count_stmt = $pdo->prepare("SELECT COUNT(*) FROM products p $where_clause");
$count_stmt->execute($where_params);
$total_products = (int) $count_stmt->fetchColumn();
$total_pages = ceil($total_products / PRODUCTS_PER_PAGE);

// Validasi halaman
if ($current_page > $total_pages && $total_pages > 0) {
    $current_page = $total_pages;
}

// PERBAIKAN: Membuat URL aman agar tidak menghapus parameter router lain
 $bq_base_get = $_GET; 

 $bq_prev_get = $bq_base_get;
 $bq_prev_get['hal_produk'] = $current_page - 1;
 $bq_url_prev = '?' . http_build_query($bq_prev_get);

 $bq_next_get = $bq_base_get;
 $bq_next_get['hal_produk'] = $current_page + 1;
 $bq_url_next = '?' . http_build_query($bq_next_get);

// Query untuk mengambil produk dengan pagination dan filter search
$query = "
    SELECT p.*, c.name as category_name 
    FROM products p 
    JOIN categories c ON p.category_id = c.id 
    $where_clause
    ORDER BY p.created_at DESC
    LIMIT " . (int)PRODUCTS_PER_PAGE . " OFFSET " . (int)$offset . "
";

$stmt = $pdo->prepare($query);
$stmt->execute($where_params);
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="bg-white p-6 rounded-lg shadow-md">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-gray-800">Kelola Produk</h1>
        <a href="create.php" class="bg-primary hover:bg-pink-700 text-white font-bold py-2 px-4 rounded-lg transition-colors">
            <i class="fas fa-plus mr-2"></i> Tambah Produk Baru
        </a>
    </div>

    <!-- FORM SEARCH PRODUK -->
    <div class="bg-gray-50 p-4 md:p-6 rounded-lg mb-6 border border-gray-200">
        <form method="GET" class="space-y-4 md:space-y-0 md:flex md:gap-4 md:items-end">
            <div class="flex-1">
                <label class="block text-sm font-medium text-gray-700 mb-2">Cari Nama Produk</label>
                <input 
                    type="text" 
                    name="search_name" 
                    value="<?php echo htmlspecialchars($search_product_name); ?>" 
                    placeholder="Masukkan nama produk..." 
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary text-sm"
                >
            </div>
            <div class="flex-1">
                <label class="block text-sm font-medium text-gray-700 mb-2">Filter Kategori</label>
                <select name="search_category" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary text-sm">
                    <option value="">-- Semua Kategori --</option>
                    <?php foreach ($categories as $category): ?>
                        <option value="<?php echo $category['id']; ?>" <?php echo ($search_category_id == $category['id']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($category['name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="flex flex-col sm:flex-row gap-2 w-full md:w-auto">
                <button 
                    type="submit" 
                    class="w-full md:w-auto bg-primary hover:bg-pink-700 text-white font-bold py-2 px-6 rounded-lg transition-colors text-sm"
                >
                    <i class="fas fa-search mr-2"></i>Cari
                </button>
                <?php if (!empty($search_product_name) || $search_category_id > 0): ?>
                    <a 
                        href="index.php" 
                        class="w-full md:w-auto text-center bg-gray-400 hover:bg-gray-500 text-white font-bold py-2 px-6 rounded-lg transition-colors text-sm"
                    >
                        <i class="fas fa-redo mr-2"></i>Reset
                    </a>
                <?php endif; ?>
            </div>
        </form>
    </div>

    <?php if ($message): ?>
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
            <span class="block sm:inline"><?php echo $message; ?></span>
        </div>
    <?php endif; ?>

    <!-- TABEL: Hanya terlihat di layar medium ke atas (tablet, desktop) -->
    <div class="hidden md:block overflow-x-auto">
        <table class="min-w-full">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Gambar</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Produk</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kategori</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Harga</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Stok</th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                <?php if (empty($products)): ?>
                    <tr>
                        <td colspan="7" class="px-6 py-4 text-center text-gray-500">
                            <?php 
                            if (!empty($search_product_name) || $search_category_id > 0) {
                                echo 'Produk tidak ditemukan sesuai pencarian Anda.';
                            } else {
                                echo 'Belum ada produk.';
                            }
                            ?>
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($products as $product): ?>
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <img src="/buketqueen/assets/images/uploads/<?php echo htmlspecialchars($product['image_url']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>" class="h-16 w-16 object-cover rounded-md">
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900"><?php echo htmlspecialchars($product['name']); ?></td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?php echo htmlspecialchars($product['category_name']); ?></td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">Rp <?php echo number_format($product['price'], 0, ',', '.'); ?></td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?php echo $product['stock']; ?></td>
                        <td class="px-6 py-4 whitespace-nowrap text-center">
                            <?php
                            $is_out_of_stock = ($product['stock'] == 0 || $product['status'] === 'out_of_stock');
                            if ($is_out_of_stock) {
                                echo '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">Habis</span>';
                            } elseif ($product['status'] === 'active') {
                                echo '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">Aktif</span>';
                            } elseif ($product['status'] === 'inactive') {
                                echo '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">Tidak Aktif</span>';
                            } else {
                                echo '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">Tidak Diketahui</span>';
                            }
                            ?>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                            <a href="edit.php?id=<?php echo $product['id']; ?>" class="text-secondary hover:text-green-700 mr-3">Edit</a>
                            <a href="delete.php?id=<?php echo $product['id']; ?>" class="text-red-600 hover:text-red-900" onclick="return confirm('Apakah Anda yakin ingin menghapus produk ini?')">Hapus</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- KARTU: Hanya terlihat di layar kecil (mobile) -->
    <div class="md:hidden space-y-4">
        <?php if (empty($products)): ?>
            <div class="text-center text-gray-500 p-4">
                <?php 
                if (!empty($search_product_name) || $search_category_id > 0) {
                    echo 'Produk tidak ditemukan sesuai pencarian Anda.';
                } else {
                    echo 'Belum ada produk.';
                }
                ?>
            </div>
        <?php else: ?>
            <?php foreach ($products as $product): ?>
            <div class="border rounded-lg p-4 shadow-sm">
                <div class="flex items-start space-x-4">
                    <img src="/buketqueen/assets/images/uploads/<?php echo htmlspecialchars($product['image_url']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>" class="h-20 w-20 object-cover rounded-md flex-shrink-0">
                    <div class="flex-1 min-w-0">
                        <h3 class="text-lg font-semibold text-gray-900"><?php echo htmlspecialchars($product['name']); ?></h3>
                        <p class="text-sm text-gray-500"><?php echo htmlspecialchars($product['category_name']); ?></p>
                        <div class="mt-2">
                            <?php
                            $is_out_of_stock = ($product['stock'] == 0 || $product['status'] === 'out_of_stock');
                            if ($is_out_of_stock) {
                                echo '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">Habis</span>';
                            } elseif ($product['status'] === 'active') {
                                echo '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">Aktif</span>';
                            } elseif ($product['status'] === 'inactive') {
                                echo '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">Tidak Aktif</span>';
                            } else {
                                echo '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">Tidak Diketahui</span>';
                            }
                            ?>
                        </div>
                        <div class="mt-2 flex items-center justify-between">
                            <div>
                                <p class="text-sm font-semibold text-gray-900">Rp <?php echo number_format($product['price'], 0, ',', '.'); ?></p>
                                <p class="text-sm text-gray-500">Stok: <?php echo $product['stock']; ?></p>
                            </div>
                            <div class="flex-shrink-0 flex space-x-2">
                                <a href="edit.php?id=<?php echo $product['id']; ?>" class="bg-secondary hover:bg-green-700 text-white text-xs font-bold py-1.5 px-3 rounded transition-colors">Edit</a>
                                <a href="delete.php?id=<?php echo $product['id']; ?>" class="bg-red-500 hover:bg-red-600 text-white text-xs font-bold py-1.5 px-3 rounded transition-colors" onclick="return confirm('Apakah Anda yakin ingin menghapus produk ini?')">Hapus</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <!-- PAGINATION CLEAN ADMIN STYLE -->
    <div style="margin-top: 24px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 12px; padding-top: 20px; border-top: 1px solid #e5e7eb;">
        
        <!-- Info Data (Kiri) -->
        <div style="font-size: 14px; color: #6b7280;">
            Menampilkan 
            <span style="font-weight: 600; color: #111827;">
                <?php 
                $start_data = (PRODUCTS_PER_PAGE * ($current_page - 1)) + 1;
                $end_data = min(PRODUCTS_PER_PAGE * $current_page, $total_products);
                if($total_products == 0) { echo '0 - 0'; } else { echo "$start_data - $end_data"; }
                ?>
            </span> 
            dari <span style="font-weight: 600; color: #111827;"><?php echo $total_products; ?></span> produk
        </div>

        <!-- Tombol Navigasi (Kanan) -->
        <div style="display: flex; gap: 8px; align-items: center;">
            
            <?php if ($current_page > 1): ?>
                <a href="<?php echo $bq_url_prev; ?>" style="padding: 8px 16px; background-color: #ffffff; border: 1px solid #d1d5db; color: #374151; text-decoration: none; border-radius: 6px; font-size: 14px; font-weight: 500;">
                    ← Sebelumnya
                </a>
            <?php else: ?>
                <span style="padding: 8px 16px; background-color: #f9fafb; border: 1px solid #f3f4f6; color: #d1d5db; border-radius: 6px; font-size: 14px; cursor: not-allowed;">
                    ← Sebelumnya
                </span>
            <?php endif; ?>

            <?php if ($current_page < $total_pages): ?>
                <a href="<?php echo $bq_url_next; ?>" style="padding: 8px 16px; background-color: #111827; border: 1px solid #111827; color: #ffffff; text-decoration: none; border-radius: 6px; font-size: 14px; font-weight: 500;">
                    Selanjutnya →
                </a>
            <?php else: ?>
                <span style="padding: 8px 16px; background-color: #f9fafb; border: 1px solid #f3f4f6; color: #d1d5db; border-radius: 6px; font-size: 14px; cursor: not-allowed;">
                    Selanjutnya →
                </span>
            <?php endif; ?>

        </div>
    </div>

</div>

<?php require_once '../includes/admin-footer.php'; ?>