<?php
require_once '../includes/auth.php';
require_once '../../includes/db.php';

if (isset($_GET['id'])) {
    $product_id = $_GET['id'];

    // 1. Ambil nama file gambar sebelum menghapus data
    $stmt = $pdo->prepare("SELECT image_url FROM products WHERE id = ?");
    $stmt->execute([$product_id]);
    $product = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($product) {
        // 2. Hapus data dari database
        $stmt = $pdo->prepare("DELETE FROM products WHERE id = ?");
        $stmt->execute([$product_id]);

        // 3. Hapus file gambar dari server jika bukan gambar default
        if ($product['image_url'] !== 'default.jpg') {
            $file_path = '../../assets/images/uploads/' . $product['image_url'];
            if (file_exists($file_path)) {
                unlink($file_path);
            }
        }
    }
}

// Arahkan kembali ke halaman daftar produk dengan pesan sukses
header('Location: index.php?status=deleted');
exit;
?>