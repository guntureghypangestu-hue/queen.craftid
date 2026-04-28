<?php
// PERISAI 1: Matikan error notice yang bikin layout hancur
ini_set('display_errors', 0);
error_reporting(0);

require_once '../includes/auth.php';
require_once '../../includes/db.php';

// Ambil pesan sukses/error dari session
 $message = '';
 $message_type = '';
if (isset($_SESSION['success'])) {
    $message = $_SESSION['success'];
    $message_type = 'success';
    unset($_SESSION['success']);
}
if (isset($_SESSION['error'])) {
    $message = $_SESSION['error'];
    $message_type = 'error';
    unset($_SESSION['error']);
}

// --- KONFIGURASI PAGINATION ---
 $bq_limit = 15;

// PERISAI 2: Variabel unik agar tidak tertimpa file lain
 $bq_page_num = 1;
if (isset($_GET['hal_pesanan'])) {
    $bq_page_num = (int) $_GET['hal_pesanan'];
}
if ($bq_page_num < 1) $bq_page_num = 1;

 $bq_offset = ($bq_page_num - 1) * $bq_limit;

// Hitung total pesanan
 $bq_total = (int) $pdo->query("SELECT COUNT(*) FROM orders")->fetchColumn();
 $bq_total_pages = ceil($bq_total / $bq_limit);

if ($bq_page_num > $bq_total_pages && $bq_total_pages > 0) {
    $bq_page_num = $bq_total_pages;
}

// PERISAI 4: Membuat URL yang aman (mempertahankan ?page=orders dari router admin)
 $bq_base_get = $_GET; 

 $bq_prev_get = $bq_base_get;
 $bq_prev_get['hal_pesanan'] = $bq_page_num - 1;
 $bq_url_prev = '?' . http_build_query($bq_prev_get);

 $bq_next_get = $bq_base_get;
 $bq_next_get['hal_pesanan'] = $bq_page_num + 1;
 $bq_url_next = '?' . http_build_query($bq_next_get);

// Query data (langsung dimasukkan angka bulat yang sudah 100% aman)
 $stmt = $pdo->prepare("SELECT * FROM orders ORDER BY created_at DESC LIMIT $bq_limit OFFSET $bq_offset");
 $stmt->execute();
 $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

require_once '../includes/admin-header.php';
?>

<div class="bg-white p-6 rounded-lg shadow-md">
    <h1 class="text-3xl font-bold text-gray-800 mb-6">Kelola Pesanan</h1>

    <?php if ($message): ?>
        <div class="<?php echo $message_type === 'success' ? 'bg-green-100 border-green-400 text-green-700' : 'bg-red-100 border-red-400 text-red-700'; ?> border px-4 py-3 rounded relative mb-4" role="alert">
            <span class="block sm:inline"><?php echo $message; ?></span>
        </div>
    <?php endif; ?>

    <div class="overflow-x-auto">
        <table class="min-w-full">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Telepon</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                <?php if (empty($orders)): ?>
                    <tr>
                        <td colspan="7" class="px-6 py-4 text-center text-gray-500">Belum ada pesanan.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($orders as $order): ?>
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">#<?php echo $order['id']; ?></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?php echo htmlspecialchars($order['customer_name']); ?></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?php echo htmlspecialchars($order['customer_phone']); ?></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">Rp <?php echo number_format($order['total_amount'], 0, ',', '.'); ?></td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <?php 
                                if ($order['status'] === 'pending') {
                                    echo '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">Pending</span>';
                                } elseif ($order['status'] === 'confirmed') {
                                    echo '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">Confirmed</span>';
                                } else {
                                    echo '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">Cancelled</span>';
                                }
                                ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?php echo date('d/m/Y H:i', strtotime($order['created_at'])); ?></td>
                            <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                                <a href="view.php?id=<?php echo $order['id']; ?>" class="text-blue-600 hover:text-blue-800">Lihat</a>
                                <?php if ($order['status'] === 'pending'): ?>
                                    | <a href="confirm.php?id=<?php echo $order['id']; ?>" class="text-green-600 hover:text-green-800" onclick="return confirm('Konfirmasi pesanan ini?')">Konfirmasi</a>
                                    | <a href="cancel.php?id=<?php echo $order['id']; ?>" class="text-red-600 hover:text-red-800" onclick="return confirm('Batalkan pesanan ini?')">Batal</a>
                                <?php endif; ?>
                                | <a href="delete.php?id=<?php echo $order['id']; ?>" class="text-gray-700 hover:text-gray-900 font-semibold" onclick="return confirm('HAPUS pesanan ini secara permanen? Data tidak bisa dikembalikan.')">Hapus</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

     <!-- PAGINATION CLEAN ADMIN STYLE -->
    <div style="margin-top: 24px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 12px; padding-top: 20px; border-top: 1px solid #e5e7eb;">
        
        <!-- Info Data (Kiri) -->
        <div style="font-size: 14px; color: #6b7280;">
            Menampilkan 
            <span style="font-weight: 600; color: #111827;">
                <?php 
                $start_data = ($bq_limit * ($bq_page_num - 1)) + 1;
                $end_data = min($bq_limit * $bq_page_num, $bq_total);
                if($bq_total == 0) { echo '0 - 0'; } else { echo "$start_data - $end_data"; }
                ?>
            </span> 
            dari <span style="font-weight: 600; color: #111827;"><?php echo $bq_total; ?></span> pesanan
        </div>

        <!-- Tombol Navigasi (Kanan) -->
        <div style="display: flex; gap: 8px; align-items: center;">
            
            <?php if ($bq_page_num > 1): ?>
                <a href="<?php echo $bq_url_prev; ?>" style="padding: 8px 16px; background-color: #ffffff; border: 1px solid #d1d5db; color: #374151; text-decoration: none; border-radius: 6px; font-size: 14px; font-weight: 500;">
                    ← Sebelumnya
                </a>
            <?php else: ?>
                <span style="padding: 8px 16px; background-color: #f9fafb; border: 1px solid #f3f4f6; color: #d1d5db; border-radius: 6px; font-size: 14px; cursor: not-allowed;">
                    ← Sebelumnya
                </span>
            <?php endif; ?>

            <?php if ($bq_page_num < $bq_total_pages): ?>
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
