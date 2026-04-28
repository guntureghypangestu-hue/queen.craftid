<?php
require_once '../includes/auth.php';
require_once '../../includes/db.php';

if (isset($_GET['id'])) {
    $category_id = $_GET['id'];

    // 1. Ambil nama file gambar sebelum menghapus data
    $stmt = $pdo->prepare("SELECT image_url FROM categories WHERE id = ?");
    $stmt->execute([$category_id]);
    $category = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($category) {
        // 2. Hapus data dari database
        $stmt = $pdo->prepare("DELETE FROM categories WHERE id = ?");
        $stmt->execute([$category_id]);

        // 3. Hapus file gambar dari server jika bukan gambar default
        if ($category['image_url'] !== 'category-default.jpg') {
            $file_path = '../../assets/images/categories/' . $category['image_url'];
            if (file_exists($file_path)) {
                unlink($file_path);
            }
        }
    }
}

header('Location: index.php?status=deleted');
exit;
?>