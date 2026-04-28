<?php
require_once '../includes/auth.php';
require_once '../../includes/db.php';

if (!isset($_GET['id'])) {
    header('Location: index.php');
    exit;
}

$order_id = $_GET['id'];
$stmt = $pdo->prepare("UPDATE orders SET status = 'cancelled' WHERE id = ? AND status = 'pending'");
$result = $stmt->execute([$order_id]);

$message = $result ? "Pesanan berhasil dibatalkan." : "Gagal membatalkan pesanan.";

require_once '../includes/admin-header.php';
?>

<div class="bg-white p-6 rounded-lg shadow-md">
    <h1 class="text-3xl font-bold text-gray-800 mb-6">Batal Pesanan</h1>
    <p class="mb-4"><?php echo $message; ?></p>
    <a href="index.php" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded">Kembali ke Daftar Pesanan</a>
</div>

<?php require_once '../includes/admin-footer.php'; ?>