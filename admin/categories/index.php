<?php
// Perisai error
ini_set('display_errors', 0);
error_reporting(0);

require_once '../includes/auth.php';
require_once '../includes/admin-header.php';
require_once '../../includes/db.php';

// Ambil pesan sukses/hapus
 $message = '';
if (isset($_GET['status']) && $_GET['status'] == 'deleted') $message = 'Kategori berhasil dihapus.';
if (isset($_GET['status']) && $_GET['status'] == 'updated') $message = 'Kategori berhasil diperbarui.';
if (isset($_GET['status']) && $_GET['status'] == 'created') $message = 'Kategori baru berhasil ditambahkan.';

// --- KONFIGURASI PAGINATION ---
define('CATEGORIES_PER_PAGE', 10);
 $current_page = isset($_GET['hal_kategori']) ? (int)$_GET['hal_kategori'] : 1;
if ($current_page < 1) $current_page = 1;
 $offset = ($current_page - 1) * CATEGORIES_PER_PAGE;

// Hitung total kategori
 $count_stmt = $pdo->query("SELECT COUNT(*) FROM categories");
 $total_categories = (int) $count_stmt->fetchColumn();
 $total_pages = ceil($total_categories / CATEGORIES_PER_PAGE);

// Validasi halaman
if ($current_page > $total_pages && $total_pages > 0) {
    $current_page = $total_pages;
}

// Membuat URL aman agar tidak menghapus parameter router lain
 $bq_base_get = $_GET; 

 $bq_prev_get = $bq_base_get;
 $bq_prev_get['hal_kategori'] = $current_page - 1;
 $bq_url_prev = '?' . http_build_query($bq_prev_get);

 $bq_next_get = $bq_base_get;
 $bq_next_get['hal_kategori'] = $current_page + 1;
 $bq_url_next = '?' . http_build_query($bq_next_get);

// Query untuk mengambil kategori dengan pagination
 $stmt = $pdo->prepare("SELECT * FROM categories ORDER BY name ASC LIMIT :limit OFFSET :offset");
 $stmt->bindValue(':limit', CATEGORIES_PER_PAGE, PDO::PARAM_INT);
 $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
 $stmt->execute();
 $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="bg-white p-6 rounded-lg shadow-md">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-gray-800">Kelola Kategori</h1>
        <a href="create.php" class="bg-primary hover:bg-pink-700 text-white font-bold py-2 px-4 rounded-lg transition-colors">
            <i class="fas fa-plus mr-2"></i> Tambah Kategori Baru
        </a>
    </div>

    <?php if ($message): ?>
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
            <span class="block sm:inline"><?php echo $message; ?></span>
        </div>
    <?php endif; ?>

    <!-- TABEL: Hanya terlihat di layar medium ke atas -->
    <div class="hidden md:block overflow-x-auto">
        <table class="min-w-full">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Gambar</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Kategori</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Slug</th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                <?php if (empty($categories)): ?>
                    <tr>
                        <td colspan="4" class="px-6 py-4 text-center text-gray-500">Belum ada kategori.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($categories as $cat): ?>
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <img src="/buketqueen/assets/images/categories/<?php echo htmlspecialchars($cat['image_url']); ?>" alt="<?php echo htmlspecialchars($cat['name']); ?>" class="h-12 w-12 object-cover rounded-md">
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900"><?php echo htmlspecialchars($cat['name']); ?></td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?php echo htmlspecialchars($cat['slug']); ?></td>
                        <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                            <a href="edit.php?id=<?php echo $cat['id']; ?>" class="text-secondary hover:text-green-700 mr-3">Edit</a>
                            <a href="delete.php?id=<?php echo $cat['id']; ?>" class="text-red-600 hover:text-red-900" onclick="return confirm('Apakah Anda yakin ingin menghapus kategori ini?')">Hapus</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- KARTU: Hanya terlihat di layar kecil (mobile) -->
    <div class="md:hidden space-y-4">
        <?php if (empty($categories)): ?>
            <div class="text-center text-gray-500 p-4">Belum ada kategori.</div>
        <?php else: ?>
            <?php foreach ($categories as $cat): ?>
            <div class="border rounded-lg p-4 shadow-sm flex items-center justify-between">
                <div class="flex items-center space-x-3">
                    <img src="/buketqueen/assets/images/categories/<?php echo htmlspecialchars($cat['image_url']); ?>" alt="<?php echo htmlspecialchars($cat['name']); ?>" class="h-14 w-14 object-cover rounded-md flex-shrink-0">
                    <div>
                        <p class="text-sm font-medium text-gray-900"><?php echo htmlspecialchars($cat['name']); ?></p>
                        <p class="text-xs text-gray-500"><?php echo htmlspecialchars($cat['slug']); ?></p>
                    </div>
                </div>
                <div class="flex-shrink-0 flex space-x-2">
                    <a href="edit.php?id=<?php echo $cat['id']; ?>" class="bg-secondary hover:bg-green-700 text-white text-xs font-bold py-1.5 px-3 rounded transition-colors">Edit</a>
                    <a href="delete.php?id=<?php echo $cat['id']; ?>" class="bg-red-500 hover:bg-red-600 text-white text-xs font-bold py-1.5 px-3 rounded transition-colors" onclick="return confirm('Apakah Anda yakin ingin menghapus kategori ini?')">Hapus</a>
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
                $start_data = (CATEGORIES_PER_PAGE * ($current_page - 1)) + 1;
                $end_data = min(CATEGORIES_PER_PAGE * $current_page, $total_categories);
                if($total_categories == 0) { echo '0 - 0'; } else { echo "$start_data - $end_data"; }
                ?>
            </span> 
            dari <span style="font-weight: 600; color: #111827;"><?php echo $total_categories; ?></span> kategori
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