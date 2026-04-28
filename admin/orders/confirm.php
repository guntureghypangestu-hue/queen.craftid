<?php
require_once '../includes/auth.php';
require_once '../../includes/db.php';

if (!isset($_GET['id'])) {
    header('Location: index.php');
    exit;
}

$order_id = $_GET['id'];
$stmt = $pdo->prepare("SELECT * FROM orders WHERE id = ? AND status = 'pending'");
$stmt->execute([$order_id]);
$order = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$order) {
    header('Location: index.php');
    exit;
}

$items = json_decode($order['items'], true);

// Kurangi stok untuk setiap item
$pdo->beginTransaction();
try {
    foreach ($items as $item) {
        $stmt = $pdo->prepare("UPDATE products SET stock = stock - ? WHERE id = ? AND stock >= ?");
        $stmt->execute([$item['quantity'], $item['id'], $item['quantity']]);
        if ($stmt->rowCount() == 0) {
            throw new Exception("Stok tidak cukup untuk produk " . $item['name']);
        }
    }

    // Update status order
    $stmt = $pdo->prepare("UPDATE orders SET status = 'confirmed' WHERE id = ?");
    $stmt->execute([$order_id]);

    $pdo->commit();
    $message = "Pesanan berhasil dikonfirmasi dan stok telah dikurangi.";
} catch (Exception $e) {
    $pdo->rollBack();
    $message = "Error: " . $e->getMessage();
}

require_once '../includes/admin-header.php';
?>

<div class="bg-white p-6 rounded-lg shadow-md">
    <h1 class="text-3xl font-bold text-gray-800 mb-6">Konfirmasi Pesanan</h1>
    <p class="mb-4"><?php echo $message; ?></p>
    <a href="index.php" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded">Kembali ke Daftar Pesanan</a>
</div>

<?php require_once '../includes/admin-footer.php'; ?>