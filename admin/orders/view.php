<?php
require_once '../includes/auth.php';
require_once '../../includes/db.php';

if (!isset($_GET['id'])) {
    header('Location: index.php');
    exit;
}

$order_id = $_GET['id'];
$stmt = $pdo->prepare("SELECT * FROM orders WHERE id = ?");
$stmt->execute([$order_id]);
$order = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$order) {
    header('Location: index.php');
    exit;
}

$items = json_decode($order['items'], true);

require_once '../includes/admin-header.php';
?>

<div class="bg-white p-6 rounded-lg shadow-md">
    <h1 class="text-3xl font-bold text-gray-800 mb-6">Detail Pesanan #<?php echo $order['id']; ?></h1>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
        <div>
            <h2 class="text-xl font-semibold mb-4">Data Pemesan</h2>
            <p><strong>Nama:</strong> <?php echo htmlspecialchars($order['customer_name']); ?></p>
            <p><strong>Telepon:</strong> <?php echo htmlspecialchars($order['customer_phone']); ?></p>
            <p><strong>Alamat:</strong> <?php echo nl2br(htmlspecialchars($order['customer_address'])); ?></p>
            <?php if ($order['notes']): ?>
                <p><strong>Catatan:</strong> <?php echo nl2br(htmlspecialchars($order['notes'])); ?></p>
            <?php endif; ?>
        </div>
        <div>
            <h2 class="text-xl font-semibold mb-4">Info Pesanan</h2>
            <p><strong>Status:</strong> 
                <span class="px-2 py-1 rounded text-sm <?php 
                    if ($order['status'] === 'pending') echo 'bg-yellow-200 text-yellow-800';
                    elseif ($order['status'] === 'confirmed') echo 'bg-green-200 text-green-800';
                    else echo 'bg-red-200 text-red-800';
                ?>">
                    <?php echo ucfirst($order['status']); ?>
                </span>
            </p>
            <p><strong>Total:</strong> Rp <?php echo number_format($order['total_amount'], 0, ',', '.'); ?></p>
            <p><strong>Tanggal:</strong> <?php echo date('d/m/Y H:i', strtotime($order['created_at'])); ?></p>
        </div>
    </div>

    <h2 class="text-xl font-semibold mb-4">Detail Produk</h2>
    <div class="overflow-x-auto">
        <table class="min-w-full bg-white border border-gray-300">
            <thead>
                <tr class="bg-gray-100">
                    <th class="py-2 px-4 border">Produk</th>
                    <th class="py-2 px-4 border">Harga</th>
                    <th class="py-2 px-4 border">Qty</th>
                    <th class="py-2 px-4 border">Subtotal</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($items as $item): ?>
                    <tr>
                        <td class="py-2 px-4 border"><?php echo htmlspecialchars($item['name']); ?></td>
                        <td class="py-2 px-4 border">Rp <?php echo number_format($item['price'], 0, ',', '.'); ?></td>
                        <td class="py-2 px-4 border"><?php echo $item['quantity']; ?></td>
                        <td class="py-2 px-4 border">Rp <?php echo number_format($item['price'] * $item['quantity'], 0, ',', '.'); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <div class="mt-6">
        <a href="index.php" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded">Kembali</a>
        <?php if ($order['status'] === 'pending'): ?>
            <a href="confirm.php?id=<?php echo $order['id']; ?>" class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded ml-2" onclick="return confirm('Konfirmasi pesanan ini?')">Konfirmasi</a>
            <a href="cancel.php?id=<?php echo $order['id']; ?>" class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded ml-2" onclick="return confirm('Batalkan pesanan ini?')">Batal</a>
        <?php endif; ?>
    </div>
</div>

<?php require_once '../includes/admin-footer.php'; ?>