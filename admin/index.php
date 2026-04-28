<?php
// Panggil file keamanan dan koneksi database
require_once 'includes/auth.php';
require_once '../includes/db.php';
require_once 'includes/admin-header.php';

// --- Query untuk Dashboard ---
// Total Produk Aktif
 $stmt = $pdo->query("SELECT COUNT(*) FROM products WHERE status = 'active'");
 $total_active_products = $stmt->fetchColumn();

// Total Produk Tidak Aktif
 $stmt = $pdo->query("SELECT COUNT(*) FROM products WHERE status != 'active'");
 $total_inactive_products = $stmt->fetchColumn();

// Total Kategori
 $stmt = $pdo->query("SELECT COUNT(*) FROM categories");
 $total_categories = $stmt->fetchColumn();

// Total Stok (dari produk aktif saja)
 $stmt = $pdo->query("SELECT SUM(stock) FROM products WHERE status = 'active'");
 $total_stock = $stmt->fetchColumn();

// Produk Terakhir Ditambahkan
 $stmt = $pdo->query("SELECT name, created_at, status FROM products ORDER BY created_at DESC LIMIT 5");
 $latest_products = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!-- Konten Dashboard -->
<div class="bg-white p-6 rounded-lg shadow-md">
    <h1 class="text-3xl font-bold text-gray-800 mb-6">Dashboard</h1>
    <p class="text-gray-600">Selamat datang di Admin Panel BuketQueen. Ini adalah ringkasan singkat toko Anda.</p>
</div>

<!-- Grid Kartu Statistik -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mt-8">
    <div class="bg-white p-6 rounded-lg shadow-md border-l-4 border-primary">
        <div class="flex items-center">
            <div class="p-3 rounded-full bg-pink-100 text-primary">
                <i class="fas fa-box text-2xl"></i>
            </div>
            <div class="ml-4">
                <p class="text-gray-500 text-sm">Produk Aktif</p>
                <p class="text-2xl font-bold text-gray-800"><?php echo $total_active_products; ?></p>
            </div>
        </div>
    </div>

    <div class="bg-white p-6 rounded-lg shadow-md border-l-4 border-red-500">
        <div class="flex items-center">
            <div class="p-3 rounded-full bg-red-100 text-red-500">
                <i class="fas fa-eye-slash text-2xl"></i>
            </div>
            <div class="ml-4">
                <p class="text-gray-500 text-sm">Tidak Aktif</p>
                <p class="text-2xl font-bold text-gray-800"><?php echo $total_inactive_products; ?></p>
            </div>
        </div>
    </div>

    <div class="bg-white p-6 rounded-lg shadow-md border-l-4 border-secondary">
        <div class="flex items-center">
            <div class="p-3 rounded-full bg-green-100 text-secondary">
                <i class="fas fa-tags text-2xl"></i>
            </div>
            <div class="ml-4">
                <p class="text-gray-500 text-sm">Total Kategori</p>
                <p class="text-2xl font-bold text-gray-800"><?php echo $total_categories; ?></p>
            </div>
        </div>
    </div>

    <div class="bg-white p-6 rounded-lg shadow-md border-l-4 border-blue-500">
        <div class="flex items-center">
            <div class="p-3 rounded-full bg-blue-100 text-blue-500">
                <i class="fas fa-warehouse text-2xl"></i>
            </div>
            <div class="ml-4">
                <p class="text-gray-500 text-sm">Total Stok</p>
                <p class="text-2xl font-bold text-gray-800"><?php echo $total_stock; ?></p>
            </div>
        </div>
    </div>
</div>

<!-- Tabel Produk Terbaru -->
<div class="bg-white p-6 rounded-lg shadow-md mt-8">
    <h2 class="text-xl font-bold text-gray-800 mb-4">5 Produk Terbaru</h2>
    <div class="overflow-x-auto">
        <table class="min-w-full">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Produk</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal Ditambahkan</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                <?php if (empty($latest_products)): ?>
                    <tr>
                        <td colspan="3" class="px-6 py-4 text-center text-gray-500">Belum ada produk.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($latest_products as $product): ?>
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900"><?php echo htmlspecialchars($product['name']); ?></td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?php echo date('d M Y, H:i', strtotime($product['created_at'])); ?></td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <!-- PERUBAHAN: Tambahkan Badge Status -->
                            <?php if ($product['status'] === 'active'): ?>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">Aktif</span>
                            <?php else: ?>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">Tidak Aktif</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>


<?php require_once 'includes/admin-footer.php'; ?>